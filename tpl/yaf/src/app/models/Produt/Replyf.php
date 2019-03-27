<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2018/10/8
 * Time: 19:05
 */

class Produt_ReplyfModel extends LunaEntity
{
    //帖子评价回复
    protected $_table = 'lh_replyform';
    protected $_pkey = 'id';
    /**删除帖子评论回复
     * @param $id 帖子评论id
     * @return mixed
     */
    public static function set_status($id,$examine)
    {
        if(count($id) == '0'){
            return false;
        }elseif(count($id) == '1'){
            if($examine == '2'){
                $sql = 'UPDATE lh_replyform SET `status` = \'2\',`is_examine` = \'1\' WHERE id = '.implode(',',$id);
            }else{
                $sql = 'UPDATE lh_replyform SET `is_examine` = \'1\' WHERE id = '.implode(',',$id);
            }
        }else{
            if($examine == '2'){
                $sql = 'UPDATE lh_replyform SET `status` = \'2\',`is_examine` = \'1\' WHERE id in ('.implode(',',$id).')';
            }else{
                $sql = 'UPDATE lh_replyform SET `is_examine` = \'1\' WHERE id in ('.implode(',',$id).')';
            }
        }
        $res =  self::query($sql);
        if($res){
            if($examine == '2'){
                //帖子评价的点赞
                Produt_TupModel::set_status('4','1',$id);
            }

        }
    }
    public static function replyform_info($data)
    {
        foreach ($data['list'] as &$v)
        {
            $v['user_name'] = UserModel::get($v['from_user_id'],array('username'))['username'];
            $v['content'] = Produt_ReModel::set_content(strip_tags($v['content']),20);
            $v['addtime'] = date('Y-m-d H:i:s',$v['create_date']);
            $v['thumbs_num'] = Produt_TupModel::count(array('pid'=>$v['id'],'type'=>'4','btype'=>'1','status'=>'1'));
        }
        return $data;
    }
}