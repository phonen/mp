<?php

namespace Addons\LdhQguess\Controller\Mobile;
use Addons\LdhQguess\Controller\MobileController;
/**
 * QQ在线竞猜移动端控制器
 * @author 么么哒
 */
class userController extends MobileController {

    public function activatInvitationCode(){


    }

    public function getAccountInfo(){


        $add_s=get_addon_settings();
        $mpid=get_mpid();
        if(!$mpid){exit;}
        $where['mpid']=$mpid;
        $where['openid']=get_openid();
        $user=M('ldhqguess_user')->where($where)->find();
        $data["id"]=$user['id'];
        $data["createDate"]=$user['regtime'];
        $data["updateDate"]=$user['logintime'];
        $data["userNo"]=$user['id'];
        $mouny=($user['moneyx']*100)+($user['zs']*100);
        $data["amount"]=$mouny;
        $data["brokerageAmount"]=$user['yongjingx']*100;
        $data["frozenAmount"]='0'; 
        $data["brokerageRecordAmount"]='3';
        $data["cashRecordAmount"]='4';
        $data["cashBrokerageRecordAmount"]='5';
        $data["rechargeRecordAmount"]='6';
        $data["winRecordAmount"]='7';
        $data["robotFlag"]='0';
        $data["channel"]='0';
        $data["rechargeCount"]='20';
        $data["playCount"]='11';
        $data["cashCount"]='12';
        $data["cashBrokerageCount"]='13';
        $data["isEnable"]='1';
        $data["parentUserNo"]=$user['parentUserNo'];
        $data["equipment"]=$add_s['equipment'];

        $res['data']=$data;
        $res['success']=true;
        $res['timeout']=false;
        $res['overdue']=false;
        $res['currentDate']=get_microtime();
        $this->ajaxReturn($res);

    }
    public function getAccountInfoByCache(){
        if(IS_AJAX){
            $addon_name=get_addon();
            $mpid=get_mpid();
            $openid=get_openid();
            $where['mpid']=$mpid;
            $where['openid']=$openid;
            $moneyx=M('ldhqguess_user')->where($where)->getField('moneyx');

            $data['userNo']= session($addon_name.'_uid');
            $data['amount']=$moneyx;
            $data['brokerageAmount']=0;
            //佣金记录
            $data['brokerageRecordAmount']=0;
            $data['cashRecordAmount']=0;
            $data['cashBrokerageRecordAmount']=0;
            //充值记录
            $where['status']=1;
            $RecordAmount=M('ldhqguess_chongzhi')->where($where)->sum('amount');
           $data['rechargeRecordAmount']=$RecordAmount*100;
            $data['winRecordAmount']=0;

            $res['data']=$data;
            $res['success']=true;
            $res['timeout']=false;
            $res['overdue']=false;
            $res['currentDate']=get_microtime();
            $this->ajaxReturn($res);
        }

    }
    public function userCodeUrl(){
        $settings=get_addon_settings();
        $domain1=$settings['domain1'];
        $addon_name=get_addon();



        $openid=get_openid();
        $mpid=get_mpid();
        $pid=I('get.pid','','int');

        if(!$openid){exit('谁');}
        $User=M('ldhqguess_user');
        $where['openid']=$openid;
        $where['mpid']=$mpid;

        if(!session($addon_name.'_uid')){
            $count=$User->where($where)->count();
            if(!$count){
                $data['openid']=$openid;
                $data['regtime']=time();
                $data['ip']=get_client_ip();
                $data['logintime']=time();
                $data['times']=1;
                $data['mpid']=$mpid;
                $data['parentUserNo']=$pid;
                $uid=$User->add($data);
            }else{
                $info=$User->where($where)->field('id,times')->find();
                $uid=$info['id'];
                $datax['ip']=get_client_ip();
                $datax['logintime']=time();
                $datax['times']=$info['times']+1;
                $User->where($where)->save($datax);

            }
            session($addon_name.'_uid',$uid);
        }
        $uid=session($addon_name.'_uid');


        ldh_log($uid,"aa.php");
        $res['data']="http://".$domain1."/ab_a/".get_mpid()."/".$uid;
        $res['success']=true;
        $res['timeout']=false;
        $res['overdue']=false;
        $res['currentDate']=get_microtime();

        $this->ajaxReturn($res);

    }

}

?>