var timerId;
var timeCount = 0;
var orderNo;
var isBegin = false;
var openTimerId;
var dialogWin;
var arr = [0, 4.68, 4.16, 3.64, 3.12, 2.6, 2.08, 1.56, 1.04, 0.52];
var curLotteryData;
var waitLotteryData;
var isOpen;
var rechargeTimerId;
var rechargeTimeCount = 0;
var getBet = false;
var openTimerId2;
require(['zepto', 'dialog', 'app'], function($, dialog) {
	dialogWin = dialog;
	var winWidth = $(window).width(),
		winHeight = $(window).height();
	var Page = {
		init: function() {},
		initSwitch: function(key) {
			var navitem = $(".l-main-item");
			if(key == 2) {
				key -= 1
			} else if(key == 4) {
				key -= 2
			}
			navitem.eq(key).show().siblings().hide();
			$(".grid-item").removeClass("active");
			switch(key) {
				case 0:
					$("#single_double").find(".grid-item").eq(0).addClass("active");
					break;
				case 1:
					$("#array_wrap").find(".grid-item").eq(0).addClass("active");
					break;
				case 2:
					$("#accurate").find(".grid-item").eq(0).addClass("active");
					break;
				default:
					break
			}
		},
		btnAnimate: function(dom, scale) {
			var scale = scale || 1.05;
			dom.animate({
				"transform": "scale(" + scale + ")"
			}, 200, function() {
				dom.animate({
					"transform": "scale(1)"
				}, 100)
			})
		},
		toggleAnimate: function(dom) {
			if(dom.css("height") == "0px") {
				$(".l-layer-mask").show();
				dom.animate({
					"height": winHeight + "px"
				}, 500)
			} else {
				dom.animate({
					"height": 0 + "px"
				}, 500, function() {
					$(".l-layer-mask").hide()
				})
			}
		},
		render: function() {
			var self = this;
			$('.icon-item-01').on("click", function() {
				window.location.href = "#home";
				$('.icon-item').eq(0).addClass('active').siblings().removeClass('active')
			});
			$('.icon-item-02').on("click", function() {
				isOpen = false;
				window.location.href = "#get-cash";
				$('.icon-item').eq(1).addClass('active').siblings().removeClass('active')
			});
			$('.icon-item-03').on("click", function() {
				window.location.href = "#check";
				$('.icon-item').eq(2).addClass('active').siblings().removeClass('active')
			});
			$('.icon-item-04').on("click", function() {
				window.location.href = "#dl";
				$('.icon-item').eq(3).addClass('active').siblings().removeClass('active')
			});
			$('.icon-item-05').on("click", function() {
				window.location.href = "#kf";
				$('.icon-item').eq(4).addClass('active').siblings().removeClass('active')
			});
			var params = {
				"playType": 1
			};
			$.ajax({
				type: "POST",
				url: Apis.getOddsConfig,
				//contentType: "application/json",
				data: params,  
				dataType: "json", 
				beforeSend: function(request) {
					request.setRequestHeader("X-Auth-Token", token)
				},
				success: function(data) {
					if(data.data != null && data.data.length > 0) {
						var arrayList = data.data;
						var lenght = arrayList.length;
						for(var i = 0; i < lenght; i += 1) {
							var item = arrayList[i];
							if(item.type == 1) {
								$("#_single").text('1赔' + parseFloat(item.odds));
								$("#_double").text('1赔' + parseFloat(item.odds))
							} else if(item.type == 2) {
								$(".three-num .array-text").text('1赔' + parseFloat(item.odds))
							} else if(item.type == 3) {
								$(".two-num .array-text").text('1赔' + parseFloat(item.odds))
							} else if(item.type == 4) {
								$(".accurate .array-text").text('1赔' + parseFloat(item.odds))
							} else if(item.type == 5) {
								$("#_flat").text('1赔' + parseFloat(item.odds))
							}
						}
					}
				},
				error: function(error) {
					$('.loading').hide()
				}
			});
			getBetLottery();
			$('.start').on("click", function() {
				var money = parseInt($('.l-hd-right span i').text());
				var money2 = parseInt($('.l-number').text());
				if(money < money2) {
					$('.cz').show();
					$('.czBox').show()
				} else {
					var money2 = parseInt($('.l-number').text());
					var betNum = $(".l-main-wrap .active").attr("data-type");
					var discType = $(".l-main-wrap .active").parent().attr("data-type");
					var htmlB = '';
					if(discType == 1) {
						htmlB += '<i>' + $(".l-main-wrap .active div span").text() + '</i>'
					} else {
						for(var i = 0; i < betNum.length; i += 1) {
							htmlB += '<i>' + betNum[i] + '</i>'
						}
					}
					$('.zj_bet_num').html(htmlB);
					if($('.single-double').css('display') == "none") {
						var oddsStr = $('.l-main-item').find('.active').parent().find('.array-text').text();
						$('.zj_odds').text(oddsStr);
						var a = $('.l-number').text();
						var b = parseInt(a) * parseFloat(oddsStr.split('赔')[1]);
						$('.zj_sy').text(parseFloat(b).toFixed(2))
					} else {
						var oddsStr = $('.l-main-item').find('.active').find('.array-text').text();
						$('.zj_odds').text(oddsStr);
						var a = $('.l-number').text();
						var b = parseInt(a) * parseFloat(oddsStr.split('赔')[1]);
						$('.zj_sy').text(parseFloat(b).toFixed(2))
					}
					if(curLotteryData != null) {
						$("#zj-qh").text(curLotteryData.issueNo + "期")
					}
					$(".zj_bet_amount").text($('.l-number').text());
					$('.zj').show()
				}
			});
			$('.btn-close2').on("click", function() {
				$('.zj').hide()
			});
			$('.zj-btn').on("click", function() {
				$('.zj').hide();
				var money2 = parseInt($('.l-number').text());
				var betNum = $(".l-main-wrap .active").attr("data-type");
				var discType = $(".l-main-wrap .active").parent().attr("data-type");
				if(discType == 1 && betNum == 5) {
					discType = 5
				}
				betAmount = money2 * 100;
				bet(betNum, betAmount, discType)
			});
			var arr = [5, 10, 15, 20, 25, 30, 40, 50, 60, 70, 100, 200, 500, 1000, 2000];
			var i = 0;
			$('.l-number').text(arr[i]);
			$('.max').on("click", function() {
				$('.l-number').text(arr[14]);
				i = 14
			});
			$('.next').on("click", function() {
				i += 1;
				if(i > 14) {
					i = 0
				}
				$('.l-number').text(arr[i])
			});
			$('.prev').on("click", function() {
				i -= 1;
				if(i < 0) {
					i = 14
				}
				$('.l-number').text(arr[i])
			});
			$('.a').on("click", function(e) {
				e.stopPropagation();
				location.href = "http://im.qq.com/pcqq/"
			});
			var callbackParam = getLocationParam("callbackParam");
			if(callbackParam != null) {
				var callArray = callbackParam.split(",");
				if(orderNo != callArray[1]) {
					orderNo = callArray[1];
					if(callArray[0] == 1) {
						startTimerQueryRechargeOrder()
					} else if(callArray[0] == 3) {}
				}
			}
			window.setTimeout("queryOpenOrder()", 800);
			self.init();
			$(".l-nav-item").on("click", function(index) {
				var _this = $(this);
				var _index = _this.index();
				console.log(_index);
				if(_index == 2) {
					$('.second').css({
						opacity: 0
					});
					$('.first').css({
						opacity: 0
					})
				} else if(_index == 4) {
					$('.first').css({
						opacity: 1
					});
					$('.second').css({
						opacity: 0
					})
				} else if(_index == 0) {
					$('.first').css({
						opacity: 0
					});
					$('.second').css({
						opacity: 1
					})
				}
				_this.addClass("active").siblings().removeClass("active");
				self.initSwitch(_index)
			});
			$(".btn-submit").on("click", function() {
				var money = parseInt($('.l-hd-right span i').text());
				var money2 = parseInt($('.l-number').text());
				if(money < money2) {
					$('.cz').show();
					$('.czBox').show()
				} else {
					var betNum = $(".l-main-wrap .active").attr("data-type");
					var discType = $(".l-main-wrap .active").parent().attr("data-type");
					if(discType == 1 && betNum == 5) {
						discType = 5
					}
					betAmount = money2 * 100;
					bet(betNum, betAmount, discType)
				}
			});
			$(".price-item").on("click", function() {
				$(this).addClass("active").siblings().removeClass("active");
				self.btnAnimate($(this))
			});
			$(".grid-item").on("click", function() {
				$(".grid-item").removeClass("active");
				$(this).addClass("active");
				self.btnAnimate($(this))
			});
			$(".l-toggle-item").on("click", function() {
				var _this = $(this);
				var _index = _this.index();
				_this.addClass("active").siblings().removeClass("active");
				var contentitem = $(".l-toggle-content");
				contentitem.eq(_index).show().siblings().hide()
			});
			$("#btn_show_record").on("click", function() {
				self.toggleAnimate($(".l-layer-slide-toggle"));
				getOpenLotteryRecord()
			});
			$("#btn_hide_record").on("click", function() {
				self.toggleAnimate($(".l-layer-slide-toggle"))
			});
			$("#btn_how-app").on("click", function() {
				self.toggleAnimate($(".rhyz"))
			});
			$('.wzj-btn').on("click", function() {
				var payOrderNo = $("#wzjOrderNo").text();
				updateRead(payOrderNo, false);
				$(".wzj").hide()
			});
			$('.zj-btn').on("click", function() {
				$(".zj").hide()
			});
			$('.z-btn').on("click", function() {
				var payOrderNo = $("#zjOrderNo").text();
				updateRead(payOrderNo, false);
				$(".z").hide()
			});
			$('.rhyz').on("click", function() {
				self.toggleAnimate($(".rhyz"))
			});
			$('#yj-img').on("click", function() {
				$(this).hide()
			})
		}
	};
	Page.render()
});

function setOtherRecord(data) {}

function numRand() {
	var x = 99999;
	var y = 11111;
	var rand = parseInt(Math.random() * (x - y + 1) + y);
	return rand
}

function timer(intDiff) {
	clearTimer(openTimerId);
	openTimerId = window.setInterval(function() {
		var second = 0;
		if(intDiff >= 0) {
			second = Math.floor(intDiff)
		} else {
			clearTimer(openTimerId);
			timer2(5);
			waitLotteryData = curLotteryData;
			setTimeout("refreshBetLottery()", 3000)
		}
		if(second <= 9) {
			second = '0' + second
		}
		$('#second_show').html(second);
		intDiff -= 1
	}, 1000)
}

function timer2(sec) {
	console.log("timer2:" + sec);
	closeAllDialog();
	$('.sec').html(sec + "s");
	$('.daojishiBox').show();
	clearTimer(openTimerId2);
	openTimerId2 = window.setInterval(function() {
		var second = 0;
		if(sec >= 0) {
			second = sec
		} else {
			$('.daojishiBox').hide();
			startTimerQueryLotteryResult();
			clearTimer(openTimerId2)
		}
		sec -= 1;
		$('.sec').html(second + "s")
	}, 1000)
}

function closeAllDialog() {
	$('.cz').hide();
	$('.czBox').hide();
	$('.zj').hide()
}

function clearTimer(objTimer) {
	if(objTimer != null) {
		window.clearInterval(objTimer)
	}
}

function startTimerQueryRechargeOrder() {
	if(orderNo != null) {
		queryRechargeOrder();
		if(rechargeTimerId != null) {
			clearTimer(rechargeTimerId);
			rechargeTimerId = null
		}
		rechargeTimerId = window.setInterval("queryRechargeOrder()", 5000)
	}
}

function queryRechargeOrder() {
	if(orderNo != null) {
		var params = {
			"orderNo": orderNo
		};
		console.log("查询充值:" + orderNo);
		$.ajax({
			type: "POST",
			url: Apis.queryRechargeOrder,
			contentType: "application/json",
			data: JSON.stringify(params),
			dataType: "json",
			beforeSend: function(request) {
				request.setRequestHeader("X-Auth-Token", token)
			},
			success: function(data) {
				if(data.data != null) {
					var result = data.data;
					if(result.status == 1) {
						getAccountInfo(true);
						dialogWin.show({
							type: '3',
							content: "充值成功",
							content1: "成功入账",
							money: (result.amount / 100),
							btnText: "确定",
							handler: function() {
								dialogWin.close()
							}
						});
						$('.loading').hide();
						clearTimer(rechargeTimerId);
						rechargeTimerId = null;
						rechargeTimeCount = 0
					}
				}
			},
			error: function(error) {
				$('.loading').hide();
				clearTimer(rechargeTimerId);
				rechargeTimerId = null;
				rechargeTimeCount = 0
			}
		})
	}
	rechargeTimeCount += 1;
	if(rechargeTimeCount > 8) {
		clearTimer(rechargeTimerId);
		rechargeTimerId = null;
		rechargeTimeCount = 0
	}
}

function getBetLottery() {
	$('.loading').show();
	var param = {};
	$.ajax({
		type: "POST",
		url: Apis.getBetLottery,
		contentType: "application/json",
		data: JSON.stringify(param),
		dataType: "json",
		async: false,
		beforeSend: function(request) {
			request.setRequestHeader("X-Auth-Token", token)
		},
		success: function(data) {
			$('.loading').hide();
			if(data.data != null) {
				var previouLottery = data.data.previouLottery;
				var hisList = data.data.historyLottery;
				if(previouLottery == null) {
					$(".l-center-top").text('-期')
				} else {
					if(previouLottery.status != 3) {
						$('.box').hide();
						$(".l-center-top").text(previouLottery.issueNo + '期(开奖中...)');
						$(".num-box").eq(0).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(1).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(2).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(3).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(4).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(5).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(6).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(7).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(8).css('background-position-y', arr[0] + "rem");
						$(".kjjg").html("<i>-</i><i>-</i><i>-</i>");
						waitLotteryData = previouLottery;
						if(previouLottery.waitOpen != null && previouLottery.waitOpen > 0) {
							timer2(previouLottery.waitOpen)
						} else {
							startTimerQueryLotteryResult()
						}
					} else {
						$(".l-center-top").text(previouLottery.issueNo + '期');
						$(".num-box").eq(0).css('background-position-y', arr[parseInt(previouLottery.openCode[0])] + "rem");
						$(".num-box").eq(1).css('background-position-y', arr[parseInt(previouLottery.openCode[1])] + "rem");
						$(".num-box").eq(2).css('background-position-y', arr[parseInt(previouLottery.openCode[2])] + "rem");
						$(".num-box").eq(3).css('background-position-y', arr[parseInt(previouLottery.openCode[3])] + "rem");
						$(".num-box").eq(4).css('background-position-y', arr[parseInt(previouLottery.openCode[4])] + "rem");
						$(".num-box").eq(5).css('background-position-y', arr[parseInt(previouLottery.openCode[5])] + "rem");
						$(".num-box").eq(6).css('background-position-y', arr[parseInt(previouLottery.openCode[6])] + "rem");
						$(".num-box").eq(7).css('background-position-y', arr[parseInt(previouLottery.openCode[7])] + "rem");
						$(".num-box").eq(8).css('background-position-y', arr[parseInt(previouLottery.openCode[8])] + "rem");
						if(previouLottery.openCode[8] == previouLottery.openCode[7]) {
							$(".kjjg").html("<i>合</i><i>" + previouLottery.openCode[8] + "</i><i>" + previouLottery.openCode[8] + "</i>")
						} else {
							if(previouLottery.openCode[8] > 4) {
								$(".kjjg").html("<i>大</i><i>" + previouLottery.openCode[8] + "</i><i>-</i>")
							} else {
								$(".kjjg").html("<i>小</i><i>" + previouLottery.openCode[8] + "</i><i>-</i>")
							}
						}
					}
				}
				if(hisList != null && hisList.length > 1) {
					var his = hisList[0];
					var htmlE = '<span>' + his.issueNo + '期</span><span>';
					if(his.status == 3) {
						var lenght = his.openCode.length;
						var bNum = his.openCode[lenght - 2];
						var eNum = his.openCode[lenght - 1];
						var mNum = his.openCode[lenght - 3];
						htmlE += '<i>' + mNum + '</i>';
						if(eNum == bNum) {
							htmlE += '<i class="active">' + bNum + '</i>'
						} else {
							htmlE += '<i>' + bNum + '</i>'
						}
						htmlE += '<i class="active">' + eNum + '</i>';
						htmlE += '</span>';
						if(eNum == bNum) {
							htmlE += '<span><i>大</i><i>小</i><i class="active">合</i></span>'
						} else if(eNum < 5) {
							htmlE += '<span><i>大</i><i class="active">小</i><i>合</i></span>'
						} else {
							htmlE += '<span><i class="active">大</i><i>小</i><i>合</i></span>'
						}
					} else {
						htmlE += '开奖中...</span>'
					}
					$(".his1").html(htmlE);
					his = hisList[1];
					htmlE = '<span>' + his.issueNo + '期</span><span>';
					if(his.status == 3) {
						lenght = his.openCode.length;
						var bNum = his.openCode[lenght - 2];
						var eNum = his.openCode[lenght - 1];
						var mNum = his.openCode[lenght - 3];
						htmlE += '<i>' + mNum + '</i>';
						if(eNum == bNum) {
							htmlE += '<i class="active">' + bNum + '</i>'
						} else {
							htmlE += '<i>' + bNum + '</i>'
						}
						htmlE += '<i class="active">' + eNum + '</i>';
						htmlE += '</span>';
						if(eNum == bNum) {
							htmlE += '<span><i>大</i><i>小</i><i class="active">合</i></span>'
						} else if(eNum < 5) {
							htmlE += '<span><i>大</i><i class="active">小</i><i>合</i></span>'
						} else {
							htmlE += '<span><i class="active">大</i><i>小</i><i>合</i></span>'
						}
					} else {
						htmlE += '开奖中...</span>'
					}
					$(".his2").html(htmlE)
				}
				curLotteryData = data.data.currentLottery;
				if(curLotteryData != null) {
					if(curLotteryData.status == 1) {
						$(".l-bottom-head").text(curLotteryData.issueNo + "期   投注");
						$("#qh").text(curLotteryData.issueNo + "期");
						if(curLotteryData.lotteryCountDown != null && curLotteryData.lotteryCountDown > 1) {
							timer(curLotteryData.lotteryCountDown)
						}
					} else {
						$(".l-bottom-head").text(curLotteryData.issueNo + "期  投注");
						$("#qh").text(curLotteryData.startBetDateLabel + "开放投注")
					}
				} else {
					$(".l-bottom-head").text("-期   投注");
					$("#qh").text("-期")
				}
			}
		},
		error: function(error) {
			$('.loading').hide()
		}
	})
}

function refreshBetLottery() {
	var param = {};
	$.ajax({
		type: "POST",
		url: Apis.getBetLottery,
		contentType: "application/json",
		data: JSON.stringify(param),
		dataType: "json",
		async: false,
		beforeSend: function(request) {
			request.setRequestHeader("X-Auth-Token", token)
		},
		success: function(data) {
			$('.loading').hide();
			if(data.data != null) {
				var previouLottery = data.data.previouLottery;
				var hisList = data.data.historyLottery;
				if(previouLottery != null) {
					if(previouLottery.status != 3) {
						waitLotteryData = previouLottery;
						$(".l-center-top").text(previouLottery.issueNo + '期(开奖中...)');
						$(".num-box").eq(0).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(1).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(2).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(3).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(4).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(5).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(6).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(7).css('background-position-y', arr[0] + "rem");
						$(".num-box").eq(8).css('background-position-y', arr[0] + "rem");
						$(".kjjg").html("<i>-</i><i>-</i><i>-</i>")
					} else {
						$(".l-center-top").text(previouLottery.issueNo + '期');
						$(".num-box").eq(0).css('background-position-y', arr[parseInt(previouLottery.openCode[0])] + "rem");
						$(".num-box").eq(1).css('background-position-y', arr[parseInt(previouLottery.openCode[1])] + "rem");
						$(".num-box").eq(2).css('background-position-y', arr[parseInt(previouLottery.openCode[2])] + "rem");
						$(".num-box").eq(3).css('background-position-y', arr[parseInt(previouLottery.openCode[3])] + "rem");
						$(".num-box").eq(4).css('background-position-y', arr[parseInt(previouLottery.openCode[4])] + "rem");
						$(".num-box").eq(5).css('background-position-y', arr[parseInt(previouLottery.openCode[5])] + "rem");
						$(".num-box").eq(6).css('background-position-y', arr[parseInt(previouLottery.openCode[6])] + "rem");
						$(".num-box").eq(7).css('background-position-y', arr[parseInt(previouLottery.openCode[7])] + "rem");
						$(".num-box").eq(8).css('background-position-y', arr[parseInt(previouLottery.openCode[8])] + "rem");
						if(previouLottery.openCode[8] == previouLottery.openCode[7]) {
							$(".kjjg").html("<i>合</i><i>" + previouLottery.openCode[8] + "</i><i>" + previouLottery.openCode[8] + "</i>")
						} else {
							if(previouLottery.openCode[8] > 4) {
								$(".kjjg").html("<i>大</i><i>" + previouLottery.openCode[8] + "</i><i>-</i>")
							} else {
								$(".kjjg").html("<i>小</i><i>" + previouLottery.openCode[8] + "</i><i>-</i>")
							}
						}
					}
				}
				if(hisList != null && hisList.length > 1) {
					var his = hisList[0];
					var htmlE = '<span>' + his.issueNo + '期</span><span>';
					if(his.status == 3) {
						var lenght = his.openCode.length;
						var bNum = his.openCode[lenght - 2];
						var eNum = his.openCode[lenght - 1];
						var mNum = his.openCode[lenght - 3];
						htmlE += '<i>' + mNum + '</i>';
						if(eNum == bNum) {
							htmlE += '<i class="active">' + bNum + '</i>'
						} else {
							htmlE += '<i>' + bNum + '</i>'
						}
						htmlE += '<i class="active">' + eNum + '</i>';
						htmlE += '</span>';
						if(eNum == bNum) {
							htmlE += '<span><i>大</i><i>小</i><i class="active">合</i></span>'
						} else if(eNum < 5) {
							htmlE += '<span><i>大</i><i class="active">小</i><i>合</i></span>'
						} else {
							htmlE += '<span><i class="active">大</i><i>小</i><i>合</i></span>'
						}
					} else {
						htmlE += '开奖中...</span>'
					}
					$(".his1").html(htmlE);
					his = hisList[1];
					htmlE = '<span>' + his.issueNo + '期</span><span>';
					if(his.status == 3) {
						lenght = his.openCode.length;
						var bNum = his.openCode[lenght - 2];
						var eNum = his.openCode[lenght - 1];
						var mNum = his.openCode[lenght - 3];
						htmlE += '<i>' + mNum + '</i>';
						if(eNum == bNum) {
							htmlE += '<i class="active">' + bNum + '</i>'
						} else {
							htmlE += '<i>' + bNum + '</i>'
						}
						htmlE += '<i class="active">' + eNum + '</i>';
						htmlE += '</span>';
						if(eNum == bNum) {
							htmlE += '<span><i>大</i><i>小</i><i class="active">合</i></span>'
						} else if(eNum < 5) {
							htmlE += '<span><i>大</i><i class="active">小</i><i>合</i></span>'
						} else {
							htmlE += '<span><i class="active">大</i><i>小</i><i>合</i></span>'
						}
					} else {
						htmlE += '开奖中...</span>'
					}
					$(".his2").html(htmlE)
				}
				curLotteryData = data.data.currentLottery;
				if(curLotteryData != null) {
					if(curLotteryData.status == 1) {
						$(".l-bottom-head").text(curLotteryData.issueNo + "期   投注");
						$("#qh").text(curLotteryData.issueNo + "期");
						if(curLotteryData.lotteryCountDown != null && curLotteryData.lotteryCountDown > 1) {
							timer(curLotteryData.lotteryCountDown)
						}
					} else {
						$(".l-bottom-head").text(curLotteryData.issueNo + "期  投注");
						$("#qh").text(curLotteryData.startBetDateLabel + "开放投注")
					}
				} else {
					$(".l-bottom-head").text("-期   投注");
					$("#qh").text("-期")
				}
			}
		},
		error: function(error) {
			$('.loading').hide()
		}
	})
}

function getNextBetLottery() {
	$('.loading').show();
	var param = {};
	if(curLotteryData != null) {
		param = {
			"issueNo": curLotteryData.issueNo
		}
	}
	$.ajax({
		type: "POST",
		url: Apis.getNextBetLottery,
		contentType: "application/json",
		data: JSON.stringify(param),
		dataType: "json",
		beforeSend: function(request) {
			request.setRequestHeader("X-Auth-Token", token)
		},
		success: function(data) {
			$('.loading').hide();
			waitLotteryData = curLotteryData;
			$(".l-center-top").text(waitLotteryData.issueNo + '期(开奖中...)');
			curLotteryData = data.data;
			if(curLotteryData != null) {
				$(".l-bottom-head").text(curLotteryData.issueNo + "期   投注");
				$("#qh").text(curLotteryData.issueNo + "期");
				if(curLotteryData.lotteryCountDown != null) {
					timer(curLotteryData.lotteryCountDown)
				}
			} else {
				$(".l-bottom-head").text("-期   投注");
				$("#qh").text("-期")
			}
		},
		error: function(error) {
			$('.loading').hide()
		}
	})
}

function queryOpenOrder() {
	var params = {};
	$.ajax({
		type: "POST",
		url: Apis.queryOpenOrder,
		contentType: "application/json",
		data: JSON.stringify(params),
		dataType: "json",
		beforeSend: function(request) {
			request.setRequestHeader("X-Auth-Token", token)
		},
		success: function(data) {
			if(data.data != null) {
				var result = data.data;
				if(result.status == 2) {
					var openNo = result.openOrderNo[result.openOrderNo.length - 1];
					var bNum = result.openOrderNo[result.openOrderNo.length - 2];
					$('.loading').hide();
					var payOrderNo = result.issueNo;
					var id = result.id;
					if(result.winAmount > 0) {
						getAccountInfo(true);
						//$("#zjOrderNo").t("data-id", id);
						$("#zjOrderNo").html(payOrderNo);
						var conHtml = '<p>开奖号码<span class="zj_open"><i>' + result.openOrderNo[result.openOrderNo.length - 3] + '</i>';
						conHtml += '<i>' + bNum + '</i>';
						conHtml += '<i class="active">' + openNo + '</i></span></p>';
						if(openNo == bNum) {
							conHtml += '<p>开奖结果 <span><i>' + openNo + '</i><i>' + openNo + '</i><i class="active">合</i></span></p>'
						} else if(openNo > 4) {
							conHtml += '<p>开奖结果 <span><i>' + openNo + '</i><i class="active">大</i></span></p>'
						} else {
							conHtml += '<p>开奖结果 <span><i>' + openNo + '</i><i class="active">小</i></span></p>'
						}
						conHtml += '<p>本次竞猜 <span>';
						if(result.discType == 1) {
							if(result.betNum == "01234") {
								conHtml += '<i>小</i>'
							} else {
								conHtml += '<i>大</i>'
							}
						} else if(result.discType == 5) {
							conHtml += '<i>合</i>'
						} else {
							var lenght = result.betNum.length;
							for(var i = 0; i < lenght; i += 1) {
								conHtml += '<i>' + result.betNum[i] + '</i>'
							}
						}
						conHtml += '</span></p>';
						conHtml += '<p>本次投注<span><i class="i-right">' + parseFloat(result.betAmount / 100) + ' 金币</i></span></p>';
						conHtml += '<p>本次收益<span><i class="i-right">' + parseFloat(result.winAmount / 100) + ' 金币</i></p>';
						$(".z-text").html(conHtml);
						$(".z").css("display", "block")
					} else {
						$("#wzjOrderNo").attr("data-id", id);
						$("#wzjOrderNo").html(payOrderNo);
						var conHtml = '<p>开奖号码<span class="wzj_open"><i>' + result.openOrderNo[result.openOrderNo.length - 3] + '</i>';
						conHtml += '<i>' + bNum + '</i>';
						conHtml += '<i>' + openNo + '</i></span></p>';
						if(openNo == bNum) {
							conHtml += '<p>开奖结果 <span><i>' + openNo + '</i><i>' + openNo + '</i><i class="active">合</i></span></p>'
						} else if(openNo > 4) {
							conHtml += '<p>开奖结果 <span><i>' + openNo + '</i><i class="active">大</i></span></p>'
						} else {
							conHtml += '<p>开奖结果 <span><i>' + openNo + '</i><i class="active">小</i></span></p>'
						}
						conHtml += '<p>本次竞猜 <span>';
						if(result.discType == 1) {
							if(result.betNum == "01234") {
								conHtml += '<i>小</i>'
							} else {
								conHtml += '<i>大</i>'
							}
						} else if(result.discType == 5) {
							conHtml += '<i>合</i>'
						} else {
							var lenght = result.betNum.length;
							for(var i = 0; i < lenght; i += 1) {
								conHtml += '<i>' + result.betNum[i] + '</i>'
							}
						}
						conHtml += '</span></p>';
						conHtml += '<p>本次投注<span><i class="i-right">' + parseFloat(result.betAmount / 100) + ' 金币</i></span></p>';
						conHtml += '<p>本次收益<span><i class="i-right">' + 0 + ' 金币</i></p>';
						$(".wzj-text").html(conHtml);
						$(".wzj").css("display", "block")
					}
				}
			} else {
				$('.loading').hide()
			}
		},
		error: function(error) {
			$('.loading').hide()
		}
	})
}

function startTimerQueryLotteryResult() {
	clearTimer(timerId);
	if(waitLotteryData != null) {
		queryLotteryResult();
		timerId = window.setInterval("queryLotteryResult()", 5000)
	}
}

function queryLotteryResult() {
	if(waitLotteryData == null) {
		clearTimer(timerId);
		timerId = null;
		timeCount = 0
	} else {
		var param = {
			"issueNo": waitLotteryData.issueNo
		};;
		$.ajax({
			type: "POST",
			url: Apis.queryLotteryResult,
			//contentType: "application/json",
			data: param,
			dataType: "json",
			beforeSend: function(request) {
				request.setRequestHeader("X-Auth-Token", token)
			},
			success: function(data) {
				if(data.data != null) {
					if(data.data.status == 3) {
						$(".l-center-top").text(data.data.issueNo + '期');
						startOpenAnim(data.data.openCode);
						waitLotteryData = null;
						clearTimer(timerId)
					}
				}
			},
			error: function(error) {}
		});
		timeCount += 1;
		if(timeCount >= 5) {
			clearTimer(timerId);
			timerId = null;
			timeCount = 0
		}
	}
}

function startOpenAnim(num) {
	var u = 0.5195;
	$(".num-box").css('backgroundPositionY', 0);
	var result = num;
	var num_arr = (result + '').split('');
	$(".num-box").each(function(index) {
		var _num = $(this);
		_num.animate({
			backgroundPositionY: (u * 60) - (u * num_arr[index]) + "rem"
		}, {
			duration: index * 800,
			easing: "easeOutCubic",
			complete: function() {
				if(index == 8) {
					if(num[8] == num[7]) {
						$(".kjjg").html("<i>合</i><i>" + num[8] + "</i><i>" + num[8] + "</i>")
					} else {
						if(num[8] > 4) {
							$(".kjjg").html("<i>大</i><i>" + num[8] + "</i><i>-</i>")
						} else {
							$(".kjjg").html("<i>小</i><i>" + num[8] + "</i><i>-</i>")
						}
					}
					queryOpenOrder()
				}
			}
		})
	})
}

function getOpenLotteryRecord() {
	var params = {};
	$.ajax({
		type: "POST",
		url: Apis.getOpenLotteryRecord,
		contentType: "application/json",
		data: JSON.stringify(params),
		dataType: "json",
		beforeSend: function(request) {
			request.setRequestHeader("X-Auth-Token", token)
		},
		success: function(data) {
			if(data.data != null) {
				var arrayList = data.data.historyList;
				var htmlE = '<div class="g-wrap-item g-navbar"><span>期号</span><div><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span><span>8</span><span>9</span><span>0</span></div></div>';
				var htmlF = '<div class="g-wrap-item g-navbar"><span>期号</span><div><span>大</span><span>小</span><span>合</span></div></div>';
				var htmlG = '<div class="g-wrap-item g-navbar"><span>期号</span><div><span>开奖结果</span></div></div>';
				if(arrayList != null) {
					var lenght = arrayList.length;
					for(var i = 0; i < lenght; i += 1) {
						var item = arrayList[i];
						var oLenght = item.openCode.length;
						var openDiscRom = item.openCode[oLenght - 1];
						var discIndex = openDiscRom;
						if(discIndex == 0) {
							discIndex = 10
						}
						htmlE += '<div class="g-wrap-item">';
						htmlE += '<span>' + item.issueNo + '</span>';
						htmlE += '<div>';
						for(var j = 1; j < 11; j += 1) {
							if(j == discIndex) {
								htmlE += '<span><i class="hot"></i></span>'
							} else {
								htmlE += '<span></span>'
							}
						}
						htmlE += '</div>';
						htmlE += '</div>';
						htmlF += '<div class="g-wrap-item">';
						htmlF += '<span>' + item.issueNo + '</span>';
						htmlF += '<div>';
						var bNum = item.openCode[oLenght - 2];
						if(bNum == openDiscRom) {
							htmlF += '<span></span><span></span><span><i class="hot"></i></span>'
						} else {
							if(openDiscRom > 4 && openDiscRom < 10) {
								htmlF += '<span><i class="hot"></i></span><span></span><span></span>'
							} else {
								htmlF += '<span></span><span><i class="hot"></i></span><span></span>'
							}
						}
						htmlF += '</div>';
						htmlF += '</div>'
					}
				}
				var statisticsList = data.data.statisticsList;
				if(statisticsList != null) {
					var lenght = statisticsList.length;
					htmlE += '<div class="g-wrap-item">';
					htmlE += '<span>遗漏</span>';
					htmlE += '<div>';
					htmlF += '<div class="g-wrap-item">';
					htmlF += '<span>遗漏</span>';
					htmlF += '<div>';
					for(var i = 0; i < lenght; i += 1) {
						var item = statisticsList[i];
						if(item.discType < 10) {
							htmlE += '<span>' + item.noOpenCount + '</span>'
						} else {
							htmlF += '<span>' + item.noOpenCount + '</span>'
						}
					}
					htmlE += '</div>';
					htmlE += '</div>';
					htmlF += '</div>';
					htmlF += '</div>'
				}
				var openDiscList = data.data.historyList;
				if(openDiscList != null) {
					var lenght = openDiscList.length;
					for(var i = 0; i < lenght; i += 1) {
						var itemData = openDiscList[i];
						if(itemData.openCode != null) {
							htmlG += '<div class="g-wrap-item">';
							htmlG += '<span>' + itemData.issueNo + '</span>';
							htmlG += '<div><span class="g-qh">';
							var ooLenght = itemData.openCode.length;
							var openDiscRom = itemData.openCode[ooLenght - 1];
							var bNum = itemData.openCode[oLenght - 2];
							htmlG += '<i>' + itemData.openCode[oLenght - 3] + "</i>";
							if(openDiscRom == bNum) {
								htmlG += '<i class="active">' + bNum + "</i>"
							} else {
								htmlG += '<i>' + bNum + "</i>"
							}
							htmlG += '<i class="active">' + openDiscRom + "</i>";
							htmlG += "</span>";
							if(bNum == openDiscRom) {
								htmlG += '<span><i>大</i><i>小</i><i class="active">合</i></span>'
							} else {
								if(openDiscRom > 4 && openDiscRom < 10) {
									htmlG += '<span><i class="active">大</i><i>小</i><i>合</i></span>'
								} else {
									htmlG += '<span><i>大</i><i class="active">小</i><i>合</i></span>'
								}
							}
						}
						htmlG += '</div>';
						htmlG += '</div>'
					}
				}
				$('.g-third').html(htmlG);
				$('.g-first').html(htmlE);
				$('.g-second').html(htmlF)
			}
		},
		error: function(error) {}
	})
} 