<?php
include_once('../models/containerModel.php');

$container=new containerModel();

$intermediateImages=$container->deleteIntermediateImages();

if(!is_int($intermediateImages))
	{
		echo $intermediateImages->Message;
		return;
	}
echo 0;

?>