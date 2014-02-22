<?php

class Notification
{
	public $Type;
	public $Message;
    public $Title;
	
	public function __construct($notificationType,$notificationTitle, $notificationMessage)
	{
		$this->Type=$notificationType;
		$this->Message=$notificationMessage;
		$this->Title=$notificationTitle;
	}
}
class notificationType {
    const __default = self::Warning;
	
    //represent css classes in dockerUI.css
    const Warning = "warning";
    const Success = "success";
    const Danger = "danger";
}
?>