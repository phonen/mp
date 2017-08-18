<?php

namespace Addons\LdhQguess\Controller\Mobile;
use Addons\LdhQguess\Controller\MobileController;

/**
 * QQ在线竞猜移动端控制器
 * @author 么么哒
 */
class yongjinController extends MobileController {

    public function getBrokerageRecord(){
        if(IS_AJAX && I('post.playType')==1){ 
            $mpid=get_mpid();
            $addon_name=get_addon();
            $uid= session($addon_name.'_uid');
            $wh['mpid']=$mpid;
            $wh['uid']=$uid;
            $list=M('ldhqguess_yongjing')->where($wh)->order("id DESC")->limit(20)->select();

            foreach($list as &$v){
                $v['brokerageStatus']='成功';
                $v['createTime']=date("m-d H:i:s",$v['createTime']);
                $v['userNo']=$v['uid'];
                $v['amount']=$v['amount']*100;

            }

            $resx['data']=$list;
            $resx['success']=true;
            $resx['timeout']=false;
            $resx['overdue']=false;
            $resx['currentDate']=get_microtime();
            $this->ajaxReturn($resx);
        }


    }
}

?>