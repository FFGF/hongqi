<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/14
 * Time: 22:20
 */

namespace Home\Controller;

class UserInfoController extends ChannelsController {

    public function index() {
        $where['user_id'] = $this->userInfo['user_id'];
        $user = M('channels_user')->where($where)->find();
        $this->assign('user', $user);
        $this->display();
    }

    public function certificate() {

        $where['user_id'] = $this->userInfo['user_id'];
        $user = M('channels_user')->where($where)->field('company_licence,user_idcardfront,user_idcardback')->find();
        readThumb($user,$this->userInfo['user_id']);
        foreach ($user as $k => $route) {
            $user[$k] = "./Uploads/" . $route;//@todo : 改成相对路径
        }
        $this->assign('user', $user);
        $this->display();
    }

    public function bank() {
        if (\IS_POST) {
            $bank['bank_name'] = I('bank_name', null);
            $bank['bank_user'] = I('bank_user', null);
            $bank['bank_no'] = I('bank_no', null);
            $bank['user_id'] = $this->userInfo['user_id'];
            $where['user_id'] = $bank['user_id'];
            $model = M('channels_bank');
            $user_exists = $model->where($where)->find();
            if (empty($user_exists)) {
                $model->add($bank);
            } else {
                $model->where($where)->save($bank);
            }
            $this->success('信息提交成功');
        }else{
            $where['user_id'] = $this->userInfo['user_id'];
            $bankInfo=M('channels_bank')->where($where)->order('create_time desc')->find();
            !empty($bankInfo) && $this->assign('bank',$bankInfo);            
            $this->display();
        }
    }

}
