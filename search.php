<?php
require_once "inc/get_info.php";
require_once "inc/conn.php";


// 错误报告
error_reporting(E_ALL);

// 头部信息
header("Content-type: text/xml; charset=utf-8");
header("Cache-Control: no-cache");

//连接mysql
$dbconn = mysql_connect($dbhost,$dbuser,$dbpass);
mysql_select_db($dbname,$dbconn);
mysql_query("SET NAMES 'UTF8'");

foreach($_POST as $key => $value){
	$$key = mysql_real_escape_string($value, $dbconn);
}

if(!isset($content) && strlen(trim($content))<2){
	Header("Location:./");
}

//插入搜索关键词
$sqlkw="SELECT id FROM ftps_Keywords WHERE sKeyword='".$content."' LIMIT 0,1";
$reskw = mysql_query($sqlkw);
if(mysql_num_rows($reskw)){
	$sqlkw="UPDATE ftps_Keywords SET sHit=sHit+1 WHERE sKeyword ='".$content."'";
}else{
	$sqlkw="INSERT INTO ftps_Keywords(sKeyword,sHit) VALUES('".$content."',1)";
}
mysql_query($sqlkw);

//插入用户搜索关键词
$ip=GetIP();
$sqlukw="INSERT INTO ftps_UserKeyWord(UserIP,UserKeyword,KeywordTime) VALUES('".$ip."','".$content."',".time().")";
mysql_query($sqlukw);

//查询数据
$sql="SELECT `fileID`,`fileName`,`fileDir`,`fileSize`,`fileTime`,`fileType`,`ftpServer`,`ftpPort`,`remoteDir`,`ftps_FtpFileInUsed`.`ftpID` FROM `ftps_FtpFileInUsed`,`ftps_FtpSrvInUsed` WHERE `ftps_FtpFileInUsed`.`ftpID`=`ftps_FtpSrvInUsed`.`ftpID` AND `fileName` LIKE '%".$content."%'";
$searchresults = mysql_query($sql,$dbconn);
$counts=0;

//返回xml数据结构
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<response>\n";
while($searchresult = mysql_fetch_array($searchresults)){
	$fileName = htmlspecialchars(stripslashes($searchresult['fileName']));
	$fileDir = htmlspecialchars(stripslashes($searchresult['fileDir']));
	$fileTime = date('Y-m-d H:i:s',$searchresult['fileTime']);
	$fileSize = $searchresult['fileSize'];
	if($fileSize>1073741824){
		$fileSize = round($fileSize/1073741824)."GB";	//round 四舍五入
	}elseif($fileSize>1048576){
		$fileSize = round($fileSize/1048576)."MB";
	}elseif($fileSize>1024){
		$fileSize = round($fileSize/1024)."KB";
	}else{
		$fileSize .= "B";
	}
	if($searchresult['ftpPort'] == 21){
		$ftpServer = $searchresult['ftpServer'];
	}else{
		$ftpServer = $searchresult['ftpServer'].":".$searchresult['ftpPort'];
	}
	$counts++;
	echo "\t<msg>\n";
	echo "\t<fileID>$searchresult[fileID]</fileID>\n";
	echo "\t<ftpID>$searchresult[ftpID]</ftpID>\n";
	echo "\t<fileName>$fileName</fileName>\n";
	echo "\t<fileDir>$fileDir</fileDir>\n";
	echo "\t<fileSize>$fileSize</fileSize>\n";
	echo "\t<fileTime>$fileTime</fileTime>\n";
	echo "\t<fileType>$searchresult[fileType]</fileType>\n";
	echo "\t<ftpServer>$ftpServer</ftpServer>\n";
	echo "\t<remoteDir>$searchresult[remoteDir]</remoteDir>\n";
	echo "\t</msg>\n";
}
echo "<countnum>\n";
echo $counts."\n";
echo "</countnum>\n";
echo "</response>";
?>
