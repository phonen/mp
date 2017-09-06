<?php

return array(
	'name' => '网站',
	'bzname' => 'Cms',
	'desc' => 'qq在线竞猜',
	'version' => '1.0.1',
	'author' => '么么哒',
    'logo' => 'logo.jpeg',
	'config' => array(
		'respond_rule' => 1,
		'setting' => 1,
		'entry' => 1,
		'menu' => 1,
		'setting_list' => array(
			'scope' => array(
				'title' => '网页授权',
				'type' => 'radio',
				'options' => array(
					0 => '需要',
					1 => '不需要'
				),
				'value' => 0,
				'group' => 'basic'
			),
			'kongpan' => array(
				'title' => '控盘等级1-11',
				'type' => 'text',
				'value' => '1',
				'group' => 'basic2'
			),
			'domain1' => array(
				'title' => '推广域名a',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'domain2' => array(
				'title' => '跳转域名b',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'domain3' => array(
				'title' => '拉取微信用户信息域名',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'domain4' => array(
				'title' => '系统运营域名',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'domain5' => array(
				'title' => '支付域名',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'qqkey' => array(
				'title' => '登录加密key',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'sh' => array(
				'title' => '微信商户名称',
				'type' => 'text',
				'value' => '',
				'group' => 'basic'
			),
			'qqurl' => array(
				'title' => '拉黑地址',
				'type' => 'text',
				'value' => 'http://xw.qq.com/',
				'group' => 'basic'
			),
			'kefu' => array(
				'title' => '客服图片',
				'type' => 'image',
				'group' => 'basic'
			),
			'yjimg' => array(
				'title' => '佣金说明图片',
				'type' => 'image',
				'group' => 'basic'
			),
			'equipment' => array(
				'title' => '设备配置',
				'type' => 'text',
				'value' => 'Mozilla/5.0 (Linux; Android 7.0; BLN-AL10 Build/HONORBLN-AL10; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/53.0.2785.49 Mobile MQQBrowser/6.2 TBS/043220 Safari/537.36 MicroMessenger/6.5.7.1041 NetType/WIFI Language/zh_CN',
				'group' => 'basic'
			),
			'tixian' => array(
				'title' => '每人每天提现次数',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'fx1' => array(
				'title' => '1级分销比例（%）',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'fx2' => array(
				'title' => '2级分销比例（%）',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'fx3' => array(
				'title' => '3级分销比例（%）',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'fx4' => array(
				'title' => '4级分销比例（%）',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'fx5' => array(
				'title' => '5级分销比例（%）',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'fx6' => array(
				'title' => '6级分销比例（%）',
				'type' => 'text',
				'value' => '',
				'group' => 'basic2'
			),
			'qy_type' => array(
				'title' => '开启独立企业付款',
				'type' => 'radio',
				'options' => array(
					0 => '关闭',
					1 => '开启'
				),
				'value' => 0,
				'group' => 'basic3'
			),
			'qy_domain' => array(
				'title' => '企业付款域名',
				'type' => 'text',
				'value' => '',
				'group' => 'basic3'
			),
			'appi' => array(
				'title' => '公众号APPID',
				'type' => 'text',
				'value' => '',
				'group' => 'basic3'
			),
			'apps' => array(
				'title' => '公众号APPSECRET',
				'type' => 'text',
				'value' => '',
				'group' => 'basic3'
			),
			'appsh' => array(
				'title' => '微信商户号',
				'type' => 'text',
				'value' => '',
				'group' => 'basic3'
			),
			'appshk' => array(
				'title' => '微信支付秘钥',
				'type' => 'text',
				'value' => '',
				'group' => 'basic3'
			),
			'appcert' => array(
				'title' => '支付证书cert',
				'type' => 'textarea',
				'group' => 'basic3'
			),
			'appkey' => array(
				'title' => '支付证书key',
				'type' => 'textarea',
				'group' => 'basic3'
			)



		),
		'setting_list_group' => array(
			'basic' => array(
				'title' => '基础配置',
				'is_show' => 1,
			),
			'basic2' => array(
				'title' => '系统配置',
				'is_show' => 1
			),
			'basic3' => array(
				'title' => '独立付款账户',
				'is_show' => 1
			)
		),
		'entry_list' => array(
			'index' => 'qq在线入口',

		),
		'menu_list'=>array(
			'member_index'=>'会员列表',
			'config_index'=>'奖项配置',
			'home' => '统计'

		)
	)
);

?>