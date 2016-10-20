<?php

namespace Admin\Model;

use Think\Model;

class ExamineModel extends Model {
    protected $tableName='channels_user';
    public function search($where,$page,$page_size){
        if($where['channels_level.to_level']){
            //先找出所有审核通过的
            $where1=$where;
            unset($where1['channels_level.to_level']);
            if(empty($page)){
                $user=$this->where($where1)->order('create_time desc')->select();
            }else{
            $user=$this->page($page,$page_size)
                ->where($where1)->order('create_time desc')->select();
            }
            //为审核通过的找出目前的to_level
            for($i=0;$i<count($user);$i++){
                $map['user_id']=$user[$i]['user_id'];
                $map['effective_time']=array('elt',date('Y-m-d'));
                $userinfo = M('channels_level')
                    ->where($map)
                    ->order('effective_time desc')
                    ->find();
                $user[$i]['level'] = $userinfo['to_level'];
            }
            //找出符合条件的to_level
            foreach($user as $key=>$val){
                if($val['level']!=$where['channels_level.to_level'])
                    unset($user[$key]);
            }
        }else{
            if(empty($page)){
                $user=$this->where($where)->order('create_time desc')->select();
            }else{
                $user=$this->page($page,$page_size)
                    ->where($where)->order('create_time desc')->select();
            }
            for($i=0;$i<count($user);$i++){
                $map['user_id']=$user[$i]['user_id'];
                $map['effective_time']=array('elt',date('Y-m-d'));
                $userinfo = M('channels_level')
                              ->where($map)
                              ->order('effective_time desc')
                              ->find();
                $user[$i]['level'] = $userinfo['to_level'];
            }
        }
        return $user;
    }
    public function num($where){
        if($where['channels_level.to_level']){
            //先找出所有审核通过的
            $where1=$where;
            unset($where1['channels_level.to_level']);
            $user=$this->where($where1)->select();
            //为审核通过的找出目前的to_level
            for($i=0;$i<count($user);$i++){
                $map['user_id']=$user[$i]['user_id'];
                $map['effective_time']=array('elt',date('Y-m-d'));
                $userlevel = M('channels_level')
                    ->where($map)
                    ->order('effective_time desc')
                    ->find();
                $user[$i]['level'] = $userlevel['to_level'];
            }
            //找出符合条件的to_level
            foreach($user as $key=>$val){
                if($val['level']!=$where['channels_level.to_level'])
                    unset($user[$key]);
            }
        }else{
            $user=$this->where($where)->select();
            for($i=0;$i<count($user);$i++){
                $map['user_id']=$user[$i]['user_id'];
                $map['effective_time']=array('elt',date('Y-m-d'));
                $userlevel = M('channels_level')
                    ->where($map)
                    ->order('effective_time desc')
                    ->find();
                $user[$i]['level'] = $userlevel['to_level'];
            }
        }
        return count($user);
    }
    //修改代理商等级
    public function changeLevel($where,$data){
        $cloud_member['auth_level']=$data['to_level'];
        $model=new Model();
        //目前先假设插入更新都成功，以后用事务或者其他方法解决
        $res1=$model->table('channels_level')->add($data);//插入一条等级变化数据
        $res3=$model->table('cloud_member')->where($where)->save($cloud_member);//更新表cloud_member
    }
}
