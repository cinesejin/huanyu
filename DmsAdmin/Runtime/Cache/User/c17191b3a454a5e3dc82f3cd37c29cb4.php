<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户登录</title>
<style type="text/css">
body{margin:0;padding:0;text-align:center; font-size:12px; background:url(DmsAdmin/Tpl/User/login/2/MainBg.jpg) repeat-x;}
#MainContainer{background:url(DmsAdmin/Tpl/User/login/2/LoginBox.jpg) no-repeat center top;text-align:left;width:695px;height:365px;padding:188px 0 0 302px;margin:auto;}
#login{width:379px; height:225px;background:url(DmsAdmin/Tpl/User/login/2/bg.jpg) no-repeat;}

.Linput{border:1px solid #a1a0a0; height:18px;width:150px;}
.xx{color:#27660b;}
.STYLE1{
	margin-top:-100px;
}
</style>
<script src="__PUBLIC__/jquery-1.8.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
function fleshVerify(type){ 
	//重载验证码
	var timenow = new Date().getTime();
	if (type){
		$('#verifyImg').attr("src", '__URL__/verifys/adv/1/'+timenow);
	}else{
		$('#verifyImg').attr("src", '__URL__/verify/'+timenow);
	}
}
</script>
</head>

<body>

<div id="MainContainer">
<div id="login">
<form action="__URL__/check" method="post" style="margin:0px; padding:0px" id="loginform">
<input type="hidden" name="act" value="verify" />
<table width="93%" height="186" cellpadding="0" cellspacing="0" class="tb">
<tr>
  <td height="62" align="right">&nbsp;</td>
  <td align="left">&nbsp;</td>
</tr>
<tr>
<td width="143" height="30" align="right" class="xx">用户名&nbsp;&nbsp;</td>
<td align="left" width="168"><input class="Linput" type="text" name="username" id="username" /></td>
</tr>
<tr>
<td width="143" height="20" align="right" class="xx">密　码&nbsp;&nbsp;</td>
<td align="left"><input class="Linput" type="password" name="password" id="password" /></td>
<td width="39" rowspan="2" align="left">&nbsp;</td>
</tr>
<?php if(isset($dispCode) and $dispCode == true): ?><tr>
<td width="143" height="36" align="right" class="xx">验证码&nbsp;</td>
<td align="left"><input name="captcha" maxlength="4" id="verify" type="text" style="width:50px;height:18px;" /> &nbsp; <img id="verifyImg" SRC="__URL__/verify/" onClick="fleshVerify()" border="0" alt="点击刷新验证码" style="cursor:pointer" align="absmiddle"></td>
</tr><?php endif; ?>
			<?php if($usernum == 1): ?><input type="hidden" value="<?php echo ($users[0]); ?>" name="usertype"><?php endif; ?>
<tr>
  <td height="36" align="right">&nbsp;</td>
  <td align="left"><?php if(($isOpenTime) == "0"): ?>对不起，该时间段暂不可访问<?php else: ?><span style="margin:0px 20px 20px 0;">
    <input type="submit" style="width:48px;height:21px;border:1px solid #a1a0a0;;background:url(DmsAdmin/Tpl/User/login/2/Submit.jpg) no-repeat;margin:0;padding:0;" value=""/>　
    <input type="reset" style="width:48px;height:21px;border:1px solid #a1a0a0;background:url(DmsAdmin/Tpl/User/login/2/Reset.jpg) no-repeat;margin:0;padding:0;" value="" />
  </span><?php endif; ?></td><td style="width:80px;"><?php if(adminshow('mimazhaohui') == true): ?><a href="__URL__/getUserPwd2/">忘记密码？</a><?php endif; ?></td>
</tr>
</table>
</form>
</div>
</div>
<div align="center" class="STYLE1"><span><?php echo ($openTimeStr); ?></span></div>
</body>
</html>