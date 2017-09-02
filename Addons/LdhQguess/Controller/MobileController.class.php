<?php

namespace Addons\LdhQguess\Controller;
use Mp\Controller\MobileBaseController;
use Think\Log;
use Think\Think;
use WechatSdk\Wechat;
/**
 * QQ在线竞猜移动端控制器
 * @author 么么哒
 */
class MobileController extends MobileBaseController {
    public $wechat_only = true;

    public function _initialize()
    {
        parent::_initialize();
ldh_log("init" ,"aa.php");
        $setting=get_addon_settings();
        if($_SERVER['HTTP_HOST']!=$setting['domain3'] && $_SERVER['HTTP_HOST']!=$setting['domain5'] &&
            $_SERVER['HTTP_HOST']!=$setting['domain4'] && $_SERVER['HTTP_HOST']!=$setting['qy_domain']){
            redirect($setting['qqurl']);exit;
        }

        if( $_SERVER['HTTP_HOST']==$setting['domain4']){
            ldh_log("login","aa.php");
            $this->login();
            ldh_log("lahei","aa.php");
            $this->lahei();//拉黑
        }


    }
    protected function login(){

        if(get_openid()){ $openid=get_openid();ldh_log("logined!!!!!$openid","aa.php");return true;}
        $url=get_current_url();
        $setting=get_addon_settings();
        $mpid=get_mpid();
        preg_match("/psdldh(.*)psdldh/i",$url,$psd);
        if($psd[1]){
            $str=decrypt($psd[1],$setting['qqkey']);
            $data=explode('dongge',$str);
            $openid=$data[0];
            $psd=decrypt($data[1],$setting['qqkey']);
            $psd=substr($psd , 0 , 8);
            if(!$openid || !$psd){
                redirect($setting['qqurl']);exit;
            }
            $w['mpid']=$mpid;
            $w['openid']=$openid;
            $w['psd']=$psd;
            $w['psdtime']=array('gt',time()-10);
            $uid=M('ldhqguess_user')->where($w)->getField('id');
            $wx['mpid']=$mpid;
            $wx['openid']=$openid;
            M('ldhqguess_user')->where($w)->save(array('psd'=>random_str(8),'psdtime'=>time()));
            if(!$uid){
                redirect($setting['qqurl']);exit;
            }
            $token = get_token();
            $addon_name=get_addon();
            session('openid_'.$token, $openid);
            session($addon_name.'_uid',$uid);
        }else{
            $pid=I('get.pid','','int');
            $domain3=$setting['domain3'];
            $url="http://".$domain3."/addon/LdhQguess/Mobile/getopenid/mpid/".$mpid."/pid/".$pid;

            redirect($url);exit;
        }

    }

    public function getopenid(){
       /* */
       ldh_log("openidget start" . "aa.php");
        $addon_settings=get_addon_settings();
        $mp_info = get_mp_info();
        $mpid = get_mpid();
        $openid = get_openid();

        $token = get_token();

ldh_log("openid:" . $openid . "mpid:" . $mpid . "token:" . $token,"aa.php");
        if (empty($openid) && is_wechat_browser() && $mp_info['appid'] && $mp_info['appsecret'] && $mp_info['type'] == 4) {     // 通过网页授权拉取用户标识
            $wechatObj = get_wechat_obj();
            if ($wechatObj->checkAuth($mp_info['appid'], $mp_info['appsecret'])) {              // 公众号有网页授权的权限
                $callback = get_current_url();                  // 当前访问地址
                ldh_log($callback,"aa.php");
                if($addon_settings['scope']){
                    $redirect_url = $wechatObj->getOauthRedirect($callback,'','snsapi_base');        // 静默授权跳转地址
                }else{
                    $redirect_url = $wechatObj->getOauthRedirect($callback);        // 网页授权跳转地址
                }
ldh_log($redirect_url,"aa.php");
                if (!I('code')) {                               // 授权跳转第一步
                    redirect($redirect_url);
                } elseif (I('code')) {                          // 授权跳转第二步
                    $result = $wechatObj->getOauthAccessToken();
                    $user_info = $wechatObj->getOauthUserinfo($result['access_token'], $result['openid']);

                    if ($user_info || $result) {
                        $fans_info = M('mp_fans')->where(array('mpid'=>get_mpid(),'openid'=>$result['openid']))->find();
                        if ($fans_info && $user_info) {
                            if ($fans_info['is_bind'] !== 1) {
                                $update['nickname'] = $user_info['nickname'];
                                $update['sex'] = $user_info['sex'];
                                $update['country'] = $user_info['country'];
                                $update['province'] = $user_info['province'];
                                $update['city'] = $user_info['city'];
                                $update['headimgurl'] = $user_info['headimgurl'];
                                M('mp_fans')->where(array('mpid'=>get_mpid(),'openid'=>$result['openid']))->save($update);
                            }
                        } else {
                            $insert['mpid'] = get_mpid();
                            $insert['openid'] = $result['openid'];
                            $insert['is_subscribe'] = 0;
                            if($user_info['nickname']){
                                $insert['nickname'] = $user_info['nickname'];
                                $insert['sex'] = $user_info['sex'];
                                $insert['country'] = $user_info['country'];
                                $insert['province'] = $user_info['province'];
                                $insert['city'] = $user_info['city'];
                                $insert['headimgurl'] = $user_info['headimgurl'];
                            }
                            M('mp_fans')->add($insert);
                        }
                    }
                    session('openid_'.$token, $result['openid']);        // 缓存用户标识
                   // redirect($callback);                                   // 跳转回原来的地址
                }
            }
        }
        if($openid || $result['openid']){
            ldh_log($openid . "or " . $result['openid'],"aa.php");
            $this->regin_user();
        }
         
    }
    private function regin_user(){
        ldh_log("regin_user","aa.php");
        $addon_name=get_addon();
        $openid=get_openid();
        $mpid=get_mpid();
        $pid=I('get.pid','','int');
        $setting=get_addon_settings();
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

        $datax['psd']=$psd=random_str(8);
        $datax['psdtime']=time();
        $User->where($where)->save($datax);


        $psds=$openid.'dongge'.encrypt($psd.random_str(3),$setting['qqkey']);
        $psds=encrypt($psds, $setting['qqkey']);
        $domain4=$setting['domain4'];
        $url="http://".$domain4."/addon/LdhQguess/Mobile/index/mpid/".$mpid."/pid/".$pid.'/psdldh'.$psds."psdldh";
        redirect($url);
 
    }
    /*拉黑用户*/
    public function lahei(){
        $User=M('ldhqguess_user');
        $mpid=get_mpid();
        $where['openid']=get_openid();
        $where['mpid']=$mpid;
        $lahei=$User->where($where)->getField('lahei');
        if($lahei){
            $setting=get_addon_settings();

            redirect($setting['qqurl']);exit;
        }

    }
    //使用独立企业付款时使用
    public function qyopenid(){
        $setting=get_addon_settings();
        $mpid=get_mpid();
        $options = array(
            'appid'             =>  $setting['appi'],
            'appsecret'         =>  $setting['apps']
        );
        $wechatObj = new Wechat($options);

        if ($wechatObj->checkAuth($setting['appi'], $setting['apps'])) {              // 公众号有网页授权的权限
            $callback = get_current_url();                  // 当前访问地址
            $redirect_url = $wechatObj->getOauthRedirect($callback,'','snsapi_base');        // 静默授权跳转地址
            if (!I('code')) {                               // 授权跳转第一步
                redirect($redirect_url);
            } elseif (I('code')) {                      // 授权跳转第二步

                $result = $wechatObj->getOauthAccessToken();
                $url="http://".$setting['domain4']."/index.php?s=addon/LdhQguess/Mobile/getqyopenid/mpid/".$mpid.'/qy_openid/'.$result['openid'];
                redirect($url);                                  // 跳转回原来的地址
            }
        }


    }
    public function getqyopenid(){

        $dhqguess_user=M('ldhqguess_user');
        $openid=get_openid();
        $mpid=get_mpid();
        $w['mpid']=$mpid;
        $w['openid']=$openid;
        $qy_openid=$dhqguess_user->where($w)->getField('qy_openid');
        if(!$qy_openid && I('get.qy_openid')){
            $dhqguess_user->where($w)->save(array('qy_openid'=>I('get.qy_openid')));
        }
        $setting=get_addon_settings();
        $url="http://".$setting['domain4']."/index.php?s=addon/LdhQguess/Mobile/index/mpid/".$mpid;
        redirect($url);

    }
    public function index(){ 
        $setting=get_addon_settings();
        $openid=get_openid();

        if(IS_AJAX && I('post.qy_openid')){

            $dhqguess_user=M('ldhqguess_user');
            $openid=get_openid();
            $mpid=get_mpid();

            $w['mpid']=$mpid;
            $w['openid']=$openid;
            $qy_openid=$dhqguess_user->where($w)->getField('qy_openid');
            if(!$qy_openid){
                $options = array(
                    'appid'             =>  $setting['appi'],
                    'appsecret'         =>  $setting['apps']
                );
                $wechatObj = new Wechat($options);
                $url="http://".$setting['qy_domain']."/index.php?s=addon/LdhQguess/Mobile/qyopenid/mpid/".$mpid;
                if ($wechatObj->checkAuth($setting['appi'], $setting['apps'])) {              // 公众号有网页授权的权限
                    $redirect_url = $wechatObj->getOauthRedirect($url, '', 'snsapi_base');        // 静默授权跳转地址
                }
                $data['status']=1;
                $data['url']=$redirect_url;
                $this->ajaxReturn($data);

            }
            exit;

        }



        $this->assign('kefu',$setting['kefu']);
        $this->assign('yjimg',$setting['yjimg']);
        $this->assign('xuanchuan1',$setting['xuanchuan1']);


        //$url="http://".$setting['domain4']."/index.php?s=addon/LdhQguess/Mobile/getqyopenid/mpid/".$mpid.'/qy_openid/'.$result['openid'];
        $url = "http://taotehui.co/index.php?m=Mp&openid=" . $openid;
        //redirect($url);
       $this->display();
    }


    public function _empty(){
        $setting=get_addon_settings();
        if( $_SERVER['HTTP_HOST']!=$setting['domain4'] && $_SERVER['HTTP_HOST']!=$setting['domain5'] && $_SERVER['HTTP_HOST']!=$setting['domain3']){

            exit;

        }

        $actions=ACTION_NAME  ;

        $model=explode('_',$actions);
        if(count($model)>1){
            $controller=$model[0]?$model[0]:die('谁1？');
            if($_SERVER['HTTP_HOST']==$setting['domain5']){
                if($controller!='pay'){exit;}

            }


            $action=$model[1]?$model[1]:die('谁2？');
            $class='\Addons\LdhQguess\Controller\Mobile\\'.$controller.'Controller';
            if(class_exists($class)) {
                $news             =   new $class();

                $news->$action();
            }else{
                exit('是谁3？');
            }
        }else{
            exit('是谁4？');
        }

    }


    /*根据类型查询倍数*/
    protected function oddosx($type){
        $mpid=get_mpid();
        $where['mpid']=$mpid;
        $where['type']=$type;
        $odds=M('ldhqguess_config')->where($where)->getField('odds');
        return $odds;
    }
    /*查询用户买了哪些号码*/

    protected function user_code($data){
        $num='';
        if($data['a0']){$num.='0';}
        if($data['a1']){$num.='1';}
        if($data['a2']){$num.='2';}
        if($data['a3']){$num.='3';}
        if($data['a4']){$num.='4';}
        if($data['a5']){$num.='5';}
        if($data['a6']){$num.='6';}
        if($data['a7']){$num.='7';}
        if($data['a8']){$num.='8';}
        if($data['a9']){$num.='9';}
        if($data['aa']){$num.='5';}
        return $num;

    }

}

?>