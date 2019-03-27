<?php
class Produt_GrModel extends LunaEntity
{
    //产品评价回复
    protected $_table = 'lh_game_reply';
    protected $_pkey = 'id';

    /**
     * 格式化产品评价列表
     */
    public static function gr_info($data)
    {
        foreach ($data['list'] as &$v)
        {
            $v['content'] = Produt_ReModel::set_content(strip_tags($v['content']),20);
            if(!empty(Produt_GcModel::get($v['comment_id'],array('topi_cid'))['topi_cid'])){
                $v['game_name'] = ProdModel::get(Produt_GcModel::get($v['comment_id'],array('topi_cid'))['topi_cid'],array('name'))['name'];
            }else{
                $v['game_name'] = '无';
            }
            $v['addtime'] = date('Y-m-d H:i:s',$v['create_date']);
            $v['user_name'] = UserModel::get($v['from_user_id'],array('username'))['username'];
            $v['thumbs_num'] = Produt_TupModel::count(array('uid'=>$v['id'],'type'=>'4','btype'=>'2','status'=>'1'));
        }
        return $data;
    }
    public static function set_status($id,$examine)
    {
        if(count($id) == '0'){
            return false;
        }elseif(count($id) == '1'){
            $sql = 'UPDATE lh_game_reply SET `is_examine` = \'1\',`status` = \''.$examine.'\' WHERE id = '.implode(',',$id);
        }else{
            $sql = 'UPDATE lh_game_reply SET `is_examine` = \'1\',`status` = \''.$examine.'\' WHERE id in ('.implode(',',$id).')';
        }
        $res =  self::query($sql);
        if($res !== false ){
            if($examine == '2'){
                //产品评价的点赞
                Produt_TupModel::set_status('4','2',$id);
            }

        }
    }
}