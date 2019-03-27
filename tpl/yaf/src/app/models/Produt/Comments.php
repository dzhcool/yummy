<?php
class Produt_CommentsModel extends LunaEntity
{
    //帖子评论
    protected $_table = 'lh_comments';
    protected $_pkey  = 'id';

    /**删除帖子评论
     * @param $id 帖子id
     * @param $type 1帖子 2 分析
     * @return mixed
     */
    public static function set_status($id,$type,$is_examine='2')
    {
        if(count($id) == '0'){
            return false;
        }elseif(count($id) == '1'){
            if($is_examine=='2'){
                $sql = 'UPDATE lh_comments SET `status` = \'0\',`is_examine` = \'1\' WHERE id = '.implode(',',$id);
            }else{
                $sql = 'UPDATE lh_comments SET `is_examine` = \'1\' WHERE id = '.implode(',',$id);
            }
        }else{
            if($is_examine=='2'){
                $sql = 'UPDATE lh_comments SET `status` = \'0\',`is_examine` = \'1\' WHERE id in ('.implode(',',$id).')';
            }else{
                $sql = 'UPDATE lh_comments SET `is_examine` = \'1\' WHERE id in ('.implode(',',$id).')';
            }
        }
        $res =  self::query($sql);
        if($res){
            if($is_examine == '2'){
                if($type == '2'){
                    //帖子评价的点赞
                    Produt_TupModel::set_status('5','1',$id);
                    //动态
                    Produt_DyModel::set_status($id,'5');
                    //收藏
                    Produt_FollowModel::set_status($id,'1');
                }else{
                    //帖子评价的点赞
                    Produt_TupModel::set_status('3','1',$id);
                }
                //评价回复
                $ids = Produt_ReplyfModel::getField(array('in'=>array('comment_id'=>$id)));
                Produt_ReplyfModel::set_status($ids);
            }
        }
    }
    //格式化帖子评价数据
    public static function comments_info($data)
    {
        foreach ($data['list'] as &$v)
        {
            //$v['title'] = Produt_ReModel::set_content(strip_tags($v['title']),10);
            $v['content'] = Produt_ReModel::set_content(strip_tags($v['content']),20);
            $v['title'] = Produt_ReModel::set_content(Produt_ReModel::getField(array('id'=>$v['topi_cid']),'title')[0],20);
            $v['addtime'] = date('Y-m-d H:i:s',$v['add_time']);
            $v['user_name'] = UserModel::get($v['from_uid'],array('username'))['username'];
            $v['reply_count'] = Produt_ReplyfModel::count(array('comment_id'=>$v['id'],'status'=>'1'));
            if($v['topic_type'] == '3'){
                $v['thumbs_num'] = Produt_TupModel::count(array('uid'=>$v['id'],'type'=>'5','btype'=>'1','status'=>'1'));
            }else{
                $v['thumbs_num'] = Produt_TupModel::count(array('uid'=>$v['id'],'type'=>'3','btype'=>'1','status'=>'1'));
            }
        }
        return $data;
    }
}