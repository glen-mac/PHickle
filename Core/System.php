<?php

require 'Penguin.php';

class Penguin Extends PengBase
{

	public function joinRoom($RoomdID, $X = 405, $Y = 360)
	{
		$this->sendPkt('s', 'j#jr', -1, $RoomdID, $X, $Y);
		$this->roomExt = $RoomdID;
		$this->roomInt = $this->arrRooms[$RoomdID]['Internal'];
	}

	public function sendMessage($Message = "hello"){
		$this->sendPkt('s', 'm#sm', $this->roomInt, $this->PengID, $Message);
	}
	
	public function sendPosition($X = 0, $Y = 0){
		$this->sendPkt('s', 'u#sp', $this->roomInt, $X, $Y);
	}

}



?>