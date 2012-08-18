<?php
/* 
作用：接受用户搜索(搜索文件名，文件夹名，TTH)，记录关键词，并返回XML
作者: comet
修改时间：2012-07-27 00:23:18 
*/
require_once "head.php";

// 头部信息
header("Content-type: text/xml; charset=utf-8");
header("Cache-Control: no-cache");

//$content = $_GET["q"];
foreach($_POST as $key => $value){
	$$key = $mysqli->real_escape_string($value);	//不转义%和_这两个符号
}

if(!isset($content) && strlen(trim($content))<2){
	Header("Location:./");
}

//插入用户搜索关键词
$ip=getIP();
$sqlukw="INSERT INTO ftps_UserKeyWord(UserIP,UserKeyword,KeywordTime) VALUES('".$ip."','".$content."',".time().")";
$mysqli->query($sqlukw);

//插入搜索关键词，分割，以便针对单个关键字统计
/* 分割规则：
空格：同时包含一个关键字； 
,：包含任意一个关键字； 
-：不包含该关键字；
mkv,john aac: (aac) AND (mkv OR john)
mkv john,aac: (mkv) AND (john OR aac)
*/
$sqlkwv = "";
$sqlkw1 = "";
$sqlkw2 = "";
$kwArr = explode(" ",$content);
foreach($kwArr as $kwv){
	$pos = strpos($kwv,',');
	if($pos === false){
		if(strlen($sqlkw1) == 0){
			$sqlkw1 .= " LOCATE('".$kwv."',CONCAT(fileName,fileDir)) > 0";
		}else{
			
			$sqlkw1 .= " AND LOCATE('".$kwv."',CONCAT(fileName,fileDir)) > 0";
		}
		$sqlkw1 = "(".$sqlkw1.")";
		$sqlkw = "SELECT id FROM ftps_Keywords WHERE sKeyword='".$kwv."' LIMIT 0,1";
		$reskw = $mysqli->query($sqlkw);
		if($reskw->num_rows){
			$sqlkw="UPDATE ftps_Keywords SET sHit=sHit+1 WHERE sKeyword ='".$kwv."'";
		}else{
			$sqlkw="INSERT ftps_Keywords(sKeyword,sHit) VALUES('".$kwv."',1)";
		}
		$mysqli->query($sqlkw);
	}else{
		$kwvArr = explode(",",$kwv);
		foreach($kwvArr as $kwvv){
			if(strlen($sqlkw2) == 0){
				$sqlkw2 .= " LOCATE('".$kwvv."',CONCAT(fileName,fileDir)) > 0";
			}else{
				$sqlkw2 .= " OR LOCATE('".$kwvv."',CONCAT(fileName,fileDir)) > 0";
			}
			$sqlkw = "SELECT id FROM ftps_Keywords WHERE sKeyword='".$kwv."' LIMIT 0,1";
			$reskw = $mysqli->query($sqlkw);
			if($reskw->num_rows){
				$sqlkw="UPDATE ftps_Keywords SET sHit=sHit+1 WHERE sKeyword ='".$kwv."'";
			}else{
				$sqlkw="INSERT ftps_Keywords(sKeyword,sHit) VALUES('".$kwv."',1)";
			}
			$mysqli->query($sqlkw);
		}
		$sqlkw2 = "(".$sqlkw2.")";
	}
}

if(strlen($sqlkw1) == 0){
	$sqlkwv = "$sqlkw2";
}elseif(strlen($sqlkw2) == 0){
	$sqlkwv = "$sqlkw1";
}else{
	$sqlkwv = "$sqlkw1 AND $sqlkw2";
}

//查询数据，用 locate 代替 like，效率高；同时搜索fileName和fileDirectory
$sql="SELECT ff.`fileID`,ff.`fileName`,ff.`fileDir`,ff.`fileSize`,ff.`fileTime`,ff.`fileType`,fs.`ftpServer`,fs.`ftpPort`,fs.`remoteDir`,fs.`ftpID` FROM `ftps_FtpFileInUsed` ff LEFT JOIN `ftps_FtpSrvInUsed` fs ON ff.`ftpID`=fs.`ftpID` WHERE $sqlkwv ORDER BY ff.`ftpID`";
//die($sql);

$res = $mysqli->query($sql);
$counts = $mysqli->affected_rows;
$totalSize = 0;

//返回xml数据结构
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<response>\n";
echo "\t<countnum>$counts</countnum>\n";
while($row = $res->fetch_array()){
	$fileName = htmlspecialchars(stripslashes($row['fileName']));
	$fileDir = htmlspecialchars(stripslashes($row['fileDir']));
	$fileTime = date('Y-m-d H:i:s',$row['fileTime']);
	$totalSize += $row['fileSize'];
	$fileSize = formatSize($row['fileSize']);
	if($row['ftpPort'] == 21){
		$ftpServer = $row['ftpServer'];
	}else{
		$ftpServer = $row['ftpServer'].":".$row['ftpPort'];
	}
	echo "\t<msg>\n";
	echo "\t<fileID>$row[fileID]</fileID>\n";
	echo "\t<ftpID>$row[ftpID]</ftpID>\n";
	echo "\t<fileName>$fileName</fileName>\n";
	echo "\t<fileDir>$fileDir</fileDir>\n";
	echo "\t<fileSize>$fileSize</fileSize>\n";
	echo "\t<fileTime>$fileTime</fileTime>\n";
	echo "\t<fileType>$row[fileType]</fileType>\n";
	echo "\t<ftpServer>$ftpServer</ftpServer>\n";
	echo "\t<remoteDir>$row[remoteDir]</remoteDir>\n";
	echo "\t</msg>\n";
}
$totalSize = formatSize($totalSize);
echo "\t<sizenum>$totalSize</sizenum>\n";
echo "</response>";

// END OF FILE
