<?php
class Produt_TupModel extends LunaEntity
{
    //点赞
    protected $_table = 'lh_thumbs_up';
    protected $_pkey = 'id';
    //去除点赞
    public static function set_status($type,$btype,$pid)
    {
        if(count($pid) == '0'){
            return false;
        }elseif(count($pid) == '1'){
            $sql = 'UPDATE lh_thumbs_up SET `status` = \'2\' WHERE type = '.$type.' AND btype = '.$btype.' AND pid = '.implode(',',$pid);
        }else{
            $sql = 'UPDATE lh_thumbs_up SET `status` = \'2\' WHERE type = '.$type.' AND btype = '.$btype.' AND pid in ('.implode(',',$pid).')';
        }
        return self::query($sql);
    }
}