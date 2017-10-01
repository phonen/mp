<?php 

namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * 后台首页控制器
 * 资源e站（Zye.cc）
 */
class IndexController extends BaseController {

	/**
	 * 后台首页
	 * 资源e站（Zye.cc）
	 */
	public function index() {
	    echo "aaaa";
        $this->assign('meta_title', '后台首页');
		//$this->display();
	}
}

?>