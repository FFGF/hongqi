<?php

namespace Home\Model;

class UserModel extends ChannelsModel {
    protected $_validate =array(
        array('company_name','require','公司名称必须填写'),
        array('company_website','require','公司网址必须填写'),
        array('company_rc','/^[0-9]*[1-9][0-9]*$/','公司注册资本必须是正整数','0','regex'),
        array('company_size','require','公司规模必须选择'),
        array('company_sv','/^[0-9]*[1-9][0-9]*$/','公司年销售额必须是正整数','0','regex'),
        array('company_email','email','公司邮箱格式不正确'),
        array('company_address','require','公司地址必须填写'),
        array('user_name','require','联系人姓名必须填写'),
        array('user_phone','/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/','个人手机号码错误！','0','regex'),
        array('user_email','email','个人邮箱格式不正确'),
        array('user_qq','/^[1-9][0-9]{4,}$/','用户QQ格式不正确','0','regex'),
    );
}
