<?php
class Produt_DyModel extends LunaEntity
{
    //帖子评论
    protected $_table = 'lh_dynamic';
    protected $_pkey  = 'id';
    /** 删除动态
     * @param $pid
     * @param $type 1 游戏；2 帖子 ，3评价，4分析数据, 5分析评论
     * @return mixed
     */
    public static function set_status($pid,$type)
    {
        if(count($pid) == '0'){
            return false;
        }elseif(count($pid) == '1'){
            $sql = 'UPDATE lh_dynamic SET `status` = \'2\' WHERE type = '.$type.' AND pid = '.implode(',',$pid);
        }else{
            $sql = 'UPDATE lh_dynamic SET `status` = \'2\' WHERE type = '.$type.' AND pid in ('.implode(',',$pid).')';
        }
        return self::query($sql);
    }
}