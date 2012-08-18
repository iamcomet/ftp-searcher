<?php 
/* 
作用：全局共用的函数
作者: comet
修改时间：2012-07-26 16:54:29 
*/

/* 
函数名：logging(字符串参数)
作用：记录输出
作者: comet
修改时间：2012-07-26 22:21:36 
*/

function logging($logmsg){
	echo $logmsg."<br />";
}

/* 
函数名：formatSize(整形参数)
作用：返回可阅读的文件大小
作者: comet
修改时间：2012-07-26 22:21:36 
*/
function formatSize($sizenum){
	if($sizenum>1073741824){
		$sizenum = round($sizenum/1073741824, 2)." GB";	//round 四舍五入
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
函数名：getURLPage(字符串参数)
作用：输出URL中页面名称
作者: comet
修改时间：2012-07-26 22:21:36 
*/
function getURLPage($url){
	preg_match('/\/([^\/]+\.[a-z]+)[^\/]*$/',$url,$match);
	return $match[1];
}

/* 
函数名：getURLExt(字符串参数)
作用：输出URL中页面的扩展名
作者: comet
修改时间：2012-07-26 22:21:36 
*/
function getURLExt($url){
	$path = parse_url($url);
	$str = explode('.',$path['path']);
	return $str[1];
}

/* 
函数名：getIP(字符串参数)
作用：返回用户的IP
作者: comet
修改时间：2012-07-28 07:46:10  
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
