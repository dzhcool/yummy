<?php
class ProdModel extends LunaEntity
{
    protected $_table = 'lh_product_game';
    protected $_pkey  = 'id';

    /**根据分类获取二级标签和内外值标签
     * @param array $prdInfo
     * @return array
     */
    public static function getTag($prdInfo=array())
    {
        $navList = Conf_NavModel::getall(array('status'=>1), array('id', 'type_name'), 'srot_id asc, id asc' );
        $tagHtmlList = array();
        $ioTag = array();
        foreach($navList as &$nav) {
            //二级标签
            $tagList = Conf_TagModel::getall(array('status'=>1, 'is_show'=> 1, 'type' => $nav['id']), array('id', 'name'));
            $tagHtml = "";
            if ($tagList) {
                foreach($tagList as $tag) {
                    if(isset($prdInfo['product_tag'])&&in_array($tag['id'],explode(',',$prdInfo['product_tag']))){
                        $tagHtml .= "<label class='check checked' data-tag='".$tag['id']."'>".$tag['name']."<i class='check-ico'></i></label>\n";
                    }else{
                        $tagHtml .= "<label class='check' data-tag='".$tag['id']."'>".$tag['name']."</label>\n";
                    }
                }
            }
            $tagHtmlList[$nav['id']]['html'] = $tagHtml;
            //内外标签
            $ioTagIList = Conf_IoModel::getall(array('status'=>1, 'is_type'=>1, 'is_show'=>1,'type' => $nav['id']), array('id', 'name'));
            $ioTagIHtml = "";
            if ($ioTagIList) {
                foreach($ioTagIList as $iotagi) {
                    if(isset($prdInfo['l_tag'])&&in_array($iotagi['id'],explode(',',$prdInfo['l_tag']))){
                        $ioTagIHtml .= "<label class='check checked' data-tag='".$iotagi['id']."'>".$iotagi['name']."<i class='check-ico'></i></label>\n";
                    }else{
                        $ioTagIHtml .= "<label class='check' data-tag='".$iotagi['id']."'>".$iotagi['name']."</label>\n";
                    }
                }
            }
            $ioTagIOList = Conf_IoModel::getall(array('status'=>1, 'is_type'=>2, 'is_show'=>1,'type' => $nav['id']), array('id', 'name'));
            $ioTagOHtml = "";
            if ($ioTagIOList) {
                foreach($ioTagIOList as $iotago) {
                    if(isset($prdInfo['k_tag'])&&in_array($iotago['id'],explode(',',$prdInfo['k_tag']))){
                        $ioTagOHtml .= "<label class='check checked' data-tag='".$iotago['id']."'>".$iotago['name']."<i class='check-ico'></i></label>\n";
                    }else{
                        $ioTagOHtml .= "<label class='check' data-tag='".$iotago['id']."'>".$iotago['name']."</label>\n";
                    }
                }
            }
            $ioTagHtml[$nav['id']]['html']['i'] = $ioTagIHtml;
            $ioTagHtml[$nav['id']]['html']['o'] = $ioTagOHtml;
        }
        return array('ioTagHtml'=>$ioTagHtml,'tagHtmlList'=>$tagHtmlList,'navList'=>$navList);
    }
}