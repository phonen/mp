require(['zepto','app','index'],function($){var Page={render:function(){var self=this;$('.icon-item-01').on("click",function(){window.location.href="#home";$('.icon-item').eq(0).addClass('active').siblings().removeClass('active')});$('.icon-item-02').on("click",function(){isOpen=false;window.location.href="#get-cash";$('.icon-item').eq(1).addClass('active').siblings().removeClass('active')});$('.icon-item-03').on("click",function(){window.location.href="#check";$('.icon-item').eq(2).addClass('active').siblings().removeClass('active')});$('.icon-item-04').on("click",function(){window.location.href="#dl";$('.icon-item').eq(3).addClass('active').siblings().removeClass('active')});$('.icon-item-05').on("click",function(){window.location.href="#kf";$('.icon-item').eq(4).addClass('active').siblings().removeClass('active')});getAccountInfoByCache(true);var params={"playType":2};$.ajax({type:"POST",url:Apis.queryWithdraw,contentType:"application/json",data:JSON.stringify(params),dataType:"json",beforeSend:function(request){request.setRequestHeader("X-Auth-Token",token)},success:function(data){if(data.data!=null){var dataList=data.data.withdrawList;var htmlE='<div class="list-item" style="height: 0.66rem;"><span style="color: #FEEDE5; text-align: center;">时间</span><span style="color: #FEEDE5;text-align: center;">金额</span><span style="color: #FEEDE5;text-align: center;">类型</span><span style="color: #FEEDE5;text-align: center;">状态</span></div>';if(dataList!=null){var lenght=dataList.length;for(var i=0;i<lenght;i+=1){var item=dataList[i];var status=dataList[i].withdrawStatus;if(item.money>0){htmlE+='<div class="list-item">';htmlE+='<span>'+item.createTime+'</span>';htmlE+='<span>'+item.money+'金币   </span>';htmlE+='<span>'+item.withdrawType+'</span>';if(status=='成功'){htmlE+='<span class="active">'+status+'</span>'}else{htmlE+='<span class="wzf">处理中</span>'}htmlE+='</div>'}}}$('.page-list-box').html(htmlE)}else{}},error:function(error){}})}};Page.render()});function setOtherRecord(data){$(".l-toggle-title2 span").text('兑换记录（总：' + parseFloat((data.cashRecordAmount+data.cashBrokerageRecordAmount) / 100) + '金币）')}