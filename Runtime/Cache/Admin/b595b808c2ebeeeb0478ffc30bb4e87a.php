<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>渠道商管理平台 V<?php echo (THINK_VERSION); ?> | 盛大云</title>
	<link rel="stylesheet" type="text/css" href="/Public/Admin/Css/style.css">
</head>
<body class="bgblue">
<div class="m_con">
	<div class="login_shadow" id="login_shadow"></div>
	<div class="m_login">
		<div class="l"><img src="/Public/Admin/Images/m-login.png"></div>
		<form method="post" action="<?php echo U('Admin/index-login');?>">
            <div class="r">
                <div class="login_form">
                    <div class="f_error" id="f_error"></div>
                    <input type="input" class="username" id="admin_id" name="admin_id" placeholder="请输入用户名" onblur="return emailCheck('email', 'email')">
                    <input type="password" class="password" id="admin_login_pwd" name="admin_login_pwd" placeholder="请输入密码">
                    <input type="submit" value="登录" class="login_button">
                </div>
            </div>
        </form>

	</div>
</div>
</body>
</html>