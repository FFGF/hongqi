<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>渠道商管理平台 V<?php echo (THINK_VERSION); ?> | 盛大云</title>	
	<link rel="stylesheet" type="text/css" href="/Public/Home/Css/platform.css">
	<script type="text/javascript" src="http://static.grandcloud.cn/www/media/v3/new/js/jquery-1.8.3.min.js"></script>	
    
</head>
<body>
<div class="wrap">
	<div class="platLeft">
		<div class="platLogo"><a href="http://www.grandcloud.cn/"><img src="/Public/Home/Images/platLogo.png"></a></div>
		<ul class="platNav">
			<li <?php if(CONTROLLER_NAME=='Index'): ?>class="active arrowDown"<?php endif; ?> >
				<i class="icon icon1"></i>管理中心<i class="arrow"></i>
				<ul class="childLi">
					<li <?php if(CONTROLLER_NAME=='Index' AND ACTION_NAME=='index'): ?>class="now"<?php endif; ?> ><a href="/index.html">帐户状态</a></li>
				</ul>			
			</li>
			<li <?php if(CONTROLLER_NAME=='Customer'): ?>class="active arrowDown"<?php endif; ?> >
				<i class="icon icon2"></i>客户管理<i class="arrow"></i>
				<ul class="childLi">
					<li <?php if(CONTROLLER_NAME=='Customer' AND ACTION_NAME=='add'): ?>class="now"<?php endif; ?> ><a href="/customer-add.html">添加客户</a></li>
					<li <?php if(CONTROLLER_NAME=='Customer' AND (ACTION_NAME=='index' OR ACTION_NAME=='info' OR ACTION_NAME=='product')): ?>class="now"<?php endif; ?> ><a href="/customer-index.html">全部客户</a></li>
					<li <?php if(CONTROLLER_NAME=='Customer' AND ACTION_NAME=='order'): ?>class="now"<?php endif; ?> ><a href="/customer-order.html">客户订单</a></li>
				</ul>
			</li>
			<li <?php if(CONTROLLER_NAME=='Finance'): ?>class="active arrowDown"<?php endif; ?> >
				<i class="icon icon3"></i>财务管理<i class="arrow"></i>
				<ul class="childLi">
					<li <?php if(CONTROLLER_NAME=='Finance' AND ACTION_NAME=='index'): ?>class="now"<?php endif; ?> ><a href="/finance-index.html">提现记录</a></li>
					<li <?php if(CONTROLLER_NAME=='Finance' AND ACTION_NAME=='generatepromoindex'): ?>class="now"<?php endif; ?> ><a href="/finance-generatepromoindex.html">生成优惠券</a></li>
					<li <?php if(CONTROLLER_NAME=='Finance' AND ACTION_NAME=='promomanagement'): ?>class="now"<?php endif; ?> ><a href="/finance-promomanagement.html">优惠券管理</a></li>
				</ul>
			</li>
			<li <?php if(CONTROLLER_NAME=='UserInfo'): ?>class="active arrowDown"<?php endif; ?> >
				<i class="icon icon4"></i>资料管理<i class="arrow"></i>
				<ul class="childLi">
					<li <?php if(CONTROLLER_NAME=='UserInfo' AND ACTION_NAME=='index'): ?>class="now"<?php endif; ?> ><a href="<?php echo U('UserInfo/index');?>">我的资料</a></li>
					<li <?php if(CONTROLLER_NAME=='UserInfo' AND ACTION_NAME=='certificate'): ?>class="now"<?php endif; ?> ><a href="<?php echo U('UserInfo/certificate');?>">我的证件</a></li>
					<li <?php if(CONTROLLER_NAME=='UserInfo' AND ACTION_NAME=='bank'): ?>class="now"<?php endif; ?> ><a href="<?php echo U('UserInfo/bank');?>">银行账户</a></li>
				</ul>
			</li>
		</ul>
	</div>
    <div class="platRight">
        <div class="platTop">
            <div class="Tit">渠道商管理平台</div>
            <div class="TopR">
                <a class="emNews" href="<?php echo U('/Message/index');?>">
                    <img src="/Public/Home/Images/e-mail.png">
                    <i id="notread"></i>
                </a>|
                <a href="<?php echo U('/index/logout');?>">退出</a>
            </div>
        </div>
        
	<div class="platCon">
		<div class="platState white">
			<div class="platTitle"><h2>帐户状态</h2></div>
			<ul class="platStateCon">
				<li></li><li></li>
                <li><i></i><span>登录账号：</span><?php echo ($info["user_name"]); ?>（<?php echo ($info["user_login_name"]); ?>）</li>
				<li><i></i><span>当前等级：</span><?php echo (formatuserlevel($info["level"])); ?></li>
				<li><i></i><span>当前收入：</span><?php echo ($info["total"]); ?> 元<input type="button" value="提现" class="cash"></li>
				<?php if(empty($info['last_login']) == false): ?><li><i></i><span>最近登录：</span><?php echo ($info["last_login"]); ?></li><?php endif; ?>
			</ul>
		</div>
		<div class="mt_30"></div>
		<div class="platState white">
			<div class="platTitle"><h2>年收入走势</h2></div>
			<div class="chart">
				<div id='canvasDiv'></div>
			</div>
		</div>
		<div class="mt_30"></div>
		<div class="platState white">
			<div class="platTitle"><h2>月收入走势</h2></div>
			<div class="chartTitle"></div>
			<div class="chart">
				<div id='canvasDiv2'></div>
			</div>
			<div class="month">
				<input type="radio" value="1" name="Month" id="m1" ><label for="m1">1月</label>
				<input type="radio" value="2" name="Month" id="m2" ><label for="m2">2月</label>
				<input type="radio" value="3" name="Month" id="m3" ><label for="m3">3月</label>
				<input type="radio" value="4" name="Month" id="m4" ><label for="m4">4月</label>
				<input type="radio" value="5" name="Month" id="m5" ><label for="m5">5月</label>
				<input type="radio" value="6" name="Month" id="m6" ><label for="m6">6月</label>
				<input type="radio" value="7" name="Month" id="m7" ><label for="m7">7月</label>
				<input type="radio" value="8" name="Month" id="m8" ><label for="m8">8月</label>
				<input type="radio" value="9" name="Month" id="m9" ><label for="m9">9月</label>
				<input type="radio" value="10" name="Month" id="m10" ><label for="m10">10月</label>
				<input type="radio" value="11" name="Month" id="m11" ><label for="m11">11月</label>
				<input type="radio" value="12" name="Month" id="m12" ><label for="m12">12月</label>
			</div>
		</div>
		<div class="mt_30"></div>
	</div>
	<div class="info_bg"></div>
	<div class="withdraw_cash">
		<div class="cashhide none"><?php echo ($info["total"]); ?></div>
		<i class="info_close"></i>
		<div class="wdcashtit">提现</div>
		<div class="wdcStart">
			<div class="wdcashcon"><i></i><span>提现金额</span><input type="text" name="money" id="money" class="wdcashIpt" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" ></div>
			<div class="wdcashcue"><span style="display:none"></span></div>
			<div class="wdcashBtn"><input type="button" value="确认转出" onclick="submit_withdrawals()"></div>
		</div>
		<div class="wdcEnd"  style="display:none">
			提现申请提交成功<br><span></span>
		</div>
	</div>

    </div>

</div>
<script type="text/javascript" src="/Public/Home/Js/platform.js"></script>
<script type="text/javascript">
    $('.sort > a').click(function(){
        var link  = window.location.href.toString();
        var type  = $(this).parent().attr('type');
        var order = $(this).attr('class').split(' ')[0];
        link = link.replace(/([?&-]type[=-])([^&-]*)/gi,"$1"+type);
        link = link.replace(/([?&-]order[=-])([^&-]*)/gi,"$1"+order);
        if(link.match(/[?&-]order[=-]/)==null){
            link += (link.indexOf('?') == -1 ? '?' : '&') + "type="+type+"&order="+order;
        }
        window.location.href = link;
    });
    $(function(){
        var data={}
        var url="message-notread.html"
        $.getJSON(url,data,function(response){
            $('#notread').html(response['number'])
        })
    })
</script>

<script type="text/javascript" src="/Public/Home/Js/ichart.latest.min.js"></script>
<script type="text/javascript">
	function submit_withdrawals(){
		var data = {"money": $('#money').val()}
		if(data.money==''){
			alert('提现金额不可以为空');
			$("#money").focus();
			return false;
		}else{
			var url = "/finance-withdraw.html";
			$.getJSON(url, data, function(response) {
				if (response.status == 1) {
					cashover();
				}else{
					alert(response.info);
				}
			});
		}
	}
</script>

<script type="text/javascript">

    Array.prototype.sum = function () {
        return this.reduce(function (partial, value) {
            return partial + value
        }, 0)
    };
    Array.prototype.max = function () {
		return this.reduce(function(partial, value) {
			return Math.max(partial,value);
		},0)
	};
    Array.prototype.min = function () {
		return this.reduce(function(partial, value) {
			return Math.min(partial,value);
		},0)
	};
    
   /*今年获得每个月天数*/
   var daysOfMonths = [];
   for(var i=0 ;i < 12;i++){
       var days = new Date(new Date().getFullYear(),i+1,0).getDate();
       daysOfMonths.push(days);
   }

	/*获得一年每天的收入情况*/
	var datalist = [];
	$.ajaxSettings.async = false;
	$.getJSON('index-datalist',function(data){
		for(var i=0; i < data.length ; i++) {
			datalist.push(data[i]);
		}
   });

  /*获得每个月收入情况*/
   var dataOfMonths=[];
   for(var i=0 ;i < 12;i++){
        var start = daysOfMonths.slice(0,i).sum();
        var end   = daysOfMonths.slice(0,i+1).sum();
		var value = parseFloat(datalist.slice(start,end).sum()).toFixed(2); //精确到小数点后两位
        dataOfMonths.push(value);
   }
   
    var max = dataOfMonths.max();
    var min = dataOfMonths.min();
    var abs_max = Math.max(Math.abs(max),Math.abs(min));
	var flag = abs_max > 10000 ?  true : false;   //判断是否超过万元
   
	/*构造要显示的data数组*/
	var data = [];
	for(var i=0 ;i<dataOfMonths.length;i++){
		var value = flag ? dataOfMonths[i] *100.0 / 1000000 : dataOfMonths[i];
		data.push({name : (i+1)+'月',value : value,color:'#41a9cc'});
	}
    
	/*计算纵坐标范围与步长*/
	var end_scale = 1000,scale_space=100,start_scale = 0;
    if(min < 0){
        var min_len = parseInt(Math.abs(min)).toString().length;
        var min_temp = parseInt(Math.abs(min)/Math.pow(10, min_len-1));
        start_scale= - 1 * (min_temp+1) * Math.pow(10, min_len-1);
        start_scale = flag ? start_scale * 100.0 / 1000000 : start_scale;
    }
	if(max > 0){
        var max_len = parseInt(max).toString().length;
		var max_temp = parseInt(max/Math.pow(10, max_len-1));
		end_scale = (max_temp+1) * Math.pow(10, max_len-1);
		end_scale = flag ? end_scale * 100.0 / 1000000 : end_scale;
	}
    scale_space = (end_scale - start_scale) / 10;


	$(function(){
		var chart = new iChart.Column2D({
			render : 'canvasDiv', //渲染的Dom目标,canvasDiv为Dom的ID
			data: data, //绑定数据
			title : '收势图', //设置标题
			width : 800, //设置宽度，默认单位为px
			height : 348, //设置高度，默认单位为px
			shadow:true, //激活阴影
			shadow_color:'#c7c7c7', //设置阴影颜色
			coordinate:{ //配置自定义坐标轴
				scale:[{ //配置自定义值轴
					 position:'left', //配置左值轴
					 start_scale: start_scale, //设置开始刻度为0
					 end_scale: end_scale,   //设置结束刻度为26 dateOfMonth.max() %Math.pow(10,dateOfMonth.max().toString().length -1)
					 scale_space: scale_space, //设置刻度间距2
					 listeners:{    //配置事件
						parseText:function(t,x,y){ //设置解析值轴文本
							return {text:t+ (flag?"万元":"元")}
						}
					}
				}]
			}
		});
		//调用绘图方法开始绘图
		chart.draw();
	});

	$(function(){
		function changeDate(){
			var myTime =new Date();
			var cDate;
			if( $(".month input:checked").val() ){
				cDate = $(".month input:checked").val();
			}else{
				cDate = myTime.getMonth()+1;
				$("#m"+cDate).attr("checked","checked");
			}

			var daysLabels= ["01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31"];
			var labels = daysLabels.slice(0,daysOfMonths[cDate-1]);

			/*获得当月每天输入情况*/
            var start =daysOfMonths.slice(0,cDate-1).sum();
            var end =  start + daysOfMonths[cDate-1];
			var flow = datalist.slice(start,end);

            var end_scale=flow.max() < 1000? 1000: flow.max() < 10000?10000:100000;
            var scale_space= flow.max() < 1000? 100 : flow.max() < 10000?1000 :10000;
            var start_scale = 0;
            var max = flow.max();
            var min = flow.min();
            var abs_max = Math.max(Math.abs(max),Math.abs(min));
            var flag = abs_max > 100000 ?  true : false;   //判断是否超过万元
            
            if(flag) {
                for(var i =0 ;i<end-start;i++){
                    flow[i] = flow[i]*100.0/1000000;
                }
            }
            
            if(min < 0){
                var min_len = parseInt(Math.abs(min)).toString().length;
                var min_temp = parseInt(Math.abs(min)/Math.pow(10, min_len-1));
                start_scale= - 1 * (min_temp+1) * Math.pow(10, min_len-1);
                start_scale = flag ? start_scale * 100.0 / 1000000 : start_scale;
            }
           if(max > 0){
                var max_len = parseInt(max).toString().length;
                var max_temp = parseInt(max/Math.pow(10, max_len-1));
                end_scale = (max_temp+1) * Math.pow(10, max_len-1);
                end_scale = flag ? end_scale * 100.0 / 1000000 : end_scale;
            }
            scale_space = (end_scale-start_scale)/10;
            
			var data = [
			         	{
			         		name : 'PV',
			         		value:flow,
			         		color:'#2e97de',
			         		line_width:1
			         	}
			         ];
			var chart2 = new iChart.LineBasic2D({
				render : 'canvasDiv2',
				data: data,
				title : cDate + '月收势图',
				width : 800,
				height : 348,
				shadow:true,
				shadow_color : '#c7c7c7',
				tip:{
					enable:true,
					shadow:true,
					listeners:{
						 //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
						parseText:function(tip,name,value,text,i){
							return "<span style='color:#005268;font-size:12px;'>"+labels[i]+"日收入:<br/>"+
							"</span><span style='color:#005268;font-size:20px;'>"+value+(flag?'万':'')+"元</span>";
						}
					}
				},
				crosshair:{		//十字线
					enable:true,
					line_color:'#ec4646'
				},
				sub_option : {
					smooth : true,
					label:false,
					hollow:false,
					hollow_inside:false,
					point_size:8
				},
				coordinate:{
					scale:[{
						 position:'left',
						 start_scale:start_scale,
						 end_scale: end_scale,
						 scale_space: scale_space,
						 scale_size:2,
						 scale_enable : false,
						 label : {color:'#9d987a',font : '微软雅黑',fontsize:11,fontweight:600},
						 scale_color:'#9f9f9f'
					},{
						 position:'bottom',
						 label : {color:'#9d987a',font : '微软雅黑',fontsize:11,fontweight:600},
						 scale_enable : false,
						 labels:labels
					}]
				}
			});
			//利用自定义组件构造左侧说明文本
			chart2.plugin(new iChart.Custom({
					drawFn:function(){
						//计算位置
						var coo = chart2.getCoordinate(),
							x = coo.get('originx'),
							y = coo.get('originy'),
							w = coo.width,
							h = coo.height;
						//在左上侧的位置，渲染一个单位的文字
						chart2.target.textAlign('start')
						.textBaseline('bottom')
						.textFont('600 11px 微软雅黑')
						.fillText('月收入('+(flag?'万':'')+'元)',x-40,y-12,false,'#9d987a')
						.textBaseline('top')
						.fillText('(日期)',x+w+12,y+h+10,false,'#9d987a');

					}
			}));
		//开始画图
		chart2.draw();
		}

		changeDate();

		$(".month :radio").click(
			changeDate
		);


	});

</script>

</body>
</html>