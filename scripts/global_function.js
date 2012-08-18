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
	var d = new Date(),str = '';
	str += d.getFullYear();
	return str;
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

