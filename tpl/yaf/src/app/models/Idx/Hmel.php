<?php
class Idx_HmelModel extends LunaEntity
{
    protected $_table = 'lh_menu_list';
    protected $_pkey  = 'id';
    public static function getProd($id){
        $v = ProdModel::get($id, array('id', 'name', 'type_nav_id', 'c_liver', 'c_krypton', 'status', 'qj_top', 'tj_card', 'tj_cards'));
        $v['type_name'] = Conf_NavModel::findOne(array('id' => $v['type_nav_id']), array('type_name'))['type_name'];
        $v['follow_num'] = Produt_FollowModel::count(array('type' => '3', 'pid' => $v['id']));
        $v['comment_num'] = Produt_GcModel::count(array('topi_cid' => $v['id'], 'status' => '1'));
        $v['recommend_num'] = Produt_ReModel::ReCount($v['id']);
        $tmp = Produt_AppoModel::findOne(array('pid'=>$v['id']));
        $v['appoint'] = $tmp['status'] == 1 ? 1 : 0;
        return $v;
    }
}