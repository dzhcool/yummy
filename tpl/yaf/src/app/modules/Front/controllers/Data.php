<?php
class DataController extends LAction {

    private $tagTypes = array(
        '1' => '类型管理',
        '2' => '作者管理',
        '3' => '小说系列管理',
    );

    private $lnovelStatus = array(
        '1' => '连载中',
        '2' => '已完结',
    );

    private $lnovelAreas = array('日本', '欧美', '港台', '内地');

    // 数据管理
    public function tag_lists() {
        $search = LInput::getVar('search');
        $pageinfo = $this->pageinfo();


        $map = array();
        if(!empty($search['tag_type'])){
            $map['tag_type'] = $search['tag_type'];
        }
        if(!empty($search['tag_name'])){
            $map['like'] = array('tag_name' => '%'.trim($search['tag_name']).'%');
        }

        $lists = Data_TagModel::gets($map, array(), null, $pageinfo['pagesize'], $pageinfo['offset']);
        $total = Data_TagModel::count($map);

        $page = PageSvc::ins()->page($total, $pageinfo['page'], $pageinfo['pagesize']);

        $this->getView()->assign('lists', $lists);
        $this->getView()->assign('page', $page);
        $this->getView()->assign('search', $search);
        $this->getView()->assign('tagTypes', $this->tagTypes);
    }
    // 资源编辑
    public function tag_edit() {
        $data = LInput::request('id', 'tag_name', 'tag_type', 'tag_content', 'tag_py');
        $data['create_time'] = time();

        if ($this->getRequest()->isPost()){
            $rst = Data_TagModel::save($data);
            if($rst){
                return $this->close('操作完成', '/data/tag_lists');
            }else{
                return $this->close('操作失败', '/data/tag_lists');
            }
        }
        $info = Data_TagModel::findOne(array('id'=>$data['id']));


        $this->getView()->assign('tagTypes', $this->tagTypes);
        $this->getView()->assign('info', $info);
    }
    // 资源删除
    public function tag_del(){
        $id   = intval(LInput::getVar('id'));
        $info = Data_TagModel::get($id);
        if (empty($info)) {
            return $this->error('操作失败，删除数据不存在');
        }

        $rst = Data_TagModel::delete($id);
        return $this->success($rst);
    }

    // 小说列表
    public function lnovel_lists() {
        $search = LInput::getVar('search');
        $pageinfo = $this->pageinfo();


        $map = array();
        if(!empty($search['name'])){
            $map['like'] = array('name' => '%'.trim($search['name']).'%');
        }

        $lists = Data_LnovelModel::gets($map, array(), 'last_update_time desc,id desc', $pageinfo['pagesize'], $pageinfo['offset']);
        $total = Data_LnovelModel::count($map);

        $page = PageSvc::ins()->page($total, $pageinfo['page'], $pageinfo['pagesize']);

        $this->getView()->assign('lists', $lists);
        $this->getView()->assign('page', $page);
        $this->getView()->assign('search', $search);
        $this->getView()->assign('lnovelStatus', $this->lnovelStatus);
    }

    // 小说编辑
    public function lnovel_edit() {
        $data = LInput::request('id', 'name', 'zone', 'status', 'meta_keywords', 'meta_title', 'meta_description', 'covers', /*'hot_search', 'hot_hits',*/ 'first_letter', 'introduction', '_authors', '_types', '_series');
        if(empty($data['id'])){
            $data['addtime'] = time();
        }
        $data['uptime'] = $data['last_update_time'] = time();
        $data['covers'] = trim($data['covers'], ',');

        if ($this->getRequest()->isPost()){
            // 数组转字符串
            if(!empty($data['_authors'])){
                $data['authors'] = implode('/', $data['_authors']);
            }
            if(!empty($data['_types'])){
                $data['types'] = implode('/', $data['_types']);
            }
            if(!empty($data['_series'])){
                $data['series'] = implode('/', $data['_series']);
            }
            unset($data['_authors']);
            unset($data['_types']);
            unset($data['_series']);

            $rst = Data_LnovelModel::save($data);
            if($rst){
                return $this->tips('操作完成', '/data/lnovel_lists');
            }else{
                return $this->tips('操作失败', '/data/lnovel_lists');
            }
        }
        $info = Data_LnovelModel::findOne(array('id'=>$data['id']));
        if(!empty($info['authors'])){
            $info['authors_array'] = explode('/', $info['authors']);
        }
        if(!empty($info['types'])){
            $info['types_array'] = explode('/', $info['types']);
        }
        if(!empty($info['series'])){
            $info['series_array'] = explode('/', $info['series']);
        }


        $this->getView()->assign('lnovelStatus', $this->lnovelStatus);
        $this->getView()->assign('lnovelAreas', $this->lnovelAreas);
        $this->getView()->assign('info', $info);
    }

    // 小说删除
    public function lnovel_del(){
        $id   = intval(LInput::getVar('id'));
        $info = Data_LnovelModel::get($id);
        if (empty($info)) {
            return $this->error('操作失败，删除数据不存在');
        }

        $rst = Data_LnovelModel::delete($id);
        return $this->success($rst);
    }

    // 卷管理
    public function volume_lists() {
        $lnovel_id = LInput::getInt('lnovel_id');
        $volume_id = LInput::getInt('volume_id');
        $pageinfo = $this->pageinfo();


        $map = array('lnovel_id' => $lnovel_id);
        if($volume_id > 0){
            $map['id'] = $volume_id;
        }


        $lists = Data_VolumeModel::gets($map, array(), 'sort asc,id desc', $pageinfo['pagesize'], $pageinfo['offset']);
        $total = Data_VolumeModel::count($map);

        $page = PageSvc::ins()->page($total, $pageinfo['page'], $pageinfo['pagesize']);

        $novelInfo = Data_LnovelModel::findOne(array('id'=>$lnovel_id));

        $this->getView()->assign('lists', $lists);
        $this->getView()->assign('page', $page);
        $this->getView()->assign('lnovel_id', $lnovel_id);
        $this->getView()->assign('novelInfo', $novelInfo);
    }

    // 卷编辑
    public function volume_edit() {
        $data = LInput::request('id', 'name', 'sort', 'lnovel_id');
        $data['uptime'] = time();
        if(empty($data['id'])){
            $data['addtime'] = time();
        }

        if ($this->getRequest()->isPost()){
            $rst = Data_VolumeModel::save($data);
            if($rst){
                return $this->tips('操作完成', '/data/volume_lists?lnovel_id='.$data['lnovel_id']);
            }else{
                return $this->tips('操作失败', '/data/volume_lists?lnovel_id='.$data['lnovel_id']);
            }
        }
        $info = Data_VolumeModel::findOne(array('id'=>$data['id']));
        $novelInfo = Data_LnovelModel::findOne(array('id'=>$data['lnovel_id']));


        $this->getView()->assign('info', $info);
        $this->getView()->assign('novelInfo', $novelInfo);
    }

    // 卷删除
    public function volume_del(){
        $id   = intval(LInput::getVar('id'));
        $info = Data_VolumeModel::get($id);
        if (empty($info)) {
            return $this->error('操作失败，删除数据不存在');
        }

        $rst = Data_VolumeModel::delete($id);
        return $this->success($rst);
    }

    // 章节管理
    public function chapter_lists() {
        $lnovel_id = LInput::getInt('lnovel_id');
        $volume_id = LInput::getInt('volume_id');
        $chapter_id = LInput::getInt('chapter_id');
        $pageinfo = $this->pageinfo();


        $map = array('lnovel_id' => $lnovel_id, 'volume_id' => $volume_id);
        if($chapter_id > 0){
            $map['id'] = $chapter_id;
        }


        $lists = Data_ChapterModel::gets($map, array(), 'sort asc,id desc', $pageinfo['pagesize'], $pageinfo['offset']);
        $total = Data_ChapterModel::count($map);

        $page = PageSvc::ins()->page($total, $pageinfo['page'], $pageinfo['pagesize']);

        $novelInfo = Data_LnovelModel::findOne(array('id'=>$lnovel_id));
        $volumeInfo = Data_VolumeModel::findOne(array('id'=>$volume_id, 'lnovel_id' => $lnovel_id));

        $this->getView()->assign('lists', $lists);
        $this->getView()->assign('page', $page);
        $this->getView()->assign('lnovel_id', $lnovel_id);
        $this->getView()->assign('volume_id', $volume_id);
        $this->getView()->assign('novelInfo', $novelInfo);
        $this->getView()->assign('volumeInfo', $volumeInfo);
    }

    // 章节编辑
    public function chapter_edit() {
        $data = LInput::request('id', 'title', 'sort', 'lnovel_id', 'volume_id');
        $content = LInput::getVar('content');
        $data['uptime'] = time();
        $data['status'] = 1;
        if(empty($data['id'])){
            $data['addtime'] = time();
        }

        $info = Data_ChapterModel::findOne(array('id'=>$data['id']));

        if ($this->getRequest()->isPost()){
            $filename = NovelSvc::ins()->save($info, $content);
            if(empty($filename)){
                return $this->tips('操作失败，保存小说内容失败', '/data/chapter_lists?lnovel_id='.$data['lnovel_id'].'&volume_id='.$data['volume_id']);
            }
            $data['local_path'] = $filename;

            $rst = Data_ChapterModel::save($data);
            if($rst){
                return $this->tips('操作完成', '/data/chapter_lists?lnovel_id='.$data['lnovel_id'].'&volume_id='.$data['volume_id']);
            }else{
                return $this->tips('操作失败', '/data/chapter_lists?lnovel_id='.$data['lnovel_id'].'&volume_id='.$data['volume_id']);
            }
        }
        $novelInfo = Data_LnovelModel::findOne(array('id'=>$data['lnovel_id']));
        $volumeInfo = Data_VolumeModel::findOne(array('id'=>$data['volume_id'], 'lnovel_id' => $data['lnovel_id']));

        // 修改时候读入小说内容
        $novelpath = $_SERVER['PRJ_ROOT'].'/'.$info['local_path'];
        if(file_exists($novelpath)){
            $info['content'] = file_get_contents($novelpath);
        }


        $this->getView()->assign('info', $info);
        $this->getView()->assign('novelInfo', $novelInfo);
        $this->getView()->assign('volumeInfo', $volumeInfo);
    }

    // 章节删除
    public function chapter_del(){
        $id   = intval(LInput::getVar('id'));
        $info = Data_ChapterModel::get($id);
        if (empty($info)) {
            return $this->error('操作失败，删除数据不存在');
        }

        $rst = Data_ChapterModel::delete($id);
        return $this->success($rst);
    }
}
