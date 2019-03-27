<?php
class LAction extends Yaf_Controller_Abstract {

    protected $autoVender = true;
    protected $uid        = 0;
    protected $pagesize   = 15;

    public function init()
    {
        $this->uid    = $_SESSION['USER']['uid'];
        $this->class  = $this->getRequest()->getControllerName();
        $this->method = $this->getRequest()->action;
        $this->uri    = strtolower($this->class.'/'.$this->method);
        if (is_callable(array($this,$this->method)))
        {
            $this->_before();
            $rst = call_user_func(array($this, $this->method));
            $this->getRequest()->action = 'skipformethodcheck';
        } else {
            throw new Err_404($this->method.' not found!');
        }
    }

    // 可通过_before数组设置class的条件检测
    // 格式为array('method'=>array('fun1', 'fun2'))
    private function _before()
    {
        $method = $this->method;
        if ($method && isset($this->_before) && !empty($this->_before[$method])){
            foreach($this->_before[$method] as $rule)
            {
                if (method_exists($this, $rule))
                {
                    $this->$rule();
                } else {
                    throw new Err_Svc('_before error, There is no method ' . $rule);
                }
            }
        }
    }


    // 简化controll方法名和统一模板渲染入口
    public function skipformethodcheckAction() {
        $this->getRequest()->action = $this->method;
        if (!LunaRouter::isApi())
        {
            $this->vender();
        }
    }

    // 设置变量和加载模板
	private function vender() {
        // 配置默认内容模板
        if (!$this->content_tpl) {
            $this->content_tpl = $this->uri;
        }

        $view = $this->getView();
        $view->assign('token', $_SESSION['token']);
        $view->assign('content_tpl', $this->content_tpl . '.tpl');
        $view->assign('uri', $this->uri);
        $view->assign('version', file_get_contents($_SERVER['PRJ_ROOT'].'/version.txt'));
        if ($this->isLogin()) {
            $this->assignVars($view);
        }

        // 输出主模板
        // if ($this->autoVender) {
        //     $this->getView()->display('main.tpl');
        // } else {
        //     $this->getView()->display($this->tpl ? $this->tpl :  $this->content_tpl . '.tpl');
        // }
        // var_dump($this->tpl);
        // var_dump($this->content_tpl);
        // exit();
        $this->getView()->display($this->tpl ? $this->tpl :  $this->content_tpl . '.tpl');
    }

    private function assignVars($view) {
        $view->assign('userinfo', $_SESSION['USER']);
        // $view->assign('menu_header', $this->getHeader());
        // $view->assign('menu_left', $this->getLeft());
    }

    protected function isLogin() {
        return !empty($_SESSION['USER']) ? true : false;
    }

    // 菜单
    private function getHeader() {
        $menus = $_SESSION['USER']['menu'];
        if ($menus) {
            $rst = array();
            foreach($menus as $menu) {
                $rst[] = array(
                    'id'    => $menu['id'],
                    'title' => $menu['title'],
                    'url'   => $menu['url'],
                    'icon'  => $menu['icon'],
                    'class' => $menu['class'],
                    'sort'  => $menu['sortnum'],
                );
            }
        }
        usort($rst, function($a, $b){
            return $a['sort'] - $b['sort'];
        });
        return $rst;
    }

    private function getLeft() {
        $menus = $_SESSION['USER']['menu'];
        $tmp = array();
        foreach($menus as $menu) {
            if($menu['url'] == '/'.$this->uri) {
                $tmp = $menu['children'];
                break;
            }

            foreach($menu['children'] as $info) {
                if($info['url'] == '/'.$this->uri) {
                    $tmp = $menu['children'];
                    break 2;
                }
            }
        }
        $rst = array();
        if ($tmp) {
            foreach($tmp as $menu) {
                $rst[] = array(
                    'id'    => $menu['id'],
                    'pid'   => $menu['pid'],
                    'title' => $menu['title'],
                    'url'   => $menu['url'],
                    'icon'  => $menu['icon'],
                    'class' => $menu['class'],
                    'sort'  => $menu['sortnum'],
                );
                if(strstr($menu["url"], "/".$this->uri)) {
                    $view = $this->getView();
                    $view->assign('current_pid', $menu['pid']);
                    $view->assign('current_id', $menu['id']);
                }
            }
        }
        return $rst;
    }

    // api响应
    protected function success($data)
    {
        return RestResult::ins()->success($data)->show();
    }

    protected function error($errmsg, $errno = 1)
    {
        return RestResult::ins()->error($errmsg, $errno)->show();
    }

    public function offset($page = 0, $pagesize = 0){
        $pagesize = ($pagesize > 0) ? $pagesize : $this->pagesize;
        $page = empty($page) ? 1 : max(1, $page);
        $offset = ($page - 1) * $pagesize;
        return $offset;
    }

    public function tips($msg ='' , $url =''){
        $this->getView()->assign('msg', $msg);
        $this->getView()->assign('url', $url);
        $this->tpl = 'common/success.tpl';
    }
    public function close($msg =''){
        $this->getView()->assign('msg', $msg);
        $this->tpl = 'common/close.tpl';
    }

    public function pageinfo(){
        $data = array();
        $data['page'] = empty(LInput::getInt('page')) ? 1 : LInput::getDefInt('page', 1);
        $data['pagesize'] = empty(LInput::getInt('pagesize')) ? $this->pagesize : LInput::getDefInt('pagesize', $this->pagesize);
        $data['offset'] = $this->offset($data['page'], $data['pagesize']);
        return $data;
    }
}
