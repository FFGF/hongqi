<?php

namespace Home\Controller;

class FinanceController extends ChannelsController {

    //查看提现记录
    public function index() {
        $user_id = $this->userInfo['user_id'];
        $data = $this->getData();
        $page = isset($_GET['p']) ? $_GET['p'] : 1;
        $withdrawals = D('Withdrawals')->getInfo($user_id,$data,$page);
        $this->page($withdrawals['count']);
        $this->assign('list', $withdrawals['list']);
        $this->display();
    }

    //生成优惠券页面
    public function generatePromoIndex() {
        if(\IS_POST){
            $data = $this->getData();
            $userinfo = $this->checkChannels($this->userInfo);
            $data['my_amount']= $userinfo['total'];
            $model = D('Promo');
            if ($model->create($data)){
                if($model->generate($data)){
                    $this->success('生成优惠券创建成功');
                }else{
                    $this->error($model->getError());
                }
            }else{
                $this->error($model->getError());
            }            
        }else{
            $this->display("promo_index");
        }        
    }

    //显示优惠券信息
    public function promoManagement() {
        $data = $this->getData();
        $page = isset($_GET['p']) ? $_GET['p'] : 1;
        $promo = D('PromoInfo');
        if (!$promo->create($data))
            $this->error($promo->getError());
        $info = $promo->getInfo($data,$page);
        $this->page($info['count']);
        $this->assign('list', $info['list']);
        $this->display("promo_manage");
    }

    //回收优惠券
    public function recyclePromo() {
        $where['promo_code'] = I('promo_code');
        $where['user_id'] =  $this->userInfo['user_id'];
        $model = D('Promo');
        if($model->recycle($where)){
            $this->success('回收成功');
        }else{
            $this->error($model->getError());
        }
    }

    //提现
    public function withdraw(){
        $data = $this->checkChannels($this->userInfo);
        $data['amount'] = I('money');
        $model = D('Withdrawals');
        if ($model->create($data)){
            if ($model->addWithdrawal($data)) {
                $this->success("提现申请提交成功！");
            } else {
                $this->error($model->getError());
            }
        }else{
            $this->error($model->getError());
        }
    }

    //获取表单提交数据
    private function getData() {
        $data['search'] = I('k_str');
        $data['value'] = I('value');
        $data['number'] = I('number');
        $data['from_date'] = I('s_time');
        $data['thru_date'] = I('e_time');
        $data['type'] = I('type');
        $data['order'] = I("order");
        $data['create_time'] = date('Y-m-d H:i:s', time());
        $data['status'] = I('status');
        $data['user_id'] = $this->userInfo['user_id'];
        $data['remarks'] = I('remarks');
        $data['my_amount']= bcsub($this->userInfo['available_amount'],$this->userInfo['freezes_amount'],2);//当前用户可用余额
        $data['freezes_amount']=$this->userInfo['freezes_amount'];
        return $data;
    }

}
