<?php
/**
 * 分页服务
 * @author dangzihao
 * @date 2015-06-26
 */
class PageSvc
{
    private static $ins = null;
    public static function ins(){
        if(self::$ins == null){
            self::$ins = new self;
        }
        return self::$ins;
    }

    public function page($total = 10, $page = 0, $pageSize = 0, $theme = ''){
        $page = max(1, $page);
        $pageSize = empty($pageSize) ? 15 : $pageSize;

        $totalPage = ceil($total/$pageSize);
        if($page > $totalPage) $page = $totalPage;
        $uper = max(1, $page-1);
        $downer = min($totalPage, $page+1);

        return $this->html($page, $totalPage, $total, $uper, $downer, $theme);
    }

    private function html($page, $totalPage, $total, $uper, $downer, $theme = ''){
        if(empty($theme)){
            $code = "&nbsp;&nbsp;共&nbsp;{$total}&nbsp;条信息&nbsp;共&nbsp;{$totalPage}&nbsp;页&nbsp;&nbsp;当前第&nbsp;{$page}&nbsp;页";
            $code .= "<ul class='pagination pull-right' style='margin: 5px 10px;'>";
            if($page == 1){
                $code .= '<li><a href="javascript:;">首页</a></li>';
                $code .= '<li><a href="javascript:;">上一页</a></li>';
            }else{
                $code .= '<li><a href='.$this->url(1).'>首页</a></li>';
                $code .= '<li><a href='.$this->url($uper).'>上一页</a></li>';
            }
            $code .= "<li><a href='javascript:;'>第{$page}页</a></li>";
            if($page == $totalPage){
                $code .= '<li><a href="javascript:;">下一页</a></li>';
                $code .= '<li><a href="javascript:;">尾页</a></li>';
            }else{
                $code .= '<li><a href='.$this->url($downer).'>下一页</a></li>';
                $code .= '<li><a href='.$this->url($totalPage).'>尾页</a></li>';
            }
            $code .= "</ul>";
        }else if($theme == 'h5'){
            $code = "<div>&nbsp;&nbsp;共&nbsp;{$total}&nbsp;条记录&nbsp;{$totalPage}&nbsp;页，当前第&nbsp;{$page}&nbsp;页</div>";
            $rcode .= '<div data-role="controlgroup" data-type="horizontal">';
            if($page == 1){
                $rcode .= '<a href="javascript:;" data-role="button">首页</a>';
                $rcode .= '<a href="javascript:;" data-role="button">第一页</a>';
            }else{
                $rcode .= '<a href='.$this->url(1).' data-role="button">首页</a>';
                $rcode .= '<a href='.$this->url($uper).' data-role="button">上一页</a>';
            }
            if($page == $totalPage){
                $rcode .= '<a href="javascript:;" data-role="button">下一页</a>';
                $rcode .= '<a href="javascript:;" data-role="button">尾页</a>';
            }else{
                $rcode .= '<a href='.$this->url($downer).' data-role="button">下一页</a>';
                $rcode .= '<a href='.$this->url($totalPage).' data-role="button">尾页</a>';
            }
            $rcode .= '</div>';
            // if($totalPage > 1) $code .= $rcode;
            $code .= $rcode;
        }
        return $code;
    }

    private function url($jumpPage = 1){
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        $pat = '/page\=([0-9]+)/';
        if(preg_match($pat, $uri)){
            $uri = preg_replace($pat, "page={$jumpPage}", $uri);
        }else{
            if(preg_match('/\?/', $uri)){
                $uri .= "&page={$jumpPage}";
            }else{
                $uri .= "?page={$jumpPage}";
            }
        }
        $http = ($_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $url = "{$http}://{$host}{$uri}";
        return $url;
    }
}
