<? header("content-Type: text/html; charset=utf-8");?>
import("COM.Interface.PayInterface");
/*
*
* 云宝支付 支付类
*
*/
class ZhiFu implements PayInterface
{
	//支付网关地址
	public $gateway_url			= 'https://pay.dinpay.com/gateway?input_charset=UTF-8';	
	/*
	* 构造函数
	*/
	function __construct() 
	{
		$Model						= M();
		$this->merchant_code			    = $MerNo?$MerNo:'';
		$SignInfo				=isset($data['ZhiFu_SignInfo'])?$data['ZhiFu_SignInfo']:'';
		$this->sign			= $SignInfo?$SignInfo:'';
		$Hui_proxy					= isset($data['ZhiFu_proxy'])?$data['ZhiFu_proxy']:'';
		$this->proxy			= $Hui_proxy?$Hui_proxy:'';
		$this->order_time            = date('Y-m-d H:i:s',systemTime());
	}

	//返回支付接口中文名称
	public static function getName()
	{
		return '智付';
	}

	//返回接口中文介绍
	public static function getMemo()
	{
		return '智付 （Dinpay）是中国领先的独立第三方支付公司';
	}

	//返回需要配置的项


	//提交表单
	public function submit()
	/*
	* 处理收到的数据
	*/
	public function receive()


?>