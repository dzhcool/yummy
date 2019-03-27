<?php
class IndexController extends LAction {

    private $url_prefix = 'https://api.muwai.com/';
    private $medal_package = 'apppackage'; //七牛勋章指上传定库
    // 默认页
    public function main(){
        $account = Sys_AccountModel::findOne(array('id'=>$_SESSION['USER']['uid']));
        $role = Sys_RoleModel::get($account['rid']);
        $res  = Sys_ResModel::getRes($role['resgroup']);
        $menu = Sys_MenuModel::getMenu($res);

        $this->getView()->assign('authMenus', $menu);
    }

    public function index() {
    }

    // 登录页
    public function login() {
        if ($this->getRequest()->isPost())
        {
            $this->loginHandler();
        } else {
            if ($_SESSION['USER']) {
                return $this->redirect('/sys/menu');
            }
            $this->autoVender = false;
        }
    }

    // 登录逻辑处理
    private function loginHandler() {
        $this->params = LInput::request('capcha', 'username', 'password', 't');
        // if ($_SESSION['capcha'] != $this->params['capcha'])
        // {
        //     throw new Err_Input('请输入正确的验证码');
        // }
        $res = CaptchaSvc::ins()->verify($this->params['capcha'], UFun::get_client_ip(1), $_COOKIE['tc_code']);
        if(empty($res) || $res['errno'] > 0){
            throw new Err_Input('请输入正确的验证码');
        }

        if ($_SESSION['token'] != $this->params['t'])
        {
            throw new Err_Input('登录失败,请刷新页面重试');
        }

        $username = trim($this->params['username']);
        $password = trim($this->params['password']);
        if (empty($username) || empty($password))
        {
            throw new Err_Input('输入错误');
        }

        $account = Sys_AccountModel::findOne(array('username'=>$username));
        if (empty($account) || $account['status']!=1 || $account['password'] != md5(Sys_AccountModel::KEY_PWD.$password)) {
            throw new Err_Input('登录失败');
        }

        $role = Sys_RoleModel::get($account['rid']);
        $res  = Sys_ResModel::getRes($role['resgroup']);
        $menu = Sys_MenuModel::getMenu($res);
        $_SESSION['USER'] = array(
            'uid'      => $account['id'],
            'role'     => $role['rolename'],
            'username' => $account['username'],
            'nickname' => empty($account['nickname']) ? $account['username'] : $account['nickname'],
            'headpic'  => $account['headpic'],
            'res'      => $res,
            'menu'     => $menu,
        );

        $this->redirect('/index/main');
    }

    // 退出
    public function logout() {
        unset($_SESSION['USER']);
        session_destroy();
        return $this->redirect("/index/login");
    }

    // 验证码
    public function capcha_old() {
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-type: image/png');
        $font = $_SERVER['PRJ_ROOT'] . '/src/application/modules/Front/statics/theme/default/fonts/PT.ttf';
        $capcha = new Capcha(320,100,4,60,$font);
        $capchas = $capcha->generate();
    }

    // 新验证码
    public function captcha(){
        CaptchaSvc::ins()->create();
        exit();
    }

}
