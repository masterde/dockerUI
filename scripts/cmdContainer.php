<?php
include_once('../models/containerModel.php');

$container=new containerModel();

$cmdContainer=$container->cmdContainer($_POST['command']);

if(!is_int($cmdContainer))
	{
		echo $cmdContainer->Message;
		return;
	}
echo 0;

?>