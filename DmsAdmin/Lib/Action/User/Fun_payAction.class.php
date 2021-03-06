<?php
/*
* 支付测试模块
*/
class Fun_payAction extends CommonAction 
{
	public function index()
	{
		$bank = X('fun_bank');
		$arr = array();
		$bankIn = false;
		foreach($bank as $v){
			if($v->use && $v->bankIn){
				$bankIn = true;
				$arr[]=$v->name;
			}
		}
		$this->assign('type',$arr);
		//打印出当前已安装的支付接口
		import("Admin.Pay.Pay");
		$payList = F('installedPayment','','./Admin/Runtime/Data/');
		$this->assign('list',$payList);
		$this->display();
	}

	/**
	* 支付确认
	*/
	public function pay_confirm()
	{
		if( $_POST['money'] == '' || floatval($_POST['money']) <= 0 )
		{
			$this->error(L('pay_amount'));
		}
		if( $_POST['type'] == '' )
		{
			$this->error("请选择充值货币");
		}
		if( $_POST['payment'] == '' )
		{
			$this->error(L('请选择支付方式'));
		}
				//判断是否有手机号码
//		if(!$this->userinfo['移动电话'])
//			{
//			$this->error('请先到资料里面去完善自己的联系方式');
//		}

		$payment	= $_REQUEST['payment'];

  		$_POST['tel'] = $this->userinfo['移动电话'];
		import("Admin.Pay.Pay");
		$pay = new Pay($payment,true,$_POST['money'],$_POST['type']);

		$events		= array(

			'success' => array(
				'app'		=> 'DmsAdmin',
				'group'		=> '',
				'model'		=> 'Fun_pay',
				'method'	=> 'success',
				'args'		=> array(
					'userid'	=> $this->userinfo['编号'],
                    'type'  =>$_POST["type"],
				    'money' =>$_POST["money"],
				    'payment'=>$payment,
                    'userType' =>$this->userobj->byname,
                    'paytypes' =>$_POST["banknames"],
				),
			),

			'fail' => array(
				'app'		=> 'DmsAdmin',
				'group'		=> '',
				'model'		=> 'Fun_pay',
				'method'	=> 'fail',
				'args'		=> array(
					'userid'	=> $this->userinfo['编号'],
                    'type'  => $_POST["type"],
				    'money'=>$_POST["money"],
				    'payment'=>$payment,
                    'userType' =>$this->userobj->byname,
                'paytypes' =>$_POST["banknames"],
				),
			)

		);

		$pay->bind($events);


		//测试提交
		//$pay->testSubmit(true);

		//正式提交
		$pay->submit();
		exit;
	}


	//检查支付接口是否已经安装
	private function checkPayment(&$model,$payment)
	{
		$where['app']		= 'Admin';
		$where['name']		= 'payment_installed';
		$where['data']		= $payment;

		if( $model->where($where)->find() )	
		{
			return 1;
		}
		return 0;
	}
	//在线支付列表
	public function paylist()
	{
		$list = new TableListAction("onlinepay");
		$list->where("编号='".$this->userinfo['编号']."'");
        $list->setShow = array(
           '订单号'=>array("row"=>"[订单号]"),
           "金额"=>array("row"=>"[金额]"),
            '支付方式'=> array("row"=>"[支付方式]"),
            '支付时间'=> array("row"=>"[支付时间]","format"=>"time"),          
            '备注'=> array("row"=>"[备注]"),
			'状态'=> array("row"=>array(array(&$this,"onlinepayState"),"[状态]")),
        );
        $data = $list->getData();
        $this->assign('data',$data);
		$this->display();
	}
	public function onlinepayState($state)
	{
		if($state==0){
			return '未支付';
		}elseif($state==1){
			return '支付成功';
		}elseif($state==2){
			return '支付失败';
		}
	}
	//在线支付转换
	public function scaleAjax()
	{
		$bank = X('fun_bank@'.$_POST['banktype']);
		$scale=$bank->bank_scale;
		if($scale!=1 && $_POST['money']>0){
			echo "进".(number_format(($_POST['money']/$scale),2)).$bank->name;
		}
	}
}
?>