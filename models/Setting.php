<?php

class Setting
{
	public $host;
	public $user;
	public $password;
	public $port;
	
	public function __construct($settingHost,$settingUser, $settingPassword, $settingPort)
	{
		$this->host=$settingHost;
		$this->user=$settingUser;
		$this->password=$settingPassword;
		$this->port=$settingPort;
	}
}
?>