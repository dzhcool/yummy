<?php
class Produt_FollowModel extends LunaEntity
{
    //关注表
    protected $_table = 'lh_follow';
    protected $_pkey  = 'id';

    /**
     * @param $pid
     * @param $type 1帖子，2 圈子，3用户, 4产品
     * @return mixed
     */
    public static function set_status($pid,$type)
    {
        if(count($pid) == '0'){
            return false;
        }elseif(count($pid) == '1'){
            $sql = 'UPDATE lh_follow SET `status` = \'2\' WHERE type = '.$type.' AND pid = '.implode(',',$pid);
        }else{
            $sql = 'UPDATE lh_follow SET `status` = \'2\' WHERE type = '.$type.' AND pid in ('.implode(',',$pid).')';
        }
        return self::query($sql);
    }
 }