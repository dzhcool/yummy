<?php
class Api_CommonController extends LAction
{
    private $url_prefix = 'https://img.xxx.com/';
    private $editorConf = array(
        "imageActionName"  => "upload",
        "imageFieldName"   => "upfile", /* 表单名称 */
        "imageMaxSize"     => 2048000, /* 大小限制，单位B */
        "imageAllowFiles"  => array(".png", ".jpg", ".jpeg", ".gif", ".bmp"), /* 上传图片格式显示 */
        "imageInsertAlign" => "none", /* 插入的图片浮动方式 */
        "imageUrlPrefix"   => "", /* 图片访问路径前缀 */
    );

    public function imgupload()
    {
        $swf = intval(LInput::getVar('swf'));
        $rst = array('STATUS'=>'FAIL');
        if($swf && $_FILES['Filedata']) {
            $file    = @$_FILES['Filedata']['tmp_name'];
            $imgname = $_FILES['Filedata']['name'];
            $imgname = 'adm/' . date("YmdHis").rand(10000,99999).'.'.substr($imgname, -3);
            $ret = LUpload::upload($file, $imgname);

            if ($ret) {
                $url = $this->url_prefix . $ret;
                $rst = array('STATUS'=>'SUCC','origin_url'=>$url,'preview_url'=>$url,'view_url'=>$url);
            }
        }
        echo json_encode($rst);exit;
    }

    public function editor()
    {
        $action = LInput::getVar('action');
        switch ($action) {
            case 'config':
                $result = json_encode($this->editorConf);
                break;

            case 'upload':
                $fieldName = $this->editorConf['imageFieldName'];
                $file = $_FILES[$fieldName];

                $imgname = 'adm/' . date("YmdHis").rand(10000,99999).'.'.substr($file['name'], -3);
                $ret = LUpload::upload($file['tmp_name'], $imgname);
                $result = json_encode(array(
                    'state'    => 'SUCCESS',
                    'url'      => 'https://img.muwai.com/' . $ret,
                    'title'    => $file['name'],
                    'original' => $file['name'],
                    'type'     => substr($ret, -3),
                    'size'     => $file['size']
                ));
                break;

            default:
                $result = json_encode(array(
                    'state' => '请求地址错误'
                ));
                break;
        }

        echo $result;
        exit;
    }
}

