<?php
// 本类由系统自动生成，仅供测试用途
defined('APP_NAME') || die('不要非法操作哦');
class MailAction extends CommonAction {
	    //邮件列表
	    public function index(){
		   //$list=M()->query("select id,state,title,send_time from email where receiver='".$_SESSION[C('USER_AUTH_NUM')]."' and type=0 order by send_time desc");
		   //$this->assign('list',$list);
           
            $list = new TableListAction('邮件');
            $list ->where(array('收件人'=>$this->userinfo['编号'],'收件人类型'=>$this->userobj->name))->order("发送时间 desc");
			$list->pageCon	= 'p1';
			$list->pagenum = 5;
			//dump($list);
            $data = $list->getData();
            $this->assign('data',$data);

			//发件箱
			$list1 = new TableListAction('邮件');
            $list1 ->where(array('发件人'=>$this->userinfo['编号'],'发件人类型'=>$this->userobj->name))->order('发送时间 desc');
			$list1->pageCon	= 'p2';
			$list1->pagenum = 5;
            $data1 = $list1->getData();
			//dump($data1);
			//dump($list1);
            $this->assign('data1',$data1);
    		$this->display();
		}
       
		//邮件状态
		function dispFunction($state){
			if($state==0){
				return "未查看";
			}
			if($state==1){
				return "已查看";
			}
			if($state==2){
				return "已回复";
			}
		}
		//发件箱
		public function sendbox()
		{
            $list = new TableListAction('邮件');
            $list->where(array('发件人'=>$this->userinfo['编号'],'发件人类型'=>$this->userobj->name))->order('发送时间 desc');
            $data = $list->getData();
			
            $this->assign('data',$data);
			$this->display();
		}
		//查看邮件详细
		public function view(){
			$model = M('邮件');
			$result = $model->where(array('收件人'=>$this->userinfo['编号'],'收件人类型'=>$this->userobj->name,'id'=>$_GET['id']))->find();
			if(!$result){
				$this->error('参数错误!');
			}
			if($result['状态'] == 0){
				M()->startTrans();
				$model->where(array('id'=>$_GET['id']))->setField('状态',1);
				M()->commit();
			}
			$this->assign('list',$result);
			$this->display();
		}
		public function sendview(){
			$model = M('邮件');
			$result = $model->where(array('发件人'=>$this->userinfo['编号'],'发件人类型'=>$this->userobj->name,'id'=>$_GET['id']))->find();
			if(!$result){
				$this->error('参数错误!');
			}
			$map['id']=$_GET['id'];
			$this->assign('list',$result);
			$this->display();
		}

		public function resend()
		{
			if($_REQUEST['id']=="")
			{
				$this->error("请选择查看的邮件",__URL__."/");
			}
			$model=M('Email');
			$map['id']=$_REQUEST['id'];
			$rf=$model->where($map)->find();
			if($rf['state']==2)
		    {
				$this->error('该信件已经回复');
			}
			$this->assign('rf',$rf);
			$this->display();
		}
		//回复邮件
		public function answer(){
			$where = array();
			$where['收件人'] = $this->userinfo['编号'];
			$where['收件人类型'] = $this->userobj->name;
			$where['id'] = $_GET['id'];
			$result = M("邮件")->where($where)->find();
			if(!$result){
				$this->error('参数错误!');
			}
			$this->assign('list',$result);
			$this->display();
		}
		public function answerSave(){
			$model=M("邮件");
			$where = array();
			$where['收件人'] = $this->userinfo['编号'];
			$where['收件人类型'] = $this->userobj->name;
			$where['id'] = $_POST['id'];
			$result = $model->where($where)->find();
			if(!$result){
				$this->error('参数错误!');
			}
			if($_POST['answerContent']==''){
				$this->error(L('内容不能为空'));
			}
			
			$data=array();
			$data['id']=$_REQUEST['id'];
			$data['回复人']=$this->userinfo['编号'];
			$data['回复内容']=$_POST['answerContent'];
			$data['回复时间']=systemTime();
			$data['状态']=2;
			M()->startTrans();
			$result=$model->save($data);
			if($result){
				//更新邮件状态
				M()->commit();
				$this->success(L('回复成功'),"__URL__/index");
			}else{
				M()->rollback();
				$this->error(L('回复失败'));
			}
		}
		//发送邮件
		public function send(){
			/*$maps['status']=array('gt',0);
			$sendto=M('role')->where($maps)->select();
			$this->assign('sendto',$sendto);*/
			$this->assign('mailset',adminshow('mailset'));
			$this->display();
				
		}
		//发送邮件
		public function send_email(){
			//防XSS跨站攻击登入 调用ThinkPHP中的XSSBehavior
		     //B('XSS');
			if($_POST['title']==""){
				$this->error(L('标题不能为空'));
			}
			if($_POST['center']==""){
				$this->error(L('内容不能为空'));
			}
			$model=M('邮件');
			$data=array();
			if(!isset($_POST['receiver']) || $_POST['receiver']==''){
				$data['收件人类型']='管理员';
			}else{
				if(trim($_POST['receiver'])==$this->userinfo['编号']){
					$this->error(L('不能发送给自己'));
				}
				if(M('用户')->where("编号='".trim($_POST['receiver'])."'")->find()){
					$data['收件人类型']=$this->userobj->name;
					$data['收件人']=trim($_POST['receiver']);
				}else{
					$this->error(L('收件人编号不存在'));
				}
			}
			$data['标题']=$_POST['title'];
			$data['内容']=$_POST['center'];
			$data['发送时间']=systemTime();
			//$data['收件人类型']='管理员';
			$data['发件人'] = $this->userinfo['编号'];
			$data['发件人类型']= $this->userobj->name;
			$data['回复人']= '';
			$data['回复内容']= '';
			$result=$model->add($data);
			if($result){
				$this->success(L('发送成功'),__URL__."/index");
			}else{
				$this->error(L('发送失败'),__URL__."/index");
			}
		}
		//删除邮件
		public function del(){
			//遍历所选
			$model	  = M('邮件');
			$map['id']= $_GET['id'];
			$map['_string'] = "(收件人='".$this->userinfo['编号']."' AND 收件人类型='".$this->userobj->name."') or (发件人='".$this->userinfo['编号']."' AND 发件人类型='".$this->userobj->name."')";
			M()->startTrans();
			$result1  = $model->where($map)->delete();
			if($result1){
				M()->commit();
				$this->success(L('删除成功'));
			}else{
				M()->rollback();
				$this->error(L('删除失败'));
			}
		}
 

}
?>