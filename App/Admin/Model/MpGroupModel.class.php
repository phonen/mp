<?php 

namespace Admin\Model;
use Think\Model;

/**
 * 公众号套餐模型
 * 资源e站（Zye.cc）
 */
class MpGroupModel extends Model {

    /**
     * 获取套餐信息
     * 资源e站（Zye.cc）
     */
    public function get_group_info($group_id) {
        if (!$group_id) {
            return false;
        }
        $mp_group = $this->find($group_id);
        if (!$mp_group) {
            return false;
        }
        return $mp_group;
    }

    /**
     * 获取套餐列表
     * 资源e站（Zye.cc）
     */
    public function get_group_lists($map = array()) {
        $lists = $this->where($map)->select();
        return $lists;
    }
}

?>