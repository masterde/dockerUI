<?php
include_once("Container.php");  
include_once('ContainerNetwork.php');
include_once("settingModel.php");  
include_once("Setting.php");  

class containerModel { 
	//new Container("1", "Container1", "Image1","hostname1","193.168.10.1","1min"),
	
	
	//docker rmi $(docker images | grep "^<none>" | awk "{print $3}")
	
	private $settings;
	private $settingModel;
	
	public function __construct()
	{		
		$this->settingModel=new settingModel();
		$this->settings=$this->settingModel->getConfig();
	}
	
    public function getContainerList($args)  
    {  
		
		$connection = ssh2_connect($this->settings->host, intval($this->settings->port));
		if (!$connection) return new Notification(notificationType::Danger,'Error!', 'SSH connection to host '.$this->settings->host.':'.$this->settings->port.' failed ! Check the port and that the service is running.');		
		ssh2_auth_password($connection, $this->settings->user,$this->settings->password);
		
		//count the number of containers running
		$stream = ssh2_exec($connection, "docker ps | wc -l");
		stream_set_blocking($stream, true);
		$output=stream_get_contents($stream);
		fclose($stream);
		//only return warning if we display active containers ($args is empty)
		if($output==1 && $args=='')return new Notification(notificationType::Warning,'Warning!', 'No active container running @ '.$this->settings->host);
		
		
		$stream = ssh2_exec($connection, "docker ps $args | cut -d ' ' -f 1 | sed -n '1!p' | xargs docker inspect");
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

		// Enable blocking for both streams
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		$errorOutput=stream_get_contents($errorStream);
		$output=stream_get_contents($stream);
		$containerArray=json_decode($output);
		fclose($errorStream);
		fclose($stream);
		if($errorOutput!="")return new Notification(notificationType::Danger,'Error!', $errorOutput.' @ '.$this->settings->host);
		$containerList=array();
		for($i=0;$i<sizeof($containerArray);$i++)
		{
			
			//used to change row color (=bootstrap class)
			$isrunning="danger";
			if($containerArray[$i]->State->Running=="true")$isrunning="success";
			
			//calculate uptime
			//the time from docker looks like this : 2014-02-06T13:32:03.776577907Z
			//strtotime doesn't parse time with ms so you have to trim the first 19char
			$time1=strtotime(substr($containerArray[$i]->State->StartedAt,0,19));
			if($isrunning=="danger")$time1=strtotime(substr($containerArray[$i]->State->FinishedAt,0,19));
			$time2=strtotime("now");
			$UPtime=$this->seconds2human($time2-$time1);
			
			$myContainer=new Container($containerArray[$i]->ID,$containerArray[$i]->Config->Image,$containerArray[$i]->Config->Hostname,$UPtime,$isrunning,$containerArray[$i]->NetworkSettings->IPAddress);
			array_push($containerList,$myContainer);
		}
		return $containerList;
       
    }  
      
    public function getContainer($containerID)  
    {  
		
		$connection = ssh2_connect($this->settings->host, intval($this->settings->port));
		if (!$connection) return new Notification(notificationType::Danger,'Error!', 'SSH connection to host '.$this->settings->host.':'.$this->settings->port.' failed ! Check the port and that the service is running.');		
		ssh2_auth_password($connection, $this->settings->user,$this->settings->password);
		$stream = ssh2_exec($connection, "docker inspect $containerID");
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

		// Enable blocking for both streams
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		$errorOutput=stream_get_contents($errorStream);
		$output=stream_get_contents($stream);
		$containerArray=json_decode($output);
		fclose($errorStream);
		fclose($stream);
		if($errorOutput!="")return new Notification(notificationType::Danger,'Error!', $errorOutput.' @ '.$this->settings->host);
			
		
		
		//used to change row color (=bootstrap class)
		$isrunning="danger";
		if($containerArray[0]->State->Running=="true")$isrunning="success";
			
		//get running processes of the container	
		$runningProcesses='';
		$stream=ssh2_exec($connection, "docker top $containerID");
		stream_set_blocking($stream, true);
		if($isrunning=="success")$runningProcesses=stream_get_contents($stream);
		fclose($stream);
		
		//calculate uptime
		//the time from docker looks like this : 2014-02-06T13:32:03.776577907Z
		//strtotime doesn't parse time with millis so you have to trim the first 19chars
		$time1=strtotime(substr($containerArray[0]->State->StartedAt,0,19));
		if($isrunning=="danger")$time1=strtotime(substr($containerArray[0]->State->FinishedAt,0,19));
		$time2=strtotime("now");
		$UPtime=$this->seconds2human($time2-$time1);
		$containerNetwork=new ContainerNetwork($containerArray[0]->NetworkSettings->IPAddress,$containerArray[0]->NetworkSettings->IPPrefixLen,$containerArray[0]->NetworkSettings->Gateway,$containerArray[0]->NetworkSettings->Bridge,$containerArray[0]->NetworkSettings->PortMapping,$containerArray[0]->NetworkSettings->Ports);	
		$myContainer= Container::longContainer($containerArray[0]->ID,$containerArray[0]->Config->Image,$containerArray[0]->Config->Hostname,$UPtime,$isrunning,$containerArray[0]->Name,$containerArray[0]->Created,$containerArray[0]->Config->ExposedPorts,$containerArray[0]->Config->Env,$containerNetwork,$runningProcesses);
		
		return $myContainer;
        
    }  
      
	  
	  public function cmdContainer($cmd)  
    {  
		$connection = ssh2_connect($this->settings->host, intval($this->settings->port));
		if (!$connection) return new Notification(notificationType::Danger,'Error!', 'SSH connection to host '.$this->settings->host.':'.$this->settings->port.' failed ! Check the port and that the service is running.'.$this->settings->host.'-'. intval($this->settings->port));		
		ssh2_auth_password($connection, $this->settings->user,$this->settings->password);
		$stream = ssh2_exec($connection,$cmd);
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

		// Enable blocking for both streams
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		$errorOutput=stream_get_contents($errorStream);
		fclose($errorStream);
		fclose($stream);
		if($errorOutput!="")return new Notification(notificationType::Danger,'Error!', $errorOutput.' @ '.$this->settings->host);
			
		return 0;
        
    }
	
	 public function createContainer($hostname,$image,$cmd)  
    {  
		$connection = ssh2_connect($this->settings->host, intval($this->settings->port));
		if (!$connection) return new Notification(notificationType::Danger,'Error!', 'SSH connection to host '.$this->settings->host.':'.$this->settings->port.' failed ! Check the port and that the service is running.'.$this->settings->host.'-'. intval($this->settings->port));		
		ssh2_auth_password($connection, $this->settings->user,$this->settings->password);
		$stream = ssh2_exec($connection,'docker run -i -d -h '.$hostname.' -t '.$image.' '.$cmd);
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

		// Enable blocking for both streams
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		$errorOutput=stream_get_contents($errorStream);
		$output=stream_get_contents($stream);
		fclose($errorStream);
		fclose($stream);
		if($errorOutput!="")return new Notification(notificationType::Danger,'Error!', $errorOutput.' @ '.$this->settings->host);
			
		return $output;
        
    }
	  
	public function infoDocker()
    {  
		$connection = ssh2_connect($this->settings->host, intval($this->settings->port));
		if (!$connection) return new Notification(notificationType::Danger,'Error!', 'SSH connection to host '.$this->settings->host.':'.$this->settings->port.' failed ! Check the port and that the service is running.'.$this->settings->host.'-'. intval($this->settings->port));		
		ssh2_auth_password($connection, $this->settings->user,$this->settings->password);
		$stream = ssh2_exec($connection,"docker info");
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

		// Enable blocking for both streams
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		$errorOutput=stream_get_contents($errorStream);
		$output=stream_get_contents($stream);
		fclose($errorStream);
		fclose($stream);
		//if($errorOutput!="")return new Notification(notificationType::Danger,'Error!', $errorOutput.' @ '.$this->settings->host);
			
		return $output.$errorOutput;
        
    }
	
	public function getImages()
	{
		$connection = ssh2_connect($this->settings->host, intval($this->settings->port));
		if (!$connection) return new Notification(notificationType::Danger,'Error!', 'SSH connection to host '.$this->settings->host.':'.$this->settings->port.' failed ! Check the port and that the service is running.'.$this->settings->host.'-'. intval($this->settings->port));		
		ssh2_auth_password($connection, $this->settings->user,$this->settings->password);
		
		//get number of images
		$stream = ssh2_exec($connection,"docker images | wc -l");
		stream_set_blocking($stream, true);
		if(stream_get_contents($stream)==1)return new Notification(notificationType::Danger,'Error!', 'No image available ! Please use <code>docker pull [image-name]</code> on the server. @ '.$this->settings->host);
		//get Images Names and Size+Unit
		$stream = ssh2_exec($connection,"docker images | awk '{print($1\"  (\"$7$8\")#\")}' | sed -n '1!p'");
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

		// Enable blocking for both streams
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		$errorOutput=stream_get_contents($errorStream);
		$output=stream_get_contents($stream);
		fclose($errorStream);
		fclose($stream);
		if($errorOutput!="")return new Notification(notificationType::Danger,'Error!', $errorOutput.' @ '.$this->settings->host);
		
		$images=explode('#',$output,-1);
		$list_images=array();
		foreach($images as $image)
		{
			array_push($list_images,preg_split('/\s+/', $image,-1,PREG_SPLIT_NO_EMPTY));
		}	
		return $list_images;
	}
	
	public function getNumberIntermediateImages()
    {  
		$connection = ssh2_connect($this->settings->host, intval($this->settings->port));
		if (!$connection) return new Notification(notificationType::Danger,'Error!', 'SSH connection to host '.$this->settings->host.':'.$this->settings->port.' failed ! Check the port and that the service is running.'.$this->settings->host.'-'. intval($this->settings->port));		
		ssh2_auth_password($connection, $this->settings->user,$this->settings->password);
		
		//get the number of unused images
		$stream = ssh2_exec($connection,'docker images -a | grep "^<none>" | awk "{print $3}" | wc -l');
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		$errorOutput=stream_get_contents($errorStream);
		$output=stream_get_contents($stream);
		fclose($errorStream);
		fclose($stream);
		if($errorOutput!="")return new Notification(notificationType::Danger,'Error!', $errorOutput.' @ '.$this->settings->host);
	
		return $output;   
    }
	
	public function deleteIntermediateImages()
    {  
		$connection = ssh2_connect($this->settings->host, intval($this->settings->port));
		if (!$connection) return new Notification(notificationType::Danger,'Error!', 'SSH connection to host '.$this->settings->host.':'.$this->settings->port.' failed ! Check the port and that the service is running.'.$this->settings->host.'-'. intval($this->settings->port));		
		ssh2_auth_password($connection, $this->settings->user,$this->settings->password);
		
		//delete intermediate images
		$stream = ssh2_exec($connection,"docker rmi $(docker images -a | grep '<none>' | awk '{print $3}')");
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		$errorOutput=stream_get_contents($errorStream);
		$output=stream_get_contents($stream);
		fclose($errorStream);
		fclose($stream);
		if($errorOutput!="")return new Notification(notificationType::Danger,'Error!', $errorOutput.' @ '.$this->settings->host);
	
		return 0;   
    }
	  
	  
	  
	  private function seconds2human($ss) 
	{
	    $ss-=7200;
		$s = $ss % 60;
		$m = (floor(($ss%3600)/60)>0)?floor(($ss%3600)/60).'':'';
		$h = (floor(($ss % 86400) / 3600)>0)?floor(($ss % 86400) / 3600).' h':'';		
		$d = (floor(($ss % 2592000) / 86400)>0)?floor(($ss % 2592000) / 86400).' d':'';
		$M = (floor($ss / 2592000)>0)?floor($ss / 2592000).'months':'';
		if(floor(($ss % 60)<60) && $m=='' && $h=='' && $d=='')return "$s sec";
		if(floor(($ss%3600)/60)<60 && $h=='' && $d=='')return "$m min";
		return "$d $h";
	}
      
}  
?>