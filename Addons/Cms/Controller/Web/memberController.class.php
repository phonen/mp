<?php

namespace Addons\LdhQguess\Controller\Web;
use Addons\LdhQguess\Controller\WebController;

/**
 * QQ在线竞猜后台管理控制器
 * @author 么么哒
 */
class memberController extends WebController {
    
    public function index(){
        //配置静态地址
        $config['rule']['list']='/addon/LdhQguess/web/member_index/page/*';
        $config['rule']['index']='/addon/LdhQguess/web/member_index';
        $where['mpid']=get_mpid();
        $lists=$this->Pages('ldhqguess_user',$where,true,true,20,0,$config,1);
        $this->assign('lists',$lists);
        $this->display();
    }
 
    public function lahei(){
        if(IS_AJAX){
            $where['id']=I('get.id')?I('get.id'):die('666');
            $where['mpid']=get_mpid();
            $data['lahei']=I('get.v');
            $res=M('ldhqguess_user')->where($where)->save($data);
            if($res){
                $this->ajaxReturn(1);
            }
        }

    }

    public function edit(){

        exit('没做');
    }

}

?>