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

        $pt = "/\*(\d{16,17})/";
        $match = preg_match($pt,$keyword);
        if($match){
            $url = 'http://taotehui.co/?g=Tbkqq&m=WxAi&a=order_json';
            $data['oid'] = substr($keyword,1);

            reply_text($this->http_post_content($url,$data));
        }
        else {
            $pt = "/^(taotehui|buyi|yhg)\d{3,4}/";
            $match = preg_match($pt,$keyword);
            if($match){
                $url = 'http://taotehui.co/?g=Tbkqq&m=WxAi&a=save_openid';
                $openid = get_openid();
                $data['openid'] = $openid;
                $data['msg'] = $keyword;
                reply_text($this->http_post_content($url,$data));
            }
            else {
                $match = preg_match("/\x{ffe5}.*\x{ffe5}/u",$keyword,$out);
                if($match){
                    // 提交地址
                    $curl='http://www.taokouling.com/index.php?m=api&a=taokoulingjm';
// 提交数据  格式例子 $data='username='.urlencode('小明').'&password=abc123&text='.urlencode('￥uwXD0YI3GnM￥');
// 账号密码是淘口令网站的  没有的自己注册一个
                    $kouling = $out[0];
                    $data='username='.urlencode('pioul').'&password=6t7y8u9i&text='.urlencode($kouling);

                    $rs2=$this->execcurl($curl,true,$data);
// 输出解密后的内容
                    $rsarr = json_decode($rs2,true);
                    $item_url = $rsarr['url'];

                    $url = 'http://taotehui.co/?g=Tbkqq&m=WxAi&a=taoke_info_openid';
                    $data['msg'] = $item_url;

                    $data['openid'] = get_openid();
                    \Think\Log::write($item_url,'WARN');
                    reply_text($this->http_post_content($url,$data));
                }
                else {
                    $match = preg_match('/https?:\/\/[\w=.?&\/;]+/',$keyword);
                    if($match){

                        $url = 'http://taotehui.co/?g=Tbkqq&m=WxAi&a=taoke_info_openid';
                        $data['msg'] = $keyword;

                        $data['openid'] = get_openid();

                        reply_text($this->http_post_content($url,$data));
                    }
                    else reply_text("1");
                }


            }
        }

	}


    protected function execcurl($url,$ispost=false,$data='',$in='utf8',$out='utf8',$cookie='')
    {
        $fn = curl_init();
        curl_setopt($fn, CURLOPT_URL, $url);
        curl_setopt($fn, CURLOPT_TIMEOUT, 60);
        curl_setopt($fn, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($fn, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($fn, CURLOPT_REFERER, $url);
        curl_setopt($fn, CURLOPT_HEADER, 0);
        if($cookie)
            curl_setopt($fn,CURLOPT_COOKIE,$cookie);
        if($ispost){
            curl_setopt($fn, CURLOPT_POST, TRUE);
            curl_setopt($fn, CURLOPT_POSTFIELDS, $data);
        }
        $fm = curl_exec($fn);
        curl_close($fn);
        if($in!=$out){
            $fm = Newiconv($in,$out,$fm);
        }
        return $fm;
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