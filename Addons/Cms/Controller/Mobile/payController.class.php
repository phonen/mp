<?php

namespace Addons\LdhQguess\Controller\Mobile;
use Addons\LdhQguess\Controller\MobileController;
use WechatSdk\Wechat;
/**
 * QQ在线竞猜移动端控制器
 * @author 么么哒
 */
class payController extends MobileController {

        public function recharge()
        {
            if(IS_AJAX && I('post.playType',0,'int')==1) {
                $amount=abs(I('post.amount',0,'int'))/100;
                if($amount<1){
                    header('HTTP/1.1 500 Internal Server Error');
                    $resx['code']=0012;
                    $resx['msg']='金额错误!';
                    $this->ajaxReturn($resx);exit;
                }

                $mpid=get_mpid();
                $openid=get_openid();
                $addon_name=get_addon();
                $appid = M('mp_setting')->where(array('mpid'=>$mpid,'name'=>'appid'))->getField('value');
                $mchid = M('mp_setting')->where(array('mpid'=>$mpid,'name'=>'mchid'))->getField('value');
                $data["mpid"]=$mpid;
                $data["orderNo"]=$orderNo=session($addon_name.'_uid').'x'.time();
                $data["transactionId"]='';
                $data["openid"]=$openid;
                $data["userNo"]=session($addon_name.'_uid');
                $data["userId"]=session($addon_name.'_uid');
                $data["mchId"]=$mchid;
                $data["appid"]=$appid;
                $data["ip"]=get_client_ip();
                $data["amount"]=$amount;
                $data["payMerchant"]=6;
                $data["payDate"]=0;
                $data["createDateLabel"]=time();
                $data["updateDateLabel"]=time();
                $re=M('ldhqguess_chongzhi')->add($data);
                $setting=get_addon_settings();
                $url="http://".$setting['domain5']."/index.php?s=addon/LdhQguess/mobile/pay_pay/mpid/{$mpid}/orderNo/{$orderNo}";
                $ext_appid = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appid'))->getField('value');
                $ext_appsecret = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appsecret'))->getField('value');
              
                $options = array(
                    'appid'             =>  $ext_appid,
                    'appsecret'         =>  $ext_appsecret
                );
                $wechatObj = new Wechat($options);
                if ($wechatObj->checkAuth($ext_appid, $ext_appsecret)) {              // 公众号有网页授权的权限
                    $redirect_url = $wechatObj->getOauthRedirect($url, '', 'snsapi_base');        // 静默授权跳转地址
                }

                if($re){
                    $kk['orderNo']=$orderNo;
                    $kk['payUrl']=$redirect_url;
                    $resx['data']=$kk;
                    $resx['success']=true;
                    $resx['timeout']=false;
                    $resx['overdue']=false;
                    $resx['currentDate']=get_microtime();
                }else{
                    header('HTTP/1.1 500 Internal Server Error');
                    $resx['code']=0012;
                    $resx['msg']='订单创建失败!';

                }
                $this->ajaxReturn($resx);


            }

        }


    /**
     * 支付成功
     * 资源e站（Zye.cc）
     */
    public function pay() {
        $mpid=get_mpid();

        $orderNo=I('get.orderno');
        $setting=get_addon_settings();
        if(!$orderNo){
            redirect($setting['qqurl']); exit;
        }

        $w['mpid']=$mpid;
        $w['orderNo']=$orderNo;
        $order=M('ldhqguess_chongzhi')->where($w)->find();
        if(!$order){
            redirect($setting['qqurl']); exit;
        }
        $ex_openid=M('ldhqguess_user')->where(array('openid'=>$order['openid']))->getField('ex_openid');
        if (!$ex_openid) {
            init_ext_fans();       // 初始化鉴权用户
            M('ldhqguess_user')->where(array('openid'=>$order['openid']))->save(array('ex_openid'=>get_ext_openid()));
        }
        M('ldhqguess_chongzhi')->where($w)->save(array('ex_openid'=>$ex_openid));
        $url2="http://".$setting['domain4']."/addon/LdhQguess/mobile/index/mpid/{$mpid}";
        if($order['status']==1){
            redirect($url2); exit;
        }
        //回调地址
        $order['notify']="http://".$setting['domain5']."/ldh/{$mpid}";
        if(IS_AJAX && I('post.pay')==1){
              $data['price']=$order['amount'];
              //$data['price']=0.01;
              $data['orderid']=$order['orderNo'];
              $data['notify']=$order['notify'];
            $data['openid']=$ex_openid;
            $jsApiParameters = get_jsapi_parameters($data);
            $this->ajaxReturn($jsApiParameters);exit;
        }



        $this->assign('url2',$url2);
        $this->assign('order',$order);
        $this->assign('sh',$setting['sh']);
        $this->display();
    }

    public function ok() {
        if (I('result_code') == 'SUCCESS' && I('return_code') == 'SUCCESS') {
            $mpid=I('mpid');
            $openid=I('openid');
            $appid=I('appid');
            $orderNo=I('out_trade_no');
            $amount=abs(I('total_fee')/100);
            $transactionId=I('transaction_id');
            $w['mpid']=$mpid;
            $w['orderNo']=$orderNo;
            $w['appid']=$appid;
            $w['ex_openid']=$openid;
            $w['status']=0;
            $ldhqguess_chongzhi=M('ldhqguess_chongzhi');
            $count=$ldhqguess_chongzhi->where($w)->count();
            if($count){
                $data['payDate'] = time();
                $data['transactionId'] = $transactionId;
                $data['amount'] =$amount;
                $data['updateDateLabel'] = time();
                $data['status'] = 1;
                $ldhqguess_chongzhi->where($w)->save($data);
                $wh['mpid']=$mpid;
                $wh['ex_openid']=$openid;
                //加钱
                M('ldhqguess_user')->where($wh)->setInc('moneyx',$amount);
               exit('ok');
            }else{
                //ldh_log($ldhqguess_chongzhi->_sql(),'pay_count1.php');

            }
        }else{
            //ldh_log(I(''),'pay_count666.php');
        }

    }



        public function getTransferCount(){

            if(IS_AJAX && I('post.playType')==1){
                $transferType=I('post.transferType',0,'int');
                $ldhqguess_tixian=M('ldhqguess_duh');
                $openid=get_openid();
                $mpid=get_mpid();
                $setting=get_addon_settings();
                $w['openid']=$openid;
                $w['mpid']=$mpid;
                $w['status']=1;
                $w['transferType']=$transferType;
                $w['createTime']=array('gt', strtotime(date("Y-m-d")." 00:00:00"));
                $count=$ldhqguess_tixian->where($w)->count();
                $res['data']=$setting['tixian']-$count;
                $res['success']=true;
                $res['timeout']=false;
                $res['overdue']=false;
                $res['currentDate']=get_microtime();
                $this->ajaxReturn($res);

            }



        }
        public function withdrawCash(){

            if(IS_AJAX && I('post.playType')==1){
                $token=get_token();
                $transferType=I('post.transferType',0,'int');
                $ldhqguess_duh=M('ldhqguess_duh');
                $openid=get_openid();
                $mpid=get_mpid();
                $setting=get_addon_settings();
                $w['openid']=$openid;
                $w['mpid']=$mpid;
                $w['transferType']=$transferType;
                $w['createTime']=array('gt', strtotime(date("Y-m-d")." 00:00:00"));
                $count=$ldhqguess_duh->where($w)->count();
                $count=$setting['tixian']-$count;

                if($setting['qy_type']){
                    $qy_openid=M('ldhqguess_user')->where(array('openid'=>get_openid()))->getField('qy_openid');
                    session('qy_openid_'.$token,$qy_openid);
                    if(!$qy_openid){
                        header('HTTP/1.1 500 Internal Server Error');
                        $resx['code']=0012;
                        $resx['msg']='无法获取到企业提现openid！';
                        $this->ajaxReturn($resx); exit;
                    }
                }else{
                    $ex_openid=M('ldhqguess_user')->where(array('openid'=>get_openid()))->getField('ex_openid');
                    session('ext_openid_'.$token,$ex_openid);
                    if(!$ex_openid){
                        header('HTTP/1.1 500 Internal Server Error');
                        $resx['code']=0012;
                        $resx['msg']='第一次提现，需要先点击进入充值界面，然后在返回提现！';
                        $this->ajaxReturn($resx); exit;
                    }

                }

                if(!$count){
                    header('HTTP/1.1 500 Internal Server Error');
                    $resx['code']=0012;
                    $resx['msg']='今日提现次数已用完！';
                    $this->ajaxReturn($resx); exit;
                }
                $resx=$this->tixian($transferType);
                if(!$resx){
                    header('HTTP/1.1 500 Internal Server Error');
                    $resx['code']=0012;
                    $resx['msg']='系统繁忙，稍后再试！';
                    $this->ajaxReturn($resx); exit;
                }
                $res['data']=2;
                $res['success']=true;
                $res['timeout']=false;
                $res['overdue']=false;
                $res['currentDate']=get_microtime();
                $this->ajaxReturn($res);

            }

        }
    private function tixian($type){
        $openid=get_openid();
        $mpid=get_mpid();
        $w['openid']=$openid;
        $w['mpid']=$mpid;
        $info=M('ldhqguess_user')->where($w)->find();
        $dd['mpid']=$mpid;
        $dd['openid']=$openid;
        $dd['createTime']=time();
        $dd['transferType']=$type;
        $dd['withdrawStatus']='成功';
        $ldhqguess_duh=M('ldhqguess_duh');
        if($type==1){
             //提现余额
            if($info['moneyx']<1){
                header('HTTP/1.1 500 Internal Server Error');
                $resx['code']=0012;
                $resx['msg']='金额必须要大于1元！';
                $this->ajaxReturn($resx); exit;
            }
            $data['partner_trade_no']=session(get_addon().'_uid').'tx'.time();
            $data['amount']=$info['moneyx'];
            $data['desc']='海洋书屋';
            $data['dongge']=1;
            $re=mch_pay($data); 

            if($re['return_code']=='SUCCESS' &&  $re['result_code']=='SUCCESS'){
                M('ldhqguess_user')->where($w)->save(array('moneyx'=>0));
                $dd['money']=$info['moneyx'];
                $dd['withdrawType']='余额';
                $ldhqguess_duh->add($dd);
                $a=true;
            }else{
                $a=false;
            }

        }elseif($type==2){
            //提现佣金
            if($info['yongjingx']<1){
                header('HTTP/1.1 500 Internal Server Error');
                $resx['code']=0012;
                $resx['msg']='金额必须要大于1元！';
                $this->ajaxReturn($resx); exit;
            }
            $data['partner_trade_no']='wx'.time();
            $data['amount']=$info['yongjingx'];
            $data['desc']='海洋书屋';
            $data['dongge']=1;
            $re=mch_pay($data);
            if($re['return_code']=='SUCCESS' &&  $re['result_code']=='SUCCESS'){
                M('ldhqguess_user')->where($w)->save(array('yongjingx'=>0));
                $dd['money']=$info['yongjingx'];
                $dd['withdrawType']='佣金';
                $ldhqguess_duh->add($dd);
                $a=true;
            }else{
                $a=false;
            }
        }
        return $a;


    }

}

?>