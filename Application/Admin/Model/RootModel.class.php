<?php

namespace Admin\Model;

use Think\Model;

class RootModel extends Model {

    protected $tableName = 'channels_admin';

    protected $_validate = array(
        array('admin_id', 'require', "错误信息：管理员ID不能为空！"),
        array('admin_id', 'checkAdminId', "错误信息：管理员ID已存在！",1,'callback'),
        array('admin_login_pwd', 'require', "错误信息：管理员密码不能为空！"),
    );

    //判断渠道商ID是否存在
    public function checkAdminId($id){
        if(empty($id))
            return false;
        $count = M('channels_admin')->where(array('admin_id' => $id))->find();
        return empty($count) ? true : false;
    }

    //添加管理员
    public function addAdmin($data){
        $data['admin_login_pwd'] = createPassword($data['admin_login_pwd']);
        $result = $this->add($data);
        if($result)
            return true;
        else{
            $this->error='添加管理员失败！';
            return false;
        }
    }

    public function changePwd($data){
        $data['admin_login_pwd'] = createPassword($data['admin_login_pwd']);
        $result = $this->where(array('admin_id'=>$data['admin_id']))->save($data);
        if($result)
            return true;
        else{
            $this->error='修改密码失败！';
            return false;
        }
    }
}