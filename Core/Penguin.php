<?php

require (realpath(dirname(__FILE__) . '/..') . "/Sockets/SockFunc.php");

class PengBase extends SocketBase
{
	const VER = 0.41;
	
	public $PengUser, $PengID, $PengCoins, $PengAge, $PengSession, $PengMemberTime, $PengMemberStat, $PengSafeChatStat;
	public $PengItem = array("colour"=>"","head"=>"","face"=>"","neck"=>"","body"=>"","hand"=>"","feet"=>"","flag"=>"","photo"=>"");
  //public $PengPuffle = array("id"=>"","type"=>"","head"=>"","state"=>"");
	private $PengPass;

	public $roomExt = 100;
	public $roomInt = 2;

	protected $arrErrors;
	protected $arrRooms;
	protected $arrServers;

	private $LoginHash;
	private $JoinHash;
	private $RawData;
	private $RandKey;
	private $LoginKey;
	private $ConfirmationKey;

	public $LoginStat;                                      

	public function __construct($user, $pass, $serv)
	{
		$this->LoginText();
		
		$curver = $this->file_get_contents_curl("https://raw.github.com/glen-mac/PHickle/master/Version.txt");
		if (self::VER < $curver)
		{
			die("\n[STAT]: THERE IS A NEWER VERSION AVAILABLE ($curver), YOUR CURRENT VERSION IS " . self::VER . "\nPLEASE UPDATE AS SOON AS POSSIBLE!\nhttps://github.com/glen-mac/PHickle \n\n");
		}
		
		$this->PengUser = trim($user);
		$this->PengPass = trim($pass);

		$this->checkAcStat();

		$this->arrErrors = parse_ini_file(realpath(dirname(__FILE__) . '/..') . '/Config/Errors.ini', true);
		$this->arrRooms = parse_ini_file(realpath(dirname(__FILE__) . '/..') . '/Config/Rooms.ini', true);
		$this->arrServers = parse_ini_file(realpath(dirname(__FILE__) . '/..') . '/Config/Servers.ini', true);
		

		$this->pengLogin();
		$this->pengJoinServ($serv);
		echo "[STAT]: You Have Logged In!".chr(10).chr(10);

	}

	private function LoginText()
	{
		echo chr(10)."		______________  ______      ______ ______      ".chr(10);
		echo "		___  __ \__  / / /__(_)________  /____  /____  ".chr(10);
		echo "		__  /_/ /_  /_/ /__  /_  ___/_  //_/_  /_  _ \ ".chr(10);
		echo "		_  ____/_  __  / _  / / /__ _  ,<  _  / /  __/ ".chr(10);
		echo "		/_/     /_/ /_/  /_/  \___/ /_/|_| /_/  \___/   ".self::VER.chr(10).chr(10);
		echo "			  Brought to you by G-Mac  ".chr(10).chr(10);
	}
	
	public function pengLogin()
	{
		echo chr(10)."[STAT]: Connecting to login server...".chr(10);
		$this->RandKey = $this->generateRandKey($this->arrServers['Login']['IP'], $this->arrServers['Login']['Port'], $this->arrServers['Login']['API']);
		$this->LoginHash = $this->generateLoginHash($this->PengPass, $this->RandKey);

		$this->send("<msg t='sys'><body action='login' r='0'><login z='w1'><nick><![CDATA[" . $this->PengUser . "]]></nick><pword><![CDATA[" . $this->LoginHash . "]]></pword></login></body></msg>");

		$LoginPckt = $this->receive();
		$this->handle_loginPack($LoginPckt);
		$this->disconnect();
	}

	public function pengJoinServ($serv)
	{
		$serv = ucwords(strtolower($serv));
		echo "[STAT]: Logging into $serv with account " . $this->PengUser . "...".chr(10);
		$this->RandKey = $this->generateRandKey($this->arrServers[$serv]['IP'], $this->arrServers[$serv]['Port'], $this->arrServers['Login']['API']);

		$this->JoinHash = $this->MD5Crypt($this->LoginKey . $this->RandKey) . $this->LoginKey;

		$this->send('<msg t="sys"><body action="login" r="0"><login z="w1"><nick><![CDATA[' . $this->RawData . ']]></nick><pword><![CDATA[' . $this->JoinHash . '#' . $this->ConfirmationKey . ']]></pword></login></body></msg>');
	
		$this->send('%xt%s%j#js%-1%' . $this->PengID . '%' . $this->LoginKey . '%en%');
		
		$this->send('%xt%s%g#gi%-1%');

		$this->handle_playerloadPack();

		$this->LoginStat = True;
	}

	public function handle_playerloadPack()
	{
		$raw = $this->receive();
		while (!(stripos($raw, "%xt%lp%"))) 
		{
     		 $raw = $this->receive();
    	}

    	$packAr = explode(chr(0), $raw);

    	foreach ($packAr as $key) 
    	{
    		if (stripos($key, "%lp%"))
    		{
    			$LoadPkt = $key;
    			break;
    		}
    	}

    	$packAr = explode("%", $LoadPkt);
    	array_shift($packAr);
    	array_pop($packAr);

    	$PlayerData = explode("|", $packAr[3]);

		for ($i=3; $i<=11 ; $i++) 
    	{ 
    		$this->PengItem[$i] = $PlayerData[$i];
  		}
    	$this->PengMemberStat = (($PlayerData[15]==0) ? false : true);
    	$this->PengCoins = $packAr[4];
    	$this->PengSafeChatStat = (($packAr[5]==0) ? false : true);
    	$this->PengAge = $packAr[8];
    	$this->PengSession = $packAr[10];
    	$this->PengMemberTime = (($packAr[11]=="") ? 0 : $packAr[11]);

	}

	public function handle_loginPack($hashRet)
	{
		$loginAr = explode("%", $hashRet);
		array_shift($loginAr);
		array_pop($loginAr);

		$this->RawData = $loginAr[3];
		$RawDataAr = explode("|", $loginAr[3]);

		$this->PengID = $RawDataAr[0];
		$this->LoginKey = $RawDataAr[3];
		$this->ConfirmationKey = $loginAr[4];
	}

	public function sendPkt()
	{
		$Args = func_get_args();
		$strPacket = '%xt%';
		
		$strPacket .= implode('%', $Args) . '%';
		
		$this->send($strPacket);
	}

	public function generateRandKey($IP, $Port, $API)
	{

		$this->connect($IP, $Port);

		$this->send("<msg t='sys'><body action='verChk' r='0'><ver v='" . $API . "' /></body></msg>");
		$ResultHS = $this->receive();
		while(!strpos($ResultHS, 'apiOK')) 
		{
			$ResultHS = $this->receive();
		}

		$this->send("<msg t='sys'><body action='rndK' r='-1'></body></msg>");
		$ResultHS = $this->receive();		
		while(!strpos($ResultHS, '</k>')) 
		{
			$ResultHS = $this->receive();
		}

		$XmlObj = simplexml_load_string($ResultHS);
		$this->RandKey = $XmlObj->body->k;

		return $this->RandKey;

	}

	private function MD5Crypt($data)
	{
		$strMd5Hash = md5($data);
		$strSwappedMd5Hash = substr($strMd5Hash, 16, 16) . substr($strMd5Hash, 0, 16);
		return $strSwappedMd5Hash;
	}
	
	private function generateLoginHash($PengPass, $RandKey)
	{
		$strKey = strtoupper($this->MD5Crypt($PengPass)) . $RandKey . 'a1ebe00441f5aecb185d0ec178ca2305Y(02.>\'H}t":E1_root';
		$strHash = $this->MD5Crypt($strKey);
		return $strHash;
	}

	private function checkAcStat()
	{
		$val = $this->file_get_contents_curl("https://ms.clubpenguin.com:8443/mobileas/api/json/account/login?appVersion=pl-1.0&user=" . $this->PengUser . "&pass=" . $this->PengPass);
		if (!stripos($val, "true"))
		{
			die("\n[STAT]: Account credentials incorrect\n");
		} 
	}

	private function file_get_contents_curl( $url )
	{
    	$options = array(
     	   	CURLOPT_RETURNTRANSFER => true,     
      	  	CURLOPT_HEADER         => false,    
       	 	CURLOPT_FOLLOWLOCATION => true,     
       	 	CURLOPT_ENCODING       => "",     
       	 	CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0", 
       	 	CURLOPT_AUTOREFERER    => true,     
        	CURLOPT_CONNECTTIMEOUT => 120,    
        	CURLOPT_TIMEOUT        => 120,      
        	CURLOPT_MAXREDIRS      => 10,       
        	CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSLVERSION     => 3    
    		);

    	$ch = curl_init( $url );
    	curl_setopt_array( $ch, $options );
    	$content = curl_exec( $ch );
    	curl_close( $ch );

    	return $content;
	}



}


?>