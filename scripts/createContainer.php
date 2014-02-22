<?php
include_once('../models/containerModel.php');

$container=new containerModel();

$cmdContainer=$container->createContainer($_POST['hostname'],$_POST['image'],$_POST['cmd']);

$patternID='/[0-9a-fA-F]{64}/';

if(@preg_match($patternID,@substr($cmdContainer,0,-1))!=1)
	{
		echo $cmdContainer->Message;
		return;
	}
echo $cmdContainer;

?>