<?php
/* 

���ã����ݿ������ļ�
����: comet
�޸�ʱ�䣺2012-07-26 16:46:56

*/
$dbhost = "localhost";
$dbuser = "ftps";
$dbpass = "ftpadmin";
$dbname = "ftps";

$mysqli = new mysqli($dbhost,$dbuser,$dbpass,$dbname);

if(mysqli_connect_errno()){
	logging("connect failed: ",mysqli_connect_error());
	exit();
}

if(!$mysqli->set_charset("utf8")){
	logging("error loading character set utf8: ",$mysqli->error());
	exit();
}

// END OF FILE
