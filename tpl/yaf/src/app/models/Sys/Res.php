<?php
class Sys_ResModel extends LunaEntity
{
    protected $_table = 'nv_res';

    // 资源列表
    static public function getRes($groupList) {
        $res = array();
        if ($groupList) {
            $sql = "select prop from nv_res where pid in ($groupList)";
            $resList = self::query($sql);
            if ($resList) {
                foreach($resList as $row) {
                    $res[] = $row['prop'];
                }
            }
        }
        return $res;
    }

    // 资源树
    static public function getResTree() {
        $groups = self::getall(array('pid'=>0), array('id', 'name', 'prop', 'pid'), 'id asc');
        if ($groups) {
            foreach($groups as &$group) {
                $group['childs'] = self::getall(array('pid'=>$group['id']), array('id', 'name', 'prop', 'pid'), 'id asc');

            }
        }
        return $groups ? $groups : array();
    }
}

