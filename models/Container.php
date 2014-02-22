<?php

class Container
{
	public $ID;
	public $Name;
	public $Image;
	public $Hostname;
	public $Uptime;
	public $Isrunning;
	public $IP;
	public $Created;
	//exposed ports is an array
	public $Exposedports;
	//Environment variable is an array
	public $Environmentvariables;
	
	//running processes in the container
	public $RunningProcesses;
	
	public $ContainerNetwork;
	
	public function __construct($containerID,$containerImage,$containerHostname,$containerUptime,$containerIsrunning,$containerIP)
	{
		$this->ID=$containerID;
		$this->Image=$containerImage;
		$this->Hostname=$containerHostname;
		$this->Uptime=$containerUptime;
		$this->Isrunning=$containerIsrunning;
		$this->IP=$containerIP;
	}
	public function longContainer($containerID,$containerImage,$containerHostname,$containerUptime,$containerIsrunning,$containerName,$containerCreated,$containerExposedports,$containerEnvironmentvariables,$containerNetwork,$containerRunningProcesses)
	{
		$instance=new self($containerID,$containerImage,$containerHostname,$containerUptime,$containerIsrunning,$containerNetwork->IP);
		$instance->Name=$containerName;
		$instance->Created=$containerCreated;
		$instance->Exposedports=$containerExposedports;
		$instance->Environmentvariables=$containerEnvironmentvariables;
		$instance->ContainerNetwork=$containerNetwork;
		$instance->RunningProcesses=$containerRunningProcesses;
		return $instance;
	}
}

?>