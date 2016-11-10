<?php

//require('/opt/conf/channels.grandcloud.cn/config/db.php');


/* 测试环境数据库配置 */
$db=array('DB_TYPE' => 'mysql','DB_HOST' => '127.0.0.1','DB_NAME' => 'hongqi1','DB_USER' => 'root','DB_PWD' => '','DB_PORT' => '3306','DB_PREFIX' => '',);

/* 生产环境数据库配置 */
//$db=array('DB_TYPE' => 'mysqli','DB_HOST' => '10.177.8.44','DB_NAME' => 'snda_cloud_accounts','DB_USER' => 'dbrw','DB_PWD' => 'hv8nbjkux61xyfbhjl9pwv1lta1figku','DB_PORT' => '3306','DB_PREFIX' => '',);


return $db;
