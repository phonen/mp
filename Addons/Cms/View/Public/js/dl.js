var img1;
var base64 = [];
require(['zepto', 'app', 'index', 'qrcode'], function($) {
	var Page = {
		render: function() {
			var self = this;
			var erw;
			var a = $('.dl-content').height();
			var b = $('.dl-content').width();
			$('#myCanvas').attr("height", a);
			$('#myCanvas').attr("width", b);
			$('.gzwm').on("click",function  () {
				$('.cpm-mask').show();
				$('.mask-head').show();
			})
			$('.mask-close').on("click",function  () {
				$('.cpm-mask').hide();
				$('.mask-head').hide();
			});
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
			$('.yj-text').on("click", function() {
				$('#yj-img').show()
			});
			$('#yj-img').on("click", function() {
				$(this).hide()
			});
			$.ajax({
				type: "POST",
				url: Apis.getAgentCount,
				contentType: "application/json",
				dataType: "json",
				beforeSend: function(request) {
					request.setRequestHeader("X-Auth-Token", token)
				},
				success: function(data) {
					console.log(data.data);
					if(data.data != null) {
						$(".dl-foot").html('<p>一级用户： <span>' + data.data[0] + '人</span></p><p>二级用户： <span>' + data.data[1] + '人</span></p><p>三级用户： <span>'+
						data.data[2] + '人</span></p><p>四级用户： <span>'+data.data[3] + '人</span></p><p>五级用户： <span>'+data.data[4] + '人</span></p>'+
						'<p>六级用户： <span>'+data.data[5] + '人</span></p>') 
					}
				},
				error: function(error) {}
			});
			$('.wx-btn').on("click", function() {
				$(".loading").show();
				var params = {};
				$.ajax({
					type: "POST",
					url: Apis.userQrCode,
					contentType: "application/json",
					data: JSON.stringify(params),
					dataType: "json",
					beforeSend: function(request) {
						request.setRequestHeader("X-Auth-Token", token)
					},
					success: function(data) {
						erw = data.data;
						var qrcode = new QRCode(document.getElementById("qrcode"), {
							width: 250,
							height: 250
						});
						qrcode.makeCode(erw);
						var c = document.getElementsByTagName("canvas")[0];
						img1 = convertCanvasToImage(c);
						$('#qrcode').hide();
						date(a, b)
					},
					error: function(error) {}
				})
			})
		}
	};
	Page.render()
});

function setOtherRecord(data) {}

function date(a, b) {
	var Mycanvas = document.getElementById("myCanvas");
	ct = Mycanvas.getContext("2d");
	ct.rect(0, 0, Mycanvas.width, Mycanvas.height);
	ct.fill();

	function draw(n) {
		if(n < 2) {
			var img = new Image;
			if(n == 1) {
				img.src = img1.src 
			} else {
				img.src = "/Addons/Cms/View/Public/img/xuanchuan1.png" ;
			}
			img.onload = function() {
				if(n == 1) {
					ct.drawImage(this, b * 0.3334, a * 0.595, b * 0.33, a * 0.22)
				} else {
					ct.drawImage(this, 0, 0, b, a);
					ct.font = "25px 黑体";
					ct.fillStyle = "#FFFFFF";
					ct.fillText($('.l-hd-left span').text(), b * 0.1, a * 0.07)
				}
				draw(n + 1)
			}
		} else {
			base64.push(Mycanvas.toDataURL("image/png"));
			document.getElementById("xcBox").innerHTML = '<img src="' + base64[0] + '">';
			$('.xcBox').show();
			$('.xc').show();
			$(".loading").hide()
		}
	}
	window.onload = draw(0)
}

function convertCanvasToImage(canvas) {
	var image = new Image();
	image.src = canvas.toDataURL("image/png");
	return image
}