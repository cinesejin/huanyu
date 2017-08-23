<?php
//error_reporting(E_ALL);
//ini_set('display_errors','On');

/*
* 阿里巴巴支付跳转
*/
if( !empty($_REQUEST) )
{
	$ch				= curl_init();  
	$orderId		= $_REQUEST['out_trade_no'];
	$siteDomain		= getSiteDomain();
	$url			= $siteDomain."/Admin/index.php?s=/Payment/receive/id/{$orderId}";
	curl_setopt($ch, CURLOPT_URL, $url); 
	echo $url;
	$fields			= "";
	foreach($_REQUEST as $key=>$val)
	{
		if($fields!='') $fields .= '&';
		$fields .= "{$key}={$val}";
	}

	// 启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。 
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
	curl_setopt($ch, CURLOPT_USERAGENT, "www.domain.com"); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //是否返回内容
	curl_setopt($ch, CURLOPT_HEADER, false);//设定是否输出页面头部内容  
	curl_setopt($ch, CURLOPT_POST, 1); // post,get 过去  

	$filecontent = curl_exec($ch);
	curl_close($ch);
	echo $filecontent;
}

//获取当前的域名带 协议
function getSiteDomain()
{
	$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

	return $http_type.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}
?>