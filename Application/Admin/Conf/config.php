<?php
//mob配置
return array(
    
    /* 密码干扰字符 **********************************************/
    'PASSWORDINTER' => 'tr!h$r_vd-3^GD@Q04g',
    'PASSWORDADMIN' => 'admin@cloud',
    /* URL配置 **********************************************/
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL' => 2, //URL模式
    'VAR_URL_PARAMS' => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR' => '-', //PATHINFO URL分割符
    
    /* SESSION 和 COOKIE 配置 **********************************************/
    'SESSION_PREFIX' => 'channels_s_', //session前缀
    'COOKIE_PREFIX' => 'channels_c_', // Cookie前缀 避免冲突

    /* 模板相关配置 **********************************************/
    'TMPL_PARSE_STRING' => array(
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Images',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Js',
    ),
    
    /*邮箱配置**********************************************/
    'MAIL_HOST' =>'mail.grandcloud.cn',//smtp服务器的名称
    'MAIL_SMTPAUTH' =>TRUE, //启用smtp认证
    'MAIL_USERNAME' =>'support@grandcloud.cn',//你的邮箱名
    'MAIL_FROM' =>'support@grandcloud.cn',//发件人地址
    'MAIL_FROMNAME'=>'盛大云',//发件人姓名
    'MAIL_PASSWORD' =>'gckf@13975',//邮箱密码
    'MAIL_CHARSET' =>'utf-8',//设置邮件编码
    'MAIL_ISHTML' =>TRUE, // 是否HTML格式邮件

    'MESSAGE_P' =>'您好！您的渠道商申请审核已经通过，请您<a href="http://www.grandcloud.cn/channels">登录</a>渠道商管理平台进行操作和管理。',//用于发邮件
    'MSG_P'       =>'您好！您的渠道商申请审核已经通过，请您登录渠道商管理平台进行操作和管理。',//用户发短信
    'MESSAGE_F' =>"您好！您的渠道商申请审核不通过，具体原因为：c，请联系您的渠道经理，感谢您的理解和配合!",
    'WITHDRAW_Y' => '您好！您的渠道管理平台提现申请已经审核通过，十个工作日内将汇至您的银行账户，请留意查看。如有疑问，请联系您的渠道经理，谢谢。',
    'WITHDRAW_N' =>"您好！您的渠道商提现申请审核不通过，具体原因为：c，请联系您的渠道经理，感谢您的理解和配合!",
    'DEDUCT' => "您好！您有一笔金额： c 元被冻结，详情请联系您的渠道经理，感谢您的理解和配合！",
    'DEDUCT_Y' => "您好！您有一笔金额：c 元被扣除，详情请联系您的渠道经理，感谢您的理解和配合！",
    'DEDUCT_N' => "您好！您被冻结的金额：c  元已经返还到您的账户，请确认您的余额，给您带来的不便敬请谅解！",
);

