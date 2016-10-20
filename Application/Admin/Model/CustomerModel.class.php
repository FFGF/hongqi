<?php

namespace Admin\Model;
use Think\Model;

class CustomerModel extends Model{

    private $cache_time;

    protected $tableName = 'cloud_member';

    protected $_validate = array(
        array('current', 'require', "错误信息：当前渠道商ID不能为空！"),
        array('current', 'checkUserId', "错误信息：当前渠道商ID不存在！",1,'callback'),
        array('new', 'require', "错误信息：新的渠道商ID不能为空！"),
        array('new', 'checkUserId', "错误信息：新的渠道商ID不存在！",1,'callback'),
        array('remark', 'require', "错误信息：备注不能为空！"),
        array('operator', 'require', "错误信息：操作人员不能为空！")
    );

    /**
     * _initialize 初始化获得该model的缓存时限配置
     */
    public function _initialize(){
        parent::_initialize();
        $data_cache_time = C('data_cache_time');
        $this->cache_time = $data_cache_time['customer_model'];
    }

    //index 专用
    public function indexList($admin_name,$where = null, $firstRow = null, $listRows = null) {

        $memberDataList = $this->memberData($where);
        $paymentResult = $this->indexPaymentData($admin_name,$where);

        foreach ($paymentResult as $v){
            $paymentDataList[$v['user_id'] . $v['payment_trans_type_id']] = $v['total'];
        }

        foreach ($memberDataList as $k => $v) {
            $memberDataList[$k]['charge'] = empty($paymentDataList[$v['user_id'] . 'CHARGE']) ? 0 : $paymentDataList[$v['user_id'] . 'CHARGE'];
            $memberDataList[$k]['consume'] = empty($paymentDataList[$v['user_id'] . 'CONSUME']) ? 0 : $paymentDataList[$v['user_id'] . 'CONSUME']+$paymentDataList[$v['user_id'] . 'RETURN'];

            //交易类型过滤
            if ($where['status'] == 'wcz') { //未充值
                if ($memberDataList[$k]['charge'] > 0 || $memberDataList[$k]['consume'] < 0)  unset($memberDataList[$k]);
            }elseif ($where['status'] == 'wxh') {//已充值 未消费
                if ($memberDataList[$k]['consume'] < 0) unset($memberDataList[$k]);
            }elseif($where['status'] == 'ycz'){
                if($memberDataList[$k]['charge'] <= 0) unset($memberDataList[$k]);
            }elseif($where['status'] == 'yxh'){
                if($memberDataList[$k]['consume'] >= 0) unset($memberDataList[$k]);
            }

            //等级状态过滤
            if (!empty($where['level']) && $memberDataList[$k]['level'] != $where['level'] ){
                unset($memberDataList[$k]);
            }
        }

        //排序规则
        $order = $where['order'];
        $type  = $where['type'];
        !empty($order) && $memberDataList = \arraySort($memberDataList, $type, $order); //数据排序

        return \page_array($listRows, $firstRow, array_values($memberDataList), 0);
    }

    //关联客户的用户资料
    private function memberData($map = null, $firstRow = null, $listRows = null) {

         if(!empty($map))
             $cache_name = 'memberData_'. $this->createCacheName($map) . '_' . $firstRow;
         if (S($cache_name)) {
             return S($cache_name);
         } else {
             if (isset($map['status'])) {
                 unset($map['status']);
             }

            if (isset($map['k_str'])) {
                $where['cloud_member.user_id|channels_customer.user_id'] = array('eq',$map['k_str']);
            }

            //渠道商的全部用户
            $result = $this->customerList($where);
            //获取渠道商等级状态
            $level_list = $this->levelList();
            //获得客户=>渠道商关系
            $relation_list = $this->relationList();

            //全部用户等级状态
            foreach($result as $key => $val){
                //获取渠道商等级状态
                $channels_user_id = $relation_list[$val['user_id']];
                $result[$key]['channels_user_id'] = $channels_user_id;
                $result[$key]['level'] = $level_list[$channels_user_id]['level'];

                //客户ID，渠道商ID搜索过滤
                if($map['k_str'] && $val['user_id']!=$map['k_str'] && $channels_user_id!=$map['k_str']){
                    unset( $result[$key]);
                }
            }
            // TODO: 缓存
            // S($cache_name, $result, $this->cache_time['min_time']);

            return $result;
         }
    }

    //客户专用 关联客户的交易信息（充值、消耗）消耗=总消耗-总返还
    private function indexPaymentData($user_name=null,$map=null) {
        $cache_name = 'indexPaymentData_'.$user_name.$this->createCacheName($map);
         if (S($cache_name)) {
             return S($cache_name);
         } else {
            $where['payment_trans.payment_trans_type_id'] = array('IN', array('CHARGE', 'CONSUME','RETURN'));

            if(!empty($map['s_time'])){
                 $where['payment_trans.transaction_date'] = array(array('egt',$map['s_time']),array('elt',$map['e_time']));
            }
            else if(!empty($map['e_time'])){
                 $where['payment_trans.transaction_date'] = array('elt',$map['e_time']);
            }

            //渠道商的全部用户
            $customer_list = $this->customerList();

            $where['user_id'] = array('IN',\arrayColumn($customer_list,'user_id'));

            $result = M('payment_trans')
                      ->where($where)
                      ->field('user_id,payment_trans_type_id,COALESCE(SUM(amount),0) as total')
                      ->group('user_id,payment_trans_type_id')
                      ->select();

            // TODO: 缓存
            // S($cache_name, $result, $this->cache_time['min_time']);

            return $result;
        }
    }

    //获得最近操作记录
    public function operationList($where = null){
        $operationList = M('channels_change_channels')
                    ->field('customer_user_id,create_time,effective_time,from,to,remark,change_by')
                    ->where($where)
                    ->order('create_time desc')
                    ->select();
         return $operationList;
    }

    //修改渠道商ID并添加操作日志
    public function saveChange($operation = null){
        //开启事务
        $this->startTrans();
        //修改渠道商ID
        $data['user_id'] = $operation['to'];                       //新的渠道商ID
        $data['customer_user_id'] = $operation['customer_user_id'];//客户ID
        $data['create_time'] = $operation['create_time'];          //创建时间
        $data['effective_time'] = $operation['effective_time'];    //生效时间

        $where['customer_user_id'] = array('eq',$operation['customer_user_id']);//客户ID
        $where['effective_time'] = array('eq',$operation['effective_time']);    //生效时间

        //查找改客户是否有未生效修改
        $row = M('channels_change_channels')->where($where)->find();

        if(isset($row)){ //存在未生效修改记录 覆盖
            $resultA = M('channels_customer')->where($where)->save($data);
            $resultB = M('channels_change_channels')->where($where)->save($operation);
            $result = ($resultA !== false && $resultB !== false);
        }else{           //不存在修改记录 添加
            $resultA = M('channels_customer')->add($data);
            $resultB = M('channels_change_channels')->add($operation);
            $result = $resultA && $resultB;
        }

        //事务处理
        if($result){
            $this->commit();
            return true;
         }else{
            $this->rollback();
            $this->error = '错误信息：修改失败！';
            return false;
         }
    }

    /**
     * customerList 获得客户列表
     * @param array $where 筛选条件
     * @return $customer_list
     */
    public function customerList($where = 1){
        $cache_name = 'customerList';
        if(S($cache_name)){
            return S($cache_name);
        }else{
             $customer_list = M('channels_customer')
                  ->join('cloud_member ON channels_customer.customer_user_id = cloud_member.user_id')
                  ->where($where)
                  ->order('channels_customer.customer_user_id DESC')
                  ->distinct(true)
                 ->getField('cloud_member.user_id,cloud_member.user_name,cloud_member.user_company,cloud_member.user_phone,cloud_member.user_email');

            // TODO: 缓存
            // S($cache_name,$level_list,$this->cache_time['min_time']);

            return $customer_list;
        }
    }

    /**
     * excelList 获得要导出的list
     * @param string $admin 管理员名字
     * @return $excel_list
     */
    public function excelList($admin){
        $map = S('Customer_index_map'+$admin);
        $index_list = $this->indexList($admin,$map);
        $excel_list = array(array('ID','公司名称','联系人','手机','邮箱','充值','消耗','渠道商ID'));

        foreach ($index_list as $k => $v){
           $v['charge'] = empty($v['charge']) ? "0 " : formatAmount($v['charge']);
           $v['consume'] = empty($v['consume']) ? "0 " : formatAmount($v['consume']);
           $row = array($v['user_id'],$v['user_company'],$v['user_name'],$v['user_phone'],$v['user_email'],$v['charge'],$v['consume'],$v['channels_user_id']);
           $excel_list[$k+1] = $row;
        }
        return $excel_list;
    }

    //获得渠道商ID=>等级对应关系
    private function levelList(){
        $cache_name = 'levelList';
        if(S($cache_name)){
            return S($cache_name);
        }else{
            $current_time = date('Y-m-d');
            $user_list = M('channels_user')->field('user_id')->select();
            foreach($user_list as $key => $val){
                $where['user_id'] = array('eq',$val['user_id']);
                $where['effective_time'] = array('elt',$current_time);
                $row = M('channels_level')->field('to_level')
                        ->where($where)
                        ->order('effective_time desc')
                        ->find();
                $level_list[$val['user_id']]['level'] = empty($row['to_level']) ? '11' : $row['to_level'];   //默认普通
            }

            // TODO: 缓存
            // S($cache_name,$level_list,$this->cache_time['min_time']);

            return $level_list;
      }
    }


    //客户 => 渠道商 对应关系
    private function relationList(){
        $cache_name = 'relationList';
        if(S($cache_name)){
            return S($cache_name);
        }else{
            $where['effective_time'] = array('elt',date('Y-m-d'));
            $customer_list = M('channels_customer')
                           ->field('customer_user_id,substring(max(concat(effective_time,create_time,user_id)),30) as channels_user_id')
                           ->where($where)
                           ->distinct(true)
                           ->order('effective_time desc')
                           ->group('customer_user_id')
                           ->select();

            foreach($customer_list as $val){
                $relation_list[$val['customer_user_id']] = $val['channels_user_id'];
            }

            //TODO:缓存
            // S($cache_name,$relation_list,$this->cache_time['mix_time']);

            return $relation_list;
        }
    }

    //获得某个用户的渠道商ID
    public function getChannelsUserId($id){
        $relation_list = $this->relationList();
        return $relation_list[$id];
    }

    //判断渠道商ID是否存在
    public function checkUserId($id){
        if(empty($id)) return false;
        $count = M('channels_user')->where('user_id='.$id)->count();
        return empty($count) ? false : true;
    }

    //判断用户ID是否存在
    public function checkCustomerId($id){
        if(empty($id)) return false;
        $count = M('channels_customer')->where('customer_user_id='.$id)->count();
        return empty($count) ? false : true;
    }


    private function createCacheName($param) {
        if (!empty($param)) {
            return md5(\serialize($param));
        } else {
            return '';
        }
    }
}
