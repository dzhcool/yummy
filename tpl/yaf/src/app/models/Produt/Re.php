<?php
class Produt_ReModel extends LunaEntity
{
    //帖子
    protected $_table = 'lh_recommend';
    protected $_pkey = 'id';

    public static function ReCount($id)
    {
        //圈子的id
        $comm = Produt_CommModel::get($id,array('pid'));
        return self::count(array('pid'=>$comm['pid']));
    }
    //获取产品的名称
    public static function gameName($id)
    {
        $comm_id = Produt_CommModel::get($id,array('pid'));
        return  ProdModel::get($comm_id['pid'],array('name'));
    }
    //截取
    public static function set_content($content,$len)
    {
        if(mb_strlen($content)>$len){
           return mb_substr( $content, 0, $len, 'utf-8' ).'...';
        }else{
            return $content;
        }
    }
    //格式化帖子数据
    public static function recommed_info($data)
    {
        foreach ($data['list'] as &$v)
        {
            $v['title'] = self::set_content(strip_tags($v['title']),10);
            $v['content'] = self::set_content(strip_tags($v['content']),20);
            $v['game_name'] = Produt_ReModel::gameName($v['pid']);
            $v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
            $v['user_name'] = UserModel::get($v['uid'],array('username'))['username'];
            $v['reply_count'] = Produt_CommentsModel::count(array('topi_cid'=>$v['id'],'status'=>'1'));
            $v['thumbs_num'] = Produt_TupModel::count(array('uid'=>$v['id'],'type'=>'1','btype'=>'1','status'=>'1'));
            $v['follow_num'] = Produt_FollowModel::count(array('pid'=>$v['id'],'type'=>'1','btype'=>'1','status'=>'1'));
        }
        return $data;
    }
    //审核帖子
    public static function recommed_examine($id,$examine)
    {
        $res = self::modify($id, array('is_examine'=>'1'));
        if($res !== false){
            //审核不通过,改变相关状态值
            if($examine == '2'){
                $recommedBtype = self::get($id,array('btype'));
                //贴子的状态
                self::modify($id, array('status'=>'2'));
                if($recommedBtype['btype'] == '3'){
                    //动态
                    Produt_DyModel::set_status(array($id),'4');
                }else{
                    //帖子的点赞
                    Produt_TupModel::set_status('1','1',array($id));
                    //动态
                    Produt_DyModel::set_status(array($id),'2');
                    //收藏
                    Produt_FollowModel::set_status(array($id),'1');
                }
                //删除帖子下的评论回复
                if(empty($id)){
                    return false;
                }else{
                    $where['topi_cid'] = $id;
                }
                $ids = Produt_CommentsModel::getField($where,'id');
                Produt_CommentsModel::set_status($ids,$recommedBtype['btype']);
            }
        }
    }
}
