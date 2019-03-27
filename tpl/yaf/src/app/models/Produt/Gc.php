<?php
class Produt_GcModel extends LunaEntity
{
    //产品评价
    protected $_table = 'lh_game_comment';
    protected $_pkey = 'id';

    /**
     * 格式化产品评价列表
     */
    public static function gc_info($data)
    {
        foreach ($data['list'] as &$v)
        {
            $v['content'] = Produt_ReModel::set_content(strip_tags($v['content']),20);
            $v['game_name'] = ProdModel::get($v['topi_cid'],array('name'))['name'];
            $v['addtime'] = date('Y-m-d H:i:s',$v['add_time']);
            $v['user_name'] = UserModel::get($v['from_uid'],array('username'))['username'];
            //$v['reply_count'] = Produt_CommentsModel::count(array('topi_cid'=>$v['id'],'status'=>'1'));
            $v['reply_count'] = 0;
            $v['thumbs_num'] = Produt_TupModel::count(array('uid'=>$v['id'],'type'=>'3','btype'=>'3','status'=>'1'));
            //$v['follow_num'] = Produt_FollowModel::count(array('pid'=>$v['id'],'type'=>'1','btype'=>'1','status'=>'1'));
        }
        return $data;
    }
    public static function set_status($id,$examine)
    {
        if(count($id) == '0'){
            return false;
        }elseif(count($id) == '1'){
            $sql = 'UPDATE lh_game_comment SET `is_examine` = \'1\',`status` = \''.$examine.'\' WHERE id = '.implode(',',$id);
        }else{
            $sql = 'UPDATE lh_game_comment SET `is_examine` = \'1\',`status` = \''.$examine.'\' WHERE id in ('.implode(',',$id).')';
        }
        $res =  self::query($sql);
        if($res !== false){
            if($examine == '2'){
                //产品评价的点赞
                Produt_TupModel::set_status('4','2',$id);
                //动态
                Produt_DyModel::set_status($id,'3');
                //评价回复
                if(empty($id)){
                    return false;
                }else{
                    $where['comment_id'] = $id;
                }
                $ids = Produt_GrModel::getField($where,'id');
                Produt_GrModel::set_status($ids,$examine);
            }

        }
    }
}