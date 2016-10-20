<?php

namespace Home\Model;
use Think\Model;

class PromoInfoModel extends Model{
    //对应数据库表前缀，表名
    protected $tableName = 'promo_code';
    //数据验证
    protected $_validate = array(
        array('from_date','number','优惠券起始时间输入有误！',"2"),
        array('thru_date','number','优惠券截止时间输入有误！',"2"),
    );
    //获得优惠券信息
    public function getInfo($data,$page){
        $page_size = C('PAGESIZE');
        $now = date("Y-m-d",time());
        $where['created_by_user_login'] = $data['user_id'];
        switch ($data['status']){
            case "未使用":
                $where['apply_time'] = array('exp','is null');
                break;
            case "已使用":
                $where['apply_time'] = array('exp','is not null');
                break;
            default:
                break;
        }
        if (!empty($data['from_date'])) {
            $s_time=date('Y-m-d H:i:s',$data['from_date']);
            $e_time = empty($data['thru_date']) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',$data['thru_date']);
            $where['create_time'] = array(array('EGT',$s_time),array('ELT',$e_time),'AND');
        }
        //模糊查询使用人或优惠券码
        if($data['search']){
            $info = $data['search'];
            $search['promo_code']  = array('like',"%$info%");
            $search['flag'] = array('like',"%$info%");
            $search['_logic'] = 'or';
            $where['_complex'] = $search;
        }
        $type = $this->getType($data['type']);
        $order = $this->getOrder($data['order']);
        $list = $this->where($where)->page($page,$page_size)->order('create_time DESC')->select();
        !empty($order) && $list = \arraySort($list,$type, $order); //数据排序
        $count = $this->where($where)->count();
        $result['list'] = $list;
        //判断优惠券是否使用,是否可回收
        for($i=0;$i<count($list);$i++){

            if($result['list'][$i]['apply_time'] == "") {
                $result['list'][$i]['used'] = 0;
                if($result['list'][$i]['thru_date'] < $now)
                    $result['list'][$i]['recycled'] = 0;
                else
                    $result['list'][$i]['recycled'] = 1;
            }
            else{
                $result['list'][$i]['used'] = 1;
                $result['list'][$i]['recycled'] = 1;

            }
            if(M('channels_recycled_promo')->find($result['list'][$i]['promo_code']))
                $result['list'][$i]['recycled'] = 2;
        }
        $result['count'] = $count;
        return $result;
    }

    public function getType($type){
        if($type == "value")
            $type = "value";
        if($type == "ctime")
            $type = "create_time";
        if($type == "atime")
            $type = "apply_time";
        if($type == "stime")
            $type = "from_date";
        if($type == "etime")
            $type = "thru_date";
        return $type;
    }

    public function getOrder($order){
        if($order == "asc")
            $order = "asc";
        if($order == "desc")
            $order = "desc";
        return $order;
    }
}