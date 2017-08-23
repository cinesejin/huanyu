<?php
import("COM.Interface.PayInterface");
/*
* 云宝支付 支付类
*
*/
class HuiChao implements PayInterface
{
	public $gateway_url			= 'https://pay.ecpss.com/sslpayment';	//支付网关地址	public $BillNo				= 0;				//订单号	public $Amount				= 0;				//金额	public $rate                = 1;	public $MerNo			    = '';				//商户ID	public $SignInfo		    = '';				//商户签名	public $orderTime           = '';               //加入订单时间	public $cmd					= "Buy";			//业务类型 默认Buy	public $products		= 'HuiChao online payment';			//商品信息    public $Remark                ='';                 //添加备注信息    public $bank            ='';                 //加入银行通道	public $custom				= '';				//定制信息,支付成功时将原样返回.	public $bank_type			= '';				//支付通道类型, 可直连到各大银行	public $need_response		= 1;				//默认为"1": 需要应答机制;		public $record_address		= 0;				//为"1": 需要用户将送货地址留在易宝支付系统;为"0": 不需要，默认为 "0".	public $AdviceURL		= '';				//支付结果浏览器通知URL,完整路径带http	public $Hui_proxy			= '';				//代理转发地址,针对于域名绑定的情况    public $return_url			= "";	//同步返回URL	public $message				= '';	public $sendMsg				= '在线充值的时候请不要关闭页面！充值成功后页面自动跳转..';	//发送充值时的提示
	

	/*
	* 构造函数
	*/
	function __construct() 
	{	  //读取接口表中的pay_type		$arr = M('pay_onlineaccount',' ')->where(array('pay_type'=>'HuiChao'))->order("pay_amount asc,id desc")->find();		//将$arr['pay_attr'] 返序列化	    $data_arr = unserialize($arr['pay_attr']);//是一个二维数组 	    //查询金额最小的金额的记录	      $data = array();		   foreach((array)$data_arr as $key=>$v)		   {		   	   foreach((array)$v as $key1=>$v1){		         $data[$key1] = $v1;		      	}		   }		//读取数据库中的设置
		$Model						= M();		$MerNo				        = isset($data['HuiMerNo'])?$data['HuiMerNo']:'';
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

	//返回需要配置的项	//返回需要配置的项	public static function getConfigInfo()	{		return array(			array(				'config_name'=>'Hui_name',				'config_value'=> '汇潮',				'name'=>'支付方式名称',				'type'=>'text',				'style'=>'width:100px',			),			array(				'config_name'=>'HuiMerNo',				'config_value'=> '',				'name'=>'商户ID',				'type'=>'text',				'style'=>'width:100px',			),			array(				'config_name'=>'HuiSignInfo',				'config_value'=> '',				'style'=>'width:430px',				'name'=>'商户签名',				'type'=>'text',			),			array(				'config_name'=>'Hui_proxy',				'config_value'=> '',				'name'=>'php转发Url',				'type'=>'text',				'style'=>'width:350px',				'memo'=>'<a href="/Admin/Common/pay_location.php.txt" target="_blank">下载php转发文件</a>',			),			array(				'config_name'=>'Hui_merchant_rate',				'config_value'=> '1',				'name'=>'实付倍数',				'type'=>'text',				'style'=>'width:50px',				'memo'=>'如设置为6倍，则表示支付1元电子币，实际要支付6元人民币',			),		);	}


	//提交表单
	public function submit()	{		//提交的地址 支付地址		$_action_url			= $this->gateway_url;		$_location_url			= '';		//得到要支付的货币金额		$Amount                 = $this->Amount;		//得到实际支付金额		$real_amount            = $Amount*$this->rate;		$this->Amount			= $real_amount;		//tring(38) "20223&20140704183318659808&1&&_FsoCnTw"        $md5src = $this->MerNo."&".$this->BillNo."&".$this->Amount."&".$this->AdviceURL."&".$this->SignInfo;		$auth = strtoupper(md5($md5src));		//是否使用代理跳转		if(	$this->Hui_proxy != '' )		{			$_action_url	= $this->Hui_proxy;			$_location_url	= base64_encode($this->gateway_url);		}		print <<<EOF<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>Payment By CreditCard online</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body><form action="{$_action_url}" method="post" name="E_FORM" id="E_FORM">  <table align="center">    <tr>      <td></td>      <td><input type="hidden" name="location_url" value="{$_location_url}"></td>    </tr>    <tr>      <td></td>      <td><input type="hidden" name="MerNo" value="{$this->MerNo}"></td>    </tr>    <tr>      <td></td>      <td><input type="hidden" name="BillNo" value="{$this->BillNo}"></td>    </tr>    <tr>      <td></td>      <td><input type="hidden" name="Amount" value="{$this->Amount}"></td>    </tr>    <tr>      <td></td>      <td><input type="hidden" name="ReturnURL" value="{$this->AdviceURL}" ></td>    </tr>    	 <tr>      <td></td>      <td><input type="hidden" name="AdviceURL" value="{$this->AdviceURL}" ></td>    </tr>	 <tr>      <td></td>      <td><input type="hidden" name="orderTime" value="{$this->orderTime}">></td>    </tr>    	 <tr>      <td></td>      <td><input type="hidden" name="defaultBankNumber" value="{$this->bank}"></td>    </tr>    <tr>      <td></td>      <td><input type="hidden" name="SignInfo" value="{$auth}"></td>    </tr>    <tr>      <td></td>      <td><input type="hidden" name="Remark" value="{$this->Remark}"></td>    </tr>    <tr>      <td></td>      <td><input type="hidden" name="products" value="{$this->products}"></td>    </tr>  </table>  <p align="center">    <input type="submit" name="b1" value="Payment">  </p></form></body></html>	<script language="javascript">document.getElementById("E_FORM").submit();</script>EOF;		exit;	}
	/*
	* 处理收到的数据
	*/
	public function receive()	{		$BillNo			= $_REQUEST['BillNo'];		//订单编号 		$Amount	 	    = $_REQUEST['Amount'];		// 订单金额		$Succeed		= $_REQUEST['Succeed'];	    // 支付状态		$Result			= $_REQUEST['Result'];		// 支付结果		$MD5info		= $_REQUEST['MD5info'];		// 取得的MD5校验信息		$Remark			= $_REQUEST['Remark'];		// 备注		$SignInfo       = $this->SignInfo ;        //密钥                	//校验源字符串        $md5src = $BillNo.$Amount.$Succeed.$SignInfo;           //MD5检验结果	    $md5sign = strtoupper(md5($md5src));		if($MD5info==$md5sign) //签名验证通过		{			if($Succeed=="88") //支付成功			{				$Model				= M();			//	$where['orderId']	= $BillNo;			//	$info				= $Model->table('pay_order')->where($where)->find();				$this->message = "success";				return true;			}			else			{				$this->message = "支付失败";				return false;			}		}		else		{			$this->message = "签名不正确！";			return false;		}	}

	//设置支付金额	public function setMoney($money)	{		$this->Amount = number_format($money,2,'.','');	}	//获取支付金额	public function getMoney()	{		return $this->Amount;	}	//设置订单id	public function setOrderId($id)	{		$this->BillNo = $id;	}	//获取订单id	public function getOrderId()	{		return $this->BillNo;	}	//设置支付返回地址	public function setServerurl($url)	{		$this->AdviceURL  = $url;	}	//设置浏览器跳转地址	public function setLocationUrl($url)	{	 	$this->return_url  = $url;	}		//是否支持银行直连	public function isSupportCredit()	{		return true;	}	//返回支付失败的提示信息	public function getMessage()	{		return $this->message;	}	//提供的直连银行的列表	public static function getBankList(){	   return array(	   'ICBC'=>'中国工商银行',	   'CCB'=>'中国建设银行',	   'ABC'=>'中国农业银行',	   'BOCSH'=>'中国银行',	   'SPDB'=>'浦发银行',	   'CMB'=>'招商银行',	   'BOCOM'=>'交通银行',	   'PSBC'=>'邮政储蓄',	   'GDB'=>'广发银行',	 	   'CMBC'=>'中信银行', 	   'CEB'=>'光大银行', 	   'HXB'=>'华夏银行', 	   'CIB'=>'兴业银行', 	   'BOS'=>'上海银行', 	   'SRCB'=>'上海农商',  	   'PAB'=>'平安银行',	   'BCCB'=>'北京银行',	   'BOC'=>'中行（大额）',	   );	}	//设置直连的银行	public function setCreditBank($bank){	    $this->bank = $bank;	}	//返回当前直连银行的中文名称	public function getCreditBankName(){	    $bankList = HuiChao::getBankList();		return $bankList[$this->bank];	}}
?>