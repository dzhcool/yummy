<?php
class Api_SysController extends LAction
{
    // 资源管理
    public function res_list()
    {
        $rst = Sys_ResModel::getResTree();
        return $this->success($rst);
    }

    public function res_save()
    {
        $data = LInput::request('id', 'name', 'prop', 'pid');
        $rst = Sys_ResModel::save($data);
        return $this->success($rst);
    }

    public function res_delete()
    {
        $id   = intval(LInput::getVar('id'));
        $info = Sys_ResModel::get($id);
        if ($info['pid'] == 0 && Sys_ResModel::count(array('pid'=>$id)) > 0) {
            throw new Err_Input('请先删除组下面的资源');
        }

        $rst = Sys_ResModel::delete($id);
        return $this->success($rst);
    }


    // 菜单管理
    public function menu_list()
    {
        $rst = Sys_MenuModel::getMenuTree();
        return $this->success($rst);
    }

    public function menu_save()
    {
        $data = LInput::request('id', 'title', 'res', 'url', 'icon', 'sortnum', 'pid');
        $rst = Sys_MenuModel::save($data);
        return $this->success($rst);
    }

    public function menu_delete()
    {
        $id   = intval(LInput::getVar('id'));
        $info = Sys_MenuModel::get($id);
        if ($info['pid'] == 0 && Sys_MenuModel::count(array('pid'=>$id)) > 0) {
            throw new Err_Input('请先删除二级菜单');
        }

        $rst = Sys_MenuModel::delete($id);
        return $this->success($rst);
    }


    // 角色管理
    public function role_list()
    {
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
        return $this->success($rst);
    }
    public function role_save()
    {
        $data = LInput::request('id', 'rolename', 'resgroup');
        $rst = Sys_RoleModel::save($data);
        return $this->success($rst);
    }
    public function role_delete()
    {
        $id   = intval(LInput::getVar('id'));
        $rst = Sys_RoleModel::delete($id);
        return $this->success($rst);
    }


    // 用户管理
    public function account_list()
    {
        $rst = Sys_AccountModel::getall();
        if ($rst) {
            foreach($rst as &$info) {
                $info['role'] = array();
                if ($info['rid']) {
                    $info['role'] = Sys_RoleModel::get($info['rid']);
                }
            }
        }
        return $this->success($rst);
    }
    public function account_save()
    {
        $data = LInput::request('id', 'username', 'password', 'nickname', 'rid', 'status');
        $data['oper'] = $this->uid;
        if (strlen($data['password']) < 6) {
            throw new Err_Input('密码不能少于6位');
        }
        $info = Sys_AccountModel::findOne(array('username'=>$data['username']));
        if ($info && strlen($data['password']) == 32) {
            $data['password'] = $info['password'];
        } else {
            $data['password'] = md5(Sys_AccountModel::KEY_PWD.$data['password']);
        }

        $rst = Sys_AccountModel::save($data);
        return $this->success($rst);
    }
    public function account_enable()
    {
        $id     = intval(LInput::getVar('id'));
        $status = intval(LInput::getVar('status'));
        $rst = Sys_AccountModel::modify($id, array('status'=>$status));
        return $this->success($rst);
    }

    public function account_delete()
    {
        $id   = intval(LInput::getVar('id'));
        $rst = Sys_AccountModel::delete($id);
        return $this->success($rst);
    }


    // 个人设置
    public function profile_save() {
        $data = LInput::request('nickname', 'oldpwd', 'newpwd', 'newpwd2', 'headpic');
        $info = Sys_AccountModel::get($this->uid);
        if ($data['newpwd']) {
            $oldpwd = md5(Sys_AccountModel::KEY_PWD.$data['oldpwd']);
            if ($oldpwd != $info['password']) {
                throw new Err_Input('旧密码输入错误');
            }
            if ($data['newpwd'] != $data['newpwd2']) {
                throw new Err_Input('新密码两次输入不一致');
            }
            $data['password'] = md5(Sys_AccountModel::KEY_PWD.$data['newpwd']);
        }
        unset($data['oldpwd']);
        unset($data['newpwd']);
        unset($data['newpwd2']);
        $rst = Sys_AccountModel::modify($this->uid, $data);
        if ($rst) {
            $_SESSION['USER']['nickname'] = $data['nickname'];
        }
        return $this->success($rst);
    }
}
