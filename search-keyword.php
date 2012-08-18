<?php
/* 
作用：获取用户输入关键字,返回相关关键字作为提示
作者: comet
修改时间：2012-08-02 21:47:10 
*/
require_once "head.php";
// 头部信息
header("Content-type: text/html; charset=utf-8");

#foreach($_GET as $key => $value){
#	$$key = $mysqli->real_escape_string($value);	//不转义%和_这两个符号
#}

$q = strtolower($_GET["term"]);
#$q = "ex";
if(!isset($q)){
	Header("Location:./");
}
$items = array();
//查询数据
$sql="SELECT sKeyword,sHit FROM ftps_Keywords WHERE LOCATE(\"$q\",sKeyword) > 0";

$res = $mysqli->query($sql);
if ($res) {		//判断记录集是否存在
	if ($res->num_rows){	//如果记录集里面有数据
		while ($row = $res->fetch_assoc()){
#			foreach($row as $k => $v){
#				echo "\"$k $v\"=>\"$v\",";
#			}
#			
			$keyword = htmlspecialchars(stripslashes($row["sKeyword"]));
			$hit = $row["sHit"];
			$k = "$keyword [$hit]";
			$items[$keyword] = $k;
		}
#		echo "</response>";
	}else{	//如果记录集里面没有数据，返回0
		$items["no data"] = "no data";
#		die("no data");
	}
}else{	//记录集不存在
	$items["query error"] = "query error";
#	die("query error");
}

$res->close();
$mysqli->close();
#print_r($items);
#die("");
if (!$q) return;

function array_to_json( $array ){

    if( !is_array( $array ) ){
        return false;
    }

    $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
    if( $associative ){

        $construct = array();
        foreach( $array as $key => $value ){

            // We first copy each key/value pair into a staging array,
            // formatting each key and value properly as we go.

            // Format the key:
            if( is_numeric($key) ){
                $key = "key_$key";
            }
            $key = "\"".addslashes($key)."\"";

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "\"".addslashes($value)."\"";
            }

            // Add to staging array:
            $construct[] = "$key: $value";
        }

        // Then we collapse the staging array into the JSON form:
        $result = "{ " . implode( ", ", $construct ) . " }";

    } else { // If the array is a vector (not associative):

        $construct = array();
        foreach( $array as $value ){

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "'".addslashes($value)."'";
            }

            // Add to staging array:
            $construct[] = $value;
        }

        // Then we collapse the staging array into the JSON form:
        $result = "[ " . implode( ", ", $construct ) . " ]";
    }

    return $result;
}

$result = array();
foreach ($items as $key=>$value) {
	if (strpos(strtolower($key), $q) !== false) {
		array_push($result, array("id"=>$value, "label"=>$key, "value" => strip_tags($key)));
	}
	if (count($result) > 11)
		break;
}
echo array_to_json($result);

// END OF FILE
