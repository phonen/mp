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
        \Think\Log::write($message.'respondddddddddddddddddddddddddddddddd' . $this->addon,'WARN');
					reply_text($message);
	}
}

?>