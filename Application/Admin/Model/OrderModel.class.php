<?php
namespace Admin\Model;

use Think\Model;

class OrderModel extends Model{

    protected $tableName='cloud_member';

    //order 专用
    public function orderList($where=null, $firstRow=null, $listRows=null){

        if(isset($where['order'])){
            $order=$where['order'];
            unset($where['order']);
        }
        if(isset($where['type'])){
            $type=$where['type'];
            unset($where['type']);
        }
        if(isset($where['channels_id'])){
            $channelsWhere=$where['channels_id'];
            unset($where['channels_id']);
        }
        if(isset($where['k_str_user_id'])){
            $memberWhere['cloud_member.user_id']=$where['k_str_user_id'];
            unset($where['k_str_user_id']);
        }
        if(isset($where['k_str_channels_id'])){
            $memberWhere['channels_customer.user_id']=$where['k_str_channels_id'];
            unset($where['k_str_channels_id']);
        }
        //渠道商下channels_customer⋈payment_trans的所有客户订单信息
        $paymentDataList=$this->orderPaymentData($where);
        //获取各个客户的渠道商更改记录
        $customer_list = $this->returnChannels();
        //确认$paymentDataList中每一条订单记录交易时的渠道商并且根据搜索条件筛选渠道商ID
        $paymentDataList = $this->configChannels($paymentDataList,$customer_list,$channelsWhere);
        //获取$paymentDataList中每一条记录使用的现金金额
        $paymentDataList = $this->configCash($paymentDataList,$where);
        //判断返还类型是套餐还是按需
        $paymentDataList = $this->configReturn($paymentDataList,$where);
        //渠道商下channels_customer⋈channels_info⋈cloud_member的所有客户信息
        $memberDataList=$this->memberData($memberWhere);
        $proportion=C('level');
        $channels=$this->getChannels();
        foreach($channels as $k=>$v){
            $level_list[$v['user_id']]=$this->returnLevel($v['user_id']);
        }
        foreach($paymentDataList as $k=>$v){
            if(!empty($memberDataList[$v['user_id']])){
                $paymentDataList[$k]['user_phone']=$memberDataList[$v['user_id']]['user_phone'];
                $paymentDataList[$k]['user_name']=$memberDataList[$v['user_id']]['user_name'];
                $level=$this->levelProportion($v['transaction_date'], $level_list[$paymentDataList[$k]['real_channels_id']]);
                $paymentDataList[$k]['proportion']=$proportion[$level];
                $paymentDataList[$k]['proportion_amount']=$this->returnAmount($v, $proportion[$level]);
            }else{
                unset($paymentDataList[$k]);
            }
        }

        !empty($order) && $paymentDataList=\arraySort($paymentDataList, $type, $order); //数据排序
        return \page_array($listRows, $firstRow, array_values($paymentDataList), 0);
    }

    //订单专用 关联客户的交易信息（充值、消耗、返还）
    private function orderPaymentData($where=null, $firstRow=null, $listRows=null){
        $cache_name='orderPaymentData_' . $this->createCacheName($where) . '_' . $firstRow;
        if(S($cache_name)){
            return S($cache_name);
        }else{
            $result=M('channels_customer')
                ->distinct(true)
                ->join('payment_trans ON channels_customer.customer_user_id = payment_trans.user_id')
                ->where($where)
                ->order('channels_customer.customer_user_id DESC')
                ->field('payment_trans.payment_trans_id,channels_customer.user_id as channels_id,payment_trans.user_id,payment_trans.payment_trans_type_id,payment_trans.amount,payment_trans.transaction_date,payment_trans.transaction_desc')
                ->limit($firstRow, $listRows)
                ->select();
//            S($cache_name, $result,60);
            return $result;
        }
    }

    //确认$paymentDataList中每一条订单记录交易时的渠道商
    private function configChannels($result,$channels,$channelsWhere){
        $cache_name = 'configChannels_' . $this->createCacheName($channelsWhere);
        if (S($cache_name)) {
            return S($cache_name);
        } else{
            foreach($result as $k=>$v){
                foreach($channels[$v['user_id']] as $m=>$n){
                    if($v['transaction_date']<$n['effective_time']){
                        $result[$k]['real_channels_id']=$channels[$v['user_id']][$m-1]['user_id'];
                        break;
                    }
                }
                if(empty($result[$k]['real_channels_id'])){
                    $temp=\arraySort($channels[$v['user_id']], 'effective_time', 'desc');//把生效时间按从大到小重新排序，然后再查一遍符合要求的数据
                    foreach($temp as $value){
                        if($result[$k]['transaction_date']>=$value['effective_time']){
                            $result[$k]['real_channels_id']=$value['user_id'];
                            break;
                        }
                    }
                }
                if(empty($result[$k]['real_channels_id']) || $result[$k]['channels_id'] != $result[$k]['real_channels_id'])
                    unset($result[$k]);
                if($channelsWhere != NULL){
                    if($result[$k]['real_channels_id'] != $channelsWhere)
                        unset($result[$k]);
                }
            }
//            S($cache_name, $result,60);
            return $result;
        }
    }

    //获取$paymentDataList中每一条记录使用和返还的现金金额
    private function configCash($result,$where = null){
        $cache_name='configCash_'.$this->createCacheName($where);
        if(S($cache_name)){
            return S($cache_name);
        }else{
            if(!empty($result)){
                $paymentWhere['billing_account_id']=array('LIKE', '%D%');
                $paymentWhere['payment_trans_id']=array('IN', \arrayColumn($result, 'payment_trans_id'));
                $payment_application=M('payment_application')->where($paymentWhere)->group('payment_trans_id')->getField('payment_trans_id,sum(amount_applied),reserved_instance_change_id');
                foreach($result as $k=>$v){
                    if(!empty($payment_application[$v['payment_trans_id']]))
                        $result[$k]['amount_applied']=$payment_application[$v['payment_trans_id']]['sum(amount_applied)'];
                    else
                        $result[$k]['amount_applied']=0;
                    $result[$k]['reserved_instance_change_id'] = $payment_application[$v['payment_trans_id']]['reserved_instance_change_id'];
                }
            }
//            S($cache_name,$result,60);
            return $result;
        }
    }

    //确认$paymentDataList中主机创建失败的记录是套餐还是按需
    private function configReturn($result,$where = null){
        $cache_name = 'configReturn_' . $this->createCacheName($where);
        if (S($cache_name)) {
            return S($cache_name);
        } else {
            if(!empty($result)){
                $paymentWhere['reserved_instance_change_id']=array('IN', \arrayColumn($result, 'reserved_instance_change_id'));
                $reserved_instance_change=M('reserved_instance_change')->where($paymentWhere)->getField('reserved_instance_change_id,product_id');
                $product=M('product')->getField('product_id,term_type');
                foreach($result as $k=>$v)
                    $result[$k]['reserved_instance_type'] = $product[$reserved_instance_change[$v['reserved_instance_change_id']]];
            }
//            S($cache_name, $result,60);
            return $result;
        }
    }

    //关联客户的用户资料
    private function memberData($where=null, $firstRow=null, $listRows=null){
        $cache_name='memberData_' . $this->createCacheName($where) . '_' . $firstRow;
        if(S($cache_name)){
            return S($cache_name);
        }else{
            if(isset($where['status'])){
                unset($where['status']);
            }
            $result=M('channels_customer')->join('cloud_member ON channels_customer.customer_user_id = cloud_member.user_id')->where($where)->order('channels_customer.customer_user_id DESC')->limit($firstRow, $listRows)->getField('cloud_member.user_id,channels_customer.user_id as channels_id,cloud_member.user_name,cloud_member.user_sd_name,cloud_member.user_company,cloud_member.user_phone,cloud_member.user_email,channels_customer.create_time');
//            S($cache_name, $result,60);
            return $result;
        }
    }

    //获取关联客户的所有渠道商ID，去除重复
    private function getChannels(){
        $result=M("channels_customer")->join('cloud_member ON channels_customer.user_id = cloud_member.user_id')->join('channels_info ON channels_info.user_id = channels_customer.user_id')->join('channels_user ON channels_user.user_id = channels_customer.user_id')->distinct(true)->field('channels_customer.user_id')->select();
        return $result;
    }

    //获得所有客户的渠道商更改记录
    private function returnChannels(){
        $cache_id='channels_customer_records';
        $result=S($cache_id);
        if (empty($result)) {
            $all=M('channels_customer')->order('effective_time asc')->select();
            foreach($all as $k=>$v){
                if(!empty($result[$v['customer_user_id']])){
                    array_push($result[$v['customer_user_id']],$v);
                }else{
                    $result[$v['customer_user_id']][0]=$v;
                }
            }
//            S($cache_id,$result,300);
        }
        return $result;
    }

    //获得渠道商的所有等级更改记录
    public function returnLevel($user_id){
        if(empty($user_id)){
            return false;
        }else{
            $cache_id='my_level_' . $user_id;
            $result=S($cache_id);
            if(empty($result)){
                $result=M('channels_level')->where(array('user_id'=>$user_id))->order(array('id'=>'DESC'))->field('from_level,to_level,effective_time')->select();
//                S($cache_id, $result, 300);
            }
            return $result;
        }
    }

    //根据交易时间和代理商等级变化的时间，返回当前经销商在不同阶段不同的返佣比例
    private function levelProportion($transaction_date, $level_list){
        //先按从小到大的顺序 查找交易时间小于生效时间的返佣比例
        foreach($level_list as $value){
            if(strtotime($transaction_date) < strtotime($value['effective_time'] . ' 00:00:00')){
                $proportion=$value['from_level'];
                break;
            }
        }
        if(empty($proportion)){
            krsort($level_list);//把生效时间按从大到小重新排序，然后再查一遍符合要求的数据
            foreach($level_list as $value){
                if(strtotime($transaction_date) >= strtotime($value['effective_time'] . ' 00:00:00')){
                    $proportion=$value['to_level'];
                    break;
                }
            }
        }
        return $proportion;
    }

    //根据对应的规则计算经销商返佣金额
    private function returnAmount($payment,$proportion){
        $signal=$payment['payment_trans_type_id'] == 'RETURN'?'-':'';
        $total=$signal.($payment['payment_trans_type_id'] == 'CHARGE' ? '0' : abs($proportion * $payment['amount_applied']));
        $payment['payment_trans_type_id'] == 'PROMOTION' && $total=0;
        if(!preg_match('/^(year)|(month)|(包月)|(包年)/i', $payment['transaction_desc'])){
            empty($signal) && $total=0;
        }
        if($payment['reserved_instance_type'] == 'TERM_DEMAND')
            $total = 0;
        return $total;
    }

    private function createCacheName($param){
        if(!empty($param)){
            return md5(\serialize($param));
        }else{
            return '';
        }
    }
}