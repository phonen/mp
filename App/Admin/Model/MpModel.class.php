<?php 

namespace Admin\Model;
use Think\Model;

/**
 * 公众号模型
 * 资源e站（Zye.cc）
 */
class MpModel extends Model {

	/**
	 * 根据公众号ID获取公众号基本信息
	 * 资源e站（Zye.cc）
	 */
	public function get_mp_info($mpid = '') {
		if (!$mpid) {
			return false;
		}
		$mp_info = $this->find(intval($mpid));
		if (!$mp_info) {
			return false;
		}
		return $mp_info;
	}

}

?>