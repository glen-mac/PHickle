<?php

abstract class SocketBase 
{
	private $socket;

	public function connect($sockHost, $sockPort)
	{
		$this->socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
		socket_connect($this->socket, $sockHost, $sockPort) or die("Could not connect to server\n");  
	}

	public function disconnect()
	{
		socket_close($this->socket);
	}

	public function send($sockData)
	{
		$sockData .= chr(0);
		socket_write($this->socket, $sockData, strlen($sockData)) or die("Could not send data to server\n");
	}

	public function receive()
	{
		$_D = socket_recv($this->socket, $result, 8192, 0);
		return $result;
	}

}



?>