<?php
class Sys_MenuModel extends LunaEntity
{
    protected $_table = 'nv_menu';

    static public function getMenu($resArray) {
        if (empty($resArray)) {
            return array();
        }

        $rst = self::getall();
        if (!is_array($rst)) {
            return array();
        }

        $pids = $temp = array();
        foreach ($rst as $v) {
            if(!isset($temp[$v['pid']])) {
                $temp[$v['pid']] = array();
            }
            $temp[$v['pid']][] = $v;
        }

        $resTree = self::recTree($temp, 0, $resArray);
        return $resTree;
    }

    private static function recTree($rs, $pid, $resArray = NULL) {
        $rst = array();
        if(isset($rs[$pid]))
        {
            foreach($rs[$pid] as $v)
            {
                if(is_array($v))
                {
                    $child = self::recTree($rs, $v['id'],$resArray);
                    usort($child, function($a, $b){
                        if($a['sortnum'] > $b['sortnum']) {
                            return 1;
                        } elseif($a['sortnum'] < $b['sortnum']) {
                            return -1;
                        } else {
                            return 0;
                        }
                    });

                    if(isset($rs[$v['id']]))
                    {
                        if(empty($child))
                        {
                            continue;
                        } else {
                            $v['res'] = '';
                            $v['children'] = $child;
                        }
                    } else {
                        if($resArray !== NULL && is_array($resArray) && !in_array($v['res'], $resArray)) {
                            continue;
                        }
                    }

                    $rst[] = $v;
                }
            }
        }
        return $rst;
    }
    static public function getMenuTree() {
        $groups = self::getall(array('pid'=>0), array('id', 'title', 'res', 'url', 'icon', 'sortnum', 'pid'), 'sortnum asc');
        if ($groups) {
            foreach($groups as &$group) {
                $group['childs'] = self::getall(array('pid'=>$group['id']), array('id', 'title', 'res', 'url', 'icon', 'sortnum', 'pid'), 'sortnum asc');

            }
        }
        return $groups ? $groups : array();
    }
}

