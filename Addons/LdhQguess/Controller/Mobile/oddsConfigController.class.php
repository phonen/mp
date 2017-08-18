<?php

namespace Addons\LdhQguess\Controller\Mobile;
use Addons\LdhQguess\Controller\MobileController;

/**
 * QQ在线竞猜移动端控制器
 * @author 么么哒
 */
class oddsConfigController extends MobileController {

    public function getAllConfig(){

        if(IS_AJAX && I('post.playType') ==1){
 
            $mpid=get_mpid(); 
            if(!$mpid){exit;}
            $where['mpid']=$mpid;
            $data=M('ldhqguess_config')->where($where)->select();
            foreach($data as $k=> $v){
                $data[$k]['createby']=unserialize($v['createby']);
                $data[$k]['updateby']=unserialize($v['updateby']);

            }
            $res['data']=$data;
            $res['success']=true;
            $res['timeout']=false;
            $res['overdue']=false;
            $res['currentDate']=get_microtime();
            $this->ajaxReturn($res);

        }

    }


}

?>