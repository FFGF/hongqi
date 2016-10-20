<?php

namespace Admin\Model;

use Think\Model;

class DeductModel extends Model{

    protected $tableName='channels_deduct';
    //数据验证
    protected $_validate=array(
        array('user_id', 'number', '渠道商ID输入有误！'),
        array('amount', 'number', '扣款金额输入有误！'),
        array('flag', 'require', '备注不能为空！'),
    );

    //获取对应的扣款记录信息
    public function getInfo($where, $page){
        $page_size=C('PAGESIZE');
        if(isset($where['order'])){
            $order=$where['order'];
            unset($where['order']);
        }
        if(isset($where['type'])){
            $type=$where['type'];
            unset($where['type']);
        }
        if(empty($page)){
            $result['list']=$this->where($where)->order('create_time DESC')->select();
        }else{
            $result['list']=$this->where($where)->page($page, $page_size)->order('create_time DESC')->select();
        }
        $result['count']=$this->where($where)->count();
        !empty($order) && $result['list']=\arraySort($result['list'], $type, $order); //数据排序
        return $result;
    }

    //添加扣款
    public function addDeduction($deduct){
        $userinfo=M("channels_info")->where(array("user_id"=>$deduct['user_id']))->find();
        $before_freezes_amount=$userinfo['freezes_amount'];
        $channel_exist=M('channels_user')->find($deduct['user_id']);
        $user_exist=M('cloud_member')->find($deduct['user_id']);
        if(!$channel_exist){
            if(!$user_exist){
                $this->error='该用户不存在！';
                return false;
            }
            $this->error='该用户不是渠道商！';
            return false;
        }
        if($deduct['amount'] == 0){
            $this->error='扣款金额不能为0元！';
            return false;
        }
        $deduct['status']=0;
        $result=$this->add($deduct);
        if($result){
            $update_result = M("channels_info")->where(array("user_id"=>$deduct['user_id']))->setInc("freezes_amount",$deduct['amount']);
            $userinfo=M("channels_info")->where(array("user_id"=>$deduct['user_id']))->find();
            $after_freezes_amount=$userinfo['freezes_amount'];
            $contents="操作时间:".date('Y-m-d H:i:s', time()).";操作前冻结金额为$before_freezes_amount;操作后冻结金额为$after_freezes_amount;扣款金额为".$deduct['amount'];
            addAccountLog("申请扣款", $contents, $deduct['user_id']);
            if(!$update_result){
                $this->error='扣款申请已提交，但账户冻结金额增加失败！';
                return false;
            }else{
                $this->sendEmail($deduct['user_id'], '', $deduct['amount']);
                $this->sendMsg($deduct['user_id'], '', $deduct['amount']);
                return true;
            }
        }else{
            $this->error='扣款申请提交失败！';
            return false;
        }
    }

    //审核扣款记录
    public function examine($data){
        $exist=$this->find($data['id']);
        if(!$exist){
            $this->error='不存在该笔扣款申请！';
            return false;
        }
        if($exist['status'] == 1 || $exist['status'] == 2){
            $this->error='该笔扣款申请已经审核过，不能重复审核！';
            return false;
        }
        $data['update_time']=date('Y-m-d H:i:s', time());
        if($data['status'] == '通过'){
            return $this->pass($data);
        }else if($data['status'] == '拒绝'){
            return $this->deny($data);
        }
    }

    //审核通过操作
    private function pass($data){
        $data['status']=1;
        $deduct=$this->find($data['id']);
        $user_id=$deduct['user_id'];
        $userinfo=M("channels_info")->where(array("user_id"=>$user_id))->find();
        $before_freezes_amount=$userinfo['freezes_amount'];
        $before_deduct_amount=$userinfo['deduct_amount'];
        $result=$this->save($data);
        if($result){
            $update_result=M("channels_info")->where(array("user_id"=>$user_id))->setDec("freezes_amount", $deduct['amount']);
            $update_result2=M("channels_info")->where(array("user_id"=>$user_id))->setInc("deduct_amount", $deduct['amount']);
            $userinfo=M("channels_info")->where(array("user_id"=>$user_id))->find();
            $after_freezes_amount=$userinfo['freezes_amount'];
            $after_deduct_amount=$userinfo['deduct_amount'];
            $contents="扣款记录id:".$deduct['id'].";操作时间:".date('Y-m-d H:i:s', time()).";操作前冻结金额为$before_freezes_amount;操作后冻结金额为$after_freezes_amount;操作前扣除金额为$before_deduct_amount;操作后扣除金额为$after_deduct_amount;扣款金额为".$deduct['amount'];
            addAccountLog("通过扣款申请", $contents, $user_id);
            if(!$update_result){
                $this->error='扣款申请已通过，但账户冻结金额减少失败！';
                return false;
            }else{
                if(!$update_result2){
                    $this->error='扣款申请已通过，但账户扣除金额增加失败！';
                    return false;
                }else{
                    $this->sendEmail($deduct['user_id'], 'Y', $deduct['amount']);
                    $this->sendMsg($deduct['user_id'], 'Y', $deduct['amount']);
                    return true;
                }
            }
        }else{
            $this->error='审核失败，扣款数据更改失败！';
            return false;
        }
    }

    //审核不通过操作
    private function deny($data){
        $deduct=$this->find($data['id']);
        $user_id=$deduct['user_id'];
        $userinfo=M("channels_info")->where(array("user_id"=>$user_id))->find();
        $before_freezes_amount=$userinfo['freezes_amount'];
        $data['status']=2;
        $result=$this->save($data);
        if(!$result){
            $this->error='审核失败，扣款数据更改失败！';
            return false;
        }else{
            $update_result=M("channels_info")->where(array("user_id"=>$user_id))->setDec("freezes_amount", $deduct['amount']);
            $userinfo=M("channels_info")->where(array("user_id"=>$user_id))->find();
            $after_freezes_amount=$userinfo['freezes_amount'];
            $contents="扣款记录id:".$deduct['id'].";操作时间:".date('Y-m-d H:i:s', time()).";操作前冻结金额为$before_freezes_amount;操作后冻结金额为$after_freezes_amount;提现金额为".$deduct['amount'];
            addAccountLog("拒绝扣款申请", $contents, $user_id);
            if(!$update_result){
                $this->error='扣款申请已拒绝，但账户冻结金额减少失败！';
                return false;
            }else{
                $this->sendEmail($deduct['user_id'], 'N', $deduct['amount']);
                $this->sendMsg($deduct['user_id'], 'N', $deduct['amount']);
                return true;
            }
        }
    }

    //发送邮件
    private function sendEmail($user_id,$status,$examine_remark){
        $user = M('channels_user')->where(array('user_id'=>$user_id))->find();
        if(strtoupper($status)=='Y'){
            $title='渠道商账户返款通知';
            $examine_remark=str_replace('c', $examine_remark, C('DEDUCT_Y'));
            $content=file_get_contents_curl("http://channels.grandcloud.cn/admin/sendemail-sendEmail.html?name=".$user['user_name']."&remark=$examine_remark&type=DEDUCT");
        }elseif(strtoupper($status)=='N'){
            $title='渠道商账户扣款通知';
            $examine_remark=str_replace('c', $examine_remark, C('DEDUCT_N'));
            $content=file_get_contents_curl("http://channels.grandcloud.cn/admin/sendemail-sendEmail.html?name=".$user['user_name']."&remark=$examine_remark&type=DEDUCT");
        }else{
            $title='渠道商账户冻结通知';
            $examine_remark=str_replace('c', $examine_remark, C('DEDUCT'));
            $content=file_get_contents_curl("http://channels.grandcloud.cn/admin/sendemail-sendEmail.html?name=".$user['user_name']."&remark=$examine_remark&type=DEDUCT");
        }
        sendMail($user['company_email'],$title,$content);
    }

    //发送短信
    private function sendMsg($user_id,$status,$examine_remark){
        $mobile = M('channels_user')->where(array('user_id'=>$user_id))->getField('user_phone');//获得经销商手机发送短信
        $message = str_replace('c', $examine_remark, C('DEDUCT'));
        strtoupper($status)=='Y' && $message = str_replace('c', $examine_remark, C('DEDUCT_Y'));
        strtoupper($status)=='N' && $message = str_replace('c', $examine_remark, C('DEDUCT_N'));
        \sendMessage($mobile, $message,$user_id);
    }
}