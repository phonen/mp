<?php

namespace Addons\LdhQguess\Controller\Mobile;
use Addons\LdhQguess\Controller\MobileController;

/**
 * QQ在线竞猜移动端控制器
 * @author 么么哒
 */
class orderController extends MobileController {

    public function queryOpenOrder(){

        //开始计算中奖
        $this->openorder();
        $mpid=get_mpid();
        $where['mpid']=$mpid;
        $where['status']=3;
        $ldhqguess_opencode=M('ldhqguess_opencode');
        $infox=$ldhqguess_opencode->where($where)->order('id DESC')->find();

        if(!$infox['issueNo']){
            $res['successxx']=2222;
            $res['success']=true;
            $res['timeout']=false;
            $res['overdue']=false;
            $res['currentDate']=get_microtime();
            $this->ajaxReturn($res);

        }
        //开始返回中奖结果
        $openid=get_openid();
        $addon_name=get_addon();
        $mpid=get_mpid();
        $ldhqguess_num=M('ldhqguess_num');
        $ldhqguess_opencode=M('ldhqguess_opencode');
        $w['openid']=$openid;
        $w['mpid']=$mpid;
        $w['issueNo']=$infox['issueNo'];
        $info=$ldhqguess_num->where($w)->find();
        if($info){
            $ww['issueNo']=$info['issueNo'];
            $ww['mpid']=$mpid;
            $opens=$ldhqguess_opencode->where($ww)->find();
            $opens['createDate']=date('Y-m-d',$opens['createDate']);
            $opens['updateDate']=date('Y-m-d',$opens['updateDate']);
            $opens['userId']=session($addon_name.'_uid');
            $opens['issueNo']=$info['issueNo'];
            $opens['userNo']=session($addon_name.'_uid');
            $opens['source']=1;
            $opens['amount']=0;
            $opens['betAmount']=$info['betAmount']*100;
            $opens['discType']=$info['discType'];
            $opens['betNum']=$this->user_code($info);
            $opens['openDiscRom']=substr($opens['openCode'], -1);
            $opens['odds']=$this->oddosx($info['discType']);

            $opens['openOrderNo']=$opens['openCode'];
            $opens['status']=2;
            $opens['channel']=0;
            $opens['playType']=1;
            $opens['isCanSee']=0;
            $opens['ip']='';
            $opens['openDate']=$opens['openDate']*1000;
            $opens['readFlag']=0;
            $opens['betNumArray']=$this->user_code($info);
            //已中奖
            if($info['status']){
                $opens['winAmount']=$info['winAmount']*100;
            }else{
                //未中奖

            }
            $res['data']=$opens;
        }
        $res['successxx']=1111;
        $res['success']=true;
        $res['timeout']=false;
        $res['overdue']=false;
        $res['currentDate']=get_microtime();
        $this->ajaxReturn($res);
    }
    /*计算中奖*/
    private function openorder(){
        $mpid=get_mpid();
        $where['mpid']=$mpid;
        $where['fenqian']=0;
        $where['status']=3;
        $ldhqguess_opencode=M('ldhqguess_opencode');
        $info=$ldhqguess_opencode->where($where)->order('id DESC')->find();
        //if(!$info){ ldh_log($ldhqguess_opencode->_sql(),'sql.php');  }
        if($info){
            $ldhqguess_num=M('ldhqguess_num');
            $w['mpid']=$mpid;
            $w['issueNo']=$info['issueNo'];
            $w['status']=0;
            $lists=$ldhqguess_num->where($w)->select();
            $num1=substr($info['openCode'], -1);
            $num2=substr($info['openCode'], -2,1);
            if($lists){
                foreach($lists as $k=> $v){
                    //两数相等 则为合
                    if($num1===$num2){
                        if($v['aa'] ){
                            //更新合
                            $winAmount=$v['betAmount']*$this->oddosx($v['discType']);
                            //更新单个中奖记录
                            $ldhqguess_num->where(array('id'=>$v['id']))->save(array('winAmount'=>$winAmount,'status'=>1));
                            //加钱
                            M('ldhqguess_user')->where(array('openid'=>$v['openid']))->setInc('moneyx',$winAmount);
                        }

                        if($v['a'.$num1] && $v['discType']<>1){
                            $winAmount=$v['betAmount']*$this->oddosx($v['discType']);
                            //更新单个中奖记录
                            $ldhqguess_num->where(array('id'=>$v['id']))->save(array('winAmount'=>$winAmount,'status'=>1));
                            //加钱
                            M('ldhqguess_user')->where(array('openid'=>$v['openid']))->setInc('moneyx',$winAmount);
                        }
                    }else{
                        if($v['a'.$num1]){
                            $winAmount=$v['betAmount']*$this->oddosx($v['discType']);
                            //更新单个中奖记录
                            $ldhqguess_num->where(array('id'=>$v['id']))->save(array('winAmount'=>$winAmount,'status'=>1));
                            //加钱
                            M('ldhqguess_user')->where(array('openid'=>$v['openid']))->setInc('moneyx',$winAmount);

                        }

                    }

                }
            }

            //完成更新 更新最后opencode
            $ldhqguess_opencode->where(array('id'=>$info['id']))->save(array('fenqian'=>1));

        }

    }


    public function getOrderRecord(){
        if(IS_AJAX && I('post.playType',0,'int')==1){

            $where['openid']=get_openid();
            $where['mpid']=get_mpid();
            $where['status']=1;
            $list=M('ldhqguess_chongzhi')->where($where)->limit(20)->select();
            foreach($list as &$v){
                $v['createDateLabel']=date("m-d H:i:s",$v['createDateLabel']);
                $v['updateDateLabel']=date("m-d H:i:s",$v['updateDateLabel']);
                $v['orderStatus']='成功';
                $v['amount']=  $v['amount']*100;
            }
            $res['data']=$list;
            $res['success']=true;
            $res['timeout']=false;
            $res['overdue']=false;
            $res['currentDate']=get_microtime();
            $this->ajaxReturn($res);

        }






     }

}

?>