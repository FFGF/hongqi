<?php

namespace Home\Model;

class CustomerModel extends ChannelsModel {

    protected $tableName = 'cloud_member';
    protected $_validate = array(
        array('up_user_id', 'require', "渠道商ID不能为空！"),
        array('user_phone', 'require', "账号不能为空！"),
        array('user_email', 'require', "邮箱不能为空！"),
        array('user_name', 'require', "姓名不能为空！"),
        array('user_login_pwd', 'require', "登录密码不能为空！"),
        array('user_login_pwd1', 'require', "确认密码不能为空！"),
        array('user_phone', '', '此手机号已被注册！', 0, 'unique'),
        array('user_email', '', '此邮箱已被注册！', 0, 'unique'),
        array('user_login_pwd', 'user_login_pwd1', '两次输入的密码不相同！', 0, 'confirm'),
        array('c_cap', 's_cap', '短信验证码错误！', 0, 'confirm'),
    );
    protected $_auto = array(
        array('auth_level', 2), //认证级别：1.邮箱 2.手机 3.邮箱+手机'        
        array('reg_date', 'time', 1, 'function'),
        array('last_date', 'time', 1, 'function'),
        array('charge_time', 'time', 1, 'function'),
        array('user_sd_name', 'user_name', 1, 'field'),
        array('user_login_name', 'user_email', 1, 'field'),
    );

    public function reg($result) {
        $uid = $this->add($result); //保存用户注册数据
        if ($uid) {
            $this->insertUserData($result);
            return true;
        } else {
            $this->error = '客户添加失败！！';
            return false;
        }
    }

    /**
     * 将数据插入到其他关联的数据表
     * 因数据表类型为MyISAM 无法开启事务
     * @param array $result
     */
    private function insertUserData($result) {
        //user_login 余额表
        $user_login_data['create_time'] = date("Y-m-d H:i:s");
        $user_login_data['update_time'] = date("Y-m-d H:i:s");
        $user_login_data['email'] = isset($result['user_email']) ? $result['user_email'] : "";
        $user_login_data['phone'] = isset($result['user_phone']) ? $result['user_phone'] : "";
        $user_login_data['user_name'] = $result['user_name'];
        $user_login_data['user_id'] = $result['user_id'];
        $user_login_data['avaiable_amount'] = 0;
        M('user_login')->add($user_login_data);

        //billing_account 账户表
        foreach (array('DEPOSIT', 'GIFTCERT', 'LIABILITY') as $account_type_id) {
            $virtual_accounts[] = array(
                'billing_account_id' => $result['user_id'] . $account_type_id[0],
                'user_id' => $result['user_id'],
                'account_type_id' => $account_type_id,
                'balance' => 0,
                'create_time' => date('Y-m-d H:i:s'),
            );
        }
        M('billing_account')->addAll($virtual_accounts);

        //记录渠道商和其所属客户关系
        M('Channels_customer')->add(array('user_id' => $result['up_user_id'], 'customer_user_id' => $result['user_id']));
    }

    //index 专用
    public function indexList($user_id, $where = null, $firstRow = null, $listRows = null) {

        if (isset($where['order'])) {
            $order = $where['order'];      unset($where['order']);
        }
        if (isset($where['type'])) {
            $type = $where['type'];   unset($where['type']);
        }

        $memberDataList = $this->memberData($user_id, $where);
        $paymentResult = $this->indexPaymentData($user_id);
        foreach ($paymentResult as $v){
            $paymentDataList[$v['user_id'] . $v['payment_trans_type_id']] = $v['total'];
        }
 
        foreach ($memberDataList as $k => $v) {
            $memberDataList[$k]['charge'] = empty($paymentDataList[$v['user_id'] . 'CHARGE']) ? 0 : $paymentDataList[$v['user_id'] . 'CHARGE'];
            $memberDataList[$k]['consume'] = empty($paymentDataList[$v['user_id'] . 'CONSUME']) ? 0 : $paymentDataList[$v['user_id'] . 'CONSUME']+$paymentDataList[$v['user_id'] . 'RETURN'];
            if ($where['status'] == 'wcz') {//未充值
                if ($memberDataList[$k]['charge'] > 0)  unset($memberDataList[$k]);
            }elseif ($where['status'] == 'wxh') {//已充值 未消费
                if ($memberDataList[$k]['consume'] < 0) unset($memberDataList[$k]);
            }
        }
        
        !empty($order) && $memberDataList = \arraySort($memberDataList, $type, $order); //数据排序

        return \page_array($listRows, $firstRow, array_values($memberDataList), 0);
    }

    //TODO
    //提供给数据图表
    public function orderForChart($user_id){
        //获取该渠道商下的所有客户订单信息payment_trans
        $paymentDataList = $this->orderPaymentData($user_id);
        //获取各个客户的渠道商更改记录
        $customer_list = $this->returnChannels();
        //确认$paymentDataList中每一条订单记录交易时的渠道商
        $paymentDataList = $this->configChannels($user_id,null,$paymentDataList,$customer_list);
        //确认$paymentDataList中每一条订单记录使用的现金金额
        $paymentDataList = $this->configCash($user_id,null,$paymentDataList);
        //确认$paymentDataList中主机创建失败的记录是套餐还是按需
        $paymentDataList = $this->configReturn($user_id,null,$paymentDataList);
        //获取该渠道商下的所有客户资料
        $memberDataList = $this->memberData($user_id,null);
        $proportion = C('level');
        $level_list=$this->returnLevel($user_id);
        foreach ($paymentDataList as $k => $v) {
            if (!empty($memberDataList[$v['user_id']])) {
                $paymentDataList[$k]['user_phone'] = $memberDataList[$v['user_id']]['user_phone'];
                $paymentDataList[$k]['user_name'] = $memberDataList[$v['user_id']]['user_name'];
                $level=$this->levelProportion($v['transaction_date'], $level_list);
                $paymentDataList[$k]['proportion'] = $proportion[$level];
                $paymentDataList[$k]['proportion_amount'] = $this->returnAmount($v,$proportion[$level]);
            } else {
                //echo '++'.$k.'++'.$v[user_id].\PHP_EOL;
                unset($paymentDataList[$k]);
            }
        }
        return $paymentDataList;
    }

    //order 专用
    public function orderList($user_id, $where = null, $firstRow = null, $listRows = null) {
        if (isset($where['order'])) {
            $order = $where['order'];   unset($where['order']);
        }
        if (isset($where['type'])) {
            $type = $where['type'];   unset($where['type']);
        }
        if (isset($where['k_str'])) {
            $memberWhere['cloud_member.user_phone|cloud_member.user_name'] = $where['k_str'];   unset($where['k_str']);
        }
        //获取该渠道商下的所有客户订单信息payment_trans
        $paymentDataList = $this->orderPaymentData($user_id, $where);
        //获取各个客户的渠道商更改记录
        $customer_list = $this->returnChannels();
        //确认$paymentDataList中每一条订单记录交易时的渠道商
        $paymentDataList = $this->configChannels($user_id, $where,$paymentDataList,$customer_list);
        //确认$paymentDataList中每一条订单记录使用的现金金额
        $paymentDataList = $this->configCash($user_id,$where,$paymentDataList);
        //确认$paymentDataList中主机创建失败的记录是套餐还是按需
        $paymentDataList = $this->configReturn($user_id,$where,$paymentDataList);
        //获取该渠道商下的所有客户资料
        $memberDataList = $this->memberData($user_id, $memberWhere);
        $proportion = C('level');
        $level_list=$this->returnLevel($user_id);
        foreach ($paymentDataList as $k => $v) {
            if (!empty($memberDataList[$v['user_id']])) {
                $paymentDataList[$k]['user_phone'] = $memberDataList[$v['user_id']]['user_phone'];
                $paymentDataList[$k]['user_name'] = $memberDataList[$v['user_id']]['user_name'];
                $level=$this->levelProportion($v['transaction_date'], $level_list);
                $paymentDataList[$k]['proportion'] = $proportion[$level];
                $paymentDataList[$k]['proportion_amount'] = $this->returnAmount($v,$proportion[$level]);
            } else {
                //echo '++'.$k.'++'.$v[user_id].\PHP_EOL;
                unset($paymentDataList[$k]);
            }
        }
        //\print_r($paymentDataList);exit;
        !empty($order) && $paymentDataList = \arraySort($paymentDataList,$type, $order); //数据排序
        return \page_array($listRows, $firstRow, array_values($paymentDataList), 0);
    }

    //获取各个客户的渠道商更改记录
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
//          S($cache_id,$result,300);
        }
        return $result;
    }

    //获得渠道商的所有等级更改记录
    public function returnLevel($user_id) {        
        if(empty($user_id)){
            return false;
        }else{
            $cache_id='my_level_'.$user_id;
            $result=S($cache_id);
             if (empty($result)) {
                $result=M('channels_level')->where(array('user_id' => $user_id))->order(array('id'=>'DESC'))->field('from_level,to_level,effective_time')->select();
//                S($cache_id,$result,300);
            }
            return $result;
        }        
    }

    //根据交易时间和代理商等级变化的时间，返回当前经销商在不同阶段不同的返佣比例
    private function levelProportion($transaction_date,$level_list) {
        //先按从小到大的顺序 查找交易时间小于生效时间的返佣比例
        foreach ($level_list as $value) {
            if(strtotime($transaction_date)<strtotime($value['effective_time'].' 00:00:00')){
                $proportion=$value['from_level'];
                break;
            }
        }
        if(empty($proportion)){
            krsort($level_list);//把生效时间按从大到小重新排序，然后再查一遍符合要求的数据
            foreach ($level_list as $value) {
                if(strtotime($transaction_date)>=strtotime($value['effective_time'].' 00:00:00')){
                    $proportion=$value['to_level'];
                    break;
                }
            }
        }
        return $proportion;
    }
    
    //根据交易类型计算经销商返佣金额
    private function returnAmount($payment,$proportion) {
        $signal=$payment['payment_trans_type_id'] == 'RETURN'?'-':'';
        $total=$signal.($payment['payment_trans_type_id'] == 'CHARGE'?'0':abs($proportion*$payment['amount_applied']));
        $payment['payment_trans_type_id'] == 'PROMOTION' && $total=0;
        if(!preg_match('/^(year)|(month)|(包月)|(包年)/i', $payment['transaction_desc'])){
            empty($signal) && $total=0;
        }
        if($payment['reserved_instance_type'] == 'TERM_DEMAND')
            $total = 0;
        return $total;
    }

    //关联客户的用户资料
    private function memberData($user_id, $where = null, $firstRow = null, $listRows = null) {
        $cache_name = 'memberData_' . $user_id . '_' . $this->createCacheName($where) . '_' . $firstRow;
        if (S($cache_name)) {
            return S($cache_name);
        } else {
            if (isset($where['status'])) {
                unset($where['status']);
            }

            $where['channels_customer.user_id'] = array('EQ', $user_id);
            $result = M('channels_customer')
                    ->join('cloud_member ON channels_customer.customer_user_id = cloud_member.user_id')
                    ->where($where)
                    ->order('channels_customer.customer_user_id DESC')
                    ->limit($firstRow, $listRows)
                    ->getField('cloud_member.user_id,cloud_member.user_name,cloud_member.user_sd_name,cloud_member.user_company,cloud_member.user_phone,cloud_member.user_email,channels_customer.create_time');

//            S($cache_name, $result, 60);  //,channels_customer.create_time'
            //echo M()->_sql().\PHP_EOL;//exit;
            return $result;
        }
    }

    //订单专用 关联客户的交易信息（充值、消耗、返还）
    private function orderPaymentData($user_id, $where = null, $firstRow = null, $listRows = null) {
        $cache_name = 'orderPaymentData_' . $user_id . '_' . $this->createCacheName($where) . '_' . $firstRow;
        if (S($cache_name)) {
            return S($cache_name);
        } else {
            $where['channels_customer.user_id'] = array('EQ', $user_id);
            $result = M('channels_customer')
                    ->distinct(true)
                    ->join('payment_trans ON channels_customer.customer_user_id = payment_trans.user_id')
                    ->where($where)
                    ->order('channels_customer.customer_user_id DESC')
                    ->field('payment_trans.payment_trans_id,channels_customer.user_id as channels_id,payment_trans.user_id,payment_trans.payment_trans_type_id,payment_trans.amount,payment_trans.transaction_date,payment_trans.transaction_desc')
                    ->limit($firstRow, $listRows)
                    ->select();
            //echo M()->_sql().\PHP_EOL;exit;
//            S($cache_name, $result,60);
            return $result;
        }
    }

    //确认$paymentDataList中每一条订单记录交易时的渠道商
    private function configChannels($user_id, $where = null,$result,$channels){
        $cache_name = 'configChannels_' . $user_id . '_' . $this->createCacheName($where);
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
            }
            //            S($cache_name,$result,60);
            return $result;
        }
    }

    //获取$paymentDataList中每一条记录使用和返还的现金金额
    private function configCash($user_id,$where = null,$result){
        $cache_name='configCash_'.$user_id.'_'.$this->createCacheName($where);
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
    private function configReturn($user_id, $where = null, $result) {
        $cache_name = 'configReturn_' . $user_id . '_' . $this->createCacheName($where);
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

    //客户专用 关联客户的交易信息（充值、消耗）消耗=总消耗-总返还
    private function indexPaymentData($user_id) {
        $cache_name = 'indexPaymentData_' . $user_id;
        if (S($cache_name)) {
            return S($cache_name);
        } else {
            $map['payment_trans.payment_trans_type_id'] = array('IN', array('CHARGE', 'CONSUME','RETURN'));
            $map['channels_customer.user_id'] = array('EQ', $user_id);

            $result = M('channels_customer')
                    ->join('payment_trans ON channels_customer.customer_user_id = payment_trans.user_id')
                    ->where($map)
                    ->field('payment_trans.user_id,payment_trans.payment_trans_type_id,COALESCE(SUM(payment_trans.amount),0) as total')
                    ->order('channels_customer.create_time DESC')
                    ->group('channels_customer.customer_user_id,payment_trans.payment_trans_type_id')
                    ->select();
            //echo M()->_sql() . '<BR><BR><BR><BR><BR><BR>';exit;
            //S($cache_name, $result, 60);
            return $result;
        }
    }

    //获取指定用户的所有产品购买信息
    public function product($where){
        $result['beijing']['bandwidth']=$this->beijing_bandwidth($where);
        $result['wuxi']['bandwidth']=$this->wuxi_bandwidth($where);
        $result['beijing']['vm']=$this->beijing_vm($where);
        $result['wuxi']['vm']=$this->wuxi_vm($where);
        return $result;
    }

    //获取指定用户的北京带宽购买信息
    private function beijing_bandwidth($where){
        $where['status_id']=array('EQ','NORMAL');
        $where['local_id']=array('EQ','beijing');
        $where['reserved_instance_type_id']=array('EQ','Bandwidth');
        return M('reserved_instance')->where($where)->field('external_id,create_time')->select();
    }

    //获取指定用户的无锡带宽购买信息
    private function wuxi_bandwidth($where){
        $where['status_id']=array('EQ','NORMAL');
        $where['local_id']=array('EQ','wuxi');
        $where['reserved_instance_type_id']=array('EQ','Bandwidth');
        return M('reserved_instance')->where($where)->field('external_id,create_time')->select();
    }

    //获取指定用户的北京主机购买信息
    private function beijing_vm($where){
        $where['status_id']=array('EQ','NORMAL');
        $where['local_id']=array('EQ','beijing');
        $where['reserved_instance_type_id']=array(array('EQ','VM'));
        $result['VM'] = M('reserved_instance')->where($where)->field('external_id,reserved_instance_type_id,create_time')->select();
        $where['reserved_instance_type_id']=array(array('EQ','VM_Reserved'));
        $result['VM_Reserved'] = M('reserved_instance')->where($where)->field('external_id,reserved_instance_type_id,create_time')->select();
        return $result;
    }

    //获取指定用户的无锡主机购买信息
    private function wuxi_vm($where){
        $where['status_id']=array('EQ','NORMAL');
        $where['local_id']=array('EQ','wuxi');
        $where['reserved_instance_type_id']=array(array('EQ','VM'));
        $result['VM'] = M('reserved_instance')->where($where)->field('external_id,reserved_instance_type_id,create_time')->select();
        $where['reserved_instance_type_id']=array(array('EQ','VM_Reserved'));
        $result['VM_Reserved'] = M('reserved_instance')->where($where)->field('external_id,reserved_instance_type_id,create_time')->select();
        return $result;
    }

    private function createCacheName($param) {
        if (!empty($param)) {
            return md5(\serialize($param));
        } else {
            return '';
        }
    }

}
