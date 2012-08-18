<?php
/* 
���ã���ȡ�û�����ؼ���,������عؼ�����Ϊ��ʾ
����: comet
�޸�ʱ�䣺2012-08-02 21:47:10 
*/
require_once "head.php";
// ͷ����Ϣ
header("Content-type: text/html; charset=utf-8");

#foreach($_GET as $key => $value){
#	$$key = $mysqli->real_escape_string($value);	//��ת��%��_����������
#}

$q = strtolower($_GET["term"]);
#$q = "ex";
if(!isset($q)){
	Header("Location:./");
}
$items = array();
//��ѯ����
$sql="SELECT sKeyword,sHit FROM ftps_Keywords WHERE LOCATE(\"$q\",sKeyword) > 0";

$res = $mysqli->query($sql);
if ($res) {		//�жϼ�¼���Ƿ����
	if ($res->num_rows){	//�����¼������������
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
	}else{	//�����¼������û�����ݣ�����0
		$items["no data"] = "no data";
#		die("no data");
	}
}else{	//��¼��������
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
