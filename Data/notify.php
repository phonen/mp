<?php

/**
 * 微信支付异步通知处理程序
 * 资源e站（Zye.cc）
 */
$xml = $GLOBALS["HTTP_RAW_POST_DATA"];		// 获取微信支付异步通知数据

//ldh_log("php://input",'xml.php');
//ldh_log($_SERVER,'server.php');
$arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		// 将xml格式的数据转换为array数组

$attach = $arr['attach'];											// 获取通知中包含的附加参数
$params = json_decode($attach, true);								// 将附加参数转换为数组
$xxx="<xml>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <return_msg><![CDATA[OK]]></return_msg>
</xml>";

if ($params['notify']) {
	$notify_url = str_replace('ldh/','index.php?s=addon/LdhQguess/mobile/pay_ok/mpid/' ,$params['notify'] );				// 将通知转发到插件控制器中进行处理
	$arr['mpid'] = $params['mpid'];
	$arr['notify_url'] = $notify_url;
	//ldh_log($params,'paramsb.php');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $notify_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$return = curl_exec($ch);
	//ldh_log($return,'okokok.php');
	if($return=='ok'){
		echo $xxx;
		//ldh_log($xxx,'ppppplaaa.php');
	}
	curl_close($ch);

}
function ldh_log($data,$files='ldh_log.php'){
	//$files='ldhlog/'.$files;
	$hr="\r\n".'//++++++++++++++++++++++++++++++++++++++++++++++'."\r\n";
	$data="<?php \r\n //".date("Y-m-d H:i:s").$hr. var_export($data,true).$hr ." \r\n    ?> ";
	file_put_contents($files,$data, FILE_APPEND);
}

?>