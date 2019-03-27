<?php
class Produt_AppoModel extends LunaEntity
{
    //精选表
    protected $_table = 'lh_dis_appoint';
    protected $_pkey  = 'id';
    public static function getIds()
    {
        $ids = self::getall(['status'=>'1'],['pid']);
        return array_column($ids, 'pid');
    }
}
