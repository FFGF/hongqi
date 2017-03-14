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
    
 
);

