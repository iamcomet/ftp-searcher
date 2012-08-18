<?php 
/* 
���ã�ȫ�ֹ��õĺ���
����: comet
�޸�ʱ�䣺2012-07-26 16:54:29 
*/

/* 
��������logging(�ַ�������)
���ã���¼���
����: comet
�޸�ʱ�䣺2012-07-26 22:21:36 
*/

function logging($logmsg){
	echo $logmsg."<br />";
}

/* 
��������formatSize(���β���)
���ã����ؿ��Ķ����ļ���С
����: comet
�޸�ʱ�䣺2012-07-26 22:21:36 
*/
function formatSize($sizenum){
	if($sizenum>1073741824){
		$sizenum = round($sizenum/1073741824, 2)." GB";	//round ��������
	}elseif($sizenum>1048576){
		$sizenum = round($sizenum/1048576, 2)." MB";
	}elseif($sizenum>1024){
		$sizenum = round($sizenum/1024, 2)." KB";
	}else{
		$sizenum .= " B";
	}
	return $sizenum;
}

/* 
��������getURLPage(�ַ�������)
���ã����URL��ҳ������
����: comet
�޸�ʱ�䣺2012-07-26 22:21:36 
*/
function getURLPage($url){
	preg_match('/\/([^\/]+\.[a-z]+)[^\/]*$/',$url,$match);
	return $match[1];
}

/* 
��������getURLExt(�ַ�������)
���ã����URL��ҳ�����չ��
����: comet
�޸�ʱ�䣺2012-07-26 22:21:36 
*/
function getURLExt($url){
	$path = parse_url($url);
	$str = explode('.',$path['path']);
	return $str[1];
}

/* 
��������getIP(�ַ�������)
���ã������û���IP
����: comet
�޸�ʱ�䣺2012-07-28 07:46:10  
*/
function getIP(){
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
           $ip = getenv("HTTP_CLIENT_IP");
       else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
           $ip = getenv("HTTP_X_FORWARDED_FOR");
       else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
           $ip = getenv("REMOTE_ADDR");
       else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
           $ip = $_SERVER['REMOTE_ADDR'];
       else
           $ip = "unknown";
   return($ip);
}

// END OF FILE
