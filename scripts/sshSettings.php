<?php
include_once('../models/settingModel.php');
$settings=new settingModel();
$setting=new Setting($_POST['host'],$_POST['user'],$_POST['password'],$_POST['port']);
$checkSSH=$settings->checkConnec($setting,false);
if(!is_int($checkSSH))
	{
		echo $checkSSH->Message;
		return;
	}
echo 0;

?>