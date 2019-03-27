<?php
class Http {
    //get请求
    public static function get($url, $data = array(), $timeout = 30, $cookie = '', $follow = 0){
        return self::curl_get_contents($url, 'GET', $data, $timeout, $cookie, $follow);
    }

    //post请求
    public static function post($url, $data = array(), $timeout = 30, $cookie = '', $follow = 0){
        return self::curl_get_contents($url, 'POST', $data, $timeout, $cookie, $follow);
    }

    //post json
    public static function postjson($url, $data = array(), $timeout = 30, $cookie = '', $follow = 0){
        return self::curl_get_contents($url, 'POST', $data, $timeout, $cookie, $follow, true);
    }

    //curl请求
    public static function curl_get_contents( $url = '', $method = "GET", $data = array(), $timeout=30, $cookie = '', $follow = 0, $json = false){
        if(empty($data)) $data = array();
        if(empty($cookie)) $cookie = '';

        $query  = array();
        $curl   = curl_init();
        if ($data) {
            foreach($data as $k=>$v){
                $query[] = $k.'='.urlencode($v);
            }
        }
        if(strtoupper($method) == 'GET' && $data){
            $url .= '?'.implode('&', $query);
        }elseif(strtoupper($method) == 'POST'){
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($json) {
                $data_string = json_encode($data);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string)
                ));
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            } else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, implode('&', $query));
            }
        }
        if(!empty($cookie)) curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        if(!empty($follow)) curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
