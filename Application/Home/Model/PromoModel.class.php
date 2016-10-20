<?php

namespace Home\Model;

class PromoModel extends ChannelsModel {

    //对应数据库表前缀，表名
    protected $tablePrefix = '';
    protected $tableName = 'promo_code';
    //数据验证
    protected $_validate = array(
        array('value', 'number', '价值输入有误！'),
        array('number', 'number', '数量输入有误！'),
        array('from_date', '/\d{1,4}-\d{1,2}-\d{1,2}/', '优惠券开始时间输入有误！'),
        array('thru_date', '/\d{1,4}-\d{1,2}-\d{1,2}/', '优惠券结束时间输入有误！'),
    );

    //生成优惠券
    public function generate($data) {
        if ($data['thru_date'] < $data['from_date']) {
            $this->error = '优惠券结束时间必须大于开始时间!';
            return false;
        }
        if ($data['thru_date'] < date('Y-m-d')) {
            $this->error = '优惠券结束时间不能早于今日!';
            return false;
        }
        if($data['value'] == 0){
            $this->error = '优惠券金额不能为0元！';
            return false;
        }
        $totalPrice = $data['value'] * $data['number'];
        if ($totalPrice > $data['my_amount']) {
            $this->error = '您的账户余额不足，请再管理中心刷新账户余额！';
            return false;
        }
        return $this->createPromoCode($data,$totalPrice);        
    }

    private function createPromoCode($data,$totalPrice) {

        $promo_in_db = $this->select();
        $exist_in_db = false;
        $exist_in_array = false;

        for ($i = 0; $i < $data['number']; $i++) {
            $new[$i]['value'] = $data['value'];
            $new[$i]['from_date'] = $data['from_date'];
            $new[$i]['thru_date'] = $data['thru_date'];
            $new[$i]['create_time'] = $data['create_time'];
            $new[$i]['created_by_user_login'] = $data['user_id'];
            $new[$i]['flag'] = $data['remarks'];
            //检查数据库表和new数组中是否存在相同的promo_code
            do {
                $new[$i]['promo_code'] = $this->generatePromoCode();
                //检查数据库
                if($promo_in_db){
                    foreach($promo_in_db as $k=>$v){
                        if($new[$i]['promo_code'] == $v['promo_code']){
                            $exist_in_db = true;
                            break;
                        }else
                            $exist_in_db = false;
                    }
                }
                //检查新数组
                if($new){
                    for($j = 0; $j < $i; $j++){
                        if($new[$i]['promo_code'] == $new[$j]['promo_code']){
                            $exist_in_array = true;
                            break;
                        }else
                            $exist_in_array = false;
                    }
                }
            } while ($exist_in_db || $exist_in_array);
        }
        $result = $this->addAll($new);
        if ($result) {
            $userinfo = M('channels_info')->where(array('user_id'=>$data['user_id']))->find();
            $before_freezes_amount = $userinfo['freezes_amount'];
            $update_result=M('channels_info')->where(array('user_id'=>$data['user_id']))->setInc('freezes_amount',$totalPrice);
            $userinfo = M('channels_info')->where(array('user_id'=>$data['user_id']))->find();
            $contents ="操作时间:".date('Y-m-d H:i:s',time()).";操作前冻结金额为$before_freezes_amount;操作后冻结金额为".$userinfo['freezes_amount'].";优惠券总金额:";
            $contents .= $totalPrice.";优惠券码:";
            foreach($new as $k=>$v)
                $contents .= $v['promo_code'].";";
            if($update_result){
                addAccountLog("创建优惠券", $contents,$data['user_id']);
                return true;
            }else{
                $contents = "更新余额操作失败!".$contents;
                addAccountLog("创建优惠券", $contents,$data['user_id']);
                $this->error='优惠卷创建成功，账户余额更新失败！';
                return false;
            }
        }else{
            $this->error='优惠卷创建失败';
            return false;
        }
    }

    //回收优惠券
    public function recycle($where) {
        $isrecycled = M('channels_recycled_promo')->where($where)->find();
        if ($isrecycled) {
            $this->error = '该优惠券已经回收过了！';
            return false;
        }
        $promo = M('promo_code')->where(array('promo_code' => $where['promo_code']))->find();
        $userinfo = M('channels_info')->where(array('user_id' => $where['user_id']))->find();
        if($promo['value']>$userinfo['freezes_amount']){
            $this->error = '您的余额存在异常，拒绝回收！';
            return false;
        }
        $result = M('channels_recycled_promo')->add($where);
        if ($result) {
            $userinfo = M('channels_info')->where(array('user_id'=>$where['user_id']))->find();
            $before_freezes_amount = $userinfo['freezes_amount'];
            $update_result = M('channels_info')->where(array('user_id' => $where['user_id']))->setDec('freezes_amount',$promo['value']);
            $userinfo = M('channels_info')->where(array('user_id'=>$where['user_id']))->find();
            $contents ="操作时间:".date('Y-m-d H:i:s',time()).";操作前冻结金额为$before_freezes_amount;操作后冻结金额为".$userinfo['freezes_amount'].";优惠券金额:".$promo['value'].";优惠券码:".$promo['promo_code'];
            if($update_result){
                addAccountLog("回收优惠券", $contents,$where['user_id']);
                return true;
            }else{
                $contents = "更新余额操作失败!".$contents;
                addAccountLog("回收优惠券", $contents,$where['user_id']);
                $this->error='优惠卷回收成功，账户余额更新失败！';
                return false;
            }
        }else{
            $this->error = '回收失败！';
            return false;
        }
    }

    //生成优惠券码
    private function generatePromoCode() {
        static $c = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $l = strlen($c);
        //经销商前缀c_
        $rand = 'C_';
        for ($i = 0; $i < 6; $i++)
            $rand.= $c[mt_rand() % $l];
        return $rand;
    }

}
