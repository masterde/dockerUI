<?php
include_once("Notification.php");  
include_once("Setting.php");  

class settingModel { 
 
	private $configFile='/var/www/.../config.xml';
	private $dockerPackage='lxc-docker';
	private $xmlConfig;
	public $configInfo;
	
	public function __construct()
		{
			
		}
    public function checkConfigFile()
    {  
		if(!file_exists($this->configFile))return new Notification(notificationType::Danger,'Error!', 'The config file cannot be found ! ( '.$this->configFile.' ) <a href="index.php?menuID=2" class="alert-link">Create one.</a>');
		$this->xmlConfig=@simplexml_load_file($this->configFile);
		if(!isset($this->xmlConfig->ssh->host))return new Notification(notificationType::Danger,'Error!', 'The setting "host" cannot be found in the config file ! ( '.$this->configFile.' )');
		if(!isset($this->xmlConfig->ssh->user))return new Notification(notificationType::Danger,'Error!', 'The setting "sshUser" cannot be found in the config file ! ( '.$this->configFile.' )');
		if(!isset($this->xmlConfig->ssh->password))return new Notification(notificationType::Danger,'Error!', 'The setting "sshPassword" cannot be found in the config file ! ( '.$this->configFile.' )');
		
		if(!isset($this->xmlConfig->ssh->port))
		{
			$this->port=22;
			//!!!!!!NEEDS FIXING!!!!!!
			//WILL NOT CHECKCONNECT BECAUSE OF RETURN
			//!!!!!!NEEDS FIXING!!!!!!
			return new Notification(notificationType::Warning,'Warning!', 'The setting "SSH Port" cannot be found in the config file! Port <b>22</b> will be used. ( '.$this->configFile.' )');	
		}
		$this->configInfo=new Setting($this->xmlConfig->ssh->host,$this->xmlConfig->ssh->user,$this->xmlConfig->ssh->password,intval($this->xmlConfig->ssh->port));
		return 0;
    }  
      
	 public function checkConnec($settings,$checkPackage)
    {  
		//PING HOST
		//http://www.cyberciti.biz/tips/simple-linux-and-unix-system-monitoring-with-ping-command-and-scripts.html
		$ping_result=exec("ping -c2 -n -i 0.2 ".$settings->host." | grep 'received' | awk -F',' '{ print $2}' | awk '{ print $1}'");
		if($ping_result!=2)return new Notification(notificationType::Danger,'Error!', 'The host '.$settings->host.' doesn\'t seem to respond ...');
		
		//TRY SSH TO THE HOST+CHECK IF DOCKER IS INSTALLED
		//http://www.php.net/manual/en/function.ssh2-exec.php
		$connection = @ssh2_connect($settings->host, intval($settings->port));
		if (!$connection) return new Notification(notificationType::Danger,'Error!', 'SSH connection to host '.$settings->host.':'.$settings->port.' failed ! Check the port and that the service is running.');
		if(!@ssh2_auth_password($connection, $settings->user,$settings->password))return new Notification(notificationType::Danger,'Error!', 'SSH connection to host '.$settings->host.' failed ! Check your username and password.');;
		
		if($checkPackage)
		{
			$stream = ssh2_exec($connection, 'dpkg -s '.$this->dockerPackage);
			$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

			// Enable blocking for both streams
			stream_set_blocking($errorStream, true);
			stream_set_blocking($stream, true);
			$errorOutput=stream_get_contents($errorStream);
			$output=stream_get_contents($stream);
			fclose($errorStream);
			fclose($stream);
			if($errorOutput!="")return new Notification(notificationType::Danger,'Error!', $errorOutput.' @ '.$settings->host);
		}
		return 0;
    } 
      
	 public function getConfig()
		{
			//$this->xmlConfig->ssh->host,$this->xmlConfig->ssh->user,$this->xmlConfig->ssh->password,intval($this->xmlConfig->ssh->port)
			if(!is_int($this->checkConfigFile()))return new Setting("","","","");
			$this->xmlConfig=simplexml_load_file($this->configFile);
			return new Setting($this->xmlConfig->ssh->host,$this->xmlConfig->ssh->user,$this->xmlConfig->ssh->password,intval($this->xmlConfig->ssh->port));
		}
	 public function createConfig($settings)
	 {
	  try
	   {
			if(!file_exists($this->configFile))
			{
				touch($this->configFile);
			}
			$xml = new DOMDocument('1.0', 'utf-8');
			$settingstag=$xml->createElement('settings');
			$ssh=$xml->createElement('ssh');
			$host=$xml->createElement('host');
			$user=$xml->createElement('user');
			$password=$xml->createElement('password');
			$port=$xml->createElement('port');
			$host->nodeValue=$settings->host;
			$user->nodeValue=$settings->user;
			$password->nodeValue=$settings->password;
			$port->nodeValue=$settings->port;
			$ssh->appendChild($host);
			$ssh->appendChild($user);
			$ssh->appendChild($password);
			$ssh->appendChild($port);
			$settingstag->appendChild($ssh);
			$xml->appendChild($settingstag);
			$xml->save($this->configFile);
			return 0;
		}
		catch (Exception $e)
		{
			return $e;
		}
	 }
}  


?>