//<![CDATA[

/*
作用：全局共用的js函数
作者: comet
修改时间：2012-08-18 13:39:38
*/

/*
函数名：*trim(字符串参数)
作用：删除字符串
作者: comet
修改时间：2012-08-18 13:39:38
*/
function trim(str){  //删除左右两端的空格
	return str.replace(/(^\s*)|(\s*$)/g, "");
}
function ltrim(str){  //删除左边的空格
	return str.replace(/(^\s*)/g,"");
}
function rtrim(str){  //删除右边的空格
	return str.replace(/(\s*$)/g,"");
}
function ldivtrim(str){  //删除最左边的</div>
	return str.replace(/(^<\/div>)/g,"");
}

/*
函数名：currentYear()
作用：获取当前四位年份
作者: comet
修改时间：2012-08-18 13:39:38
*/
function currentYear(){
	var d = new Date();
	var str = '';
	str += d.getFullYear();
	return str;
}

/*
函数名：timestampToStr()
作用：输入php unixtime，输出 yyyy-MM-dd hh:mm:ss
作者: comet
修改时间：2012-08-20 15:37:11 
*/
function timestampToStr(x,y) {
	x = new Date(x*1000);	//先转换成毫秒
	var z = {M:x.getMonth()+1,d:x.getDate(),h:x.getHours(),m:x.getMinutes(),s:x.getSeconds()};
	y = y.replace(/(M+|d+|h+|m+|s+)/g,function(v) {return ((v.length>1?"0":"")+eval('z.'+v.slice(-1))).slice(-2)});
	return y.replace(/(y+)/g,function(v) {return x.getFullYear().toString().slice(-v.length)});
}

/*
函数名：htmlencode(字符串参数)
作用：htmlencode字符串
作者: comet
修改时间：2012-08-18 14:12:38
*/
function htmlencode(s){
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(s));
    return div.innerHTML;
}

/*
函数名：htmldecode(字符串参数)
作用：htmldecode字符串
作者: comet
修改时间：2012-08-18 14:12:38
*/
function htmldecode(s){
	var div = document.createElement('div');
	div.innerHTML = s;
	return div.innerText || div.textContent;
}

