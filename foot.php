<?php
/* 
作用：底部包含文件
作者: comet
修改时间：2012-07-27 00:23:18 
*/

function showFoot(){
//	$EndTime = (time()-startTime)
	echo "<div class=\"clearDiv\">&nbsp;</div>";	//多了一个空行，总比错位好看。 2009-08-30
	echo "<hr class=\"hrcolor\">";
	echo "<div class=\"footSiteName\">$siteTitle.$siteVer</div>";
	echo "<div class=\"footSiteName\">$copyright.$thisYear.$copylink</div>";
	echo "<div class=\"footSiteName\">$sysStat</div>";
//	echo "<tr><td align='center' class=fileName>" & EndTime & " s</td></tr>";
	echo "</body></html>";
	exit();
}

// END OF FILE
