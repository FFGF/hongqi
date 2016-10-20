<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>渠道商管理平台 V<?php echo (THINK_VERSION); ?> | 盛大云</title>	
	<link rel="stylesheet" type="text/css" href="/Public/Home/Css/platform.css">
	<script type="text/javascript" src="http://static.grandcloud.cn/www/media/v3/new/js/jquery-1.8.3.min.js"></script>	
    
    <link rel="stylesheet" type="text/css" href="/Public/Home/Css/style.css">
    <link rel="stylesheet" href="/Public/Home/Css/BeatPicker.min.css"/>

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
            <div class="platTitle"><h2>我的站内信</h2></div>
            <i class="refresh"></i>
            <div class="recordCou">
                <div class="emSear">
                    <input type="button" class="signRead" value="标记已读">
                    <form action="<?php echo U('/Message/index');?>" method="get">
                    <input type="input" class="searchEmail" placeholder="搜索：主题" name="searchMsg">
                    <input type="submit" class="searchBtn" value="">
                    </form>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $("#emailall").click(function(){
                            if(this.checked){
                                $(".email input[name='mailid']").each(function(){this.checked=true;});
                            }else{
                                $(".email input[name='mailid']").each(function(){this.checked=false;});
                            }
                        });
                        $(".signRead").click(function(){
                            var msg_id=''
                            $(".email input[name='mailid']:checked").parents("tr").removeClass("unread").addClass("read");
                            $(".email input[name='mailid']:checked").parents("tr").children(".fgf").each(function(){
                                if(msg_id==''){
                                    msg_id=this.value
                                }else{
                                    msg_id=msg_id+','+this.value
                                }
                            })
                            var data={'msg_id':msg_id}
                            var url="message-readSign.html"
                            $.getJSON(url,data,function(response){
                                 $('#notread').html(response['number'])
                            })
                        })
                    })
                </script>
                <table class="Customer_all" cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <th width="10%" class="leftradiu"><input type="checkbox" id="emailall" title="选中/取消选中"></th>
                        <th width="70%" class="lan">主题</th>
                        <th width="20%">时间<div class="sort" type="create_time">
                            <a class="asc <?php if(($_REQUEST['order']== 'asc') AND ($_REQUEST['type']== 'create_time') ): ?>now<?php endif; ?>" title="从小到大"></a>
                            <a class="desc <?php if(($_REQUEST['order']== 'desc') AND ($_REQUEST['type']== 'create_time') ): ?>now<?php endif; ?> " title="从大到小"></a></div></th>
                    </tr>
                    </thead>
                    <tbody class="email">
                    <?php if(!empty($msg)): if(is_array($msg)): $i = 0; $__LIST__ = $msg;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['status'] == 0): ?><tr class="unread">
                                    <td><input type="checkbox" name="mailid" value="111"></td>
                                    <td><a href="<?php echo U('/Message/showMsg?msg_id='.$vo['msg_id']);?>"><?php echo ($vo['title']); ?></a></td>
                                    <td><?php echo ($vo['create_time']); ?></td>
                                    <input name="msg_id" type="hidden" value="<?php echo ($vo['msg_id']); ?>" class="fgf"/>
                                </tr>
                                <?php else: ?>
                                <tr class="read">
                                    <td><input type="checkbox" name="mailid" value="111"></td>
                                    <td><a href="<?php echo U('/Message/showMsg?msg_id='.$vo['msg_id']);?>"><?php echo ($vo['title']); ?></a></td>
                                    <td><?php echo ($vo['create_time']); ?></td>
                                </tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                        <?php else: ?>
                        <td colspan="4" class="text-center"> Oh! 暂时还没有内容! </td><?php endif; ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="11"></td>
                    </tr>
                    </tfoot>
                </table>
                <div class="page" align="center"><?php echo ($_page); ?></div>
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

    <script src="/Public/Home/Js/jquery-1.11.0.min.js"></script>
    <script src="/Public/Home/Js/BeatPicker.min.js"></script>
    <script type="text/javascript" src="/Public/Home/Js/platform.js"></script>

</body>
</html>