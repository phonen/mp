<?php 

namespace Addons\IdouGuestbook\Model;
use Think\Model;

/**
 * 留言数据管理模型
 * 资源e站（Zye.cc）
 */
class IdouGuestbookListModel extends Model {

    /**
     * 自动验证
     * 资源e站（Zye.cc）
     */
    protected $_validate = array(
    	array('nickname', 'require', '用户昵称不能为空'),
    	array('content', 'require', '留言内容不能为空')
    );

    /**
     * 自动完成
     * 资源e站（Zye.cc）
     */
    protected $_auto = array(
   		array('mpid', 'get_mpid', 1, 'function'),
   		array('openid', 'get_openid', 1, 'function'),
   		array('create_time', 'time', 1, 'function')
    );

}

?>