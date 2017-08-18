require(['zepto', 'dialog'],
function($, dialog) { 
    var Page = {
        render: function() {
            $('.cz-btn').bind("click",
            function() {
                $('.cz').show();
                $('.czBox').show()
            });
            $('.btn-close').on("click",
            function() {
                $('.cz').hide()
            });
            $(".czBtn").on("click",
            function() {
                var amount = $(this).attr("data-type");
                amount = amount * 100;
                recharge(amount)
            })
        }
    };
    Page.render()
});
var accountAmount = 0; 
var apiHomeSrc = location.origin;
var Apis = {
    'activatInvitationCode': apiHomeSrc + '/addon/LdhQguess/Mobile/user_activatInvitationCode',
    'getAccountInfo': apiHomeSrc + '/addon/LdhQguess/Mobile/user_getAccountInfo',
    'getAccountInfoByCache': apiHomeSrc + '/addon/LdhQguess/Mobile/user_getAccountInfoByCache',
    'getBetLottery': apiHomeSrc + '/addon/LdhQguess/Mobile/disc_getBetLottery',
    'getNextBetLottery': apiHomeSrc + '/addon/LdhQguess/Mobile/disc_getNextBetLottery',
    'queryLotteryResult': apiHomeSrc + '/addon/LdhQguess/Mobile/disc_queryLotteryResult',
    'getBetOpenLotteryRecord': apiHomeSrc + '/addon/LdhQguess/Mobile/disc_getBetOpenLotteryRecord',
    'getOddsConfig': apiHomeSrc + '/addon/LdhQguess/Mobile/oddsConfig_getAllConfig',
    'discBet': apiHomeSrc + '/addon/LdhQguess/Mobile/disc_bet',
    'getOpenAmount': apiHomeSrc + '/addon/LdhQguess/Mobile/disc_getOpenAmount',
    'getWinRecord': apiHomeSrc + '/addon/LdhQguess/Mobile/disc_getBetRecord',
    'getOpenLotteryRecord': apiHomeSrc + '/addon/LdhQguess/Mobile/query_queryLotteryRecord',
    'getBrokerageRecord': apiHomeSrc + '/addon/LdhQguess/Mobile/yongjin_getBrokerageRecord',
    'getOrderRecord': apiHomeSrc + '/addon/LdhQguess/Mobile/order_getOrderRecord',
    'recharge': apiHomeSrc + '/addon/LdhQguess/Mobile/pay_recharge',
    'withdrawCash': apiHomeSrc + '/addon/LdhQguess/Mobile/pay_withdrawCash',
    'getTransferCount': apiHomeSrc + '/addon/LdhQguess/Mobile/pay_getTransferCount',
    'queryRechargeOrder': apiHomeSrc + '/addon/LdhQguess/Mobile/order_queryRechargeOrder',
    'queryOpenOrder': apiHomeSrc + '/addon/LdhQguess/Mobile/order_queryOpenOrder',
    'queryRank': apiHomeSrc + '/addon/LdhQguess/Mobile/query_queryRankList',
    'userQrCode': apiHomeSrc + '/addon/LdhQguess/Mobile/user_userCodeUrl',
    'queryWithdraw': apiHomeSrc + '/addon/LdhQguess/Mobile/query_queryWithDrawList',
    'updateRead': apiHomeSrc + '/addon/LdhQguess/Mobile/disc_updateRead',
    'getAgentCount': apiHomeSrc + '/addon/LdhQguess/Mobile/query_getAgentCount'
};
function setAmount(amount) {
    $('.l-hd-right span i').text(parseFloat(amount))
};
function bet(betNum, betAmount, discType) {
    if (betNum == null || betAmount == null || discType == null) {
        return false
    }
    var betParam = {
        "betNum": betNum,
        "betAmount": betAmount,
        "discType": discType
    };
    console.log(JSON.stringify(betParam));
    $('.loading').show();
    $.ajax({
        type: "POST",
        url: Apis.discBet,
        //contentType: "application/json",
        data: betParam,
        dataType: "json",
        beforeSend: function(request) {
            request.setRequestHeader("X-Auth-Token", token)
        }, 
        success: function(data) { 
            $('.loading').hide();
            getAccountInfo(true);
            if (data.data != null) {
                dialogWin.show({
                    type: '1',
                    content: "恭喜投注成功！",
                    btnText: "确定",
                    handler: function() {
                        dialogWin.close()
                    }
                })
            }
        },
        error: function(error) {
            $('.loading').hide();
            showErrorMsg(error, dialogWin, false)
        }
    })
}
function updateRead(payOrderNo, asyncFlag) {
    if (payOrderNo == null) {
        return false
    }
    if (asyncFlag == null) {
        asyncFlag = true
    }
    var param = {
        "id": payOrderNo
    };
    $.ajax({
        type: "POST",
        url: Apis.updateRead,
        contentType: "application/json",
        data: JSON.stringify(param),
        dataType: "json",
        async: asyncFlag,
        beforeSend: function(request) {
            request.setRequestHeader("X-Auth-Token", token)
        },
        success: function(data) {
            if (data.success == true) {}
        },
        error: function(error) {}
    })
}
function getAccountInfo(asyncFlag) {
    if (asyncFlag == null) {
        asyncFlag = true
    }
    var getAccountInfoParam = {};
    $.ajax({
        type: "POST",
        url: Apis.getAccountInfo,
        contentType: "application/json",
        data: JSON.stringify(getAccountInfoParam),
        dataType: "json",
        async: asyncFlag,
        beforeSend: function(request) {
            request.setRequestHeader("X-Auth-Token", token)
        },
        success: function(data) {
            if (data.data != null) {
                accountAmount = (parseFloat(data.data.amount) / 100).toFixed(2);
                $('.l-hd-left span').text("会员ID：" + data.data.userNo);
                setAmount(accountAmount);
                setOtherRecord(data.data);
                sessionStorage.setItem("userid", data.data.userNo);
                sessionStorage.setItem("money", accountAmount)
            }
        },
        error: function(error) {}
    })
}
function getAccountInfoByCache(asyncFlag) {
    if (asyncFlag == null) {
        asyncFlag = true
    }
    var getAccountInfoParam = {};
    $.ajax({
        type: "POST",
        url: Apis.getAccountInfoByCache,
        contentType: "application/json",
        data: JSON.stringify(getAccountInfoParam),
        dataType: "json",
        async: asyncFlag,
        beforeSend: function(request) {
            request.setRequestHeader("X-Auth-Token", token)
        },
        success: function(data) {
            if (data.data != null) {
                $('.l-hd-left span').text("会员ID：" + data.data.userNo);
                setOtherRecord(data.data);
                sessionStorage.setItem("userid", data.data.userNo)
            }
        },
        error: function(error) {}
    })
}
function showErrorMsg(error, dialogWin, reloadFlag) {
    if (reloadFlag == null) {
        reloadFlag = true
    }
    var msg = "";
    if (error.responseText == null || error.responseText == "") {
        msg = "系统异常"
    } else {
        var errorData = JSON.parse(error.responseText);
        msg = errorData.msg
    }
    dialogWin.show({
        type: '1',
        content: msg,
        btnText: "确定",
        handler: function() {
            if (reloadFlag == true) {
                location.reload(true)
            } else {
                dialogWin.close()
            }
        }
    })
}
function recharge(amount) {
    if (amount == null) {
        return false
    }
    var param = {
        "amount": amount,
        "playType": 1
    };
    $('.loading').show();
    $.ajax({
        type: "POST",
        url: Apis.recharge,
        //contentType: "application/json",
        data: param,
        dataType: "json",
        beforeSend: function(request) {
            request.setRequestHeader("X-Auth-Token", token)
        },
        success: function(data) {
            $('.loading').hide();
			 
            if (data.data != null) { 
                window.location.href = data.data.payUrl
            }
        }, 
        error: function(error) {  
            $('.loading').hide();
			 $('.cz').hide(); 
            showErrorMsg(error, dialogWin, false)
        }
    })
}
function withdrawCash(transferType) {
    if (transferType == null) {
        return false
    }
    var param = {
        "transferType": transferType,
        "playType": 1
    };
    $.ajax({
        type: "POST",
        url: Apis.getTransferCount,
       // contentType: "application/json",
        data: param, 
        dataType: "json",
        beforeSend: function(request) {
            request.setRequestHeader("X-Auth-Token", token)
        },
        success: function(data) {
            if (data.data != null) {
                dialogWin.close();
                var transferTypeText = transferType == "1" ? "余额": "佣金";
                var transferCount = data.data;
                if (transferCount <= 0) {
                    dialogWin.show({
                        type: '4',
                        content: "您本日" + transferTypeText + "兑换机会已用完",
                        content2: "次日0点恢复兑换",
                        btnText: "确定",
                        handler: function() {}
                    })
                } else {
                    dialogWin.show({
                        type: '4',
                        content: "您本日还剩" + data.data + "次",
                        content2: transferTypeText + "兑换机会，确定兑换？",
                        btnText: "确定",
                        handler: function() {
							
                            $.ajax({
                                type: "POST",
                                url: Apis.withdrawCash,
                                //contentType: "application/json",
                                data: param, 
                                dataType: "json",
                                beforeSend: function(request) {
                                    request.setRequestHeader("X-Auth-Token", token)
                                },
                                success: function(data) {
                                    if (data.success == true) {
                                        dialogWin.show({
                                            type: '2',
                                            content: "操作成功",
                                            btnText: "确定",
                                            handler: function() {
                                                getAccountInfo(false);
                                                dialogWin.close()
                                            }
                                        })
                                    }
                                },
                                error: function(error) {
                                    showErrorMsg(error, dialogWin, false)
                                }
                            })
                        }
                    })
                }
            }
        },
        error: function(error) {
            showErrorMsg(error, dialogWin, false)  
        }
    })
}