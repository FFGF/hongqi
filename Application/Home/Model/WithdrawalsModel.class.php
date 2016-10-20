<?php

namespace Home\Model;

use Think\Model;

class WithdrawalsModel extends Model {

    protected $tableName='channels_withdrawals';
    //数据验证
    protected $_validate = array(
        array('amount', 'number', '提现金额输入有误！'),
    );

    //获取对应的提现记录信息
    public function getInfo($user_id, $data, $page){
        if(isset($data['order'])){
            $order=$data['order'];
        }
        if(isset($data['type'])){
            $type=$data['type'];
        }
        $where['user_id']=$user_id;
        $page_size=C('PAGESIZE');
        if(!empty($data['status'])){
            $data['status'] == '审核中' && $where['channels_withdrawals.status']=array('EQ', 0);
            $data['status'] == '通过' && $where['channels_withdrawals.status']=array('EQ', 1);
            $data['status'] == '拒绝' && $where['channels_withdrawals.status']=array('EQ', 2);
            $data['status'] == '审核状态' && $where['channels_withdrawals.status']=array('IN', array(0, 1, 2));
        }
        //搜索开始时间
        if(!empty($data['from_date'])){
            $s_time=date('Y-m-d H:i:s', $data['from_date']);
            $e_time=empty($data['thru_date']) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $data['thru_date']);
            $where['channels_withdrawals.create_time']=array(array('EGT', $s_time), array('ELT', $e_time), 'AND');
        }
        //模糊查询使用人或优惠券码
        if($data['search']){
            $info=$data['search'];
            $search['channels_bank.bank_no']=array('like', "%$info%");
            $search['_logic']='or';
            $where['_complex']=$search;
        }
        $result['list']=$this->where($where)->page($page, $page_size)->order('create_time DESC')->select();
        //获取所有渠道商银行卡更改记录
        $bankChangeRecord=$this->returnBank();
        //根据提现记录的创建时间确认每一笔提现的银行卡号，开户行，开户姓名
        $result['list']=$this->configBank($result['list'], $bankChangeRecord);
        !empty($order) && $result['list']=\arraySort($result['list'], $type, $order); //数据排序
        $result['count']=$this->where($where)->count();
        return $result;
    }

    //添加新的提现申请
    public function addWithdrawal($data){
        $withdraw['amount'] = $data['amount'];
        $withdraw['user_id'] = $data['user_id'];
        $withdraw['type'] = 1;
        $withdraw['status'] = 0;
        //如未填写银行信息不准提现
        $bank_exist = M('channels_bank')->where(array("user_id"=>$withdraw['user_id']))->find();
        if(!$bank_exist){
            $this->error='请先提交银行账户信息！';
            return false;
        }
        //提现金额必须大于0
        if($withdraw['amount']<=0){
            $this->error='提现金额必须大于0！';
            return false;
        }
        //提现金额不能超出可用余额
        if($withdraw['amount']>$data['total']){
            $this->error='提现金额超出余额，无法提现！';
            return false;
        }
        //设置关账期
        $closed = require(APP_PATH."Common/Conf/closed.php");
        $closed_from = $closed[date('Y')][date('m')]['from'];
        $closed_to = $closed[date('Y')][date('m')]['to'];
        if(!empty($closed_from) && !empty($closed_to))
            if(date('d') >= $closed_from && date('d') <= $closed_to){
                $this->error='目前处于对账期，暂时无法提现，请您谅解！';
                return false;
            }
        //插入一条提现申请
        $result = M("channels_withdrawals")->add($withdraw);
        if($result){
            $userinfo = M("channels_info")->where(array("user_id"=>$withdraw['user_id']))->find();
            $before_freezes_amount = $userinfo['freezes_amount'];
            $update_result = M("channels_info")->where(array("user_id"=>$withdraw['user_id']))->setInc("freezes_amount",$withdraw['amount']);
            $userinfo = M("channels_info")->where(array("user_id"=>$withdraw['user_id']))->find();
            $after_freezes_amount = $userinfo['freezes_amount'];
            $contents = "操作时间:".date('Y-m-d H:i:s',time()).";操作前冻结金额为$before_freezes_amount;操作后冻结金额为$after_freezes_amount;提现金额为".$withdraw['amount'];
            if($update_result){
                addAccountLog("申请提现", $contents,$withdraw['user_id']);
                return true;
            }else{
                $contents = "更新余额操作失败!".$contents;
                addAccountLog("申请提现", $contents,$withdraw['user_id']);
                $this->error='提现申请提交成功，账户余额更新失败！';
                return false;
            }
        }else{
            $this->error = '提现申请提交失败！';
            return false;
        }
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

}
