<?php
defined('APP_NAME') || die('不要非法操作哦');
class TransferAction extends CommonAction{
	public function index()
	{
		$banks = M('转账设置')->where(array('status'=>1))->select();
		$bankdata = array();
		foreach((array)$banks as $bank)
		{
			$bankdata[$bank['id']] = $bank['title'];
		}
		$this->assign('pwd3Switch',adminshow('pwd3Switch'));
		$this->assign('giveMoneyPass2',CONFIG('giveMoneyPass2'));
		$this->assign('giveMoneyPass3',CONFIG('giveMoneyPass3'));
		$this->assign('giveMoneySmsSwitch',CONFIG('giveMoneySmsSwitch'));
		$this->assign('giveMoneySmsContent',CONFIG('giveMoneySmsContent'));
		$this->assign('bankdata',$bankdata);
		$this->display();
	}
	public function giveType()
	{
		$bank = M('转账设置')->where(array('id'=>$_POST['giveToid']))->find();
		if($bank)
		{
			$bank['balance'] = $this->userinfo[$bank['bank']];
		}
		if($bank)
		{
			$this->ajaxReturn($bank,'成功',1);
		}else{
			$this->ajaxReturn('','失败',0);
		}
	}
	//转账验证
	public function giveAjax()
	{
		$user='';
		if($_POST['userid']!=$this->userinfo['编号'])
		{
			$user = $this->userobj->getuser($_POST['userid']);
		}
		if($user && $_POST['userid']!= '')
		{
			$this->ajaxReturn(array('姓名'=>$user['姓名']),'成功',1);
		}
		else
		{
			$this->ajaxReturn('','失败',0);
		}
	}
	//转账提交
	public function giveSave()
	{
                    //验证转账货币、转账类型是否已选择
		if(empty($_POST['giveTo']) || empty($_POST['giveTypes']) || $_POST['giveTypes']=='wu')
		{
			$this->error('请选择转账类别 !');
		}
		//验证是否有提交的转账类型
		$have = M('转账设置')->where(array('id'=>$_POST['giveTo'],$_POST['giveTypes']=>1,'status'=>1))->find();
		if(!$have)
		{
			$this->error('操作失败!');
		}
		//获取转账货币的节点
		$bank=X('fun_bank@'.$have['bank']);
		M()->startTrans();
		$mess='';
		//判断是否服务中心限定
		if($have['shop']!='无'){
			if($this->userinfo['服务中心']==0){
				$mess = L('仅限服务中心使用此功能');
			}
		}
		//如果需要验证二级密码
		if(CONFIG('giveMoneyPass2')==1 && !chkpass($_POST["pass2"],$this->userinfo["pass2"])){
		    $mess = L('交易密码错误');
		}
        //如果需要验证三级密码
        if(CONFIG('giveMoneyPass3')==1 && !chkpass($_POST["pass3"],$this->userinfo["pass3"])){
	        $mess .= L('三级密码错误');
        }
        //如果被锁定
        if($this->userinfo[$bank->name.'锁定']==1)
        {
        	$mess=L('您的账户处于锁定状态.不能操作');
        }
        //验证用户条件谁否可以转账
        if(!transform($bank->giveMoneyWhere,$this->userinfo))
        {
        	$mess=$bank->giveMoneyMsg;
        }
        //如果有验证没通过,报告错误信息
        if($mess != ""){				
            $this->error($mess);
            exit();
        }
		if(CONFIG('giveMoneySmsSwitch')==1){
			$verify = S($this->userinfo['编号'].'_'.$bank->name.'转账');
			if(!$verify || $verify != intval($_POST['giveSmsVerfy']) || !$_POST['giveSmsVerfy']){
				$this->error('短信验证码错误或已过期!');
			}
		}
		//如果转账给其他人
		$message='';
		if($_POST['giveTypes']=='toyou')
		{
			$userid = trim($_POST["userid"]);
            if($userid =="" || !$this->userobj->have($userid)){
                $message .= L('转入账户不存在')."<br/>";     //输出用户不存在提示
    		}
            if(strtolower($userid) == strtolower($this->userinfo["编号"])){
                $message .= L('转入账户不能为自己')."<br/>";
            }
            // 网体判断
            if($have["nets"]!="无"){
                foreach(X('net_rec,net_place') as $net){
                    if($net->name == $have["nets"]){
                        $up = $net->getups($this->userinfo,0,0,"编号='$userid'");
                        $down = $net->getdown($this->userinfo,0,0,"编号='$userid'");
                        if(!$up && !$down){
                            $message .= L('只能转入'.$net->byname.'网体')."<br/>";
                        }
                    }elseif($net->name.'上级' == $have["nets"]){
						$up = $net->getups($this->userinfo,0,0,"编号='$userid'");
						if(!$up){
                            $message .= L('只能转入'.$net->byname.'上级')."<br/>";
                        }
					}elseif($net->name.'下级' == $have["nets"]){
						$up = $net->getdown($this->userinfo,0,0,"编号='$userid'");
						if(!$up){
                            $message .= L('只能转入'.$net->byname.'下级')."<br/>";
                        }
					}
                }
            }
            //转账他人的限制
            if($have["toyoutype"]!=""){
                $fwzx=M("用户")->where(array('编号'=>$userid))->getField('服务中心');
                $toyoutype=explode(',',$have["toyoutype"]);
                $typestr=$this->userinfo['服务中心']."-".$fwzx;
                if(!in_array($typestr,$toyoutype)){
                    $this->error('转账他人选择受限');
                }
            }
            //默认编号和数据库一致
            if($message==''){
            	$userid=M('用户')->where(array("编号"=>$userid))->getField('编号');
            }
		}
        $givesum = $_POST["givesum"];
       	//如果转账金额为空,报错
        if($givesum=='')
		{
			 $message .= L('转账金额不能为空')."<br/>";
		}
        if(!is_numeric($givesum)|| $givesum <= 0){		//如果转账金额不是大于0的数字,报错
            $message .= L('转账金额不是大于0的数字')."<br/>";
        }else if($have["minnum"] > $givesum){	//如果转账金额小于最小转账金额限定,报错
            $message .= L('转账金额小于最小转账金额').$have['minnum']."<br/>";
        }else if($have["maxnum"] !=0 && $have["maxnum"] < $givesum){	//如果转账金额大于最大转账金额限定,报错
            $message .= L('转账金额大于最大转账金额').$have['maxnum']."<br/>";
        }
        if($have['intnum'] !="0" && $givesum % $have['intnum'] != 0){//如果转账金额不符合设定的整数倍限定,报错
            $message .= L('转账金额必须为').$have['intnum'].L('的整数倍')."<br/>";
        }
        if($message != ""){		//输出错误信息
            $this->error($message);
        }
        $m_user = M('用户');
        //获取转账的用户
        $re = $m_user->where("编号='".$this->userinfo["编号"]."'")->lock(true)->find();
        $re_h = M('货币')->where("编号='".$this->userinfo["编号"]."'")->lock(true)->find();//货币分离
        $re=$re+$re_h;
        //获取转账金额
        $givesum = floatval($givesum);
        //做上限判断,账户金额不能大于多少
        if($_POST['giveTypes']=='toyou')
        {
        	$touser=$m_user->where(array('编号'=>$userid))->lock(true)->find();
        	$touser_h=M('货币')->where(array('编号'=>$userid))->lock(true)->find();
        	$touser=$touser+$touser_h;
        }
        else
        {
        	$touser=$re;
        }
        //获取转账到那种货币的节点
        $tobank=X('fun_bank@'.$have['tobank']);
        $tops = $tobank->getcon('top',array('where'=>'','val'=>0,'msg'=>''));
        foreach($tops as $top)
        {
        	if(transform($top['where'],$touser) && $touser[$tobank->name] + $givesum > $top['val'])
        	{
        		if($top['msg'] == '')
        		{
        			$this->error('要转入的账户超过限额');
        		}
        		else
        		{
        			$this->error($top['msg']);
        		}
        	}
        }
        //计算手续费
        $taxstr="";
        $tax = ($have['tax']/100) * $givesum;
        //如果手续费大于手续费上限，按照手续费上限扣除
        if($tax>$have['taxtop'] && $have['taxtop']>0){
        	$tax=$have['taxtop'];
        }elseif($tax<$have['taxlow'] && $have['taxlow']>0){//手续费下限同理 
        	$tax=$have['taxlow'];
        }
        if($have['taxfrom']==0){
        	$taxstr="，扣除手续费".$tax;
        	$givesum1  = $givesum;		//转入的金额
        	$givesum+=$tax;
        }else{
        	$givesum1  = $givesum - $tax;		//扣除手续费后转入的金额
        }
        //判断完成
        if($re[$bank->name] < $givesum){  //如果余额不足,输出错误信息
			$m_user->execute('unlock tables');
            $this->error(L('余额不足'.$givesum.$taxstr));
        }else{
        	//转换比率
        	$givesum1 = $givesum1 * ($have["sacl"]/100);
            //转账成功
            if($userid !=""){	//如果是转入其他人的账户, 转账处理,当前用户扣除货币,转入人增加货币
                $bank->set($this->userinfo["编号"],$userid,-$givesum,'转账转出',$_POST["memo"]."(转给[{$userid}]的{$tobank->byname})".$taxstr);
                $data=array(
            		"转出编号"=>$this->userinfo["编号"],
            		"转出货币"=>$bank->name,
            		"转出金额"=>$givesum,
            		"手续费"=>$tax,
            		"转入编号"=>$userid,
            		"转入货币"=>$tobank->name,
            		"转入金额"=>$givesum1,
            		"转换比率"=>$have["sacl"],
            		"操作时间"=>systemTime(),
            		"状态"=>"未审核",
            		"备注"=>$_POST["memo"]
            	);
                if(CONFIG('sureGiveMoney')==1){
                	M("转账明细")->add($data);
                }ELSE{
                	$tobank->set($userid,$this->userinfo["编号"],$givesum1,'转账转入',$_POST["memo"]."(转自[".$this->userinfo["编号"]."]的{$bank->byname})");
                }
            }else{
            	$userid=$this->userinfo["编号"];
            	//如果转入自己的其他货币账户, 扣除转出账户金额,增加转入账户金额
                $bank->set($this->userinfo["编号"],$this->userinfo["编号"],-$givesum,'转账转出',$_POST["memo"]."(转给自己的{$tobank->byname})".$taxstr);
                $data=array(
            		"转出编号"=>$this->userinfo["编号"],
            		"转出货币"=>$bank->name,
            		"转出金额"=>$givesum,
            		"手续费"=>$tax,
            		"转入编号"=>$userid,
            		"转入货币"=>$tobank->name,
            		"转入金额"=>$givesum1,
            		"转换比率"=>$have["sacl"],
            		"操作时间"=>systemTime(),
            		"状态"=>"未审核"
            	);
                if(CONFIG('sureGiveMoney')==1){
                	M("转账明细")->add($data);
                }ELSE{
                	$tobank->set($userid,$this->userinfo["编号"],$givesum1,'转账转入',$_POST["memo"]."(转自自己的{$bank->byname})");
                }
            }
            //写入用户操作日志
            X("user")->adduserlog($this->userinfo,$_SESSION['ip'],'用户转账');
            //写入用户操作日志结束
			//发送的验证码注销
			S($this->userinfo['编号'].'_'.$bank->name.'转账',null,300);
			//写入用户操作日志结束
			//添加用户转账短信提醒
            /*if($this->userobj->getatt('zhzhmsmsSwitch')){
            	if($this->userobj->getatt('zhzhmsmsSwitch1')){
          			 $copy=1; 
            	}
				sendSms($this->userinfo,$this->userobj->byname.'转账',$this->userobj->getatt('zhzhmsmsContent'),$copy);
            }*/
            
            //添加用户转账邮件提醒
            if(CONFIG('zhzhmmailSwitch')){
				sendMail($this->userinfo,$this->userobj->byname.'转账',CONFIG('zhzhmmailContent'));
            }
            //添加结束
           	M()->commit();
           	M()->startTrans();
           	sendSms('zhzh',$this->userinfo['编号'],$this->userobj->byname.'转账',$data);
			if(CONFIG('sureGiveMoney')==1){
				sendSms('zhzhget',$userid,$this->userobj->byname.'转账',$data);
			}
			M()->commit();
            $this->success(L('转账成功'),"__URL__/index/");
        }
	}
}
?>