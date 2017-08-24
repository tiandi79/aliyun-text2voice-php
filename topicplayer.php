<?php

if(isset($_GET['r']) && $_GET['r']!= '') {
	require 'speech.php';
	$speech = new speech();

	if($speech->doconvert(trim($_GET['r']))) 
	{
		header('location:'.speech::$_mp3file."?".rand());
	}
	else
		echo "unknow error.";
	exit;
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>文本合成语音工具</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<META HTTP-EQUIV="Expires" CONTENT="0"> 
</head>
<body>
<textarea name="r" form="thisform" placeholder="请输入300字以内的文字。" cols="50" rows="10"></textarea> 
<form method= 'GET' id="thisform">
<br>
<input type="submit" value="合成">
</form>
</body>
</html>

