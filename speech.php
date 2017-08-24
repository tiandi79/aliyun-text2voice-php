<?php
/********************
*  阿里云文本转语音功能
*  by tiandi 
*  www.tiandiyoyo.com
*********************/

class speech {
	private $ACCESS_ID = "";
	private $SECRET = "";
	private $_content;
	private $_debug;
	private $_host = "http://nlsapi.aliyun.com";
	private $_path = "/speak?";
	private $_voice_name = "xiaoyun";
	private $_volume = 50;
	private $_encode_type = "mp3";
	public static $_mp3file = "topic.mp3";


	function __construct($flag = false) {
		$this->_debug = $flag;
	}

	public function doconvert($content) {
		$this->_content = $content;
		$method = "POST";
		$content_type = "text/plain";
		$accept = "audio/wav,application/json";
		$date = gmdate("l d F Y H:i:s")." GMT";
		$content = $this->base64md5($content);
		$feature = $method."\n".$accept."\n".$content."\n".$content_type."\n".$date;
		$this->logger('feature',$feature);
		$signature = $this->getsign($feature);
		$this->logger('signature',$signature);
		$url = $this->_host.$this->_path."encode_type=".$this->_encode_type."&voice_name=".$this->_voice_name."&volume".$this->_volume;

		$headers = array();

		array_push($headers, "content-type:".$content_type);
		array_push($headers, "accept:".$accept);
		array_push($headers, "Date:".$date);
		array_push($headers, "Authorization: Dataplus ".$this->ACCESS_ID.":".$signature);

		$fp = fopen(self::$_mp3file, 'w');
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FILE,$fp);
		if (1 == strpos("$".$this->_host, "https://"))
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_content);
		$rs = curl_exec($curl);
		$httpret = curl_getinfo($curl);
		$this->logger('curlresult',$rs);
		$this->logger('httpresponse',$httpret);
		curl_close ($curl);
		fclose($fp);

		if('200' == $httpret['http_code']) {
			$this->logger('content',$this->_content);
			$this->logger('contentlength',mb_strlen($this->_content,'utf8'));
			return true;
		}
		elseif($rs != true) {
			$js = json_decode($rs);
			foreach($js as $k=>$v) {
				echo $k.":".$v."<br>";
			}
		}
		return false;
	}

	private function logger($name,$str) {
		if($this->_debug) {
			echo "<br>*****************".$name."******************<br>";
			var_dump($str);
		}
	}

	private function base64md5($str) {
		return base64_encode(md5($str,true));
	}

	private function getsign($str) {
		return base64_encode(hash_hmac('sha1',$str,$this->SECRET,true));
	}

	private function mbstrsplit ($string, $len=300) {
		$start = 0;
		$strlen = mb_strlen($string);
		while ($strlen) {
			$array[] = mb_substr($string,$start,$len,"utf8");
			$string = mb_substr($string, $len, $strlen,"utf8");
			$strlen = mb_strlen($string);
		}
		return $array;
	}

}

