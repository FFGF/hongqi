<?php

//全局公共库文件

/**
 * AES-CBC
 * @param type $data 
 * @param type $isaes 解密或加密
 * @return type
 */
function AES($data, $isaes = true) {
    $privateKey = "1234567890abcDEF";
    $iv = "1234567890abcdef";

    if ($isaes) {//加密        
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
        return str_replace('==', '', base64_encode($encrypted));
    } else { //解密
        $encryptedData = base64_decode($data);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
        return trim($decrypted);
    }
}


/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

/**
 * 日志记录
 * @param type $type 信息类型
 * @param type $contents 处理后的日志信息
 * @param type $user_id 用户ID
 */

function addOtherLog($type, $contents, $user_id = null) {
    $user_info = session('user');
    $user_id = empty($user_id) ? $user_info['user_id'] : $user_id;
    $data['user_id'] = $user_id;
    $data['type'] = $type;
    $data['contents'] = $contents;
    M('channels_other_logs')->add($data);
}

function addAccountLog($type, $contents, $user_id = null) {
    $user_info = session('user');
    $user_id = empty($user_id) ? $user_info['user_id'] : $user_id;
    $data['user_id'] = $user_id;
    $data['type'] = $type;
    $data['contents'] = $contents;
    M('channels_account_logs')->add($data);
}

/**
 * 检测邮箱和手机是否被注册使用
 * @param array $field 
 * @return boole
 */
function isReged($field) {
    empty($field['mobile']) && $map['user_phone'] = $field['mobile'];
    empty($field['email']) && $map['user_email'] = $field['email'];
    $count = M('cloud_member')->where($map)->count();
    return $count >= 1;
}

function sendMessage($mob, $message,$user_id=null) {
    //return true; //测试期间避免浪费公司资费，暂停发送短信

    if (empty($mob)) {
        \returnJson('-1', '手机号码不能为空');
    } else {
        $gateway = 'http://10.177.248.3/apiSendsmsnew.php?key=CN1NETOPS&phone=' . $mob . '&msg=' . $message;
        $result = http($gateway, 'GET'); //http($gateway,'GET','','',true);
        $log['mob'] = $mob;
        $log['message'] = $message;
        $log['result'] = $result;
        addOtherLog('SMS', serialize($log),$user_id);
        return $result[0];
    }
}

/**
 * 监控 报警邮件
 * @param $message 邮件内容
 * @param $emailto 接收人邮件地址
 */
function alertMail($message, $emailto) {
    if (empty($message) || empty($emailto)) {
        return false;
    }
    $mailsubject = "系统问题，请及时修复！";
    import('Common.Org.Mail');
    if (is_array($emailto)) {
        foreach ($emailto as $email) {
            Email($email, $mailsubject, $message, 'Grandcloud Channels');
        }
    } else {
        Email($emailto, $mailsubject, $message, 'Grandcloud Channels');
    }
}

//json编码信息并发送给客户端
function returnJson($code, $message, $results = '') {
    $json = array();
    $json['code'] = $code;
    $json['message'] = $message;
    $json['results'] = $results;
    exit(json_encode($json));
}

//创建密码
function createPassword($password) {
    if (empty($password)) {
        return false;
    } else {
        $salt = C('PASSWORDINTER');
        return md5($password . $salt);
    }
}

/**
 * 多维数组排序
 * @param type $array 要排序的数组
 * @param type $key 排序的卷标
 * @param type $order 升序或降序
 */
function arraySort($array, $key, $order) {
    foreach ($array as $v) {
        $ages[] = $v[$key];
    }
    $order = $order == 'asc' ? SORT_ASC : ($order == 'desc' ? SORT_DESC : SORT_ASC);
    array_multisort($ages, $order, $array);
    return $array;
}

/**
 * 数组分页函数  核心函数  array_slice 
 * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中 
 * $count   每页多少条数据 
 * $page   当前第几页 
 * $array   查询出来的所有数组 
 * order 0 - 不变     1- 反序 
 */
function page_array($count, $page, $array, $order) {
    global $countpage; #定全局变量  
    $page = (empty($page)) ? '1' : $page; #判断当前页面是否为空 如果为空就表示为第一页面   
    $start = ($page - 1) * $count; #计算每次分页的开始位置  
    if ($order == 1) {
        $array = array_reverse($array);
    }
    $totals = count($array);
    $countpage = ceil($totals / $count); #计算总页面数  
    $pagedata = array();
    $pagedata = array_slice($array, $start, $count);
    return $pagedata;  #返回查询数据  
}

function http($url, $method, $postfields = null, $headers = array(), $debug = false) {
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ci, CURLOPT_TIMEOUT, 30);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);

    switch ($method) {
        case 'POST':
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                $this->postdata = $postfields;
            }
            break;
    }
    curl_setopt($ci, CURLOPT_URL, $url);
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);

    $response = curl_exec($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);

    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);

        echo '=====info=====' . "\r\n";
        print_r(curl_getinfo($ci));

        echo '=====$response=====' . "\r\n";
        print_r($response);
    }
    curl_close($ci);
    return array($http_code, $response);
}


function getId() {
    $id = time();
    $result = M('cloud_member_id')->add(array('dateline' => $id));
    return $result ? substr('999990000000000',0,15-strlen($result)).$result : false;
}

//格式化公司地址
function formatCompanyAddress($address){
    $company_address=json_decode($address,true);//第二次申请，将JSON地址解析为数组地址
    return implode($company_address);
}

//格式化返佣类型
function formatTransType($type) {
    switch ($type) {
        case 1:
            return '正常返佣';
        case 2:
            return '超额返佣';
        default:
            return '其他';
    }
}


//格式化交易类型
function formatPaymentTransType($payment_trans_type_id) {
    switch ($payment_trans_type_id) {
        case 'CHARGE':
            return '充值';
        case 'CONSUME':
            return '消费';
        case 'PROMOTION':
            return '赠送';
        case 'RETURN':
            return '返还';
        case 'REFUND':
            return '退款';
        default:
            return '其他';
    }
}

//使用舍去法格式化用户的余额
function formatAmount($amount){
    if(!empty($amount)){
        return floor($amount*100)/100;
    }else{
        return $amount;
    }
}


//读取缩略图位置
function readThumb(&$user, $user_id){
    $date=substr($user['company_licence'], 0, 10);
    $pic_cl=substr($user['company_licence'], 11);
    $pic_uf=substr($user['user_idcardfront'], 11);
    $pic_ub=substr($user['user_idcardback'], 11);
    $user['company_licence']=$date . '/' . 'T' . $pic_cl;
    $user['user_idcardfront']=$date . '/' . 'T' . $pic_uf;
    $user['user_idcardback']=$date . '/' . 'T' . $pic_ub;
}

/**
 * 返回input数组中键值为column_key的列
 */

function arrayColumn($input, $columnKey, $indexKey = NULL)
{
    $columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
    $indexKeyIsNull = (is_null($indexKey)) ? TRUE : FALSE;
    $indexKeyIsNumber = (is_numeric($indexKey)) ? TRUE : FALSE;
    $result = array();

    foreach ((array)$input as $key => $row)
    {
        if ($columnKeyIsNumber)
        {
            $tmp = array_slice($row, $columnKey, 1);
            $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
        }
        else
        {
            $tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
        }
        if ( ! $indexKeyIsNull)
        {
            if ($indexKeyIsNumber)
            {
                $key = array_slice($row, $indexKey, 1);
                $key = (is_array($key) && ! empty($key)) ? current($key) : NULL;
                $key = is_null($key) ? 0 : $key;
            }
            else
            {
                $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
            }
        }

        $result[$key] = $tmp;
    }
    return $result;
}
//格式化代理商等级
function formatUserLevel($level){
    $arr=C('level_name');
    switch($level){
        case 15:
        case 14:
        case 13:
        case 12:
        case 11:
            return $arr[$level];
        default:
            return '其他';
    }
}