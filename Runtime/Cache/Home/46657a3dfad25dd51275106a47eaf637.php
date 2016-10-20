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
            <div class="platTitle"><h2>公司信息</h2></div>
            <ul class="dataMyself">
                <li><i></i><span>公司名称：</span><?php echo ($user['company_name']); ?></li>
                <li><i></i><span>公司网址：</span><?php echo ($user['company_website']); ?></li>
                <li><i></i><span>注册资本：</span><?php echo ($user['company_rc']); ?>万</li>
                <li><i></i><span>公司规模：</span><?php echo ($user['company_size']); ?>人</li>
                <li><i></i><span>年销售额：</span><?php echo ($user['company_sv']); ?>万</li>
                <li><i></i><span>固定电话：</span><?php echo ($user['company_tel']); ?></li>
                <li><i></i><span>公司邮箱：</span><?php echo ($user['company_email']); ?></li>
                <li><i></i><span>公司地址：</span><?php echo (formatcompanyaddress($user['company_address'])); ?></li>
                <li><i></i><span>主营业务：</span><?php echo ($user['company_business']); ?></li>
            </ul>
        </div>
        <div class="mt_30"></div>
        <div class="platState white">
            <div class="platTitle"><h2>个人信息</h2></div>
            <ul class="dataMyself">
                <li><i></i><span>联系人：</span><?php echo ($user['user_name']); ?></li>
                <li><i></i><span>手机号码：</span><?php echo ($user['user_phone']); ?></li>
                <li><i></i><span>邮箱：</span><?php echo ($user['user_email']); ?></li>
                <li><i></i><span>QQ：</span><?php echo ($user['user_qq']); ?></li>
            </ul>
        </div>
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

    <script type="text/javascript">
        $(function(){
            $(".now").removeClass("now");
            $(".icon4").next().next().children().eq(0).addClass('now');
        });
        $(".icon2").parent().removeClass("active arrowDown");
        $(".icon4").parent().addClass("active arrowDown");
    </script>

</body>
</html>