<?php
include_once("models/notificationModel.php");  
  
class notificationController {  
     public $notificationModel;   
    // public $lastNotification;
	 
     public function __construct()    
     {    
          $this->notificationModel = new notificationModel();  
     }   

	 public function getNotification()  
     {  
		$notification = $this->notificationModel->getNotification();  
     }
	
}  
?>