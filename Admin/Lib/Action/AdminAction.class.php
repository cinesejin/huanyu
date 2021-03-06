<?php
// 管理员模块
class AdminAction extends CommonAction
{
    /**
     * 管理员列表
     */
     public function index(){
         $setButton = array(
            '添加管理员' => array("class" => "add", "href" => "__APP__/Admin/add", "target" => "navTab"),
             '修改管理员' => array("class" => "edit", "href" => "__APP__/Admin/edit/id/{tl_id}", "target" => "navTab"),
             '删除管理员' => array("class" => "delete", "href" => "__APP__/Admin/delete/id/{tl_id}", "target" => "ajaxTodo", "title" => "确定要删除吗?"),
             '重置权限列表' => array("class" => "delete", "href" => "__APP__/Admin/updateNode", "target" => "ajaxTodo", "title" => "重置权限后需取消除超管外的权限?"),
             '后台登陆域名绑定' => array("class" => "edit", "href" => "__APP__/Admin/binddomain", "target" => "dialog", "title" => "确定要删除吗?"),
            );
         $list = new TableListAction("admin"); // 实例化Model 传表名称
         $list -> table("admin as a") -> join("left join (select c.admin_id,d.name from role_admin c inner join role d on c.role_id=d.id ) as b on b.admin_id=a.id");
         $list -> setButton = $setButton; // 定义按钮显示
         $list -> order("last_login_time desc"); //定义查询条件
         $list -> field("a.*,b.name");
         $list -> addshow("编号", array("row" => '[id]'));
         $list -> addshow("登录帐号", array("row" => "[account]", "searchMode" => "text", "excelMode" => "text", 'searchPosition' => 'top', "searchRow" => 'a.account'));
         $list -> addshow("管理员名称", array("row" => "[nickname]", "searchMode" => "text", "excelMode" => "text", 'searchPosition' => 'top', "searchRow" => 'a.nickname'));
         $list -> addshow("所属权限组", array("row" => "[name]", "searchMode" => "text", "excelMode" => "text", 'searchPosition' => 'top', "searchRow" => 'b.name'));
         $list -> addshow("登录次数", array("row" => "[login_count]", "searchMode" => "text", "excelMode" => "text", 'searchPosition' => 'top', "searchRow" => 'a.login_count'));
         $list -> addshow("最近登录时间", array("row" => "[last_login_time]", "searchMode" => "date", "format" => "time", "excelMode" => "date", 'searchPosition' => 'top', "searchRow" => 'a.last_login_time'));
         $list -> addshow("最近登录IP", array("row" => "[last_login_ip]", "searchMode" => "text", "excelMode" => "text", 'searchPosition' => 'top', "searchRow" => 'a.last_login_ip'));
         $list -> addshow("状态", array("row" => array(array(& $this, "_showstatus"), "[status]"), "searchMode" => "text", "searchSelect" => array("待审" => "0", "正常" => "1", "禁用" => "1"), 'searchRow' => 'a.status'));
         $this -> assign('list', $list -> getHtml());
         $this -> display();
         }
     function _showstatus($status){
         if($status == 0){
             return "待审";
             }else if($status == 1){
             return "正常";
             }else{
             return "禁用";
             }
         }
     // 添加管理员页面
    public function add(){
         // 修复所有权限节点的表
        M() -> startTrans();
         $Sync = D('Sync');
         $Sync -> syncAppNodeList();
         // 获取节点树
        $newtreeList = $this -> getNewTreeList(array());
         $roleList = M('Role') -> select();
         if(isset($roleList))
             foreach($roleList as $k => $val){
             $roleAccessList = M('RoleAccess') -> where(array('role_id' => $val['id'])) -> select();
             $roleAccessStr = '';
             if($roleAccessList)
                 foreach($roleAccessList as $roleAccess){
                 $roleAccessStr .= $roleAccess['node_id'] . '-';
                 }
             $roleList[$k]['access'] = trim($roleAccessStr, '-');
             }
         M() -> commit();
         $this -> assign('roleList', $roleList);
         $this -> assign('newtreeList', $newtreeList);
         $this -> assign('adminAccessArray', array());
         $this -> assign('roleAccessArray', array());
         $this -> display();
         }
     // 修改管理员页面
    public function edit(){
         if(strpos($_GET['id'], ',') !== false){
             $this -> error('参数错误!');
             }
         // 当前管理员
        $Admin = D('Admin');
         $admin_id = intval($_REQUEST['id']);
         $Sync = D('Sync');
         M() -> startTrans();
         // 修复所有权限节点的表
        $Sync -> syncAppNodeList();
         // 失效key值
        M('yubicloud', null) -> where(array('account_id' => $admin_id, "state" => 1, "endtime" => array('lt', strtotime(date('Y-m-d', systemTime()))))) -> save(array('state' => 2));
         M() -> commit();
         // 获取当前管理员 【管理员授权节点】
        $adminAccessArray = $Admin -> getAdminAccess($admin_id);
         // 获取当前管理员 【角色授权节点】
        $roleAccessArray = $Admin -> getRoleAccess($admin_id);
         // 获取节点树
        $newtreeList = $this -> getNewTreeList($adminAccessArray, $roleAccessArray);
         $roleList = M('Role') -> select();
         $roleAdminResult = M('RoleAdmin') -> field('role_id') -> where(array('admin_id' => $admin_id)) -> select();
         $roleAdmin = array();
         if(isset($roleAdminResult))
             foreach($roleAdminResult as $val){
             $roleAdmin[] = $val['role_id'];
             }
         if(isset($roleList))
             foreach($roleList as $k => $val){
             $roleAccessList = M('RoleAccess') -> where(array('role_id' => $val['id'])) -> select();
             $roleAccessStr = '';
             if($roleAccessList)
                 foreach($roleAccessList as $roleAccess){
                 $roleAccessStr .= $roleAccess['node_id'] . '-';
                 }
             $roleList[$k]['access'] = trim($roleAccessStr, '-');
             }
         $this -> assign('roleList', $roleList);
         $this -> assign('roleAdmin', $roleAdmin);
         $this -> assign('roleList', $roleList);
         $this -> assign('newtreeList', $newtreeList);
         $this -> assign('adminAccessArray', $adminAccessArray);
         $this -> assign('roleAccessArray', $roleAccessArray);
         $name = $this -> getActionName();
         $model = M('admin');
         $vo = $model -> getById ($admin_id);
         // 当前登录的会员
        $admins = M('admin') -> where(array('id' => $_SESSION[ C('RBAC_ADMIN_AUTH_KEY') ])) -> find();
         // yubikey列表
        $yubiprefixs = M('yubicloud', null) -> where(array('account_id' => $admin_id)) -> select();
         $this -> assign('yubiprefixs', $yubiprefixs);
         // 临时密码
        if(!F('passrand' . md5($_SERVER["SERVER_NAME"])))
            {
             F('passrand' . md5($_SERVER["SERVER_NAME"]), $this -> getguid());
             }
         $rndpass = (F('passrand' . md5($_SERVER["SERVER_NAME"])) . $vo['password'] . date('Ymd', time()));
         $rndpass = md5($rndpass);
         $this -> assign('rndpass', $rndpass);
         $this -> assign('admins', $admins);
         $this -> assign ('vo', $vo);
         $this -> display();
         }
     // 添加保存
    public function insert()
    {
         M() -> startTrans();
         $model = D("Admin");
         if (false === $model -> create ()){
             $this -> error($model -> getError ());
             }
         // 添加之前检查有无定义过滤器方法
        if (method_exists ($this, '_filter_insert_before')){
             $this -> _filter_insert_before ($model);
             }
         $result = $model -> add ();
         if ($result !== false) { // 保存成功
                // 设置权限
                $this -> addAccess($result, $_POST);
             // 保存操作日志
            $logData['帐号'] = $_POST['account'];
             $logData['昵称'] = $_POST['nickname'];
             //$logData['密码'] = $_POST['password'];
             $this -> saveAdminLog($logData, '', '添加管理员');
             // 添加之后检查有无定义过滤器方法
            if (method_exists ($this, '_filter_insert_after')){
                 $this -> _filter_insert_after ($model, $result);
                 }
             M() -> commit();
             $this -> success("添加完成");
             }else{
             // 失败提示
            $this -> error ('新增失败!');
             }
         }
     // 修改保存
    public function update()
    {
         M() -> startTrans();
         $model = D('Admin');
         // 查出修改之前的数据
        $oldData = $model -> find($_REQUEST['id']);
         if (false === $model -> create ()){
             $this -> error ($model -> getError ());
             }
         // 修改之前检查有无定义过滤器方法
        if (method_exists ($this, '_filter_update_before')){
             $this -> _filter_update_before ($model);
             }
         $admins = M('admin') -> where(array('id' => $_SESSION[ C('RBAC_ADMIN_AUTH_KEY') ])) -> find();
         if($admins['admin_status']){
             // 设置权限
            $this -> addAccess($_REQUEST['id'], $_POST);
             }
         // 获取之前的数据
        $Model = M('Admin');
         $result = $Model -> find($_REQUEST['id']);
         // 判断是否是超级管理员 admin
        if(!$result['admin_status']){
             if(C('RBAC_SUPER_ADMIN_ACCOUNT') == $result['account']){
                 // 修改会员的权限
                // 查看是否已经存在超级管理员
                $counts = $Model -> where(array('admin_status' => 1)) -> count();
                 if(!$counts){
                     $Model -> where(array('id' => $_REQUEST['id'])) -> save(array('admin_status' => 1));
                     }
                 }
             }
         // 更新数据
        $result = $model -> save ();
         // 修改之后检查有无定义过滤器方法
        if (method_exists ($this, '_filter_update_after')){
             $this -> _filter_update_after ($model, $_REQUEST['id']);
             }
         // 查出修改之后的数据
        $newData = $model -> find($_REQUEST['id']);
         $oldData['帐号'] = $result['account'];
         $oldData['昵称'] = $result['nickname'];
         $this -> saveAdminLog($newData, $oldData, '修改管理员');
         M() -> commit();
         $this -> success("修改完成");
         }
     // 删除管理员
    public function delete()
    {
         // 获取之前的数据
        $Model = M('Admin');
         $errMsg = "";
         $succNum = 0;
         $errNum = 0;
         foreach(explode(',', $_REQUEST['id']) as $id){
             $admin = $Model -> find($id);
             $oldData['帐号'] = $admin['account'];
             $oldData['昵称'] = $admin['nickname'];
             M() -> startTrans();
             $result = M("admin") -> where(array('id' => $id)) -> delete();
             if($result){
                 M('admin_access') -> where(array('admin_id' => $id)) -> delete();
                 $this -> saveAdminLog($oldData, '', '删除管理员');
                 M() -> commit();
                 $succNum++;
                 }else{
                 $errNum++;
                 $errMsg .= $admin['account'] . '：删除失败<br/>';
                 M() -> rollback();
                 }
             }
         if($errNum != 0){
             $this -> error("删除成功：" . $succNum . '条记录；删除失败：' . $errNum . '条记录；<br/>' . $errMsg);
             }else{
             $this -> success("删除成功：" . $succNum . '条记录；');
             }
         }
     // 重置权限列表
    public function updateNode(){
         $admins = M('admin') -> where(array('id' => $_SESSION[ C('RBAC_ADMIN_AUTH_KEY') ])) -> find();
         if(!$admins['admin_status']){
             $this -> error('无权限操作');
             }
         M() -> startTrans();
         M('node') -> where('level>1') -> delete();
         M() -> commit();
         M() -> execute("truncate table admin_access");
         M() -> execute("truncate table role_access");
         // 重新保存所有权限节点
        $Sync = D('Sync');
         $Sync -> syncAppNodeList();
         $this -> success('重置完成');
         }
     // 绑定后台登陆域名
    public function bind(){
         if($_POST['status'] == 1){
             if(isset($_POST['domain'])){
                 $_POST['domain'] = $_SERVER['HTTP_HOST'];
                 }
             M() -> startTrans();
             CONFIG('binddomain', $_POST['domain']);
             M() -> commit();
             $this -> success('绑定成功');
             }else{
             // 取消绑定
            M() -> startTrans();
             CONFIG('binddomain', "");
             M() -> commit();
             $this -> success('取消绑定成功');
             }
         }
     // 添加 yubicloud
    public function addyubicloudprefix(){
         $this -> assign('managerid', $_GET['aid']);
         $this -> display();
         }
     // 添加保存yubicloud
    public function addyubicloudprefix_save(){
         file_put_contents('record.txt', strtolower($_POST['newyubiopt']));
         if($_POST['newyubiopt']){
             require(ROOT_PATH . "/Public/yubicloud.class.php");
             $yubicloudobj = new Yubicloud();
             $res = $yubicloudobj -> checkOnYubiCloud(strtolower($_POST['newyubiopt']));
             if($res == 'OK')
            {
                 $yubicloudprefix = substr(strtolower($_POST['newyubiopt']), 0, 12);
                 $res2 = M('yubicloud') -> where(array('yubi_prefix' => $yubicloudprefix, 'account_id' => $_REQUEST['id'])) -> find();
                 if($res2){
                     $this -> success('已经绑定过');
                     }
                 $days = 0;
                 if(isset($_POST['yubidays']) && $_POST['yubidays'] > 0){
                     $days = (int)$_POST['yubidays'];
                     }
                 $admininfo = M("admin") -> where(array("id" => $_REQUEST['id'])) -> find();
                 $data = array(
                    'account_id' => $_REQUEST['id'],
                     'yubi_prefix' => $yubicloudprefix,
                     'yubi_prefix_name' => $_POST['yubiname'],
                     'addtime' => systemTime(),
                     'endtime' => ($days == 0)?0:(strtotime(date("Y-m-d", systemTime())) + 86400 * $days),
                     'state' => 1,
                    );
                 M() -> startTrans();
                 M('yubicloud', null) -> add($data);
                 M() -> commit();
                 $this -> saveAdminLog('', '', $yubicloudprefix . "绑定" . $admininfo['account'] . "成功");
                 $this -> success('绑定成功');
                 }
             }
         $this -> error('验证失败！');
         }
     // 添加保存yubicloud
    public function delyubicloudprefix(){
         // 获取删除的yubikey信息
        $yubiinfo = M('yubicloud', null) -> where(array("id" => $_REQUEST['kid'])) -> find();
         if(!isset($yubiinfo)){
             $this -> error("未获取到yubi信息");
             }
         // 当前管理员
        $admininfo = M("admin") -> where(array("id" => $_REQUEST['aid'])) -> find();
         if(!isset($admininfo)){
             $this -> error("未获取到管理员信息");
             }
         $this -> assign("yubiprefix", $yubiinfo);
         $this -> assign("admininfo", $admininfo);
         $this -> display();
         }
     // 取消 yubicloud
    public function cancelyubiprefix(){
         if(isset($_POST['ayubiopt'])){
             $admininfo = M("admin") -> where(array("id" => $_REQUEST['adid'])) -> find();
             if(!isset($admininfo)){
                 $this -> error("无效信息");
                 }
             // 验证key
            $yubicloudprefix = substr(strtolower($_POST['ayubiopt']), 0, 12);
             $yubicloud = M('yubicloud', null) -> where(array("yubi_prefix" => $yubicloudprefix, "account_id" => array(array("eq", $admininfo['id']), array("eq", $_SESSION[C('RBAC_ADMIN_AUTH_KEY')]), "or"))) -> find();
             if(!isset($yubicloud)){
                 $this -> error("验证失败");
                 }
             if($_REQUEST['id']){
                 M() -> startTrans();
                 M('yubicloud', null) -> delete($_REQUEST['id']);
                 M() -> commit();
                 $this -> saveAdminLog('', '', "删除" . $admininfo['account'] . "的yubikey值" . $yubicloudprefix);
                 $this -> success('已删除');
                 }else{
                 $this -> error("无效信息");
                 }
             }else{
             $this -> error("请输入yubikey值");
             }
         }
    /**
     * 奖权限保存到数据库中，然后再写入到静态文件中，以便于在管理员登陆时直接读取下次时直接读取
     */
     public function addAccess($admin_id, $post){
         // 权限设置
        $Admin = D('Admin');
         $AdminAccess = M('AdminAccess');
         // 获取选中的节点
        // $admin_id	= $_REQUEST['id'];
        $nodeList = array();
         $moduleArr = array();
         $Node = M('Node');
         if(isset($post['node'])){
             foreach($post['node'] as $_node)
            {
                 $_node_array = explode('_', $_node);
                 $node['admin_id'] = $admin_id;
                 $node['node_id'] = $_node_array[0];
                 $node['pid'] = $_node_array[1];
                 $node['level'] = $_node_array[2];
                 $nodeList[] = $node;
                 $moduleArr[$node['pid']] = 1;
                 }
             }
         $appArr = array();
         foreach($moduleArr as $module => $val){
             $moduleRe = $Node -> find($module);
             $node['admin_id'] = $admin_id;
             $node['node_id'] = $moduleRe['id'];
             $node['pid'] = $moduleRe['pid'];
             $node['level'] = $moduleRe['level'];
             $nodeList[] = $node;
             $appArr[$node['pid']] = 1;
             }
         foreach($appArr as $app => $val){
             $appRe = $Node -> find($app);
             $node['admin_id'] = $admin_id;
             $node['node_id'] = $appRe['id'];
             $node['pid'] = $appRe['pid'];
             $node['level'] = $appRe['level'];
             $nodeList[] = $node;
             }
         // 清空之前的授权
        $AdminAccess -> where("admin_id='{$admin_id}'") -> delete();
         // 插入新的授权
        foreach($nodeList as $node)
        {
             $AdminAccess -> add($node);
             }
         // 权限组设置
        $Role = M('Role');
         $RoleAdmin = M('RoleAdmin');
         $roleList = array();
         if(isset($post['role'])){
             foreach($post['role'] as $_role)
            {
                 $role['role_id'] = $_role;
                 $role['admin_id'] = $admin_id;
                 $roleList[] = $role;
                 }
             }
         // 清空之前的角色关联
        $RoleAdmin -> where("admin_id='{$admin_id}'") -> delete();
         // 插入新的角色关联
        foreach($roleList as $role)
        {
             $RoleAdmin -> add($role);
             }
         // 将管理员的权限写入静态文件
        import ('ORG.Util.RBAC');
         RBAC :: writeAccessList($admin_id);
         }
     // 获取权限节点树
    public function getNewTreeList($adminAccessArray = array(), $roleAccessArray = array()){
         // 同步所有应用的节点数据
        $Node = D('Node');
         $ftreeList = $Node -> getNodeTree();
         if($ftreeList)
             foreach($ftreeList as & $treeList){
             foreach($treeList as $fk => $firstList){
                 $firstAccess = 0; //0无权限  1部分权限  2全部权限
                 $firstAccessNum = 0;
                 $roleAccess = 0;
                 $count = 0;
                 foreach($firstList as $sk => $secondList){
                    
                     if(!isset($secondList['title'])){
                        
                         foreach($secondList as $tk => $thirdList){
                             if(in_array($thirdList['id'], $adminAccessArray)){
                                 $firstAccessNum++;
                                 }
                             if(in_array($thirdList['id'], $roleAccessArray)){
                                 $roleAccess = 1;
                                 }
                             $count++;
                             }
                         }else{
                         if(in_array($secondList['id'], $adminAccessArray)){
                             $firstAccessNum++;
                             }
                         if(in_array($secondList['id'], $roleAccessArray)){
                             $roleAccess = 1;
                             }
                         $count++;
                         }
                     }
                 if($firstAccessNum == 0){
                     $treeList[$fk]['adminAccess'] = 0;
                     }elseif($firstAccessNum < $count){
                     $treeList[$fk]['adminAccess'] = 1;
                     }else{
                     $treeList[$fk]['adminAccess'] = 2;
                     }
                 $treeList[$fk]['roleAccess'] = $roleAccess;
                 }
             }
         return $ftreeList;
         }
     // 生成随机临时密码
    function getguid(){
         if (function_exists('com_create_guid')){
             return com_create_guid();
             }else{
             mt_srand((double)microtime() * 10000); //optional for php 4.2.0 and up.
             $charid = strtoupper(md5(uniqid(rand(), true)));
             $hyphen = chr(45); // "-"
             $uuid = chr(123)// "{"
             . substr($charid, 0, 8) . $hyphen
             . substr($charid, 8, 4) . $hyphen
             . substr($charid, 12, 4) . $hyphen
             . substr($charid, 16, 4) . $hyphen
             . substr($charid, 20, 12)
             . chr(125); // "}"
             return $uuid;
             }
         }
    }
?>