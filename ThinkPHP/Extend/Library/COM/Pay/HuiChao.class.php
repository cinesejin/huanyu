<?php
import("COM.Interface.PayInterface");
/*
* 云宝支付 支付类
*
*/
class HuiChao implements PayInterface
{
	public $gateway_url			= 'https://pay.ecpss.com/sslpayment';	//支付网关地址
	

	/*
	* 构造函数
	*/
	function __construct() 
	{
		$Model						= M();
		$this->MerNo			    = $MerNo?$MerNo:'';
		$SignInfo				=isset($data['HuiSignInfo'])?$data['HuiSignInfo']:'';
		$this->SignInfo			= $SignInfo?$SignInfo:'';
		$Hui_proxy					= isset($data['Hui_proxy'])?$data['Hui_proxy']:'';
		$this->Hui_proxy			= $Hui_proxy?$Hui_proxy:'';
		$rate                       = isset($data['Hui_merchant_rate'])?$data['Hui_merchant_rate']:1;
		$this->rate			        = $rate ? (float)$rate : 1;
		$this->orderTime            = date('Ymd',systemTime());
	}


	//返回支付接口中文名称
	public static function getName()
	{
		return '汇潮';
	}

	//返回接口中文介绍
	public static function getMemo()
	{
		return '汇潮支付有限公司，是提供国内人民币卡收单服务的第三方支付平台， 全力为国内互联网支付的商家提供国内一流的收单服务。';
	}

	//返回需要配置的项


	//提交表单
	public function submit()
	/*
	* 处理收到的数据
	*/
	public function receive()


?>