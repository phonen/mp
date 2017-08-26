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
        if($result)reply_text(substr($keyword,1));
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


}

?>