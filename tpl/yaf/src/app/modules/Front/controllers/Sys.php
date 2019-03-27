<?php
class SysController extends LAction {

    // 资源管理
    public function res() {
        $rst = Sys_ResModel::getResTree();
        $this->getView()->assign('lists', $rst);
    }
    // 资源编辑
    public function res_edit() {
        $data = LInput::request('id', 'name', 'prop', 'pid');

        if ($this->getRequest()->isPost()){
            $rst = Sys_ResModel::save($data);
            if($rst){
                return $this->tips('操作完成', '/sys/res');
            }else{
                return $this->tips('操作失败', '/sys/res');
            }
        }
        $res_lists = Sys_ResModel::getall(array('pid'=>0));
        $info = Sys_ResModel::findOne(array('id'=>$data['id']));

        // 解决从一级菜单添加二级时候默认选中pid
        if(empty($info)){
            $info['pid'] = intval($data['pid']);
        }

        $this->getView()->assign('res_lists', $res_lists);
        $this->getView()->assign('info', $info);
    }
    // 资源删除
    public function res_del(){
        $id   = intval(LInput::getVar('id'));
        $info = Sys_ResModel::get($id);
        if ($info['pid'] == 0 && Sys_ResModel::count(array('pid'=>$id)) > 0) {
            return $this->error('操作失败，请先删除组下面的资源');
        }

        $rst = Sys_ResModel::delete($id);
        return $this->success($rst);
    }

    // 菜单管理
    public function menu() {
        $rst = Sys_MenuModel::getMenuTree();
        $this->getView()->assign('lists', $rst);
        // $page = PageSvc::ins()->page(1, 1000, 20);
    }
    // 菜单编辑
    public function menu_edit() {
        $data = LInput::request('id', 'title', 'res', 'url', 'icon', 'sortnum', 'pid');

        if ($this->getRequest()->isPost()){
            $rst = Sys_MenuModel::save($data);
            if($rst){
                return $this->tips('操作完成', '/sys/menu');
            }else{
                return $this->tips('操作失败', '/sys/menu');
            }
        }
        $menu_lists = Sys_MenuModel::getall(array('pid'=>0));
        $info = Sys_MenuModel::findOne(array('id'=>$data['id']));

        // 解决从一级菜单添加二级时候默认选中pid
        if(empty($info)){
            $info['pid'] = intval($data['pid']);
        }

        $this->getView()->assign('menu_lists', $menu_lists);
        $this->getView()->assign('info', $info);
    }
    // 菜单删除
    public function menu_del(){
        $id   = intval(LInput::getVar('id'));
        $info = Sys_MenuModel::get($id);
        if ($info['pid'] == 0 && Sys_MenuModel::count(array('pid'=>$id)) > 0) {
            return $this->error('操作失败,请先删除子菜单');
        }

        $rst = Sys_MenuModel::delete($id);
        return $this->success($rst);
    }

    // 用户管理
    public function account() {
        $rst = Sys_AccountModel::getall();
        if($rst){
            foreach($rst as &$info) {
                $info['role'] = array();
                if ($info['rid']) {
                    $info['role'] = Sys_RoleModel::get($info['rid']);
                }
            }
        }

        $this->getView()->assign('account_lists', $rst);
    }

    // 角色管理
    public function role() {
        $resList = Sys_ResModel::getall(array('pid'=>0), array('id', 'name'));
        $this->getView()->assign('resList', $resList);

        $rst = Sys_RoleModel::getall();
        if ($rst) {
            foreach($rst as &$info) {
                $nameList = array();
                if ($info['resgroup']) {
                    $resids = explode(',', $info['resgroup']);
                    foreach($resids as $id) {
                        $res = Sys_ResModel::get($id);
                        $nameList[] = $res['name'];
                    }
                }
                $info['namelist'] = implode(', ', $nameList);
            }
        }
        $this->getView()->assign('lists', $rst);
    }

    // 角色编辑
    public function role_edit() {
        $data = LInput::request('id', 'rolename', 'res');

        if ($this->getRequest()->isPost()){
            if(empty($data['res'])){
                return $this->tips('操作失败，请选择授权资源', '/sys/role');
            }
            $data['resgroup'] = implode(',', $data['res']);
            unset($data['res']);

            $rst = Sys_RoleModel::save($data);
            if($rst){
                return $this->tips('操作完成', '/sys/role');
            }else{
                return $this->tips('操作失败', '/sys/role');
            }
        }
        $res_lists = Sys_ResModel::getall(array('pid'=>0));
        $info = Sys_RoleModel::findOne(array('id'=>$data['id']));
        $info['res'] = array();
        if(!empty($info['resgroup'])){
            $info['res'] = explode(',', $info['resgroup']);
        }

        foreach($res_lists as $k=>$v){
            if(in_array($v['id'], $info['res'])){
                $res_lists[$k]['_isCheck'] = 1;
            }
        }

        // 解决从一级菜单添加二级时候默认选中pid
        if(empty($info)){
            $info['pid'] = intval($data['pid']);
        }

        $this->getView()->assign('res_lists', $res_lists);
        $this->getView()->assign('info', $info);
    }
    // 角色删除
    public function role_del(){
        $id   = intval(LInput::getVar('id'));
        $info = Sys_RoleModel::get($id);
        if (empty($info)) {
            return $this->error('操作失败，删除数据不存在');
        }

        $rst = Sys_RoleModel::delete($id);
        return $this->success($rst);
    }

    // 个人设置
    public function profile() {
        $profile = Sys_AccountModel::get($this->uid);
        $this->getView()->assign('profile', $profile);
    }
}
