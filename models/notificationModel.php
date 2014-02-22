<?php
include_once("Notification.php");  
include_once("settingModel.php");  
  
class notificationModel { 
 
	private $settings;
 
    
      
	  public function checkConfigFile()
	  {
		//check config
		$this->settings=new settingModel();
		$notification=$this->settings->checkConfigFile();
		if(!is_int($notification))return $notification;
		$notification=$this->settings->checkConnec($this->settings->configInfo,true);
		if(!is_int($notification))return $notification;
		
		return 0;
	  }
	  
}  


?>