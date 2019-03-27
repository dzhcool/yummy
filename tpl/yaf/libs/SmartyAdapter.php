<?php
/**
 * Smarty适配器
 */
class SmartyAdapter implements Yaf_View_Interface {

    public $_smarty;
    public $_ext = 'tpl';

    public function __construct($extraParams = array()) {
        $this->_smarty = new Smarty();

        if (!empty($extraParams['template_dir'])) {
            $this->setScriptPath($extraParams['template_dir']);
        }

        foreach ($extraParams as $key => $value) {
            $this->_smarty->$key = $value;
        }

        $this->_smarty->left_delimiter = $extraParams['left_delimiter'];
        $this->_smarty->right_delimiter = $extraParams['right_delimiter'];
        $this->_smarty->compile_check = true;

        UFun::mkdirs($extraParams['compile_dir']);
        UFun::mkdirs($extraParams['cache_dir']);
    }

    public function getEngine() {
        return $this->_smarty;
    }

    public function setScriptPath($path){
        if (is_readable($path)) {
            $this->_smarty->template_dir = $path;
            return;
        }
        throw new Err_Input('Invalid path provided');
    }

    public function getScriptPath(){
        return $this->_smarty->template_dir;
    }

    public function setBasePath($path, $prefix = 'Zend_View'){
        return $this->setScriptPath($path);
    }

    public function addBasePath($path, $prefix = 'Zend_View'){
        return $this->setScriptPath($path);
    }

    public function __set($key, $val){
        $this->_smarty->assign($key, $val);
    }

    public function __isset($key){
        return (null !== $this->_smarty->get_template_vars($key));
    }

    public function __unset($key){
        $this->_smarty->clear_assign($key);
    }

    public function assign($spec, $value = null) {
        if (is_array($spec)) {
            $this->_smarty->assign($spec);
            return;
        }
        $this->_smarty->assign($spec, $value);
    }

    public function clearVars() {
        $this->_smarty->clear_all_assign();
    }

    public function render($name, $value = NULL) {
        if(!preg_match('/\.([\w]+)/', $name)){
            $name .= '.'.$this->_ext;
        }
        return $this->_smarty->fetch($name);
    }

    public function display($name, $value = NULL) {
        if(!preg_match('/\.([\w]+)/', $name)){
            $name .= '.'.$this->_ext;
        }
        echo $this->_smarty->fetch($name);
    }
}
