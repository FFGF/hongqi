<?php



//根据代理等级返回响应的返佣比例
function levelToPro($level){
       $arr=C('level');
       return $arr[$level];
}

//格式化审核状态
function formatExamineStatus($status){
    switch($status){
        case "I":
            return '待审核';
        case "F":
            return '审核失败';
        case "P":
            return '审核通过';
        default:
            return '其他';
    }
}

//格式化交易状态
function formatTransStatus($status){
    switch($status){
        case 0:
            return '待审核';
        case 1:
            return '审核通过';
        case 2:
            return '审核失败';
        default:
            return '其他';
    }
}

function formatDeductStatus($status){
    switch($status){
        case 0:
            return '待审核';
        case 1:
            return '审核通过';
        case 2:
            return '审核失败';
        default:
            return '其他';
    }
}


/**
 * 邮件发送函数
 */
function sendMail($to, $title, $content) {
    import('Common.Org.Mail');
    $mail = new \PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    $mail->Host=C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
    $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
    $mail->Username = C('MAIL_USERNAME'); //你的邮箱名
    $mail->Password = C('MAIL_PASSWORD') ; //邮箱密码
    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
    $mail->AddAddress($to,"");
    $mail->WordWrap = 50; //设置每行字符长度
    $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
    $mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
    $mail->Subject =$title; //邮件主题
    $mail->Body = $content; //邮件内容
    $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
    return($mail->Send());
}

/**
 * 导出excel
 */

function exportExcel($data,$filename){
    import('Common.Org.Excel');
    $excel = new \Excel();
    $excel->download($data,$filename);
}
//邮件发送获得HTML函数
function file_get_contents_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function site_msg($title,$message,$user_id,$admin_id){
    $msg['title'] = $title;
    $msg['message'] = $message;
    $msg['create_by'] = $admin_id;
    $msg_id = M('channels_msg')->add($msg);
    $user_msg['user_id'] = $user_id;
    $user_msg['msg_id'] = $msg_id;
    M('channels_user_msg')->add($user_msg);
}

//对管理员上传的文件进行重命名
function changeFileName($info){
    $route='./Uploads/'.$info['imgFile']['savepath'];//更换环境需要更改
    $file=$route.$info['imgFile']['savename'];
    $new_file=$route.$info['imgFile']['name'];
    !empty($file) && $renameResult[]=rename($file,iconv('UTF-8','GBK',$new_file));
    return in_array(false, $renameResult)==true?false:true;
}

/** 红旗学校公共函数 */

//对用户上传的照片进行重命名
function changePhotoName(&$info,$user_id){
    $route='./Uploads/'.$info['student_photo']['savepath'];//更换环境需要更改
    $ext_licenct=$info['student_photo']['ext'];//营业执照后缀
    $company_licence=$route.$info['student_photo']['savename'];
    $info['student_photo']['savename']=$user_id.'studentphoto'.'.'.$ext_licenct;
    $new_licence=$route.$user_id.'studentphoto'.'.'.$ext_licenct;

    !empty($company_licence) && $renameResult[]=rename($company_licence,$new_licence);
     return in_array(false, $renameResult)==true?false:true;
}
