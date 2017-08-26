<?php

namespace Addons\IdouGuestbook\Controller;
use Mp\Controller\ApiController;

/**
 * 留言板响应控制器
 * @author 么么哒
 */
class RespondController extends ApiController {

	/**
	 * 微信交互
	 * @param $message array 微信消息数组
	 */
	public function wechat($message = array()) {
        $keyword = $message['content'];



        $pt1 = "/^(taotehui|buyi|yhg)\d{3,4}/";

        $result = $this->query_order($keyword);
        if($result){
            $url = 'http://taotehui.co/?g=Tbkqq&m=WxAi&a=order_json';
            $data['oid'] = substr($keyword,1);

            reply_text($this->http_post_content($url,$data));
        }
        else {
            reply_text("1");
        }



	}

	protected function query_order($keyword){
        $pt = "/\*(\d{16,17})/";
        $result = preg_match($pt,$keyword);
        return $result;
    }

	protected function proxy_setting() {

    }

    protected function http_get_content($url, $cache = false){
        // 定义当前页面请求的cache key
        $key = md5($url);
        // 如果使用cache时只读一次
        if($cache){
            $file_contents = $_SESSION[$key];
            if(!empty($file_contents)) return $file_contents;
        }

        // 通过curl模拟请求页面
        $ch = curl_init();
        // 设置超时时间
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $url);
        // 以下内容模拟来源及代理还有agent,避免被dns加速工具拦截
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:111.222.333.4', 'CLIENT-IP:111.222.333.4'));
        curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com");
        //curl_setopt($ch, CURLOPT_PROXY, "http://111.222.333.4:110");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);

        curl_close($ch);

        // 匹配出当前页的charset
        $charset = preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i", $file_contents, $temp) ? strtolower($temp[1]) : "";
        //$title = preg_match("/<title>(.*)<\/title>/isU", $file_contents, $temp) ? $temp[1] : "";

        // 非utf8编码时转码
        if($charset != 'utf-8'){
            $file_contents = iconv(strtoupper($charset), "UTF-8", $file_contents);
        }
        // 将结果记录到session中，方便下次直接读取
        $_SESSION[$key] = $file_contents;

        return $file_contents;
    }

    protected function http_post_content($url, $data,$cache = false){
        // 定义当前页面请求的cache key
        $key = md5($url);
        // 如果使用cache时只读一次
        if($cache){
            $file_contents = $_SESSION[$key];
            if(!empty($file_contents)) return $file_contents;
        }

        // 通过curl模拟请求页面
        $ch = curl_init();
        // 设置超时时间
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $url);
        // 以下内容模拟来源及代理还有agent,避免被dns加速工具拦截
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:111.222.333.4', 'CLIENT-IP:111.222.333.4'));
        curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com");
        //curl_setopt($ch, CURLOPT_PROXY, "http://111.222.333.4:110");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $file_contents = curl_exec($ch);

        curl_close($ch);

        // 匹配出当前页的charset
        $charset = preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i", $file_contents, $temp) ? strtolower($temp[1]) : "";
        //$title = preg_match("/<title>(.*)<\/title>/isU", $file_contents, $temp) ? $temp[1] : "";

        // 非utf8编码时转码
        if($charset != 'utf-8'){
            $file_contents = iconv(strtoupper($charset), "UTF-8", $file_contents);
        }
        // 将结果记录到session中，方便下次直接读取
        $_SESSION[$key] = $file_contents;

        return $file_contents;
    }

}

?>