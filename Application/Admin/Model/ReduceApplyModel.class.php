<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/21
 * Time: 9:25
 */
namespace Admin\Model;
use Think\Model;

class ReduceApplyModel extends Model{

    protected $tableName='reduce_apply';
    //财务人员审核
    public function getExamineFinance($charge_standard_name){
        $maps['reduce_apply.examine_finance'] = "审核中";
        $maps['charge_task.charge_standard_name'] = $charge_standard_name;
        $result = $this->join('charge_task on charge_task.id = reduce_apply.charge_task_id')
                        ->where($maps)
                        ->field('reduce_apply.*,charge_task.student_id')
                        ->select();
        return $result;
    }
    //校长审核
    public function getExamineSchoolmaster($charge_standard_name){
        $maps['reduce_apply.examine_schoolmaster'] = "审核中";
        $maps['charge_task.charge_standard_name'] = $charge_standard_name;
        $result = $this->join('charge_task on charge_task.id = reduce_apply.charge_task_id')
            ->where($maps)
            ->field('reduce_apply.*,charge_task.student_id')
            ->select();
        return $result;
    }
    //董事长审核
    public function getExamineChairman($charge_standard_name){
        $maps['reduce_apply.examine_chairman'] = "审核中";
        $maps['charge_task.charge_standard_name'] = $charge_standard_name;
        $result = $this->join('charge_task on charge_task.id = reduce_apply.charge_task_id')
            ->where($maps)
            ->field('reduce_apply.*,charge_task.student_id')
            ->select();
        return $result;
    }
    //如果正在审核中或者审核通过，这不能进行下一次申请
    public function rightReduceApply($charge_task_id){
        $maps['charge_task_id'] = $charge_task_id;
        $maps['finish'] = '否';
        //如果正在审核中
        if($this->where($maps)->find()){
            return false;
        }
        $maps['finish'] = "是";
        $maps['examine_chairman'] = "通过";
        //如果审核已经通过
        if($this->where($maps)->find()){
            return false;
        }
        return true;
    }
}