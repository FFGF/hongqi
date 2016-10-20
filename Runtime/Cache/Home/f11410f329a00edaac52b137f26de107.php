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
            <div class="platTitle"><h2>提现方式</h2></div>
			<?php if(empty($bank)): ?><form action="<?php echo U('UserInfo/bank');?>" onsubmit="return check()" method="post">
                <div class="certfBank">
                    <div class="CBcon"><i></i><span>开户行</span><input type="text" name="bank_name" id="bank_name">&nbsp;&nbsp;&nbsp;&nbsp;填写完整信息(如浦发银行上海静安支行)</div>
                    <div class="CBcon"><i></i><span>开户名称</span><input type="text" name="bank_user" id="bank_user"></div>
                    <div class="CBcon"><i></i><span>银行卡号</span><input type="text" name="bank_no" id="bank_no" onkeyup="this.value=this.value.replace(/\D/g,'').replace(/....(?!$)/g,'$& ')" ></div>
                    <div class="CBbtn"><input type="submit" value="提交" onclick="javascript:return confirm('提交之后不可修改，只能后台修改')"></div>
                </div>
            </form>
			<?php else: ?>
			<div class="certfBank">
				<div class="CBcon"><i></i><span>开  户  行</span><?php echo ($bank["bank_name"]); ?></div>
				<div class="CBcon"><i></i><span>开户名称</span><?php echo ($bank["bank_user"]); ?></div>
				<div class="CBcon"><i></i><span>银行卡号</span><?php echo ($bank["bank_no"]); ?></div>				
			</div><?php endif; ?>
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
        function check(){
            if(!$("#bank_name").val()){
                alert('请输入开户行');
                return false;
            }
            if(!$("#bank_user").val()){
                alert('请输入开户名称');
                return false;
            }
            if(!$("#bank_no").val()){
                alert('请输入银行卡号');
                return false;
            }
        }
    </script>

</body>
</html>