var urlConfig = {
	baseUrl: Baseroot+"Addons/Cms/View/Public/js/",
	baseviews:Baseroot+"Addons/Cms/View/Mobile/"
};

require.config({
	paths: {
		zepto: urlConfig.baseUrl + "zepto.min",
		dialog: urlConfig.baseUrl + "dialog", 
		fastclick: urlConfig.baseUrl + "fastclick",
		app: urlConfig.baseUrl + "app",
		index: urlConfig.baseUrl + "index",
		bdlist: urlConfig.baseUrl + "bdlist",
		cclist: urlConfig.baseUrl + "cclist",
		check: urlConfig.baseUrl + "check",
		dhlist: urlConfig.baseUrl + "dhlist",
		dl: urlConfig.baseUrl + "dl",
		getcash: urlConfig.baseUrl + "getcash",
		kf: urlConfig.baseUrl + "kf",
		yjlist: urlConfig.baseUrl + "yjlist",
		zjlist: urlConfig.baseUrl + "zjlist",
		require: urlConfig.baseUrl + "require",
		new_file: urlConfig.baseUrl + "new_file",
		token: urlConfig.baseUrl + "token",
		qrcode: urlConfig.baseUrl + "qrcode.min"
	},
	urlArgs: "time=" + (new Date()).getTime()
});
$(function() {
	vipspa.start({
		view: '#ui-view',
		errorTemplateId: '#error',
		router: {
			'home': {
				templateUrl: urlConfig.baseviews+ 'views/index.html',
				controller: urlConfig.baseUrl +'index.js?t=20170521'
			},
			'get-cash': {
				templateUrl: urlConfig.baseviews+'views/get-cash.html',
				controller: urlConfig.baseUrl +'getcash.js?t=20170521'
			},
			'check': {
				templateUrl: urlConfig.baseviews+'views/check.html',
				controller: urlConfig.baseUrl +'check.js?t=20170521'
			},
			'dl': {
				templateUrl: urlConfig.baseviews+'views/dl.html?t=20170531',
				controller: urlConfig.baseUrl +'dl.js?t=20170531'
			},
			'kf': {
				templateUrl:urlConfig.baseviews+ 'views/kf.html',
				controller: urlConfig.baseUrl +'kf.js?t=20170521'
			},
			'cclist': {
				templateUrl: urlConfig.baseviews+'views/cc-list.html',
				controller:urlConfig.baseUrl +'cclist.js?t=20170520'
			},
			'dhlist': {
				templateUrl:urlConfig.baseviews+'views/dh-list.html',
				controller: urlConfig.baseUrl +'dhlist.js?t=20170521'
			},
			'yjlist': {
				templateUrl:urlConfig.baseviews+ 'views/yj-list.html',
				controller: urlConfig.baseUrl +'yjlist.js?t=20170521'
			},
			'zjlist': {
				templateUrl: urlConfig.baseviews+'views/zj-list.html',
				controller: urlConfig.baseUrl +'zjlist.js?t=20170521'
			},
			'bdlist': {
				templateUrl:urlConfig.baseviews+'views/bd-list.html',
				controller: urlConfig.baseUrl +'bdlist.js?t=20170521'
			},
			'defaults': 'home'
		}
	})
});