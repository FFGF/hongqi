<?php

//将手机和邮箱替换为***
function replaceStar($mobORweb) {
    $map = isMailOrPhone($mobORweb);
    if (isset($map['email'])) {
        $input = explode('@', $map['email']);
        $len = strlen($input[0]);
        $end = $len <= 3 ? 1 : 3;
        return substr($input[0], 0, $end) . '****@' . $input[1];
    } else {
        return substr($map['mobile'], 0, 3) . "*****" . substr($map['mobile'], 8, 3);
    }
}

/**
 * 检测是否为邮箱或手机
 * @param array $mobORweb 包含两个必须字段 ['type']=验证的类型  ['info']=带验证的信息内容
 * @return boolean
 */
function isMailOrPhone($mobORweb) {
    if ($mobORweb['type'] == 'email') {
        $preg_email = '/^[a-z0-9][a-z\.0-9-_]+@[a-z0-9_-]+(?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i';
        preg_match($preg_email, $mobORweb['info']) && $map['email'] = $mobORweb['info'];
    } elseif ($mobORweb['type'] == 'mobile') {
        preg_match("/^(?:13|15|18)[0-9]{9}$/", $mobORweb['info']) && $map['mobile'] = $mobORweb['info'];
    } else {
        $map = false;
    }
    $map['tips'] = ($mobORweb['type'] == 'mobile' ? '手机' : ($mobORweb['type'] == 'email' ? '邮箱' : ''));
    return $map;
}



//对用户上传的照片进行重命名
function changePhotoName(&$info,$user_id){
    $route='./Uploads/'.$info['company_licence']['savepath'];//更换环境需要更改
    $ext_licenct=$info['company_licence']['ext'];//营业执照后缀
    $ext_front=$info['user_idcardfront']['ext'];//身份证前后缀
    $ext_back=$info['user_idcardback']['ext'];//身份证背后缀
    $company_licence=$route.$info['company_licence']['savename'];
    $user_idcardfront=$route.$info['user_idcardfront']['savename'];
    $user_idcardback=$route.$info['user_idcardback']['savename'];
    $info['company_licence']['savename']=$user_id.'licence'.'.'.$ext_licenct;
    $info['user_idcardfront']['savename']=$user_id.'front'.'.'.$ext_front;
    $info['user_idcardback']['savename']=$user_id.'back'.'.'.$ext_back;
    $new_licence=$route.$user_id.'licence'.'.'.$ext_licenct;
    $new_front=$route.$user_id.'front'.'.'.$ext_front;
    $new_back=$route.$user_id.'back'.'.'.$ext_back;
    
    !empty($company_licence) && $renameResult[]=rename($company_licence,$new_licence);
    !empty($user_idcardfront) && $renameResult[]=rename($user_idcardfront,$new_front);
    !empty($user_idcardback) && $renameResult[]=rename($user_idcardback,$new_back);
    return in_array(false, $renameResult)==true?false:true;
}


//格式化交易状态
function formatTransStatus($status) {
    switch ($status) {
        case 0:
            return '审核中';
        case 1:
            return '通过';
        case 2:
            return '拒绝';
        default:
            return '其他';
    }
}

//格式化优惠券使用状态
function formatPromoStatus($status) {
    switch ($status) {
        case 0:
            return '未使用';
        case 1:
            return '已使用';
        default:
            return '其他';
    }
}
//生成缩略图
function makeThumb($info){
    $route='./Uploads/'.$info['company_licence']['savepath'];//更换环境需要更改
    $company_licence=$route.$info['company_licence']['savename'];
    $user_idcardfront=$route.$info['user_idcardfront']['savename'];
    $user_idcardback=$route.$info['user_idcardback']['savename'];
    $company_licence_T='T'.$info['company_licence']['savename'];
    $user_idcardfront_T='T'.$info['user_idcardfront']['savename'];
    $user_idcardback_T='T'.$info['user_idcardback']['savename'];
    $image = new \Think\Image();
    $image->open($company_licence);
    $image->thumb(570, 760)->save($route.$company_licence_T);//网上没收到，宽长暂时定为
    $image1 = new \Think\Image();
    $image1->open($user_idcardfront);
    $image1->thumb(360, 576)->save($route.$user_idcardfront_T);//身份证宽长比例1.6:1
    $image2 = new \Think\Image();
    $image2->open($user_idcardback);
    $image2->thumb(360, 360)->save($route.$user_idcardback_T);//身份证宽长比例1.6:1
}