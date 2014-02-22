<div class="row">

    <?php include 'views/subMenuView.php'; ?>

<div class="col-sm-9 col-sm-offset-2 col-md-10 col-md-offset-1 main">
<h1>Dashboard</h1>
<div id="notificationContainer">
</div>
<?php

	$updown="Uptime";
	$startstopbutton='<button type="button" isStart="false" class="btn btn-danger" id="btnStartStop" data-loading-text="Stopping..." containerID="'.$myContainer->ID.'">Stop</button>';
	$restartbutton='<button type="button" id="btnRestart" class="btn btn-warning" data-loading-text="Restarting..." containerID="'.$myContainer->ID.'">Restart</button>';
	if($myContainer->Isrunning=="danger")
		{
			$updown="Downtime";
			$startstopbutton='<button type="button" isStart="true" class="btn btn-success" id="btnStartStop" data-loading-text="Starting..." containerID="'.$myContainer->ID.'">Start</button>';
			$restartbutton='';
		}
		
	$summary=array("Container ID"=>$myContainer->ID,"Image"=>$myContainer->Image,$updown=>$myContainer->Uptime);


	echo '<div class="panel panel-'.$myContainer->Isrunning.'">';
	echo '<div class="panel-heading">';
    echo '<h3 class="panel-title"><strong>'.$myContainer->Hostname.'</strong></h3>';
	echo '</div>';
	echo '<div class="panel-body">';
	echo '<div class="panel">';
	echo '<div class="panel-heading">';
    echo '<h3 class="panel-title">Summary</h3>';
	echo '</div>';
	echo '<div class="panel-body">';
	foreach($summary as $key=>$value)
	{
		echo '<div class="col-md-1">';
		echo '<b>'.$key.'</b>';
		echo '</div>';
		echo '<div class="col-md-5" style="overflow:hidden; text-overflow:ellipsis;">';
		echo $value;
		echo '</div>';
	}
	echo '</div>';
	echo '</div>';
	
	if($myContainer->Isrunning!="danger")
	{
		$summary=array("IP Address"=>$myContainer->ContainerNetwork->IP.'/'.$myContainer->ContainerNetwork->Prefix,"Gateway"=>$myContainer->ContainerNetwork->GW,"Bridge"=>$myContainer->ContainerNetwork->Bridge);
		echo '<div class="panel">';
		echo '<div class="panel-heading">';
		echo '<h3 class="panel-title">Network</h3>';
		echo '</div>';
		echo '<div class="panel-body">';
		foreach($summary as $key=>$value)
		{
			echo '<div class="col-md-1">';
			echo '<b>'.$key.'</b>';
			echo '</div>';
			echo '<div class="col-md-5" style="overflow:hidden; text-overflow:ellipsis;">';
			echo $value;
			echo '</div>';
		}
		echo '</div>';
		echo '</div>';
		
		//display top processes
		echo '<div class="panel hidden-sm hidden-xs">';
		echo '<div class="panel-heading">';
		echo '<h3 class="panel-title">Processes</h3>';
		echo '</div>';
		echo '<div class="panel-body">';
		echo '<pre><code>'.$myContainer->RunningProcesses.'</code></pre>';
		echo '</div>';
		echo '</div>';
	}
	
    echo '</div>';
	echo '<div class="panel-footer">';
	echo '<div class="btn-group">';
	echo $startstopbutton;
	echo $restartbutton;
	echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-loading-text="Deleting..." id="btncontainerDelete" data-target="#modalDeleteContainer">Delete</button>';
	echo '</div>';
	
	echo '</div>';
	echo '</div>';

?>
</div>
</div>
<?php include 'views/deleteContainerModalView.php'; ?>