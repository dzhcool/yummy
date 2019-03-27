<?php
class UserController extends LAction {

    // 用户列表
	public function list() {
	}

    // 用户详情
	public function detail() {
        $this->autoVender = false;
        $id = intval(LInput::getVar('id'));
        $info = UserModel::get($id);
        $this->getView()->assign('info', $info);
	}

    // 用户反馈列表
	public function feedback() {
	}

	//勋章管理页面
    public function medal() {

	    $this->getView();
    }

}
