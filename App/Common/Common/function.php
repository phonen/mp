<?php  

use WechatSdk\Wechat;
use WechatSdk\JsSdk;

/**
 * 添加钩子
 * 资源e站（Zye.cc）
 */
function add_hook($tag,$name) {
    \Think\Hook::add($tag,$name);
}

/**
 * 执行钩子
 * 资源e站（Zye.cc）
 */
function hook($tag, $params=NULL) {
    return \Think\Hook::listen($tag,$params);
}

/**
 * 生成插件访问链接
 * 资源e站（Zye.cc）
 */
function create_addon_url($url, $param = array()){
    if (!$param['mpid']) {
       $param['mpid'] = get_mpid();
    }
    $urlArr = explode('/', $url);
    switch (count($urlArr)) {
        case 1:
            if (in_array(CONTROLLER_NAME, array('Mobile', 'Web'))) {
                $act = strtolower(CONTROLLER_NAME);
                return U('/addon/'.get_addon().'/'.$act.'/'.$url.'@'.C('HTTP_HOST'), $param);
            } else {
                $param['addon'] = get_addon();
                return U('Mp/'.CONTROLLER_NAME.'/'.$url.'@'.C('HTTP_HOST'), $param);
            }
            break;
        case 2:
            if (in_array($urlArr[0], array('Mobile', 'Web'))) {
                $act = strtolower($urlArr[0]);
                return U('/addon/'.get_addon().'/'.$act.'/'.$urlArr[1].'@'.C('HTTP_HOST'), $param);
            } else {
                $param['addon'] = get_addon();
                return U('Mp/'.$urlArr[0].'/'.$urlArr[1].'@'.C('HTTP_HOST'), $param);
            }
            break;
        case 3:
            if (in_array($urlArr[1], array('Mobile', 'Web'))) {
                return U('/addon/'.$urlArr[0].'/'.strtolower($urlArr[1]).'/'.$urlArr[2].'@'.C('HTTP_HOST'), $param);
            } else {
                $param['addon'] = $urlArr[0];
                return U('Mp/'.$urlArr[1].'/'.$urlArr[2].'@'.C('HTTP_HOST'), $param);
            }
            break;
        default:
            return '';
            break;
    }
}

/**
 * 生成移动端访问链接
 */
function create_mobile_url($url, $param = array()) {
    if (!$param['mpid']) {
       $param['mpid'] = get_mpid();
    }
    return U('/addon/'.get_addon().'/mobile/'.$url.'@'.C('HTTP_HOST'), $param);
}

/**
 * 生成插件后台访问链接
 */
function create_web_url($url, $param = array()) {
    if (!$param['mpid']) {
       $param['mpid'] = get_mpid();
    }
    return U('/addon/'.get_addon().'/web/'.$url.'@'.C('HTTP_HOST'), $param);
}

/**
 * 设置/获取当前公众号标识
 * 资源e站（Zye.cc）
 */
function get_mpid($mpid = '') {
    if ($mpid) {                            // 手动设置当前公众号
        session('mpid', intval($mpid));
        session('token', M('mp')->where(array('id'=>$mpid))->getField('token'));
    } elseif (I('mpid')) {                  // 如果浏览器中带有公众号标识，则设置为当前公众号
        session('mpid', intval(I('mpid')));   
        session('token', M('mp')->where(array('id'=>I('mpid')))->getField('token'));      
    }
    $mpid = session('mpid');                        // 返回当前公众号标识
    if (empty($mpid)) {                             // 如果公众号标识不存在，则返回0
        return 0;
    }
    return $mpid;
}

/**
 * 设置/获取当前公众号标识
 * 资源e站（Zye.cc）
 */
function get_token($token = '') {
    if ($token) {
        session('token', $token);
        session('mpid', M('mp')->where(array('token'=>$token))->getField('id'));
    } elseif (I('token')) {
        session('token', I('token'));
        session('mpid', M('mp')->where(array('token'=>I('token')))->getField('id'));
    }
    $token = session('token');
    if (empty($token)) {
        return null;
    }
    return $token;
}

/**
 * 获取公众号信息
 * 资源e站（Zye.cc）
 */
function get_mp_info($mpid = '') {
    if (empty($mpid)) {
        $mpid = get_mpid();
    }
	 
    $mp_info = D('Mp')->get_mp_info($mpid);
    return $mp_info;
}
function send_post($url, $post_data) {

    $postdata = http_build_query($post_data);
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $postdata,
            'timeout' => 15 * 60 // 超时时间（单位:s）  
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}
/**
 * 设置/获取用户标识
 * 资源e站（Zye.cc）
 */
function get_openid($openid = '') {
    $token = get_token();                     
    if (empty($token)) {                         // 如果公众号标识不存在
        return null;
    }
    if ($openid) {                              // 设置当前用户标识
        session('openid_'.$token, $openid);
    } elseif (I('openid')) {                    // 如果浏览器带有openid参数，则缓存用户标识
        session('openid_'.$token, I('openid'));
    }
    $openid = session('openid_'.$token);                 // 获取当前用户标识
    if (empty($openid)) {
        return null;
    }
    return $openid;
}

/**
 * 获取用户借权标识
 */
function get_ext_openid($ext_openid = '') {
    $token = get_token();                     
    if (empty($token)) {                         // 如果公众号标识不存在
        return null;
    }
    if ($ext_openid) {                              // 设置当前用户标识
        session('ext_openid_'.$token, $ext_openid);
    } elseif (I('ext_openid')) {                    // 如果浏览器带有openid参数，则缓存用户标识
        session('ext_openid_'.$token, I('ext_openid'));
    }
    $ext_openid = session('ext_openid_'.$token);                 // 获取当前用户标识
    if (empty($ext_openid)) {
        return null;
    }
    return $ext_openid;
}

/**
 * 初始化粉丝信息
 */
function init_fans() {
    $mp_info = get_mp_info();
    $mpid = get_mpid();
    $openid = get_openid();
    $token = get_token();
    $addon_settings=get_addon_settings(); 
    if (empty($openid) && is_wechat_browser() && $mp_info['appid'] && $mp_info['appsecret'] && $mp_info['type'] == 4) {     // 通过网页授权拉取用户标识
        $wechatObj = get_wechat_obj();
        if ($wechatObj->checkAuth($mp_info['appid'], $mp_info['appsecret'])) {              // 公众号有网页授权的权限
            $callback = get_current_url();                  // 当前访问地址
            if($addon_settings['scope']){
                $redirect_url = $wechatObj->getOauthRedirect($callback,'','snsapi_base');        // 静默授权跳转地址
            }else{
                $redirect_url = $wechatObj->getOauthRedirect($callback);        // 网页授权跳转地址
            }
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
                redirect($callback);                                   // 跳转回原来的地址
            }
        }
    }
}

/**
 * 初始化鉴权用户
 */
function init_ext_fans() {
    $token = get_token();
    $ext_openid = get_ext_openid();
    $ext_appid = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appid'))->getField('value');
    $ext_appsecret = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appsecret'))->getField('value');
    if (empty($ext_openid) && is_wechat_browser() && $ext_appid && $ext_appsecret) {     // 通过网页授权拉取用户标识
            $options = array(    
                'appid'             =>  $ext_appid,               
                'appsecret'         =>  $ext_appsecret            
            );
            $wechatObj = new Wechat($options);
            if ($wechatObj->checkAuth($ext_appid, $ext_appsecret)) {              // 公众号有网页授权的权限
                $callback = get_current_url();                  // 当前访问地址
                $redirect_url = $wechatObj->getOauthRedirect($callback,'','snsapi_base');        // 静默授权跳转地址
                if (!I('code')) {                               // 授权跳转第一步
                    redirect($redirect_url);
                } elseif (I('code')) {                           // 授权跳转第二步
                    $result = $wechatObj->getOauthAccessToken();
                    session('ext_openid_'.$token, $result['openid'],120);        // 缓存用户标识
                    redirect($callback);                                 // 跳转回原来的地址
                }
            }
    }
}
 
/**
 * 获取jssdk参数
 */
function get_jssdk_sign_package() {
    $mp_info = get_mp_info();
    $appid = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appid'))->getField('value');
    $appsecret = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appsecret'))->getField('value');
    !empty($appid) || $appid = $mp_info['appid'];        // 优先使用借权的appid
    !empty($appsecret) || $appsecret = $mp_info['appsecret'];        // 优先使用借权的appsecret
    $jssdk = new JsSdk($appid, $appsecret);
    $sign_package = $jssdk->getSignPackage();        // 获取jssdk配置包
    return $sign_package;
}

/**
 * 获取微信支付参数
 * 资源e站（Zye.cc）
 */
function get_jsapi_parameters($data) {  
    vendor('WechatPaySdk.WxPayPubHelper');
    $appid = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appid'))->getField('value');
    $appsecret = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appsecret'))->getField('value');
    $mchid = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'mchid'))->getField('value');
    $paysignkey = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'paysignkey'))->getField('value');
    $jsApi = new JsApi_pub($appid,$mchid,$paysignkey,$appsecret); 
    $orderid = $data['orderid'];      
    if($orderid == ""){
        $orderid = $data['single_orderid'];
    }   
    $price= abs(floatval($data['price']));
    if($price<1){$price=100;}
    $data['mpid'] = get_mpid();
    unset($data['price']);
    $unifiedOrder = new UnifiedOrder_pub($appid,$mchid,$paysignkey,$appsecret);
    $unifiedOrder->setParameter("openid",$data['openid']);
    $unifiedOrder->setParameter("body",$orderid);
    $unifiedOrder->setParameter("out_trade_no",$orderid);
    $unifiedOrder->setParameter("total_fee",$price*100);
    $unifiedOrder->setParameter("notify_url", C('HTTP_HOST') . '/Data/notify.php');
    $unifiedOrder->setParameter("trade_type","JSAPI");
    $unifiedOrder->setParameter("attach", json_encode($data));//附加数据
    $prepay_id = $unifiedOrder->getPrepayId();
    $jsApi->setPrepayId($prepay_id);
    $jsApiParameters = $jsApi->getParameters();
    return $jsApiParameters;
}

/**
 * 企业付款
 */
function mch_pay($params = array()) {
    if(!$params['dongge']){return false;}
    vendor('WechatPaySdk.WxPayPubHelper');
    $mpid = get_mpid();
    $token=get_token();
    $mp_info = get_mp_info();
    $openid = get_ext_openid();
    $settings = D('MpSetting')->get_settings();
    $sslcert = APP_PATH . '/Mp/Conf/'. $mpid . '_' . $openid . '_apiclient_cert.pem';
    $sslkey = APP_PATH . '/Mp/Conf/'. $mpid . '_' . $openid . '_apiclient_key.pem';
    $setss=get_addon_settings();
    if($setss['qy_type']){
        $openid=session('qy_openid_'.$token);
        $settings['appid']=$setss['appi'];
        $settings['appsecret']=$setss['apps'];
        $settings['mchid']=$setss['appsh'];
        $settings['paysignkey']=$setss['appshk'];
        $settings['sslcert']=$setss['appcert'];
        $settings['sslkey']=$setss['appkey'];

    }
 
    file_put_contents($sslcert, isset($settings['sslcert']) ? $settings['sslcert'] : '');
    file_put_contents($sslkey, isset($settings['sslkey']) ? $settings['sslkey'] : '');
    $orderid = isset($params['partner_trade_no']) ? $params['partner_trade_no'] : $mpid.time();
    $total_amount = isset($params['amount']) ? $params['amount']*100 : '';
    $mchpay = new MchPay_pub($settings['appid'], $settings['mchid'], $settings['paysignkey'], $settings['appsecret']);
    $mchpay->setParameter('partner_trade_no', $orderid);
    $mchpay->setParameter('openid', isset($params['openid']) ? $params['openid'] : $openid);
    $mchpay->setParameter('amount', $total_amount);
    $mchpay->setParameter('check_name', isset($params['check_name']) ? $params['check_name'] : 'NO_CHECK');
    $mchpay->setParameter('desc', isset($params['desc']) ? $params['desc'] : '');
    $result = $mchpay->getResult($sslcert, $sslkey);
    if (isset($result['return_code']) && isset($result['result_code']) && $result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
        if (!M('mp_payment')->where(array('orderid'=>$orderid))->find()) {
            $data['mpid'] = $mpid;
            $data['openid'] = isset($params['openid']) ? $params['openid'] : $openid;
            $data['orderid'] = $orderid;
            $data['create_time'] = time();
            $result['total_fee'] = $total_amount;
            $data['detail'] = json_encode($result);
            M('mp_payment')->add($data);
        } 
    }
    unlink($sslcert);
    unlink($sslkey);
    return $result;
}

/**
 * 现金红包
 */
function redpack_pay($params = array()) {
    vendor('WechatPaySdk.WxPayPubHelper');
    $mpid = get_mpid();
    $mp_info = get_mp_info();
    $openid = get_openid();
    $settings = D('MpSetting')->get_settings();
    $sslcert = APP_PATH . '/Mp/Conf/'. $mpid . '_' . $openid . '_apiclient_cert.pem';
    $sslkey = APP_PATH . '/Mp/Conf/'. $mpid . '_' . $openid . '_apiclient_key.pem';
    file_put_contents($sslcert, isset($settings['sslcert']) ? $settings['sslcert'] : '');
    file_put_contents($sslkey, isset($settings['sslkey']) ? $settings['sslkey'] : '');
    $orderid = isset($params['mch_billno']) ? $params['mch_billno'] : $mpid.time();
    $total_amount = isset($params['total_amount']) ? $params['total_amount']*100 : '';
    $mchpay = new Redpack_pub($settings['appid'], $settings['mchid'], $settings['paysignkey'], $settings['appsecret']);
    $mchpay->setParameter('mch_billno', $orderid);
    $mchpay->setParameter('send_name', isset($params['send_name']) ? $params['send_name'] : $mp_info['name']);
    $mchpay->setParameter('re_openid', isset($params['re_openid']) ? $params['re_openid'] : $openid);
    $mchpay->setParameter('total_amount', $total_amount);
    $mchpay->setParameter('total_num', isset($params['total_num']) ? $params['total_num'] : 1);
    $mchpay->setParameter('wishing', isset($params['wishing']) ? $params['wishing'] : '');
    $mchpay->setParameter('act_name', isset($params['act_name']) ? $params['act_name'] : '');
    $mchpay->setParameter('remark', isset($params['remark']) ? $params['remark'] : '');
    $result = $mchpay->getResult($sslcert, $sslkey);
    if (isset($result['return_code']) && isset($result['result_code']) && $result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
        if (!M('mp_payment')->where(array('orderid'=>$orderid))->find()) {
            $data['mpid'] = $mpid;
            $data['openid'] = $result['re_openid'];
            $data['orderid'] = $orderid;
            $data['create_time'] = time();
            $result['total_fee'] = $total_amount;
            $data['detail'] = json_encode($result);
            M('mp_payment')->add($data);
        } 
    }
    unlink($sslcert);
    unlink($sslkey);
    return $result;
}

/**
 * 获取插件模型
 * 资源e站（Zye.cc）
 */
function get_addon_model($model) {
    return D('Addons')->get_addon_model($model);
}

/**
 * 获取插件侧边导航
 * 资源e站（Zye.cc）
 */
function get_addon_config($addon) {
    if (empty($addon)) {
        return false;
    }

    $addon_config = include ADDON_PATH . $addon . '/config.php';
    return $addon_config;
}

/**
 * 获取插件配置信息
 * 资源e站（Zye.cc）
 */
function get_addon_settings($addon = '', $mpid = '') {
    if ($addon == '') {
        $addon = get_addon();
    }
    if ($mpid == '') {
        $mpid = get_mpid();
    }
    if (!$addon || !$mpid) {
        return false;
    }
    $addon_settings = D('AddonSetting')->get_addon_settings($addon, $mpid);
    if (!$addon_settings) {
        return false;
    }
    return $addon_settings;
}

/**
 * 获取功能入口信息
 * 资源e站（Zye.cc）
 */
function get_addon_entry($act, $addon = '', $mpid = '') {
    if ($addon == '') {
        $addon = get_addon();
    }
    if ($mpid == '') {
        $mpid = get_mpid();
    }
    if (!$act || !$addon || !$mpid) {
        return false;
    }
    $addon_entry = D('AddonEntry')->get_addon_entry($act, $addon, $mpid);
    if (empty($addon_entry)) {
        $addon_config = get_addon_config($addon);
        foreach ($addon_config['entry_list'] as $k => $v) {
            if ($v['act'] == $act) {
                $addon_entry['name'] = $v['name'];
                $addon_entry['act'] = $v['act'];
                $addon_entry['url'] = U('Mobile/'.$v['act'].'@'.C('HTTP_HOST'), array('addon'=>$addon));
                break;
            }
        }
    } else {
        $addon_entry['url'] = U('Mobile/'.$addon_entry['act'].'@'.C('HTTP_HOST'), array('addon'=>$addon));
        $addon_entry['rule'] = D('MpRule')->get_entry_($addon_entry['id']);
    }
    
    if (!isset($addon_entry)) {
        return false;
    }
    return $addon_entry;
}

/**
 * 获取入口信息
 * 资源e站（Zye.cc）
 */
function get_entry_info($entry_id) {
    if (!$entry_id) {
        return false;
    }
    $entry_info = D('AddonEntry')->get_entry_info($entry_id);
    return $entry_info;
}

/**
 * 获取插件响应规则
 * 资源e站（Zye.cc）
 */
function get_addon_rule($addon = '', $mpid = '') {
    if ($addon == '') {
        $addon = get_addon();
    }
    if ($mpid == '') {
        $mpid = get_mpid();
    }
    if (!$addon || !$mpid) {
        return false;
    }
    $addon_rule = D('MpRule')->get_respond_rule();
    return $addon_rule;
}

/**
 * 获取当前访问的完整URL地址
 * 资源e站（Zye.cc）
 */
function get_current_url() {
    $url = 'http://';
    if (isset ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] == 'on') {
        $url = 'https://';
    }
    if ($_SERVER ['SERVER_PORT'] != '80') {
        $url .= $_SERVER ['HTTP_HOST'] . ':' . $_SERVER ['SERVER_PORT'] . $_SERVER ['REQUEST_URI'];
    } else {
        $url .= $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
    }
    // 兼容后面的参数组装
    if (stripos ( $url, '?' ) === false) {
        $url .= '?t=' . time ();
    }
    return $url;
}

/**
 * 根据公众号标识获取公众号基本信息
 * 资源e站（Zye.cc）
 */
function get_wechat_info($token = '') {
    $token || $token = session('token');                // 获取token
    $wechatInfo = M('mp')->where(array('token'=>$token))->find();
    return $wechatInfo;
}

/**
 * 获取微信api对象
 * 资源e站（Zye.cc）
 */
function get_wechat_obj() {
    $wechatInfo = get_mp_info();
    $options = array(
        'token'             =>  $wechatInfo['valid_token'],                 
        'encodingaeskey'    =>  $wechatInfo['encodingaeskey'],      
        'appid'             =>  $wechatInfo['appid'],               
        'appsecret'         =>  $wechatInfo['appsecret']            
    );
    $wechatObj = new Wechat($options);
    $wechatObj->getRev();
    return $wechatObj;
}

/**
 * 回复文本消息
 * 资源e站（Zye.cc）
 */
function reply_text($text) {
    $wechatObj = get_wechat_obj();
    if (!$text) {
        return;
    }
    return $wechatObj->text($text)->reply();
}

/**
 * 回复图文消息
 * 资源e站（Zye.cc）
 */
function reply_news($articles) {
    $wechatObj = get_wechat_obj();
    return $wechatObj->news($articles)->reply();
}

/**
 * 回复音乐消息
 * 资源e站（Zye.cc）
 */
function reply_music($arr) {
    if (!isset($arr['title']) || !isset($arr['description']) || !$arr['musicurl']) {
        return false;
    }
    $wechatObj = get_wechat_obj();
    return $wechatObj->music($arr['title'], $arr['description'], $arr['musicurl'], $arr['hgmusicurl'], $arr['thumbmediaid'])->reply();
} 

/**
 * 发送客服消息
 * 资源e站（Zye.cc）
 */
function send_custom_message($data) {
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->sendCustomMessage($data);
    if (!$result) {
        return $wechatObj->errMsg;
    }
    return $result;
}

function get_menu() {
    $wechatObj = get_wechat_obj();
    return $wechatObj->getMenu();
}

function create_menu($data) {
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->createMenu($data);
    if (!$result) {
        $result['errcode'] = $wechatObj->errCode;
        $result['errmsg'] = $wechatObj->errMsg;
    }
    return $result;
}

function delete_menu() {
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->deleteMenu();
    if (!$result) {
        return $wechatObj->errMsg;
    }
    return $result;
}

/**
 * 创建二维码ticket
 * @param int|string $scene_id 自定义追踪id,临时二维码只能用数值型
 * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)；2:永久二维码(此时expire参数无效)
 * @param int $expire 临时二维码有效期，最大为1800秒
 * @return array('ticket'=>'qrcode字串','expire_seconds'=>1800,'url'=>'二维码图片解析后的地址')
 */
function get_qr_code($scene_id,$type=0,$expire=1800){
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->getQRCode($scene_id,$type,$expire);
    if (!$result) {
        $return['errcode'] = 1001;
        $return['errmsg'] = $wechatObj->errMsg;
        return $return;
    }
    return $result;
}

/**
 * 获取二维码图片
 * @param string $ticket 传入由getQRCode方法生成的ticket参数
 * @return string url 返回http地址
 */
function get_qr_url($ticket) {
    $wechatObj = get_wechat_obj();
    return $wechatObj->getQRUrl($ticket);
}

/**
 * 长链接转短链接接口
 * @param string $long_url 传入要转换的长url
 * @return boolean|string url 成功则返回转换后的短url
 */
function get_short_url($long_url){
    $wechatObj = get_wechat_obj();
    return $wechatObj->getShortUrl($long_url);
}

/**
 * 获取接收TICKET
 */
function get_rev_ticket(){
    $wechatObj = get_wechat_obj();
    return $wechatObj->getRevTicket();
}

/**
* 获取二维码的场景值
*/
function get_rev_scene_id(){
    $wechatObj = get_wechat_obj();
    return $wechatObj->getRevSceneId();
}

/**
 * 利用微信接口获取微信粉丝信息
 * 资源e站（Zye.cc）
 */
function get_fans_wechat_info($openid = '') {
    $openid || $openid = get_openid();
    $wechatObj = get_wechat_obj();
    return $wechatObj->getUserInfo($openid);
}

/**
 * 获取粉丝基本资料
 * 资源e站（Zye.cc）
 */
function get_fans_info($openid = '', $field = '') {
    if ($openid == '') {
        $openid = get_openid();
    }
    if (!$openid) {
        return false;
    }
    $fans_info = D('MpFans')->get_fans_info($openid, $field);
    if (!$fans_info) {
        return false;
    }
    return $fans_info;
}

/**
 * 获取粉丝头像
 * 资源e站（Zye.cc）
 */
function get_fans_headimg($openid = '', $attr = 'width=50 height=50') {
    if ($openid == '') {
        $openid = get_openid();
    }
    if (!$openid) {
        return false;
    }
    $headimgurl = get_fans_info($openid, 'headimgurl');
    if (empty($headimgurl)) {
        $headimgurl = __ROOT__ . '/Public/Admin/img/noname.jpg';
    }
    return "<img src='".$headimgurl."' ".$attr." />";
}

function get_fans_nickname($openid) {
    if ($openid == '') {
        $openid = get_openid();
    }
    if (!$openid) {
        return false;
    }
    $nickname = get_fans_info($openid, 'nickname');
    if (empty($nickname)) {
        $nickname = '匿名';
    }
    return $nickname;
}

function get_nickname($openid) {
    return D('MpFans')->get_fans_info($openid, 'nickname');
}

function get_headimg($openid) {
    return D('MpFans')->get_fans_info($openid, 'headimgurl');
}

function get_message($msgid) {
    $message = D('MpMessage')->get_message($msgid);
    switch ($message['msgtype']) {
        case 'text':
            return $message['content'];
            break;
        case 'image':
            // 感谢 @  平凡<58000865@qq.com> 提供的微信图片防盗链解决方案
            return '<img src="http://www.zorhand.com/img?url='.$message['picurl'].'" width="100" height="100" />';      
            break;
        default:
            return '';
            break;
    }
}

/**
 * 将图片路径或者媒体文件转换为可访问的图片地址
 * 资源e站（Zye.cc）
 */
function tomedia($path) {
    if (preg_match('/(.*?)\.(jpg|jpeg|png|gif)$/', $path)) {
        if (preg_match('/^\.\/(.*)\.(jpg|png|gif|jpeg)$/', $path)) {
            return str_replace('./', SITE_URL, $path);
        } else {
            return $path;
        }
    } else {
        return SITE_URL . 'Public/Admin/img/nopic.jpg';
    }
}

/**
 * 增加积分
 * 资源e站（Zye.cc）
 */
function add_score($value,$remark='',$type='score',$flag='',$source='addon') {
    return D('MpScoreRecord')->add_score($value,$remark,$type,$flag,$source);
}

/**
 * 获取积分
 */
function get_score($type='', $source='', $flag='', $openid='') {
    return D('MpScoreRecord')->get_score($type, $source, $flag, $openid);
}

/**
 * 创建目录或文件
 * 资源e站（Zye.cc）
 */
function create_dir_or_files($files) {
    foreach ( $files as $key => $value ) {
        if (substr ( $value, - 1 ) == '/') {
            mkdir ( $value );
        } else {
            @file_put_contents ( $value, '' );
        }
    }
}
/**
 * 生成随机字符串
 * @param $length int 字符串长度
 * @return $nonce string 随机字符串
 */
function get_nonce($length=32) {
	$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$nonce = '';
	for ($i=0; $i<$length; $i++) {
		$nonce .= $str[mt_rand(0, 61)];
	}
	return $nonce;
}

/**
 * 检测用户是否登录
 * 资源e站（Zye.cc）
 */
function is_user_login() {
    $user_id = session('user_id');
    if (!$user_id || $user_id < 0) {
        return false;
    } else {
        return true;
    }
}

/**
 * 获取当前用户ID
 * 资源e站（Zye.cc）
 */
function get_user_id() {
	$user_id = session('user_id');
    if (!$user_id || $user_id < 0) {
        return false;
    }
    return $user_id;
}

/**
 * 获取用户资料
 * 资源e站（Zye.cc）
 */
function get_user_info($user_id = '') {
    if (!$user_id) {
        $user_id = get_user_id();
    }
    $user_info = M('user')->find($user_id);
    return $user_info;
}



/**
 * 判断是否处在微信浏览器内
 * 资源e站（Zye.cc）
 */
function is_wechat_browser() {
    $agent = $_SERVER ['HTTP_USER_AGENT'];
    if (! strpos ( $agent, "icroMessenger" )) {
        return false;
    }
    return true;
}


/**
 * 执行sql文件
 * 资源e站（Zye.cc）
 */
function execute_sql_file($sql_path) {
    // 读取SQL文件
    $sql = file_get_contents($sql_path);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);
    
    // 替换表前缀
    $orginal = 'dc_';
    $prefix = C('DB_PREFIX');
    $sql = str_replace("{$orginal}", "{$prefix}", $sql);
    
    // 开始安装
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) {
            continue;
        }
        $res = M()->execute($value);
    }
}

/**
 * 生成分页导航
 * 资源e站（Zye.cc）
 */
function pagination($count, $per = 10, $params = array()) {
    if (!$count || intval($count) < 0) {
        return '';
    }
    if (get_addon()) {
        $params['addon'] = get_addon();
    }
    $Page = new \Think\Page($count, $per, $params);
    $Page->setConfig('rollPage', 7);
    $Page->setConfig('lastSuffix', false);
    $Page->setConfig('page_begin_wrap', '<div class="page-control"><ul class="pagination pull-right">');    
    $Page->setConfig('page_end_wrap', '</ul></div>');
    $Page->setConfig('link_begin_wrap', '<li>');
    $Page->setConfig('link_end_wrap', '</li>');
    $Page->setConfig('current_begin_wrap', '<li class="active"><a>');
    $Page->setConfig('current_end_wrap', '</a></li>');
    $Page->setConfig('first', '<<');
    $Page->setConfig('last', '>>');
    $Page->setConfig('prev', '<');  
    $Page->setConfig('next', '>');  
    $pagination = $Page->show();
    return $pagination;
}

/**
 * 获取当前访问的插件名称
 * 资源e站（Zye.cc）
 */
function get_addon() {
    preg_match('/\/addon\/([^\/]+)/', '/'.$_SERVER['PATH_INFO'], $m);
    if (!$m[1]) {
        return false;
    }
    return $m[1];
}

function get_agent() {
    $agent = $_SERVER ['HTTP_USER_AGENT']; 
    return $agent;
}

function get_ip(){
    if (isset($_SERVER['HTTP_CLIENT_IP']) and !empty($_SERVER['HTTP_CLIENT_IP'])){
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        return strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
    }
    if (isset($_SERVER['HTTP_PROXY_USER']) and !empty($_SERVER['HTTP_PROXY_USER'])){
        return $_SERVER['HTTP_PROXY_USER'];
    }
    if (isset($_SERVER['REMOTE_ADDR']) and !empty($_SERVER['REMOTE_ADDR'])){
        return $_SERVER['REMOTE_ADDR'];
    } else {
        return "0.0.0.0";
    }
}

/*
 * 么么哒随机字符串
 * */
function random_str($length)
{
    //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
    $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

    $str = '';
    $arr_len = count($arr);
    for ($i = 0; $i < $length; $i++)
    {
        $rand = mt_rand(0, $arr_len-1);
        $str.=$arr[$rand];
    }

    return $str;
}
/**
 * 么么哒分页处理
 * @param type $total 信息总数
 * @param type $size 每页数量
 * @param type $number 当前分页号（页码）
 * @param type $config 配置，会覆盖默认设置
 * @return \Page|array
 */
function page($total, $size = 0, $number = 0, $config = array()) {
    //配置
    $defaultConfig = array(
        //当前分页号
        'number' => $number,
        //接收分页号参数的标识符
        'param' => 'page',
        //分页规则
        'rule' => '',
        //是否启用自定义规则
        'isrule' => false,
        //分页模板
        'tpl' => '',
        //分页具体可控制配置参数默认配置
        'tplconfig' => array('listlong' => 6, 'listsidelong' => 2, "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""),
    );
    //分页具体可控制配置参数
    $cfg = array(
        //每次显示几个分页导航链接
        'listlong' => 6,
        //分页链接列表首尾导航页码数量，默认为2，html 参数中有”{liststart}”或”{listend}”时才有效
        'listsidelong' => 2,
        //分页链接列表
        'list' => '*',
        //当前页码的CSS样式名称，默认为”current”
        'currentclass' => 'current',
        //第一页链接的HTML代码，默认为 ”<<”，即显示为 <<
        'first' => '&laquo;',
        //上一页链接的HTML代码，默认为”<”,即显示为 <
        'prev' => '&#8249;',
        //下一页链接的HTML代码，默认为”>”,即显示为 >
        'next' => '&#8250;',
        //最后一页链接的HTML代码，默认为”>>”,即显示为 >>
        'last' => '&raquo;',
        //被省略的页码链接显示为，默认为”…”
        'more' => '...',
        //当处于首尾页时不可用链接的CSS样式名称，默认为”disabled”
        'disabledclass' => 'disabled',
        //页面跳转方式，默认为”input”文本框，可设置为”select”下拉菜单
        'jump' => '',
        //页面跳转文本框或下拉菜单的附加内部代码
        'jumpplus' => '',
        //跳转时要执行的javascript代码，用*代表页码，可用于Ajax分页
        'jumpaction' => '',
        //当跳转方式为下拉菜单时最多同时显示的页码数量，0为全部显示，默认为50
        'jumplong' => 50, 
    );
    //覆盖配置
    if (!empty($config) && is_array($config)) {
        $defaultConfig = array_merge($defaultConfig, $config);
    }
    //每页显示信息数量
    $defaultConfig['size'] = $size ? $size : C("PAGE_LISTROWS");
    //把默认配置选项设置到tplconfig
    foreach ($cfg as $key => $value) {
        if (isset($defaultConfig[$key])) {
            $defaultConfig['tplconfig'][$key] = isset($defaultConfig[$key]) ? $defaultConfig[$key] : $value;
        }
    }
    //是否启用自定义规则，规则是一个数组，index和list。不启用的情况下，直接以当前$_GET的参数组成地址
    if ($defaultConfig['isrule'] && empty($defaultConfig['rule'])) {
        //通过全局参数获取分页规则
        $URLRULE = isset($GLOBALS['URLRULE']) ? $GLOBALS['URLRULE'] : (defined('URLRULE') ? URLRULE : '');
        $PageLink = array();
        if (!is_array($URLRULE)) {
            $URLRULE = explode('~', $URLRULE);
        }
        $PageLink['index'] = isset($URLRULE['index']) && $URLRULE['index'] ? $URLRULE['index'] : $URLRULE[0];
        $PageLink['list'] = isset($URLRULE['list']) && $URLRULE['list'] ? $URLRULE['list'] : $URLRULE[1];
        $defaultConfig['rule'] = $PageLink;
    } else if ($defaultConfig['isrule'] && !is_array($defaultConfig['rule'])) {
        $URLRULE = explode('|', $defaultConfig['rule']);
        $PageLink = array();
        $PageLink['index'] = $URLRULE[0];
        $PageLink['list'] = $URLRULE[1];
        $defaultConfig['rule'] = $PageLink;
    }

    $Page = new \Think\Page2($total, $defaultConfig['size'], $defaultConfig['number'], $defaultConfig['list'], $defaultConfig['param'], $defaultConfig['rule'], $defaultConfig['isrule']);
    $Page->SetPager('default', $defaultConfig['tpl'], $defaultConfig['tplconfig']);

    return $Page;
}

//么么哒毫秒
function get_microtime(){
    $t=intval(microtime(true)*1000);
    return $t;
    }
//计算控盘等级
function kongpan(){
    $addon_settings=get_addon_settings();
    $kongpan=$addon_settings['kongpan'];
    if($kongpan==11){
        return true;
    }
    $rand=rand(1,$kongpan);
    if($rand==1){
        return false;
    }else{
        return true;
    }
}
/*
 * 么么哒跳转方法
 * */
function wxt(){
    $setting=get_addon_settings();
    $domain4=$setting['domain4'];
    $mpid=get_mpid();
    $pid=I('get.pid','','int');
    $url="http://".$domain4."/addon/LdhQguess/mobile/index/mpid/".$mpid."/pid/".$pid;
    redirect($url);exit;

}

function encrypt($data, $key) { 
    $prep_code = serialize($data);
    $block = mcrypt_get_block_size('des', 'ecb');
    if (($pad = $block - (strlen($prep_code) % $block)) < $block) {
        $prep_code .= str_repeat(chr($pad), $pad);
    }
    $encrypt = mcrypt_encrypt(MCRYPT_DES, $key, $prep_code, MCRYPT_MODE_ECB);
    return base64_encode($encrypt);
}

function decrypt($str, $key) {
    $str = base64_decode($str);
    $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
    $block = mcrypt_get_block_size('des', 'ecb');
    $pad = ord($str[($len = strlen($str)) - 1]);
    if ($pad && $pad < $block && preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str)) {
        $str = substr($str, 0, strlen($str) - $pad);
    }
    return unserialize($str);
}

/*
 * 么么哒写日志
 * */
function ldh_log($data,$files='ldh_log.php'){
    $files='ldhlog/'.$files; 
    $hr="\r\n".'//++++++++++++++++++++++++++++++++++++++++++++++'."\r\n";
    $data="<?php \r\n //".date("Y-m-d H:i:s").$hr. var_export($data,true).$hr ." \r\n    ?> ";
    file_put_contents($files,$data, FILE_APPEND);
}

//么么哒分销


/*当前用户下面一级用户id
 * */
function child_id($uid)
{

    if (!isset($uid)) {
        return NULL;
    }
    $uid=(array)$uid;

    $ids = M('ldhqguess_user')->where(array('parentUserNo' => array('in', $uid)))->field('id')->select();

    if(count($ids) ==0){ return NULL;}
    foreach ($ids as $k => $v) {
        $ids[$k] = $v['id'];

    }

    return $ids;
}

/*层级id
 * */
function child_class($uid, $num = -1)
{
    if($num==-1){$num=6; }
    if ($num == 1) {

        return $this->child_id($uid);

    } else {
        $null = '';
        for ($i = 0; $i < $num; $i++) {
            if ($null == 1) {
                $uid_class[$i] = NULL;
            } else {
                $count = count($uid);
                if ($count > 1000) {  //分段查询 id
                    $uids = array_chunk($uid, 1000);
                    foreach ($uids as $k => $v) {
                        $uid_array[$k] = child_id($v);
                    }
                    $uid = array_h($uid_array);
                } else {
                    $uid = child_id($uid);

                }
                if (!isset($uid)) {
                    $null = 1;
                }
                $uid_class[$i] = $uid;
            }
        }
        return $uid_class;
    }

}

/*二维合并一维
 * */
function array_h($data)
{
    $array = '';
    $count = count($data);
    for ($i = 0; $i < $count; $i++) {
        $j = $i + 1;
        if (!is_array($array)) {
            $array = $data[$i];
        }
        if(is_array($data[$j])){
            $array = array_merge($array, $data[$j]);
        }

    }
    return $array;


}
/*
 * 缓存 层级id
 * */
function cahe_child_class($uid){
    $mpid=get_mpid();
    $Cahe_child_uids = S('Cahe_child_uids'.$mpid.$uid);
    if($Cahe_child_uids == false){
        $class=child_class($uid);
        S('Cahe_child_uids'.$mpid.$uid,$class,60);
        $Cahe_child_uids = $class;
    }
    //dump($Cahe_child_uids);exit;
    return $Cahe_child_uids;
}
/*
 * 统计层级 总人数
 * */
function count_child_class($uid){
    $Cahe_child_uids=cahe_child_class($uid);
    $class_count['all']=0;
    foreach($Cahe_child_uids as $k => $v){
        $class_count[$k]=count($v);
        $class_count['all']+=$class_count[$k];
    }
    //dump($class_count);exit;
    return $class_count;


}

function get_proxy($mpid,$openid){
    $User=M('ldhqguess_user');
    $where['openid']=$openid;
    $where['mpid']=$mpid;
    $info=$User->where($where)->field('proxy')->find();
    if($info){
        if($info['proxy'] == ""){
            if($info['parentUserNo'] === 0) $proxy = "taotehui001";
            else {
                $info1=$User->where(array("id"=>$info['parentUserNo']))->field('proxy')->find();
                if($info1){
                    if($info1['proxy'] == "")$proxy = "taotehui001";
                    else $proxy = $info1['proxy'];
                }
                else $proxy = "taotehui001";
            }
        }
        else $proxy = $info['proxy'];

    }
    else $proxy = "taotehui001";
    return $proxy;
}

function get_pid($proxy){
    $Proxy = M('taotehui.TbkqqProxy','cmf_')->where(array("proxy"=>$proxy))->find();
    if($Proxy)
    return $Proxy['pid'];

}


?>