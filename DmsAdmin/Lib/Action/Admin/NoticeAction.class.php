<?php
// 本类由系统自动生成，仅供测试用途
defined('APP_NAME') || die('不要非法操作哦!');
class NoticeAction extends CommonAction {
	
	
	//上传首页图
	public function indeximg(){
        $setButton=array(                 // 底部操作按钮显示定义
			'上传图片'=>array("class"=>"add","target"=>"dialog" ,"href"=>"__URL__/upimg",'mask'=>true),
			'修改'=>array("class"=>"edit","target"=>"dialog" ,"href"=>"__URL__/editimg/id/{tl_id}",'mask'=>true),
			'删除'=>array("class"=>"delete","href"=>"__URL__/delimg/id/{tl_id}","target"=>"ajaxTodo" ,"title"=>"确定要删除吗?"),
        );
        $list=new TableListAction("首页图片"); // 实例化Model 传表名称 
        $list->order("顺序号 asc,id asc");
        $list->setButton = $setButton;       // 定义按钮显示
        $list->addshow("顺序号",array("row"=>'[顺序号]','css'=>'width:50px;'));
        $list->addshow("位置",array("row"=>array(array(&$this,"zhuangtai"),"[位置]"),"css"=>'width:50px'));
        $list->addshow("标题",array("row"=>'[title]','css'=>'width:150px;'));
        $list->addshow("图片",array('row'=>array(array($this,'getimg'),'[imgpath]'),"searchMode"=>"text",'css'=>'width:150px;'));
        $list->addshow("地址",array("row"=>'[urlpath]','css'=>'width:150px;'));
		$this->assign('list',$list->getHtml());
		$this->display();
	}
	public function upimg(){
		$this->assign('DEFAULT_THEME',CONFIG('DEFAULT_THEME'));
		$this->display();
	}
     public function zhuangtai($status){
		if($status=='1'){
			return "首页轮播";
		}elseif($status=='2'){
			return "首页列表";
		}
		elseif($status=='3'){
			return "LOGO";
		}
	 }
		// 获得图片
	public function getimg($imgstr){
		if($imgstr == ''){
			return '无';
		}else{
			$string = explode('.',$imgstr);
			unset($string[0]);
			$stg = implode('.',$string);
		    return '<img src='.$stg.' style="width:120px;" />';
		}
	}
	//保存图片
	public function upimgsave(){
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 3145728 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath =  './Upload/indeximg/';// 设置附件上传目录
		$upload->uploadReplace =true;
		$upload->saveRule=time();
        if(!file_exists_case($upload->savePath)) 
        {
            mk_dir($upload->savePath);  //如果目录不存在自动创建目录
        }
 		if(!$upload->upload()) {// 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
		}
		
	//	dump($info);
		$data=array('title'=>$_POST['title'],'位置'=>$_POST['Position'],'顺序号'=>$_POST['顺序号'],'urlpath'=>$_POST['urlpath'],'imgpath'=>$upload->savePath.$info[0]['savename']);
		// 保存表单数据 包括附件数据
		$User = M("首页图片"); // 实例化User对象
		$User->add($data); // 写入用户数据到数据库
		$this->success('数据保存成功！');
	}
	public function editimg(){
		if(strpos($_GET['id'],',') !== false){
			$this->error('参数错误!');
		}
		$this->assign('DEFAULT_THEME',CONFIG('DEFAULT_THEME'));
		$model		= M('首页图片');
		$map['id']  = $_GET['id'];
		$list	    = $model->where($map)->find();
		$this->assign('list',$list);
		$this->display();
	}
	public function editimgSave(){
		$data['title'] = $_POST['title'];
		$data['位置'] = $_POST['Position'];
		$data['顺序号'] = $_POST['顺序号'];
		$data['urlpath'] = $_POST['urlpath'];

		$m = M('首页图片');
		if($m->where(array('id'=>$_GET['id']))->save($data)){
			$this->success('修改完成！');
		}else{
			$this->error('未发生改变！');
		}
	}
	public function delimg(){
		$imgid = $_REQUEST['id'];
		$M = M('首页图片');
		$result =$M->where(array('id'=>$imgid))->find();
		$imgpath = $upload->savePath.$result['imgpath'];
		if(file_exists($imgpath)){
			unlink($imgpath);
		}
		$M->delete($result['id']);
		$this->success("删除成功");
	}
	
	
	
	//公告列表
	public function index(){
        $setButton=array(                 // 底部操作按钮显示定义
			'发布公告'=>array("class"=>"add","target"=>"navTab" ,"href"=>"__URL__/send"),
			'修改'=>array("class"=>"edit","href"=>"__URL__/edit/id/{tl_id}","target"=>"navTab","title"=>"修改公告"),
			'删除'=>array("class"=>"delete","href"=>"__URL__/del/id/{tl_id}","target"=>"ajaxTodo" ,"title"=>"确定要删除吗?"),
			'置顶'=>array("class"=>"edit","href"=>"__URL__/editTop/id/{tl_id}","target"=>"ajaxTodo" ,"title"=>"确定要置顶吗?"),
        );
        $list=new TableListAction("公告"); // 实例化Model 传表名称 
        $list->setButton = $setButton;       // 定义按钮显示
		$list->order("顺序 desc,id desc");  //定义查询条件
        //$list->addshow("id",array("row"=>"[id]"));      // 增加列表显示字段
        $list->addshow("标题",array("row"=>'<a target="navTab" title="详情" href="'.__URL__.'/view/id/[id]">[标题]</a>',"searchMode"=>"text",'searchRow'=>'[标题]'));
        
        $list->addshow("创建时间",array("row"=>"[创建时间]","searchMode"=>"date","format"=>"time"));
		$list->addshow("语言",array("row"=>array(array(&$this,"showlang"),"[语言]")));
		$list->addshow("查看权限",array("row"=>array(array(&$this,"showAuthority"),"[查看权限]",'[netname]')));
		$list->addshow("操作",array("row"=>'<a href="__APP__/User/User/showNotice/id/[id]" target="_blank">预览</a>'));
		$this->assign('list',$list->getHtml());
        $this->display();
	}
	
	//董事长致辞列表
	public function dirindex(){
        $setButton=array(                 // 底部操作按钮显示定义
			'发布致辞'=>array("class"=>"add","target"=>"navTab" ,"href"=>"__URL__/dirsend"),
			'修改'=>array("class"=>"edit","href"=>"__URL__/diredit/id/{tl_id}","target"=>"navTab","title"=>"修改致辞内容"),
			'删除'=>array("class"=>"delete","href"=>"__URL__/dirdel/id/{tl_id}","target"=>"ajaxTodo" ,"title"=>"确定要删除吗?"),
        );
        $list=new TableListAction("致辞"); // 实例化Model 传表名称 
        $list->setButton = $setButton;       // 定义按钮显示
        $list->addshow("标题",array("row"=>'<a target="navTab" title="详情" href="'.__URL__.'/dirview/id/[id]">[标题]</a>',"searchMode"=>"text",'searchRow'=>'[标题]'));
        $list->addshow("创建时间",array("row"=>"[创建时间]","searchMode"=>"date","format"=>"time"));
		$list->addshow("语言",array("row"=>array(array(&$this,"showlang"),"[语言]")));
		$list->addshow("状态",array("row"=>array(array(&$this,"zhis"),"[应用]")));
		$this->assign('list',$list->getHtml());
        $this->display();
	}
	//判断状态
	public function zhis($arg){
		if($arg==1){
			return '已应用';
		}else{
			return '已关闭';
		}
	}
	
	//置顶操作
	public function editTop()
	{
		if(strpos($_GET['id'],',') !== false){
			$this->error('参数错误!');
		}
		$id = $_GET['id'];
		//获取除了自身之外的最高排序
		M()->startTrans();
		$maxnum=M('公告')->where(array('id'=>array("neq",$id)))->max("顺序");
		$maxnum++;
		$ret = M('公告')->where(array('id'=>$id))->save(array('顺序'=>$maxnum));
		if($ret)
		{
			M()->commit();
			$this->success('置顶完成！');
		}else{
			M()->rollback();
			$this->error('置顶失败！');
		} 
	}
	public function showlang($lang)
	{
		if($lang=="zh-cn"){
		  return "中文";
		}
		if($lang=="en-us"){
		  return "英文";
		}

	}
	public function showAuthority($quanxian,$netname){
		if($quanxian == 0){
			return '全部'.$this->userobj->byname;
		}else{
			$usernum = M('用户')->where(array('id'=>$quanxian))->getField('编号');
			return $usernum.'的'.$netname.'网络';
		}
	}
	public function edit(){
		if(strpos($_GET['id'],',') !== false){
			$this->error('参数错误!');
		}
		$model		= M('公告');
		$map['id']  = $_GET['id'];
		$list	    = $model->where($map)->find();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function diredit(){
		if(strpos($_GET['id'],',') !== false){
			$this->error('参数错误!');
		}
		$model		= M('致辞');
		$map['id']  = $_GET['id'];
		$list	    = $model->where($map)->find();
		$this->assign('list',$list);
		$this->display();
	}
	public function editSave(){
		$data['标题'] = $_POST['标题'];
		$data['发布人'] = $_POST['发布人'];
		$data['内容'] = get_magic_quotes_gpc() ? stripslashes($_POST['内容']) : $_POST['内容'];

		$m = M('公告');
		M()->startTrans();
		if($m->where(array('id'=>$_GET['id']))->save($data)){
			M()->commit();
			$this->success('修改完成！');
		}else{
			M()->rollback();
			$this->error('未发生改变！');
		}
	}
	
	public function direditSave(){
		$data['标题'] = $_POST['标题'];
		$data['发布人'] = $_POST['发布人'];
		$data['内容'] = get_magic_quotes_gpc() ? stripslashes($_POST['内容']) : $_POST['内容'];
		$data['应用'] = $_POST['zhici'];
		$m = M('致辞');
		M()->startTrans();
		if($m->where(array('id'=>$_GET['id']))->save($data)){
			M()->commit();
			$this->success('修改完成！');
		}else{
			M()->rollback();
			$this->error('未发生改变！');
		}
	}
	//公告详细信息
	public function view(){
		if($_GET['id']==""){
			$this->error("请选择查看的公告",__URL__."/");
		}
		$model		= M('公告');
		$map['id']  = $_GET['id'];
		$list	    = $model->where($map)->find();
		$this->assign('list',$list);		
		$this->display('view');
	}
	
	//公告详细信息
	public function dirview(){
		if($_GET['id']==""){
			$this->error("请选择查看的致辞",__URL__."/");
		}
		$model		= M('致辞');
		$map['id']  = $_GET['id'];
		$list	    = $model->where($map)->find();
		$this->assign('list',$list);		
		$this->display('dirview');
	}
	// 遍历选择
	public function send(){
		$sendto=array();

		$sendto[]=array("name"=>"全部".$this->userobj->byname,"path"=>'',"isuser"=>1);
		//判断如果是豪华版可以发布团队公告
		if(C('VERSION_SWITCH') == '0'){
			//遍历所有下级节点
			foreach(X('net_rec,net_place') as $net)
			{
				$sendto[]=array("name"=>$this->userobj->name.$net->name."网络","path"=>$net->objPath(),"isuser"=>0);
				
			}
		}
		$this->assign('lang',C('DEFAULT_LANG'));
		$this->assign('sendto',$sendto);
		$this->display();
	}
	
	// 遍历选择
	public function dirsend(){
		$sendto=array();

		$sendto[]=array("name"=>"全部".$this->userobj->byname,"path"=>'',"isuser"=>1);
		//判断如果是豪华版可以发布团队公告
		if(C('VERSION_SWITCH') == '0'){
			//遍历所有下级节点
			foreach(X('net_rec,net_place') as $net)
			{
				$sendto[]=array("name"=>$this->userobj->name.$net->name."网络","path"=>$net->objPath(),"isuser"=>0);
				
			}
		}
		$this->assign('lang',C('DEFAULT_LANG'));
		$this->assign('sendto',$sendto);
		$this->display();
	}
	//发布公告
	public function send_notice(){
		if($_POST['title']==""){
			$this->error("标题不能为空");
		}			
		if($_POST['content']==""){
			$this->error("内容不能为空");
		}
		if($_POST['lang']==""){
			$this->error("语言不能为空");
		}

		$model=M('公告');
		$data=array();
		$data['标题']=$_POST["title"];
		$data['标题特效']="";
		$data['内容']=get_magic_quotes_gpc() ? stripslashes($_POST['content']) : $_POST['content'];
		$data['创建时间']=systemTime();
		$data['语言']=$_POST['lang'];
		$data['type']=$_POST['type'];
		$data['发布人']=$_SESSION['loginAdminName'];
		$data['查看权限']='0';
		//dump($_POST);
		if($_POST['path'] !=''){
			$obj = X('>',$_POST['path']);
			if(get_class($obj)=="net_rec"||get_class($obj)=="net_place")
			{
				if($_POST["receiver"]==""){
					$this->error("请填入".$this->userobj->byname."编号");
				}
				if(!$this->userobj->have($_POST["receiver"]))
				{
					$this->error($this->userobj->byname."不存在");
				}
				
				$user=$this->userobj->getuser($_POST["receiver"]);
				$data['netname']=$obj->name;
				$data['查看权限']=$user['id'];
			}
		}
		$result=$model->add($data);
		if($result){
			$this->success("发布成功",__URL__."/index");
		}else{
			$this->error("发布失败",__URL__."/index");
			}
	}
	
	//发布致辞
	public function dirsend_notice(){
		if($_POST['title']==""){
			$this->error("标题不能为空");
		}			
		if($_POST['content']==""){
			$this->error("内容不能为空");
		}
		if($_POST['lang']==""){
			$this->error("语言不能为空");
		}

		$model=M('致辞');
		$data=array();
		$data['标题']=$_POST["title"];
		$data['标题特效']="";
		$data['内容']=get_magic_quotes_gpc() ? stripslashes($_POST['content']) : $_POST['content'];
		$data['创建时间']=systemTime();
		$data['语言']=$_POST['lang'];
		$data['发布人']=$_SESSION['loginAdminName'];
		$data['应用']=$_POST['zhici'];
		$result=$model->add($data);
		if($result){
			$this->success("发布成功",__URL__."/dirindex");
		}else{
			$this->error("发布失败",__URL__."/dirindex");
			}
	}
	//删除公告
    public function del(){
		$model=M("公告");
		$succNum = 0;
		$errNum = 0; 
		$errMsg = '';
		M()->startTrans();
		foreach(explode(',',$_GET['id']) as $id){
			if($id == '') continue;
			$map['id']=$id;
			$result=$model->where($map)->delete();
			if($result){
				$succNum++;
			}else{
				$errNum++;
			}
		}
		M()->commit();
		if($errNum !=0){
			$this->error("删除成功：".$succNum .'条记录；删除失败：'.$errNum .'条记录；');
		}else{
			$this->success("删除成功：".$succNum .'条记录；');
		}
		
    }
    
    	//删除公告
    public function dirdel(){
		$model=M("致辞");
		$succNum = 0;
		$errNum = 0; 
		$errMsg = '';
		M()->startTrans();
		foreach(explode(',',$_GET['id']) as $id){
			if($id == '') continue;
			$map['id']=$id;
			$result=$model->where($map)->delete();
			if($result){
				$succNum++;
			}else{
				$errNum++;
			}
		}
		M()->commit();
		if($errNum !=0){
			$this->error("删除成功：".$succNum .'条记录；删除失败：'.$errNum .'条记录；');
		}else{
			$this->success("删除成功：".$succNum .'条记录；');
		}
		
    }
}
?>