<?php

namespace Addons\LdhQguess\Controller\Mobile;
use Addons\LdhQguess\Controller\MobileController;
/**
 * QQ在线竞猜移动端控制器
 * @author 么么哒
 */
class discController extends MobileController {
    //么么哒算法 投注
    public function bet(){
        if(IS_AJAX) {
            $betNum = I('post.betNum');
            $betAmount = I('post.betAmount', '', 'int')/100;
            $betAmount=abs($betAmount);
            $discType = I('post.discType', '', 'int');
            $ldhqguess_user=M('ldhqguess_user');
            $where['mpid']=get_mpid();
            $where['openid']=get_openid();

            $moneyx=$ldhqguess_user->where($where)->getField('moneyx');
            $zs=$ldhqguess_user->where($where)->getField('zs');
            $moneyx=$zs+$moneyx;
            if($betAmount>$moneyx){ 
                /*钱不够*/
                header('HTTP/1.1 500 Internal Server Error');
                $resx['code']=0012;
                $resx['msg']='钱不够';
                $this->ajaxReturn($resx);
            }
            if (!$discType) {  exit; }
            $betNum1=array('01234',56789);//大小
            $betNum2=array('012',345,678);//3位数
            $betNum3=array('01',23,45,67,89);//2位数
            $betNum4=array('0',1,2,3,4,5,6,7,8,9);//1位数
            $betNum5=5;//合

            if($discType==1 && in_array($betNum,$betNum1)){
                $this->buynum($betNum,$betAmount,$discType); exit;
            }
            if($discType==2 && in_array($betNum,$betNum2)){
                $this->buynum($betNum,$betAmount,$discType); exit;
            }
            if($discType==3 && in_array($betNum,$betNum3)){
                $this->buynum($betNum,$betAmount,$discType); exit;
            }
            if($discType==4 && in_array($betNum,$betNum4)){
                $this->buynum($betNum,$betAmount,$discType); exit;
            }
            if($discType==5 && $betNum==$betNum5){
                $this->buynum($betNum,$betAmount,$discType); exit;
            }

            header('HTTP/1.1 500 Internal Server Error');
            $resx['code']=0012;
            $resx['msg']='错误';
            $this->ajaxReturn($resx);

        }

    }


    //购买
    private function buynum($betNum,$betAmount,$discType){
        if($betNum==5 && $discType==5){
            $data['aa']=1;
        }else{
            $str=strval($betNum);
            $numarr=str_split($str);
            foreach($numarr as $v){
                $data['a'.$v]=1;
            }
        }
        //开始添加购买记录
        $mpid=get_mpid();
        if(!$mpid){exit;}
        $where['mpid']=$mpid;
        $ldhqguess_opencode=M('ldhqguess_opencode');
        $ldhqguess_num=M('ldhqguess_num');
        $where['status']=1;
        $issueNo=$ldhqguess_opencode->where($where)->order('id DESC')->getField('issueNo');
        //检查重复购买
        $ww['mpid']=$mpid;
        $ww['openid']=get_openid();
        $ww['issueNo']=$issueNo;
        $count=$ldhqguess_num->where($ww)->count();
        if($count>0){
            /*重复*/
            header('HTTP/1.1 500 Internal Server Error');
            $resx['code']=0012;
            $resx['msg']='投注失败, 每一期只能投注一次！';
            $this->ajaxReturn($resx);

        }
        //先扣钱再购买

        $ldhqguess_user=M('ldhqguess_user');
        $wu['mpid']=get_mpid();
        $wu['openid']=get_openid();
        $moneyx=$ldhqguess_user->where($wu)->getField('moneyx');
        $zs=$ldhqguess_user->where($wu)->getField('zs');
        $moneyx=$zs+$moneyx;
        if($betAmount>$moneyx){
            header('HTTP/1.1 500 Internal Server Error');
            $resx['code']=0012;
            $resx['msg']='钱不够';
            $this->ajaxReturn($resx);
        }else{
            $ldhqguess_user->where($wu)->setDec('moneyx',$betAmount-$zs);
            $ldhqguess_user->where($wu)->save(array('zs'=>0));
            //已扣钱往下执行
        }
        //扣钱后立即分钱
        $this->yongjing($betAmount-$zs);

        //开始添加购买记录
        $data['mpid']=get_mpid();
        $data['openid']=get_openid();
        $data['issueNo']=$issueNo;
        $data['betAmount']=$betAmount;
        $data['discType']=$discType;
        $data['buytime']=time();
        $res=$ldhqguess_num->add($data);
        if($res){
            /*购买成功*/
            $resx['data']=$issueNo;
            $resx['success']=true;
            $resx['timeout']=false;
            $resx['overdue']=false;
            $resx['currentDate']=get_microtime();
            $this->ajaxReturn($resx);
        }

    }
    /*
     * 分销佣金
     * */

    private function yongjing($money){
        if($money<=0){return false;}
        $addon_name=get_addon();
        $uid=session($addon_name.'_uid');
        $setting=get_addon_settings();
        //默认6级分钱
        if($setting['fx1']){
            $pid1=$this->addyongjing($uid,$money,$setting['fx1'],1,$uid);
        }
        if($setting['fx2'] && $pid1){
            $pid2=$this->addyongjing($pid1,$money,$setting['fx2'],2,$uid);
        }
        if($setting['fx3'] && $pid2){
            $pid3=$this->addyongjing($pid2,$money,$setting['fx3'],3,$uid);
        }
        if($setting['fx4'] && $pid3){
            $pid4=$this->addyongjing($pid3,$money,$setting['fx4'],4,$uid);
        }
        if($setting['fx5'] && $pid4){
            $pid5=$this->addyongjing($pid4,$money,$setting['fx5'],5,$uid);
        }
        if($setting['fx6'] && $pid5){
            $pid6=$this->addyongjing($pid5,$money,$setting['fx6'],6,$uid);
        }


    }
    private function addyongjing($uid,$money,$fx,$lv,$fromid){
        $mpid=get_mpid();
        $yongjing=M('ldhqguess_yongjing');
        $user=M('ldhqguess_user');
        $w['mpid']=$mpid;
        $w['id']=$uid;
        $pid=$user->where($w)->getField('parentUserNo'); //上一级
        if($pid){
            unset($w['id']);
            $w['id']=$pid;
            $fxy=$money*$fx/100;
            $user->where($w)->setInc('yongjingx',$fxy);
            $data['mpid']=$mpid;
            $data['uid']=$pid;
            $data['fromid']=$fromid;
            $data['lv']=$lv;
            $data['amount']=$fxy;
            $data['status']=1;
            $data['createTime']=time();
            $yongjing->add($data);
            return $pid;
        }else{
            return false;
        }

    }

    public function getBetLottery(){

        if(IS_AJAX){
            $mpid=get_mpid();
            if(!$mpid){exit;}
            $where['mpid']=$mpid;
            $ldhqguess_opencode=M('ldhqguess_opencode');
            $where['status']=1;
            $openDate=$ldhqguess_opencode->where($where)->order('id DESC')->getField('openDate');
            if(time()>$openDate){
                $ldhqguess_opencode->where($where)->save(array('status'=>2));
                $this->addqishu();//添加期数
            }
            unset($where['status']);
            $info=$ldhqguess_opencode->where($where)->order('id DESC')->limit(4)->select();
            if($info){
                foreach($info as $k=>$v){

                    $info[$k]['createDate']=date('Y-m-d',$v['createDate']);
                    $info[$k]['updateDate']=date('Y-m-d',$v['updateDate']);
                    $info[$k]['startBetDate']=date('Y-m-d H:i:s',$v['startBetDate']);
                    $info[$k]['endBetDate']=date('Y-m-d H:i:s',$v['endBetDate']);
                    $info[$k]['openDate']=date('Y-m-d H:i:s',$v['openDate']);
                    if($k>1){
                        $info[$k]['openFinishDate']=date('Y-m-d H:i:s',$v['openFinishDate']);
                    }elseif($k==1){
                        unset($info[$k]['openFinishDate']);
                        $Down=$v['openDate']-time();
                        $info[$k]['lotteryCountDown']=$Down>0?$Down:0;
                        $waitOpen=($v['openDate']+6)-time();
                        if($waitOpen>0){
                            $info[$k]['openDate']=$waitOpen;
                        }

                    }else{
                        unset($info[$k]['openFinishDate']);
                        $Down=$v['openDate']-time();
                        $info[$k]['lotteryCountDown']=$Down>0?$Down:0;

                    }
                }
                $data['currentLottery']=$info[0];
                $data['previouLottery']=$info[1];
                $data['historyLottery'][]=$info[2];
                $data['historyLottery'][]=$info[3];
                $data['isStartOpen']=false;
                $res['data']=$data;
                $res['success']=true;
                $res['timeout']=false;
                $res['overdue']=false;
                $res['currentDate']=get_microtime(); 
                $this->ajaxReturn($res);
            }

        }

    }

    public function queryLotteryResult(){
        $issueNo=I('post.issueNo',0,'int');
        if(IS_AJAX && $issueNo){
            $mpid=get_mpid();
            if(!$mpid){exit;}
            $where['mpid']=$mpid;
            $where['issueNo']=$issueNo;
            $where['status']=array('lt',3);
            $where['openDate']=array('lt',time()+10);
            $ldhqguess_opencode=M('ldhqguess_opencode');
            $info=$ldhqguess_opencode->where($where)->find();
            //开始算计中奖
            if($info){
                $this->jisuan($issueNo);
            }
            $where['status']=3;
            $info=$ldhqguess_opencode->where($where)->find();
            $info['createDate']=date('Y-m-d',$info['createDate']);
            $info['updateDate']=date('Y-m-d',$info['updateDate']);
            $info['startBetDate']=date('Y-m-d H:i:s',$info['startBetDate']);
            $info['endBetDate']=date('Y-m-d H:i:s',$info['endBetDate']);
            $info['openDate']=date('Y-m-d H:i:s',$info['openDate']);
            $info['openFinishDate']=date('Y-m-d H:i:s',$info['openFinishDate']);
            $res['data']=$info;
            $res['success']=true;
            $res['timeout']=false;
            $res['overdue']=false;
            $res['currentDate']=get_microtime();
            $this->ajaxReturn($res);



        }


    }
    //添加期数
    private function addqishu(){
        $mpid=get_mpid();
        $today_s=strtotime(date('Y-m-d').' 00:00:00');
        $today_e=strtotime(date('Y-m-d').' 23:59:59');
        $where['mpid']=$mpid;
        $where['createDate']=array('between',"$today_s,$today_e");;
        $ldhqguess_opencode=M('ldhqguess_opencode');
        $count=$ldhqguess_opencode->where($where)->count();
        $qishu=date('Ymd')*10000+$count;
        $data['mpid']=$mpid;
        $data['createDate']=time();
        $data['updateDate']=time();
        $data['issueNo']=$qishu;
        $data['startBetDate']=time();
        $data['endBetDate']=time()+60;
        $data['openDate']=time()+60;
        $ldhqguess_opencode->add($data);

    }
    //计算中奖
    private function jisuan($qishu){
        $ldhqguess_num=M('ldhqguess_num');
        $mpid=get_mpid();
        $where['mpid']=$mpid;
        $where['issueNo']=$qishu;
        $res=$ldhqguess_num->where($where)->find();
        if(kongpan() && $res){
            //控盘操作
            $data['openCode']=$this->kongpancode($qishu);
        }else{
            $data['openCode']=rand(230123123,250321321);

        }
       // $data['openCode']=230123100;
        $where['status']=array('lt',3);
        $data['status']=3;
        $data['updateDate']=time();
        $data['openFinishDate']=time();
        $ldhqguess_opencode=M('ldhqguess_opencode');
        $resx=$ldhqguess_opencode->where($where)->save($data);
        if($resx){
            return true;
        }else{
            return false;
        }

    }

      

    /*么么哒100%控盘算出 号码*/
    private function kongpancode($issueNo){
        $mpid=get_mpid();
        $where['mpid']=$mpid;
        $where['issueNo']=$issueNo;
        $ldhqguess_num=M('ldhqguess_num');
        $resx=$ldhqguess_num->where($where)->select();
        $arr=array();
        $arr[0]=0;
        $arr[1]=0;
        $arr[2]=0;
        $arr[3]=0;
        $arr[4]=0;
        $arr[5]=0;
        $arr[6]=0;
        $arr[7]=0;
        $arr[8]=0;
        $arr[9]=0;
        $arr['aa']=0;
        /*当合最小，计算合中的数字使用*/
        $aa=array();
        $aa[0]=0;
        $aa[1]=0;
        $aa[2]=0;
        $aa[3]=0;
        $aa[4]=0;
        $aa[5]=0;
        $aa[6]=0;
        $aa[7]=0;
        $aa[8]=0;
        $aa[9]=0;
        $aa['aa']=0;

        foreach($resx as $k =>$v){

            $oddsx=$this->oddosx($v['discType']);
            $arr[0]+=$v['a0']*$oddsx*$v['betAmount'];
            $arr[1]+=$v['a1']*$oddsx*$v['betAmount'];
            $arr[2]+=$v['a2']*$oddsx*$v['betAmount'];
            $arr[3]+=$v['a3']*$oddsx*$v['betAmount'];
            $arr[4]+=$v['a4']*$oddsx*$v['betAmount'];
            $arr[5]+=$v['a5']*$oddsx*$v['betAmount'];
            $arr[6]+=$v['a6']*$oddsx*$v['betAmount'];
            $arr[7]+=$v['a7']*$oddsx*$v['betAmount'];
            $arr[8]+=$v['a8']*$oddsx*$v['betAmount'];
            $arr[9]+=$v['a9']*$oddsx*$v['betAmount'];
            $arr['aa']+=$v['aa']*$oddsx*$v['betAmount'];
            //开合使用
            if($v['discType']<>1){
                $aa[0]+=$v['a0']*$oddsx*$v['betAmount'];
                $aa[1]+=$v['a1']*$oddsx*$v['betAmount'];
                $aa[2]+=$v['a2']*$oddsx*$v['betAmount'];
                $aa[3]+=$v['a3']*$oddsx*$v['betAmount'];
                $aa[4]+=$v['a4']*$oddsx*$v['betAmount'];
                $aa[5]+=$v['a5']*$oddsx*$v['betAmount'];
                $aa[6]+=$v['a6']*$oddsx*$v['betAmount'];
                $aa[7]+=$v['a7']*$oddsx*$v['betAmount'];
                $aa[8]+=$v['a8']*$oddsx*$v['betAmount'];
                $aa[9]+=$v['a9']*$oddsx*$v['betAmount'];
                $aa['aa']+=$v['aa']*$oddsx*$v['betAmount'];
            }

         }

        //求最小的一个数组
        $min=array_search(min($arr), $arr);
        //dump($arr);
       // dump($aa);
       // dump($min);
        if($min=='aa'){
            $money=$arr['aa'];
            unset($arr['aa']);
            unset($aa['aa']);
            $arr_min=array_search(min($arr), $arr);
            $aa_min=array_search(min($aa), $aa);
            $money=$money+$aa[$aa_min];
            if($money<$arr[$arr_min]){
                //如果除大小以外，开合+上其他指定数字 是最小 就开合
                $opencode=rand(2301231,2503213).$aa_min.$aa_min;
                $xx=rand(1,9);
                $opencode=rand(2301231,2503213).$xx.$xx;
            }else{
                $opencode=rand(2301231,2503213).rand(1,9).$arr_min;
            }

        }else{
            $opencode=rand(2301231,2503213).rand(1,9).$min;

        }
        return $opencode;
    }




    public function getBetRecord(){
        $openid=get_openid();
        $addon_name=get_addon();
        $mpid=get_mpid();
        $ldhqguess_num=M('ldhqguess_num');
        $ldhqguess_opencode=M('ldhqguess_opencode');
        $w['openid']=$openid;
        $w['mpid']=$mpid;
        $list=$ldhqguess_num->where($w)->order('id DESC')->limit(20)->select();
        foreach($list as $v){
            $ww['issueNo']=$v['issueNo'];
            $ww['mpid']=$mpid;
            $opens=$opens2=$ldhqguess_opencode->where($ww)->find();
            $opens['createDate']=date('Y-m-d',$opens['createDate']);
            $opens['updateDate']=date('Y-m-d',$opens['updateDate']);
            $opens['userId']=session($addon_name.'_uid');
            $opens['userNo']=session($addon_name.'_uid');
            $opens['source']=1;
            $opens['amount']=0;
            $opens['betAmount']=$v['betAmount']*100;
            $opens['discType']=$v['discType'];
            $opens['betNum']=$this->user_code($v);
            $opens['odds']=$this->oddosx($v['discType']);
            if($opens['status']==3){
                $opens['openDiscRom']=substr($opens['openCode'], -1);
                $opens['winAmount']=$v['winAmount']*100;
                $opens['openOrderNo']=$opens['openCode'];
                $opens['status']=2;
            }else{
                //还没有开出来
                $opens['status']=1;
                $opens['winAmount']=0;
            }
            $opens['channel']=0;
            $opens['playType']=1;
            $opens['isCanSee']=0;
            $opens['ip']='';
            $opens['openDate']=$opens['openDate']*1000;
            $opens['createDateLabel']=date('m-d H:i:s',$opens2['createDate']);;
            $opens['updateDateLabel']=date('Y-m-d H:i:s',$opens2['updateDate']);;
            $opens['openDateLabel']=date('m-d H:i',$opens['openFinishDate']);
            $data[]=$opens;

        }

        $res['data']=$data;
        $res['success']=true;
        $res['timeout']=false;
        $res['overdue']=false;
        $res['currentDate']=get_microtime();
        $this->ajaxReturn($res);
    }

    public function updateRead(){

        $res['success']=true;
        $res['timeout']=false;
        $res['overdue']=false;
        $res['currentDate']=get_microtime();
        $this->ajaxReturn($res);
    }
}

?>