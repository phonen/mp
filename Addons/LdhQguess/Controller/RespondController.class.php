<?php

namespace Addons\LdhQguess\Controller;
use Mp\Controller\ApiController;

/**
 * QQ在线竞猜响应控制器
 * @author 么么哒
 */
class RespondController extends ApiController {

	/**
	 * 微信交互
	 * @param $message array 微信消息数组
	 */
	public function wechat($message = array()) {
					reply_text($message);
	}
}

?>