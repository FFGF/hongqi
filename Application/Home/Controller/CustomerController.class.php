<?php

namespace Home\Controller;

class CustomerController extends ChannelsController {
    
    public function index() {
        $user_id=$this->userInfo['user_id'];
        $where=$this->initMapIndex();
        $model=D('Customer');
        $result_count=$model->indexList($user_id,$where);
        //\print_r($result_count);exit;
        $page=$this->page(count($result_count));        
        $_list=$model->indexList($user_id,$where,$page->nowPage,$page->listRows);
        $this->assign('_list', $_list);
        $this->display();
    }
    //初始化查询条件和排序条件
    private function initMapIndex() {
        $k_str = I('request.k_str');
        if (!empty($k_str)) {            
            $map['user_company|user_phone|user_email|user_name'] = array('LIKE',"%$k_str%");
        } else {
            $s_time = I('request.s_time');
            $e_time = I('request.e_time');
            if (!empty($s_time)) {
                $s_time=date('Y-m-d H:i:s',$s_time);
                $e_time = empty($e_time) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',$e_time);
                $map['_string'] = "channels_customer.create_time>='$s_time' AND channels_customer.create_time<='$e_time'";
            }
            $status=I('request.status');
            if(!empty($status)){
                $status=='未充值' && $map['status']='wcz';   $status=='未消费' && $map['status']='wxh';
            }
        }
        
        $order=I('request.order');  $type=I('request.type');
        !empty($order) && $map['order']=$order;
        empty($order) && $map['order']='desc';
        !empty($type) && $map['type']=$type;
        empty($type) && $map['type']='create_time';

        return $map;
    }

    
    //订单列表
    public function order() {
        $user_id=$this->userInfo['user_id'];
        $where=$this->initMapOrder();
        $model=D('Customer');
        $result=$model->orderList($user_id,$where);
        $page=$this->page(count($result));
        $this->assign('_list', $model->orderList($user_id,$where,$page->nowPage,$page->listRows));
        $this->display();
    }    
    //初始化查询条件和排序条件
    private function initMapOrder() {
        $k_str=I('request.k_str');
        if (!empty($k_str)) {
            $map['k_str'] = array('LIKE',"%$k_str%");
        } else {
            $s_time = I('request.s_time');  $e_time = I('request.e_time');
            if (!empty($s_time)) {
                $s_time=date('Y-m-d H:i:s',$s_time);
                $e_time = empty($e_time) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',$e_time);
                $map['_string'] = "payment_trans.transaction_date>='$s_time' AND payment_trans.transaction_date<='$e_time'";
            }
            $status=I('request.status');
            if(!empty($status)){
                $status=='充值' && $status='CHARGE';    $status=='消费' && $status='CONSUME';     $status=='返还' && $status='RETURN';    $status=='赠送' && $status='PROMOTION';
            }            
        }
        $map['payment_trans.payment_trans_type_id'] = empty($status)||$status=='交易类型'?array('IN', array('CHARGE', 'CONSUME', 'RETURN', 'PROMOTION')):array('EQ',$status);
        $order=I('request.order');  $type=I('request.type');
        !empty($order) && $map['order']=$order;
        empty($order) && $map['order']='desc';
        !empty($type) && $map['type']=$type=='time'?'transaction_date':'proportion_amount';
        empty($type) && $map['type']='transaction_date';

        return $map;
    }

    //添加客户
    public function add() {
        if (\IS_POST) {
            $this->addSubmit();
        } else {
            $this->display();
        }
    }
    
    //用户交易详情
    public function info() {
        $customer_user_id=I('request.id');
        if(empty($customer_user_id)){
            $this->error('ID错误');
        }
        $where['user_id']=array('EQ',$customer_user_id);
        $field="payment_trans_type_id,amount,transaction_desc,transaction_date";
        $_list = $this->simpleList('payment_trans', $where, 'transaction_date desc', '', $field);
        $this->assign('_list', $_list);
        $this->display();
    }

    //用户产品明细
    public function product() {
        $customer_user_id=I('request.id');
        if(empty($customer_user_id)){
            $this->error('ID错误');
        }
        $where['user_id']=array('EQ', $customer_user_id);
        $product=D('Customer')->product($where);
        $product_beijing =D('Ccbeijing')->detail($product['beijing']);
        $product_wuxi = D('Ccwuxi')->detail($product['wuxi']);
        $result = array_merge((array)$product_beijing,(array)$product_wuxi);
        $result = \arraySort($result,'start_time','desc');
        $page=$this->page(count($result));
        $this->assign('_list', \page_array($page->listRows,$page->nowPage,$result, 0));
        $this->display();
    }

    //安全检测：手机号码或邮箱重复检测
    public function isRepeat() {
        $info['info'] = I('get.info');
        $info['type'] = I('get.type');
        $map = \isMailOrPhone($info);
        if (isset($map['mobile']) || isset($map['email'])) {
            if (\isReged($map)) {
                \returnJson('-1', '该' . $map['tips'] . '已被注册！');
            } else {
                \returnJson('1', '该' . $map['tips'] . '可以注册！');
            }
        } else {
            \returnJson('-1', $map['tips'] . '格式不正确！');
        }
    }

    //发短信验证码
    public function sendMsg() {
        $info['info'] = I('get.mobile');
        $info['type'] = 'mobile';
        $map = isMailOrPhone($info);
        if (isset($map['mobile'])) {
            !$this->ckSendNum($map['mobile']) && returnJson('-1', '已超发送上限，请您明天继续！');
            $cap = mt_rand(11111, 99999);
            S('sndaCloud_cap_' . $map['mobile'], $cap, 600); //服务端 验证码
            $message = str_replace('%code%', $cap, C('MESSAGE_1'));
            if (\sendMessage($map['mobile'], $message)) {
                \returnJson('1', '验证短信发送成功！');
            } else {
                \returnJson('-1', '验证短信发送失败！');
            }
        } else {
            \returnJson('-1', '请输入手机号码！');
        }
    }

    private function ckSendNum($mob) {
        $s_name = 'sndaCloud_cap_num_' . $mob;
        $num = S($s_name);
        if ($num == false) {
            return S($s_name, 1, 86400);
        } elseif ($num <= 5) {
            return S($s_name, $num + 1, 86400);
        } else {
            return false;
        }
    }
    
    private function addSubmit() {
        $data['user_phone'] = I('request.mobile');
        $data['user_email'] = I('request.email');
        $data['user_name'] = I('request.name');
        $data['user_company'] = I('request.company');
        $data['user_login_pwd'] = \createPassword(I('request.password1'));
        $data['user_login_pwd1'] = \createPassword(I('request.password2'));
        $data['c_cap'] = I('request.captcha'); //客户端 验证码
        $data['s_cap'] = S('sndaCloud_cap_' . $data['user_phone']); //服务端 验证码
        $data['up_user_id'] = $this->userInfo['user_id'];

        $model = D('Customer');
        $result = $model->create($data);
        if ($result) {
            S('sndaCloud_cap_' . $data['user_phone'], null); //验证码仅可以使用1次，提交后自动失效            
            $result['user_id'] = \getId(); //数据验证通过后，生成客户的ID
            $model->reg($result) && \returnJson('1', '客户添加成功！');
        }
        \returnJson('-1', $model->getError());
    }

}
