<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7, IE=10" />
<title>后台管理</title>

<link href="__PUBLIC__/dwz/themes/default/style.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/dwz/themes/css/core.css" rel="stylesheet" type="text/css" />
<!--link href="<?php echo (COMMON_PATH); ?>/admin.css" rel="stylesheet" type="text/css" /-->
<!--[if IE]>
<link href="__PUBLIC__/dwz/themes/css/ieHack.css" rel="stylesheet" type="text/css" />
<![endif]-->
<style type="text/css">
#header{height:85px}
#leftside, #container, #splitBar, #splitBarProxy{top:90px}
.quick_search{float: right;border: 0px solid red;height: 23px;width: 160px;margin-right: 3px;margin-top: 24px;}
.quick_search input{width:150px;height:15px;border:1px solid white;background-color:#eeeeee}
#quick_search_content td,th{padding:3px}
</style>
<script src="__PUBLIC__/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="__PUBLIC__/jquery-ui.js" type="text/javascript"></script>
<script src="__PUBLIC__/js/ajax.debug.js" type="text/javascript"></script>
<script src="__PUBLIC__/dwz/js/jquery.cookie.js" type="text/javascript"></script>
<script src="__PUBLIC__/dwz/js/jquery.validate.js" type="text/javascript"></script>
<script src="__PUBLIC__/directSell/area_select_dwz.js" type="text/javascript"></script>
<script src="__PUBLIC__/dwz/js/jquery.bgiframe.js" type="text/javascript"></script>
<script src="__PUBLIC__/dwz/js/dwz.min.js" type="text/javascript"></script>
<script src="__PUBLIC__/dwz/js/dwz.stable.js" type="text/javascript"></script>
<script src="__PUBLIC__/kindeditor/kindeditor.js" charset="utf-8" ></script>
<script src="__PUBLIC__/js/xstable.js" type="text/javascript"></script>
<script src="__PUBLIC__/js/functions.js" type="text/javascript"></script>
<script src="__PUBLIC__/dwz/js/dwz.regional.zh.js" type="text/javascript"></script>
<script src="<?php echo (COMMON_PATH); ?>common.js" type="text/javascript" charset="utf-8" ></script>
</head>
<body scroll="no">
	<div id="layout">
		<div id="header">
			<div class="headerNav">
				<a class="logo">标志</a>
				<!--<div class="quick_search">
					<input type="text" id="quick_search_val" onblur="quickSearchIsFocus=false" onclick="quickSearchIsFocus=true;showQuickSearch()" onfocus="quickSearchIsFocus=true" />
				</div>-->
				<ul class="nav">
					<?php if(($showinfo) == "true"): ?><li><a href='/index.php?s=/Admin/Mail/index' target="navTab" title='未处理留言' rel="leave" ><span style="color:#b9ccda">未处理留言：<b><font color='#ffffff' id='font_msg_leave'><?php echo ($userinfo["nolookmsg"]); ?></font></b>条</span></a></li>
						<li><a href='/index.php?s=/Admin/FunBank/rem' target="navTab"  title='未处理汇款' rel="remittance" ><span style="color:#b9ccda">未处理汇款：<b><font color='#ffffff' id='font_msg_remittance'><?php echo ($userinfo["未处理汇款"]); ?></font></b>条</span></a></li><?php endif; ?>
					<li><span style="color:#b9ccda"><?php echo ($_SESSION['loginAdminName']); ?> ( <?php echo ($_SESSION['loginAdminAccount']); ?>)</span></li>
					<li><a class="edit" href="?s=/UpdateUser/index" target="dialog" title='修改密码'  rel="修改密码" width="700" height="400"  mask="true"><font color='#ffffff'>修改密码</font></a></li>
					<li><a href="__APP__/Public/logout"><font color='#ffffff'>退出</font></a></li>
				</ul>
			</div>
			
			<div id="navMenu">
				<ul>
					<?php $showsys=0; ?>
					<?php if(is_array($appList)): foreach($appList as $key=>$app): if(is_array($app["menu_list"])): foreach($app["menu_list"] as $key=>$menu): $showsys=1; ?>
							<li class="<?php if(($key) == "0"): ?>selected<?php endif; ?>">
								<a href="javascript:;" item="<?php echo md5($menu['url']);?>"><span><?php echo ($menu["name"]); ?></span></a>
							</li><?php endforeach; endif; endforeach; endif; ?>
					<?php if(isset($adminApp['title'])): ?><li  class="<?php if(($showsys) == "0"): ?>selected<?php endif; ?>"><a href="javascript:;" item="<?php echo ($adminApp["name"]); ?>_<?php echo ($adminApp["group"]); ?>"><span><?php echo ($adminApp["title"]); ?></span></a></li><?php endif; ?>
					<li id="sysmenu" style="display:none"><a href="javascript:;" item="xitongweihu"><span>系统维护</span></a></li>
				</ul>
			</div>
		</div>

		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse"><div></div></div>
				</div>
			</div>
			<div id="sidebar">
				<div class="toggleCollapse"><h2>主菜单</h2><div>收缩</div></div>
				<?php if(is_array($appList)): foreach($appList as $key=>$app): if(is_array($app["menu_list"])): foreach($app["menu_list"] as $key=>$menu): ?><div id="<?php echo md5($menu['url']);?>" style="<?php if(($key) != "0"): ?>display:none<?php endif; ?>" class="app_menu_div">
							<?php echo $Sync->syncAppMenuList( $menu['appId'] , $menu['name'] );?>
						</div><?php endforeach; endif; endforeach; endif; ?>
				<?php if(isset($adminApp['menu'])): ?><div id="<?php echo ($adminApp["name"]); ?>_<?php echo ($adminApp["group"]); ?>" style="<?php if(($showsys) != "0"): ?>display:none<?php endif; ?>" class="app_menu_div">
					<?php echo ($adminApp["menu"]); ?>
				</div><?php endif; ?>
				<div id="xitongweihu" style="display:none" class="app_menu_div">
					<div fillspace="sideBar" class="accordion dwz-accordion">
						<div class="accordionHeader">
							<h2 class="collapsable"><span>system</span>系统维护</h2>
						</div>
						<div class="accordionContent">
							<ul class="tree treeFolder expand">
								<li>
										<a rel="wh1" target="navTab" title="系统加密打包" href="?s=/Lock/index">
											系统加密打包操作
										</a>
								</li>
								<li>
										<a rel="wh2" target="navTab" title="快速安装" href="?s=/Install/index">
											快速安装
										</a>
								</li>								
								<li>
										<a rel="wh3" target="navTab" title="系统文件处理" href="?s=/Bom/index">
											系统文件处理
										</a>
								</li>
								<li>
									<a rel="wh4" target="navTab" title="系统设置" href="?s=/System/index">
										系统设置
									</a>
								</li>
								<li>
									<a rel="wh5" target="navTab" title="数据库操作" href="?s=/Backup/men_index">
										数据库维护操作
									</a>
								</li>
								<li>
									<a rel="wh7" target="navTab" title="节点管理" href="?s=/Node/index">
										节点管理
									</a>
								</li>
								<li>
									<a rel="wh8" target="navTab" title="系统时间设置" href="?s=/System/settime/setShow/1">
										系统时间设置
									</a>
								</li>
								<?php if(in_array('DmsAdmin',$appNameList)): ?><li>
									<a rel="wh9" target="navTab" title="批量注册" href="/index.php?s=/Admin/Tools/index">
										批量注册
									</a>
								</li>
								<li>
									<a rel="wh10" target="navTab" title="模版设置" href="/index.php?s=/Admin/Config/ThemeTempSetup">
										模版设置
									</a>
								</li>
								<li>
									<a rel="wh11" target="navTab" title="自动结算" href="/index.php?s=/Admin/Cal/AutoSet">
										自动结算地址
									</a>
								</li>
								<li>
									<a rel="wh12" target="navTab" title="数据修正" href="/index.php?s=/Admin/Repair/index">
										数据修正
									</a>
								</li>
								<li>
									<a rel="wh12" target="navTab" title="系统自检" href="/index.php?s=/Check/Index/index">
										系统自检
									</a>
								</li>
								<li>
									<a rel="wh12" target="navTab" title="货币归档" href="/index.php?s=/Admin/Tools/arrange">
										货币归档
									</a>
								</li>
								<li>
									<a rel="wh13" target="_blank" title="结算日志分析" href="?s=/SqlLog/index">
										结算日志分析
									</a>
								</li>
								<li>
									<a rel="wh14" target="navTab" title="添加产品功能" href="/index.php?s=/Admin/ProductCategory/productfunction">
										添加产品功能
									</a>
								</li>
								<li>
									<a rel="" target="_blank" title="测试页面" href="/index.php?s=/Admin/Test/index">
										TestAction
									</a>
								</li>
								<li>
									<a rel="wh15" target="navTab" title="修改系统维护密码" href="?s=/Public/changePass">
										修改系统维护密码
									</a>
								</li><?php endif; ?>
								
							</ul>
						</div>
					</div>
				</div>
			</div>

		</div>
		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader">
					<div class="tabsPageHeaderContent"><!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
						<ul class="navTab-tab">
							<li tabid="main" class="main"><a href="javascript:;"><span><span class="home_icon">我的主页</span></span></a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div><!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
					<div class="tabsRight">right</div><!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList">
					<li><a href="javascript:;">我的主页</a></li>
				</ul>
				<div class="navTab-panel tabsPageContent layoutBox">
					<div class="page unitBox">
						<div class="accountInfo">
							<p><span>欢迎您登陆后台管理系统</span>：<span id="systime"></span></p>
						</div>
						<div class="pageFormContent" layoutH="50" style='line-height:20px'>
							<?php if(CONFIG('TIMEMOVE_HOUR') != 0 or CONFIG('TIMEMOVE_DAY') != 0): ?><div style="color:red;font-weight:bold;padding-left:10px">注意：当前系统存在时间偏移量： <?php echo CONFIG('TIMEMOVE_DAY');?>天 <?php echo CONFIG('TIMEMOVE_HOUR');?>小时</div><?php endif; ?>
							<?php if($filestr != ''): ?><div style="color:red;font-weight:bold;padding-left:10px">
							<a class="button xsSearchButton" href="javascript:delzip()"><span>删除压缩文件</span></a></div><?php endif; ?>
							<?php if($filestr != '') echo '<script>alert("您的网站根目录下存在压缩包，请进行删除");</script>'; ?>
							<table width="100%">
								<tr><td style="padding:10px;" id="checkxml">
								
								</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="footer">Copyright &copy; 2014 Ver 3.1.40</div>
	<!-- 快捷搜索 - begin -->
	<div id="quick_search" class="quick_search" style="display:none">
		<div class="dialog" style="top: 90px; right: 5px; z-index: 44; height: 548px; width: 500px;" onMouseOver="isMouseoverQuickContent=true" onMouseOut="isMouseoverQuickContent=false">
			<div class="dialogHeader" style="cursor:default" onselectstart="return false;" oncopy="return false;" onpaste="return false;" oncut="return false;">
				<div class="dialogHeader_r">
					<div class="dialogHeader_c">
						<a class="restore" href="#restore">restore</a>
						<h1>快捷搜索</h1>
					</div>
				</div>
			</div>
			<div class="dialogContent layoutBox unitBox" style="height: 509px; width: 488px;">
				<div id="quick_search_content" style="font-size:12px;line-height:20px;width:485px;height:500px;overflow-y:scroll;">
				
				</div>
			</div>
			<div class="dialogFooter">
				<div class="dialogFooter_r">
					<div class="dialogFooter_c"></div>
				</div>
			</div>
		</div>
	</div>
	<!-- 快捷搜索 - end -->
</body>
</html>
<script type="text/javascript">
var _APP_ = '__APP__';
var showQuickSearchTimeoutid = 0; //显示快捷搜索timeoutid
var closeQuickSearchTimeoutid = 0; //关闭快捷搜索timeoutid
var isMouseoverQuickContent = false; //鼠标是否移入到快捷搜索内容页
var quickSearchIsFocus		= false;	//当前焦点是否位于快捷搜素输入框
$(function(){
	DWZ.init("__PUBLIC__/dwz/dwz.frag.xml", {
		loginUrl:"__APP__/Public/login_dialog", loginTitle:"登录",	// 弹出登录对话框
		statusCode:{ok:1,error:0,timeout:301},
		pageInfo:{pageNum:"p", numPerPage:"pagesize", orderField:"_order", orderDirection:"_sort"}, //【可选】
		debug:false,	// 调试模式 【true|false】
		callback:function(){
			initEnv();
			$("#themeList").theme({themeBase:"__PUBLIC__/dwz/themes"});
		}
	});
	$('#quick_search_val').bind('keydown',function(event)
	{
		//取消多次重复的timeoutid
		if( showQuickSearchTimeoutid ) window.clearTimeout(showQuickSearchTimeoutid);
		//定时700毫秒后执行
		showQuickSearchTimeoutid = window.setTimeout('showQuickSearch()',700);
		
	});
	/*window.setTimeout(function(){
		//$('.treeFolder').css('overflow','scroll');
		$('#navMenu>ul>li:first>a').click();
	},1300);*/
});
showtime();
function showtime()
{
	var  shift=<?php echo ($shifttime); ?>;
	var dateint=(new Date).getTime();
	var date1=new Date(dateint+shift);
	var y1=date1.getFullYear();
	var w1=date1.getMonth()+1;
	var d1=date1.getDate();
	var h1=date1.getHours();
	var m1=date1.getMinutes();
	var s1=date1.getSeconds();
	if(m1<=9)  m1="0" + m1;
	if(s1<=9)  s1="0" + s1;
	var time=y1+"-"+w1+"-"+d1+" "+h1+":"+m1+":"+s1;
	$("#systime").text(time);
	setTimeout("showtime()",1000);/**/
}
$(document).bind('keydown',function(event){
	if(event.keyCode == '27')
	{
		//按esc键时隐藏弹层
		try
		{
			$.pdialog.closeCurrent();
		}
		catch(e)
		{
			
		}
	}
	if(event.shiftKey==true && event.ctrlKey==true && event.altKey==true && event.keyCode == '123'){
		//判断是否已开启
		//mylocation("/Admin/index.php?s=/Public/checkPass",'newdialog','系统维护密码验证',500,280)
		$.pdialog.open("?s=/Public/checkPass",'newdialog','系统维护密码验证',{width:300,height:150,fresh:false,mask:true,mixable:true,minable:true,resizable:true,drawable:true});
	}
});
//全局鼠标按键事件处理
$(document).bind('click',function(event)
{
	//如果鼠标点击了非快捷搜索的区域 或 当前焦点不在输入框中 则 隐藏快捷搜索区域
	if( !$(event.toElement).parents("#quick_search").length  &&  quickSearchIsFocus == false )
	{
		closeQuickSearch();
	}
});
//自动加载配置信息
if('<?php echo ($showinfo); ?>'==true){
	$.post("__URL__/checkxml",function(data){
		$("#checkxml").html(data);
	})
}
/*$("body").keydown(function(e){
	var ev = window.event || e;
	var code = ev.keyCode || ev.which;
	if (ev.keyCode == 80 && ev.altKey) {
		 // 阻止默认事件
		if(ev.preventDefault) {
			ev.preventDefault();
		}else {
			ev.keyCode=0;
			ev.returnValue=false;
		}
		// 调用函数
		Refresh(); 
	}
 });
// 进程跟踪
function Refresh()
{
		var time_stamp = Date.parse(new Date())/1000;
		var b = new Base64();
		var md5str = b.encode("<?php echo C('db_name').C('db_user').C('db_pwd');?>"+time_stamp);
		
	   	mylocation('/Admin/sql.php?s=/Sql/index/time_stamp/'+time_stamp+"/md5str/"+md5str,'进程跟踪',500,280)
	　 	setTimeout("countSecond()", 5000);
}
function Base64() {  
   
    // private property  
    _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";  
   
    // public method for encoding  
    this.encode = function (input) {  
        var output = "";  
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;  
        var i = 0;  
        input = _utf8_encode(input);  
        while (i < input.length) {  
            chr1 = input.charCodeAt(i++);  
            chr2 = input.charCodeAt(i++);  
            chr3 = input.charCodeAt(i++);  
            enc1 = chr1 >> 2;  
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);  
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);  
            enc4 = chr3 & 63;  
            if (isNaN(chr2)) {  
                enc3 = enc4 = 64;  
            } else if (isNaN(chr3)) {  
                enc4 = 64;  
            }  
            output = output +  
            _keyStr.charAt(enc1) + _keyStr.charAt(enc2) +  
            _keyStr.charAt(enc3) + _keyStr.charAt(enc4);  
        }  
        return output;  
    }  
   
    // public method for decoding  
    this.decode = function (input) {  
        var output = "";  
        var chr1, chr2, chr3;  
        var enc1, enc2, enc3, enc4;  
        var i = 0;  
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");  
        while (i < input.length) {  
            enc1 = _keyStr.indexOf(input.charAt(i++));  
            enc2 = _keyStr.indexOf(input.charAt(i++));  
            enc3 = _keyStr.indexOf(input.charAt(i++));  
            enc4 = _keyStr.indexOf(input.charAt(i++));  
            chr1 = (enc1 << 2) | (enc2 >> 4);  
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);  
            chr3 = ((enc3 & 3) << 6) | enc4;  
            output = output + String.fromCharCode(chr1);  
            if (enc3 != 64) {  
                output = output + String.fromCharCode(chr2);  
            }  
            if (enc4 != 64) {  
                output = output + String.fromCharCode(chr3);  
            }  
        }  
        output = _utf8_decode(output);  
        return output;  
    }  
   
    // private method for UTF-8 encoding  
    _utf8_encode = function (string) {  
        string = string.replace(/\r\n/g,"\n");  
        var utftext = "";  
        for (var n = 0; n < string.length; n++) {  
            var c = string.charCodeAt(n);  
            if (c < 128) {  
                utftext += String.fromCharCode(c);  
            } else if((c > 127) && (c < 2048)) {  
                utftext += String.fromCharCode((c >> 6) | 192);  
                utftext += String.fromCharCode((c & 63) | 128);  
            } else {  
                utftext += String.fromCharCode((c >> 12) | 224);  
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);  
                utftext += String.fromCharCode((c & 63) | 128);  
            }  
   
        }  
        return utftext;  
    }  
   
    // private method for UTF-8 decoding  
    _utf8_decode = function (utftext) {  
        var string = "";  
        var i = 0;  
        var c = c1 = c2 = 0;  
        while ( i < utftext.length ) {  
            c = utftext.charCodeAt(i);  
            if (c < 128) {  
                string += String.fromCharCode(c);  
                i++;  
            } else if((c > 191) && (c < 224)) {  
                c2 = utftext.charCodeAt(i+1);  
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));  
                i += 2;  
            } else {  
                c2 = utftext.charCodeAt(i+1);  
                c3 = utftext.charCodeAt(i+2);  
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));  
                i += 3;  
            }  
        }  
        return string;  
    }  
}*/
function mylocation(url,title,width,height){
	$.pdialog.open(url,'newdialog',title,{width:width,height:height,fresh:false,mask:false,mixable:true,minable:true,resizable:true,drawable:true});
}
function delzip(){
	$.post("__URL__/delzip",function(data){
      alert(data);
      window.location.reload();
   },'html');
}
</script>