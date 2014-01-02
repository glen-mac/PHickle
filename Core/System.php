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
	
	public function addItem($ID){
		$this->sendXt('s', 'i#ai', $this->roomInt, $ID);
	}
	
	public function addFurniture($ID){
		$this->sendXt('s', 'i#af', $this->roomInt, $ID);
	}
	
	public function throwSnowball($X = 0, $Y = 0){
		$this->sendXt('s', 'u#sb', $this->roomInt, $X, $Y);
	}
	
	public function sendEmote($ID){
		$this->sendXt('s', 'u#se', $this->roomInt, $ID);
	}
	
	public function sendJoke($ID){
		$this->sendXt('s', 'u#sj', $this->roomInt, $ID);
	}

}



?>