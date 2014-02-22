<?php

class ContainerNetwork
{
	public $IP;
	public $Prefix;
	public $GW;
	public $Bridge;
	public $PortMapping;
	public $Ports;

	
	public function __construct($containerIP,$containerPrefix,$containerGW,$containerBridge,$containerPortMapping,$containerPorts)
	{
		$this->IP=$containerIP;
		$this->Prefix=$containerPrefix;
		$this->GW=$containerGW;
		$this->Bridge=$containerBridge;
		$this->PortMapping=$containerPortMapping;
		$this->Ports=$containerPorts;
	}
}

?>