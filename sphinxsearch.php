<?php

require ( "sphinxapi.php" );
require_once "inc/get_info.php";
require_once "inc/conn.php";
// 头部信息
header("Content-type: text/xml");
header("Cache-Control: no-cache");

$dbconn = mysql_connect($dbhost,$dbuser,$dbpass);
mysql_select_db($dbname,$dbconn);
mysql_query("SET NAMES 'UTF8'");
foreach($_POST as $key => $value){
	$$key = mysql_real_escape_string($value, $dbconn);
}

if(!isset($content) && strlen(trim($content))<2){
	Header("Location:./");
}

$cl = new SphinxClient ();

$q = $content;
$sql = "";
$mode = SPH_MATCH_ALL;
$host = "localhost";
$port = 9312;
$index = "*";
$groupby = "";
$groupsort = "@ftpID";
$filter = "";
$filtervals = array();
$distinct = "";
$sortby = "";
$limit = 1000;
$ranker = SPH_RANK_PROXIMITY_BM25;
$select = "";


////////////
// do query
////////////

$cl->SetServer ( $host, $port );
$cl->SetConnectTimeout ( 1 );
$cl->SetArrayResult ( true );
$cl->SetWeights ( array ( 100, 1 ) );
$cl->SetMatchMode ( $mode );
if ( count($filtervals) )	$cl->SetFilter ( $filter, $filtervals );
if ( $groupby )				$cl->SetGroupBy ( $groupby, SPH_GROUPBY_ATTR, $groupsort );
if ( $sortby )				$cl->SetSortMode ( SPH_SORT_EXTENDED, $sortby );
#if ( $sortexpr )			$cl->SetSortMode ( SPH_SORT_EXPR, $sortexpr );
if ( $distinct )			$cl->SetGroupDistinct ( $distinct );
if ( $select )				$cl->SetSelect ( $select );
if ( $limit )				$cl->SetLimits ( 0, $limit, ( $limit>1000 ) ? $limit : 1000 );
$cl->SetRankingMode ( $ranker );
$res = $cl->Query ( $q, $index );

////////////////
// print me out
////////////////

if ( $res===false )
{
	print "Query failed: " . $cl->GetLastError() . ".<br />";

} else
{
#	if ( $cl->GetLastWarning() )
#		print "WARNING: " . $cl->GetLastWarning() . "<br /><br />";

#	print "Query '$q' retrieved $res[total] of $res[total_found] matches in $res[time] sec.<br />";
#	print "Query stats:<br />";
#	if ( is_array($res["words"]) )
#		foreach ( $res["words"] as $word => $info )
#			print "    '$word' found $info[hits] times in $info[docs] documents<br />";
#	print "<br />";

//返回xml数据结构
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<response>\n";
	if ( is_array($res["matches"]) )
	{
				$counts=0;
#		$n = 1;
#		print "Matches:<br />";

		foreach ( $res["matches"] as $docinfo )
		{
#			print "$n. doc_id=$docinfo[id] <br/>";

			$store_num = 10;
			$display_num = 10;

			$sql="SELECT `fileID`,`fileName`,`fileDir`,`fileSize`,`fileTime`,`fileType`,`ftpServer`,`ftpPort`,`remoteDir`,`ftps_FtpFileInUsed`.`ftpID` FROM `ftps_FtpFileInUsed`,`ftps_FtpSrvInUsed` WHERE `ftps_FtpFileInUsed`.`ftpID`=`ftps_FtpSrvInUsed`.`ftpID` AND `fileID` = ".$docinfo['id'];

			$searchresults = mysql_query($sql,$dbconn);



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
			}}
			echo "<countnum>\n";
			echo $counts."\n";
			echo "</countnum>\n";

	}else{
			echo "<countnum>\n";
			echo "0\n";
			echo "</countnum>\n";

	}
	echo "</response>";
}
//
// $Id: test.php 2055 2009-11-06 23:09:58Z shodan $
//

?>
