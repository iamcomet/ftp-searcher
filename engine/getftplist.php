<?php

require "./conndb_class.php";

$DB = new DBConfig();
$DB -> config();
$DB -> conn();
// fix character chao by comet
mysql_query("SET NAMES 'utf8'");

//$NewLine = "<br />";
//global $NewLine;
//$NewLine = "\n";

// get ftp server configuration
getConfig();
$DB -> close();


function logging($logmsg)
{
	echo $logmsg."\n";
}

function getConfig()
{
	$results = mysql_query("SELECT ftpID,ftpServer,ftpPort,ftpUser,ftpPass,remoteDir FROM ftps_FtpSrvInUsed") or die('Query failed: ' . mysql_error());
	while ($line = mysql_fetch_array($results, MYSQL_ASSOC))
	{
		$ftp_id = $line["ftpID"];
		$ftp_server = $line["ftpServer"];
		$ftp_port = $line["ftpPort"];
		$ftp_user_name = $line["ftpUser"];
		$ftp_user_pass = $line["ftpPass"];
		$ftp_remote_dir = $line["remoteDir"];	// DO NOT end with "/"
	
		$StartGetTime = time();
		$FileCount = 0;
		$FolderCount = 0;
		$SumSize = 0;
		$ot="";
	
		// Connect to FTP Server
		$conn_id = ftp_connect($ftp_server);
		// Login to FTP Server
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
		
		// Verify Log In Status
		if ((!$conn_id) || (!$login_result)) {
			logging($ftp_server." connection has failed! ");
		    exit;
		} else {
			logging($ftp_server." connected! ");
		}

		// get the file list 
		$listline = ftp_rawlist($conn_id, $ftp_remote_dir,true);
		$listlineNum = count($listline);
		
		// close the connection
		ftp_close($conn_id);
		logging($ftp_server." connection has closed! ");

		// delete old files
		mysql_query("DELETE FROM ftps_FtpFileInUsed WHERE ftpID =".$ftp_id);
		
	// Get file list content
	$lFolderName = $ftp_remote_dir;
	logging($ftp_server." getting files... ");
	//$NewLine = "\n";
	for ($i = 0; $i < $listlineNum; $i++) 
	{
		if(strlen($listline[$i]) != 0){
			$firstchr = substr($listline[$i],0,1);
			//echo "firstchr:".strlen($firstchr)."@".$firstchr.$NewLine;
			switch ($firstchr) 
			{
				case "-":
					//lContent has lSzie, lTime, lFileName
					$lContent = trim(substr($listline[$i],30,strlen($listline[$i])-29));
					$lContentLen = strlen($lContent);
					$lSzie = substr($lContent,0,strpos($lContent,chr(32)));
					//change "May  1 13:10" to Unixtime
					$lTime = strtotime(substr($lContent,strlen($lSzie)+1,12));
					$lFileName = substr($lContent,strlen($lSzie)+14,$lContentLen-strlen($lSzie)-14);
					$FileCount += 1;
					$SumSize = $SumSize + $lSzie;
					$lFileType = 0;
					mysql_query("INSERT ftps_FtpFileInUsed(ftpID,fileName,fileDir,fileSize,fileTime,fileType) VALUES(".$ftp_id.",'".$lFileName."','".$lFolderName."',".$lSzie.",".$lTime.",".$lFileType.")");
					break;
				case "d":
					//lContent has lSzie, lTime, lFileName
					$lContent = trim(substr($listline[$i],30,strlen($listline[$i])-29));
					$lContentLen = strlen($lContent);
					$lSzie = substr($lContent,0,strpos($lContent,chr(32)));
					//change "May  1 13:10" to Unixtime
					$lTime = strtotime(substr($lContent,strlen($lSzie)+1,12));
					$lFileName = substr($lContent,strlen($lSzie)+14,$lContentLen-strlen($lSzie)-14);
					$FolderCount += 1;
					$SumSize = $SumSize + $lSzie;
					$lFileType = 1;
					//echo "lFolderName:".$lFolderName.$NewLine;
					mysql_query("INSERT ftps_FtpFileInUsed(ftpID,fileName,fileDir,fileSize,fileTime,fileType) VALUES(".$ftp_id.",'".$lFileName."','".$lFolderName."',".$lSzie.",".$lTime.",".$lFileType.")");
					break;
				case "/":	//linux folder begin with "./", but php consider it "/"   2012-05-29
					$lContent = trim($listline[$i]);
					//$lFolderName = substr($lContent,0,strlen($lContent)-1);	//windows folder
					$lFolderName = substr($lContent,1,strlen($lContent)-2);		//linux folder, more a "/"
					//echo "!".$lFolderName.$NewLine;
					break;
				case "t":		//windows folder next line is "total 141965", like sector counts.
					$lContent = trim($listline[$i]);
					$lFolderName = "/".$lFolderName;	//windows folder need to add "/"
					break;
					// linux folder begin with "./", but php consider it "/"   2012-05-29
				default:
					$lContent = trim($listline[$i]);
					$lFolderName = substr($lContent,0,strlen($lContent)-1);
					echo $lContent."@@".$lFolderName.$NewLine;
			}
		}
	}
	$NeedTime = time()-$StartGetTime;
	//echo "UPDATE ftps_FtpSrvInUsed set lastUpdate = ".$StartGetTime.",fileCount = ".$FileCount.",sumSize =".$SumSize.",updateNeed = ".$NeedTime." WHERE ftpID = ".$ftp_id ;
	mysql_query("UPDATE ftps_FtpSrvInUsed SET lastUpdate = ".$StartGetTime.",fileCount = ".$FileCount.",sumSize =".$SumSize.",updateNeed = ".$NeedTime." WHERE ftpID = ".$ftp_id);
	logging($ftp_server." finish ! ");
//	echo "FileCount: ".$FileCount.$NewLine;
//	echo "FolderCount: ".$FolderCount.$NewLine;
//	echo "SumSize: ".($SumSize/1024)." KB".$NewLine;
//	echo "NeedTime: ".$NeedTime.$NewLine;

	}
}

?>
