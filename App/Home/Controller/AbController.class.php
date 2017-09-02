<?php
namespace Home\Controller;
use Think\Controller;

/*么么哒域名跳转控制器*/
class AbController extends Controller {

	private $wechat_only = false;

	public function _initialize(){
		if (!is_wechat_browser() &&  $this->wechat_only) {
			redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx8dcd98079e13d33f&redirect_uri=&wxref=mp.weixin.qq.com&from=singlemessage&isappinstalled=0&response_type=code&scope=snsapi_base&state=&connect_redirect=1#wechat_redirect');
		}

	}
	public function a(){
		//推广域名进来

		$mpid=I('get.mpid','','int');
		$pid=I('get.pid','','int');
		$map['mpid'] = $mpid;
		$map['addon'] = 'LdhQguess';
		$settings = M('addon_setting')->where($map)->select();
		if (!$settings) {
			return false;
		}
		foreach ($settings as $k => $v) {
			$addon_settings[$v['name']] = $v['value'];
		}
		//禁止其他域名进来
		if($_SERVER['HTTP_HOST']<>$addon_settings['domain1']){
			exit;
		}
		$domain2=$addon_settings['domain2'];
		$url='http://'.$domain2.'/ab_b/'.$mpid.'/'.$pid;

		$this->assign('url',$url);
		$this->display();
	}

	public function b(){
		//跳转域名进来
		$mpid=I('get.mpid','','int');
		$pid=I('get.pid','','int');
		$map['mpid'] = $mpid;
		$map['addon'] = 'LdhQguess';
		$settings = M('addon_setting')->where($map)->select();
		if (!$settings) {
			return false;
		}
		foreach ($settings as $k => $v) {
			$addon_settings[$v['name']] = $v['value'];
		}
		//禁止其他域名进来
		if($_SERVER['HTTP_HOST']<>$addon_settings['domain2']){
			exit;
		}
		$domain4=$addon_settings['domain4'];
		$url="http://".$domain4."/addon/LdhQguess/Mobile/index/mpid/".$mpid."/pid/".$pid;

		redirect($url);exit;
		//微信用户信息地址出去

	}


}