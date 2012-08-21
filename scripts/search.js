//<![CDATA[
	$(function(){
		$("#foot").html(" Power by <a href=\"http://www.3gComet.com\" target=\"_blank\">星魂驿站</a> "+currentYear());
		//表单提交
		$("#loading").ajaxStart(function(){
			$(this).show();
		});
		$("#loading").ajaxStop(function(){
			$(this).hide();
		});
		//获取url参数，如果带任何参数则使用扩展引擎
		function getUrl(paras){
			var reg = new RegExp("(^|&)"+ paras +"=([^&]*)(&|$)");
			var para = window.location.search.substr(1).match(reg);
			var actionpage = "search.php";
			if(para != null && para[2] == 1){
				var actionpage = "sphinxsearch.php";
				//return unescape([para[2]]);
			}
			return actionpage;
		};

		//搜索关键字提示
		function log( message ) {
			$( "<div/>" ).text( message ).prependTo( "#log" );
			$( "#log" ).scrollTop( 0 );
		}
		$( "#content" ).autocomplete({
			source: "search-keyword.php",
			minLength: 2
		});

		//提交表单之前的检测
		$("#searchform").submit(function(){
			var actionpage = getUrl("se");
			$("#notless").remove();
			if($("#content").val().length<2){
				var $notless = $("<div id='notless' class='notless'>最少输入两个字符</div>");
				$(this).append($notless);
				$("#content").focus();
				return false;
			}
			$.ajax({
				type:"POST",
				url:actionpage,
				data:{
					content: $("#content").val(),
					action: "search"
				},
				dataType:"xml",
				//请求成功后的回调函数
				success:function(xml){
					$("#searchresult").html("");
					if(actionpage == "sphinxsearch.php"){
						var engineinfo = " <div class='engineinfo'>本次搜索使用扩展引擎，最多返回1000条相关信息。</div>";
						$("#searchresult").append(engineinfo);
					}
					$("#searchresult").addClass("srborder");
					//对返回的xml进行处理并显示
					showMessages(xml);
					//点击srvlist切换srvcontent是否显示
					$("div.srvlist").click(function(){
						$(this).next().slideToggle('fast');
					});
				}
			});
			return false;
		});
	});
	function showMessages(xml){
		var countnum = $("countnum",xml).text();
		var sizenum = $("sizenum",xml).text();
		htmlcountnum = "<div class='filecount'>有 "+countnum+" 条与 <span class='keywords'>"+htmlencode($("#content").val())+"</span> 相关的信息，共 "+sizenum+"</div>";
		$("#searchresult").append(htmlcountnum);
		var htmlfile = "";
		if(countnum){	//有记录才显示
			var lastftpID = 0;
			$("msg",xml).each(function(){
				var ftpServer = $("ftpServer",this).text();
				var remoteDir = $("remoteDir",this).text();
				var fileID = $("fileID",this).text();
				var ftpID = $("ftpID",this).text();
				if(lastftpID != ftpID){
					htmlfile = htmlfile+"</div><div class='srvlist'>ftp://"+ftpServer+remoteDir+"</div><div class='srvcontent'>";
					lastftpID = ftpID;
				}
				var fileName = $("fileName",this).text();
				var fileDir = $("fileDir",this).text();
				var fileSize = $("fileSize",this).text();
				var fileTime = $("fileTime",this).text();
				var fileType = $("fileType",this).text();

				if(fileType == 0) {	//0:file, 1:folder
					htmlfile = htmlfile+"<div class='filelist'><a href='fileinfo.php?fileID="+fileID+"'>"+fileName+"</a> <span class='filesize'>"+fileSize+"</span><span class='filetime'> [ "+fileTime+" ]</span><div class='filelink'>ftp://"+ftpServer+fileDir+"/"+fileName+"</div></div><br/>";
				}else{
					htmlfile = htmlfile+"<div class='folderlist'><a href='browse.php?fileID="+fileID+"'>"+fileName+"</a>  [ "+fileTime+" ]</div><div class='filelink'>ftp://"+ftpServer+fileDir+"/"+fileName+"</div><br/>";
				}
			});
		}
		htmlfile = htmlfile+"</div>";	//闭合最后的<div class='srvcontent'>
		$("#searchresult").append(ltrimdiv(htmlfile));
	}
//]]>
