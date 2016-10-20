<?php

//mob配置
return array(
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
    'MAIL_ADDRESS' => '', // 邮箱地址
    'MAIL_SMTP' => '', // 邮箱SMTP服务器
    'MAIL_LOGINNAME' => '', // 邮箱登录帐号
    'MAIL_PASSWORD' => '', // 邮箱密码
    'MAIL_PORT'=>'',
    'MAIL_SECURE'=>'ssl',
    'MAIL_CHARSET' => 'UTF-8', //编码
    'MAIL_AUTH' => true, //邮箱认证
    'MAIL_HTML' => true, //true HTML格式 false TXT格式
    
    'MESSAGE_1' => '尊敬的用户，您的动态验证码是%code%，该验证码10分钟内有效，请尽快使用。为了您的账户安全，请勿泄露该验证码。',
);

