<?php
namespace Admin\Model;

use Think\Model;

class WithdrawalsModel extends Model{

    protected $tableName='channels_withdrawals';
    //数据验证
    protected $_validate=array(
        array('user_id', 'number', '渠道商ID输入有误！'),
        array('amount', 'number', '提现金额输入有误！'),
    );

    //获取对应的提现记录信息
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
        //获取所有渠道商银行卡更改记录
        $bankChangeRecord = $this->returnBank();
        //根据提现记录的创建时间确认每一笔提现的银行卡号，开户行，开户姓名
        $result['list']=$this->configBank($result['list'], $bankChangeRecord);
        $result['count']=$this->join('channels_bank ON channels_bank.user_id=channels_withdrawals.user_id')->where($where)->count();
        !empty($order) && $result['list']=\arraySort($result['list'], $type, $order); //数据排序
        return $result;
    }

    //根据提现记录的创建时间确认每一笔提现的银行卡号，开户行，开户姓名
    private function configBank($withdrawals, $bankChangeRecord){
        foreach($withdrawals as $k=>$v){
            if(!empty($bankChangeRecord[$v['user_id']])){
                foreach($bankChangeRecord[$v['user_id']] as $m=>$n){
                    if($v['create_time']<$n['create_time']){
                        $withdrawals[$k]['bank_no']=$bankChangeRecord[$v['user_id']][$m-1]['bank_no'];
                        $withdrawals[$k]['bank_name']=$bankChangeRecord[$v['user_id']][$m-1]['bank_name'];
                        $withdrawals[$k]['bank_user']=$bankChangeRecord[$v['user_id']][$m-1]['bank_user'];
                        break;
                    }
                }
                if(empty($withdrawals[$k]['bank_no'])){
                    $temp=\arraySort($bankChangeRecord[$v['user_id']], 'create_time', 'desc');//把生效时间按从大到小重新排序，然后再查一遍符合要求的数据
                    foreach($temp as $value){
                        if($withdrawals[$k]['create_time']>=$value['effective_time']){
                            $withdrawals[$k]['bank_no']=$value['bank_no'];
                            $withdrawals[$k]['bank_name']=$value['bank_name'];
                            $withdrawals[$k]['bank_user']=$value['bank_user'];
                            break;
                        }
                    }
                }
            }
        }
        return $withdrawals;
    }

    //获取所有渠道商银行卡更改记录
    private function returnBank(){
        $cache_id='channels_bank_changeRecord';
        $result=S($cache_id);
        if(empty($result)){
            $all=M('channels_bank')->order('create_time asc')->select();
            foreach($all as $k=>$v){
                if(!empty($result[$v['user_id']])){
                    array_push($result[$v['user_id']], $v);
                }else{
                    $result[$v['user_id']][0]=$v;
                }
            }
            //          S($cache_id,$result,300);
        }
        return $result;
    }

    //添加超额提现
    public function addWithdrawal($withdraw){
        $channel_exist=M('channels_user')->find($withdraw['user_id']);
        $user_exist=M('cloud_member')->find($withdraw['user_id']);
        if(!$channel_exist){
            if(!$user_exist){
                $this->error='该用户不存在！';
                return false;
            }
            $this->error='该用户不是渠道商！';
            return false;
        }
        if($withdraw['amount'] == 0){
            $this->error='提现金额不能为0元！';
            return false;
        }
        $withdraw['type']=2;
        $withdraw['status']=0;
        $result=$this->add($withdraw);
        if($result){
            $contents="操作时间:".date('Y-m-d H:i:s', time()).";超额提现金额为".$withdraw['amount'];
            addAccountLog("申请超额提现", $contents, $withdraw['user_id']);
            return true;
        }else{
            $this->error='提现申请提交失败！';
            return false;
        }
    }

    //审核提现记录
    public function examine($data){
        $exist=$this->find($data['id']);
        if(!$exist){
            $this->error='不存在该笔提现申请！';
            return false;
        }
        if($exist['status'] == 1 || $exist['status'] == 2){
            $this->error='该笔提现申请已经审核过，不能重复审核！';
            return false;
        }
        $data['type'] = $exist['type'];
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
        if($data['type'] == 1){           //正常返佣
            return $this->passNormal($data);
        }elseif($data['type'] == 2){      //超额返佣
            return $this->passExtra($data);
        }
    }

    //审核不通过操作
    private function deny($data){
        $data['status']=2;
        if($data['type'] == 1){           //正常返佣
            return $this->denyNormal($data);
        }elseif($data['type'] == 2){      //超额返佣
            return $this->denyExtra($data);
        }
    }

    private function passNormal($data){
        $withdraw=$this->find($data['id']);
        $user_id=$withdraw['user_id'];
        //获得修改前数据
        $userinfo=M("channels_info")->where(array("user_id"=>$user_id))->find();
        $before_freezes_amount=$userinfo['freezes_amount'];
        $before_deduct_amount=$userinfo['deduct_amount'];
        //
        $result=$this->save($data);
        if(!$result){
            $this->error='审核失败，扣款数据更改失败！';
            return false;
        }else{
            $update_result=M("channels_info")->where(array("user_id"=>$user_id))->setDec("freezes_amount", $withdraw['amount']);
            $update_result2=M("channels_info")->where(array("user_id"=>$user_id))->setInc("deduct_amount", $withdraw['amount']);
            //获得修改后数据
            $userinfo=M("channels_info")->where(array("user_id"=>$user_id))->find();
            $after_freezes_amount=$userinfo['freezes_amount'];
            $after_deduct_amount=$userinfo['deduct_amount'];
            //
            $contents="提现记录id:".$withdraw['id'].";操作时间:".date('Y-m-d H:i:s', time()).";操作前冻结金额为$before_freezes_amount;操作后冻结金额为$after_freezes_amount;操作前扣除金额为$before_deduct_amount;操作后扣除金额为$after_deduct_amount;扣款金额为".$withdraw['amount'];
            addAccountLog("通过提现申请", $contents, $user_id);
            if(!$update_result){
                $this->error='扣款申请已通过，但账户冻结金额减少失败！';
                return false;
            }else{
                if(!$update_result2){
                    $this->error='扣款申请已通过，但账户扣除金额增加失败！';
                    return false;
                }else{
                    //todo:站内信
                    $this->sendEmail($user_id, 'Y', '');
                    $this->sendMsg($user_id, 'Y', '');
                    return true;
                }
            }
        }
    }

    //通过超额提现
    private function passExtra($data){
        $withdraw=$this->find($data['id']);
        $result=$this->save($data);
        if(!$result){
            $this->error='审核失败，提现数据更改失败！';
            return false;
        }else{
            $contents="提现记录id:".$withdraw['id'].";操作时间:".date('Y-m-d H:i:s', time()).";提现金额为".$withdraw['amount'];
            addAccountLog("通过超额提现申请", $contents, $withdraw['user_id']);
            //todo:站内信
            $this->sendEmail($withdraw['user_id'], 'Y', '');
            $this->sendMsg($withdraw['user_id'], 'Y', '');
            return true;
        }
    }

    //拒绝正常返佣
    private function denyNormal($data){
        $withdraw=$this->find($data['id']);
        $user_id=$withdraw['user_id'];
        $userinfo=M("channels_info")->where(array("user_id"=>$user_id))->find();
        $before_freezes_amount=$userinfo['freezes_amount'];
        if($data['flag'] == ''){
            $this->error='拒绝申请时必须填写备注！';
            return false;
        }
        $result=$this->save($data);
        if(!$result){
            $this->error='审核失败，提现数据更改失败！';
            return false;
        }else{
            $update_result=M("channels_info")->where(array("user_id"=>$user_id))->setDec("freezes_amount", $withdraw['amount']);
            $userinfo=M("channels_info")->where(array("user_id"=>$user_id))->find();
            $after_freezes_amount=$userinfo['freezes_amount'];
            $contents="提现记录id:".$withdraw['id'].";操作时间:".date('Y-m-d H:i:s', time()).";操作前冻结金额为$before_freezes_amount;操作后冻结金额为$after_freezes_amount;提现金额为".$withdraw['amount'];
            addAccountLog("拒绝提现申请", $contents, $user_id);
            if(!$update_result){
                $this->error='提现申请已拒绝，但账户冻结金额减少失败！';
                return false;
            }else{
                //todo:站内信
                $this->sendEmail($user_id, 'N', $data['flag']);
                $this->sendMsg($user_id, 'N', $data['flag']);
                return true;
            }
        }
    }

    //拒绝超额返佣
    private function denyExtra($data){
        $withdraw=$this->find($data['id']);
        if($data['flag'] == ''){
            $this->error='拒绝申请时必须填写备注！';
            return false;
        }
        $result=$this->save($data);
        if(!$result){
            $this->error='审核失败，提现数据更改失败！';
            return false;
        }else{
            $contents="提现记录id:".$withdraw['id'].";操作时间:".date('Y-m-d H:i:s', time()).";提现金额为".$withdraw['amount'];
            addAccountLog("拒绝超额提现申请", $contents, $withdraw['user_id']);
            //todo:站内信
            $this->sendEmail($withdraw['user_id'], 'N', $data['flag']);
            $this->sendMsg($withdraw['user_id'], 'N', $data['flag']);
            return true;
        }
    }

    //发送邮件
    private function sendEmail($user_id,$status,$examine_remark){
        $title='渠道商提现审核通知';
        $user = M('channels_user')->where(array('user_id'=>$user_id))->find();
        if(strtoupper($status)=='Y'){
            $content=file_get_contents_curl("http://channels.grandcloud.cn/admin/sendemail-sendEmail.html?name=".$user['user_name']."&type=WITHDRAWALS");
        }else{
            $examine_remark=str_replace('c', $examine_remark, C('WITHDRAW_N'));
            $content=file_get_contents_curl("http://channels.grandcloud.cn/admin/sendemail-sendEmail.html?name=".$user['user_name']."&remark=$examine_remark&type=WITHDRAWALS");
        }
        sendMail($user['company_email'],$title,$content);
    }

    //发送短信
    private function sendMsg($user_id,$status,$examine_remark){
        $mobile = M('channels_user')->where(array('user_id'=>$user_id))->getField('user_phone');//获得经销商手机发送短信
        strtoupper($status)=='Y' && $message = C('WITHDRAW_Y');
        strtoupper($status)=='N' && $message = str_replace('c', $examine_remark, C('WITHDRAW_N'));
        \sendMessage($mobile, $message,$user_id);
    }
}