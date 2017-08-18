<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;

/**
 * 空白...首页控制器
 * 资源e站（Zye.cc）
 */
class IndexController extends Controller {

	/**
	 * 初始化
	 * 资源e站（Zye.cc）
	 */
	public function _initialize() {
		 

		exit;
		$system_settings = D('Admin/SystemSetting')->get_settings();
		$this->assign('system_settings', $system_settings);
	}
    
    /**
     * 首页
     * 资源e站（Zye.cc）
     */
    public function index() {
    	$this->display();
    }
}