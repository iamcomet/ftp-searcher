//<![CDATA[
	$(function(){
		$("#loading").hide();
		//表单提交
		$("#loading").ajaxStart(function(){
			$(this).show();
		});
		$("#loading").ajaxStop(function(){
			$(this).hide();
		});
		function getUrl(paras){
			var reg = new RegExp("(^|&)"+ paras +"=([^&]*)(&|$)");
			var para = window.location.search.substr(1).match(reg);
			var actionpage = "search.php";
			if(para != null && para[2] == 1){
				var actionpage = "sphinxsearch.php";
				//return unescape([para[2]]);
			}
			return actionpage;
		}
		
		 //文本框失去焦点后
		$("#content").blur(function(){
			 var $parent = $(this).parent();
			 $parent.find(".formtips").remove();
			 //验证
			 if( $(this).is("#content") ){
				if( this.value=="" || this.value.length < 2 ){
					alert("请输入至少2位");
		            $parent.append('<span class="formtips onError"> </span>');
				}
			 }
		});//end blur

		$("#searchform").submit(function(){
			var actionpage = getUrl("se");

			$("#content").trigger('blur');
			var numError = $("form .onError").length;
			if(numError){
				return false;
			}
			$.post(actionpage,{
				content: $("#content").val(),
				action: "search"
			},function(xml){
				$("#searchresult").html("");
				if(actionpage == "sphinxsearch.php"){
					var engineinfo = " <div class='engineinfo'>本次搜索使用扩展引擎，最多返回1000条相关信息。</div>";
					$("#searchresult").append(engineinfo);
				}
				$("#searchresult").addClass("srborder");
				showMessages(xml);
			},"xml");
			return false;
		});
	});
	function showMessages(xml){
		var countnum = $("countnum",xml).text();
		htmlcountnum = "<div class='filecount'>有 "+countnum+" 条与 "+$("#content").val()+" 相关的信息</div>";
		$("#searchresult").append(htmlcountnum);
		if(countnum){	//有记录才显示
			var lastftpID = 0;
			$("msg",xml).each(function(){
				var ftpServer = $("ftpServer",this).text();
				var remoteDir = $("remoteDir",this).text();
				var fileID = $("fileID",this).text();
				var ftpID = $("ftpID",this).text();
				if(lastftpID != ftpID){
					var htmlfile = "<div class='ftplist'>ftp://"+ftpServer+remoteDir+"</div>";
					$("#searchresult").append(htmlfile);
					lastftpID = ftpID;
				}
				var fileName = $("fileName",this).text();
				var fileDir = $("fileDir",this).text();
				var fileSize = $("fileSize",this).text();
				var fileTime = $("fileTime",this).text();
				var fileType = $("fileType",this).text();

				if(fileType == 0) {
					var htmlfile = "<div class='filelist'><a href='fileinfo.php?fileID="+fileID+"'>"+fileName+"</a> <span class='filesize'>"+fileSize+"</span><span class='filetime'> [ "+fileTime+" ]</span><div class='filelink'>ftp://"+ftpServer+remoteDir+"/"+fileName+"</div><br/>";
				}else{
					var htmlfile = "<div class='folderlist'><a href='browse.php?fileID="+fileID+"'>"+fileName+"</a>  [ "+fileTime+" ]</div><div class='filelink'>ftp://"+ftpServer+remoteDir+"/"+fileName+"</div><br/>";
				}			
				$("#searchresult").append(htmlfile);
			});
		}
	}
//]]>
