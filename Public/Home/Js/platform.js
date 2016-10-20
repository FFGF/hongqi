$(function(){
	if( $(window).width() < 1200 ){
		$(".platLeft").css("left","0px");
		$(".platLeft").css("margin-left","0px");
	}else{
		$(".platLeft").css("left","50%");
		$(".platLeft").css("margin-left","-598px");
	}
	$(window).resize(function(){
		if( $(window).width() < 1200 ){
			$(".platLeft").css("left","0px");
			$(".platLeft").css("margin-left","0px");
		}else{
			$(".platLeft").css("left","50%");
			$(".platLeft").css("margin-left","-598px");
		}
	})
	//选中状态菜单
	if( $(".active > .childLi").length != 0 ){
		$(".active").addClass('arrowDown');
		$(".active > .childLi").show();
	}
	//可切换菜单
	$(".platNav > li").click(function(){
		$(this).addClass("active").siblings("li").removeClass("active arrowDown").children().css("display","block");
		$(this).siblings().children("ul").hide();
		if( $(".active > .childLi").length != 0 ){
			$(".active").addClass('arrowDown');
			$(".active > .childLi").show();
		}

	})
	//优惠券管理页面的状态选择
	$(".couponState i").click(function(){
		$(".couSta").show();
		$(".couSta b").click(function(){
			$(".coupState").val( $(this).html() );
			$(".couSta").hide();
		})
	})
	$(".coupState").focus(function(){
		$(".couSta").show();
		$(".couSta b").click(function(){
			$(".coupState").val( $(this).html() );
			$(".couSta").hide();
		})
	})	
	$(".couSta").mouseover(function(){
		$(".couSta").show();
	}).mouseout(function(){
		$(".couSta").hide();
	})
	$(".coupState").mouseout(function(){
		$(".couSta").hide();
	})
	//全部客户页面详细按钮
	$(".info_more").click(function(){
		$(".info_bg").show();
		$(".detailed").show();
		$(".info_close").click(function(){
			$(".info_bg").hide();
			$(".detailed").hide();
		})
	})
	//管理中心页面提现功能
	$(".cash").click(function(){
		var cashtimer = null;
		if($(".cashhide").html()<=0)
			var cashhide = 0;
		else
			cashhide = parseFloat( $(".cashhide").html() );
		$(".info_bg").show();
		$(".withdraw_cash").show();
		$(".wdcashcue span").hide();
		$(".wdcashIpt").val( cashhide );
		$(".wdcashIpt").bind("keyup",function(){
			if( parseFloat( $(this).val() ) > cashhide){
				$(".wdcashcue span").html("您最多提取的金额为"+ cashhide + "元，请重新输入").show();
				$(".wdcashIpt").focus();
			}else{
				$(".wdcashcue span").hide();
			}
		})
		$(".info_close").click(function(){
			$(".info_bg").hide();
			$(".withdraw_cash").hide();
			$(".wdcStart").show();
			$(".wdcEnd").hide();
		})
	})

})
//提现成功返回
function cashover(){
	$(".wdcStart").hide();
	$(".wdcEnd").show();
	var i=5;
	$(".wdcEnd span").html( i );
		cashtimer = setInterval(function(){
		i--;
		$(".wdcEnd span").html( i );
		if (i==0) { 
			clearInterval(cashtimer);
			$(".info_bg").hide();
			$(".withdraw_cash").hide();
			$(".wdcStart").show();
			$(".wdcEnd").hide();
		};
	},1000)
}
function toggel(attr){
	if(attr == "reSearchM"){
		$(".reSearchM").removeClass("none");
		$(".RCsearch").addClass("none");
		$(".RCsearchSimple").html("简易");
	}else{
		$(".RCsearch").removeClass("none");
		$(".reSearchM").addClass("none");
		$(".RCsearchSimple").html("高级");
	}
	$(".change").click(function(){
		$(".reSearchM").toggleClass("none");
		$(".RCsearch").toggleClass("none");
		if( $(".RCsearchSimple").html() == "简易"){
			$(".RCsearchSimple").html("高级");
		}else{
			$(".RCsearchSimple").html("简易");
		}
	})
}
//toggel("RCsearch");