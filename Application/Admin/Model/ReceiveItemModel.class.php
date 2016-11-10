<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/5
 * Time: 14:50
 */
namespace Admin\Model;
use Think\Model;

class ReceiveItemModel extends Model{

    protected $tableName='receive_item';
    //获得详细的收费，退费，减免费数据
    public function getChargeTaskItem($charge_task_id){
        $maps_receive_item['charge_task_id'] = $charge_task_id;
        //收费明细
        $result_receive_item = $this->join('teacher on receive_item.create_by = teacher.teacher_id')
                                            ->where($maps_receive_item)->field('receive_item.*,teacher.teacher_name')->select();
        $result_receive_item_copy = $result_receive_item;
        //把获得的数据中的null和等于零字段去掉，为了呈现好看
        foreach($result_receive_item_copy as $key=>$value){
            foreach($value as $k=>$v){
                if($v == null || $v == '0'){
                    unset($result_receive_item[$key][$k]);
                }
            }
        }
        return $result_receive_item;
    }

    public function getReceiveItemSum($charge_task_id,$type){
        $maps['charge_task_id'] = $charge_task_id;
        $maps['type'] = $type;
        $result = $this->where($maps)
            ->field('sum(student_tuition) sum_student_tuition,sum(student_data) sum_student_data,
                                                    sum(student_accommodation) sum_student_accommodation,sum(student_meal) sum_student_meal,
                                                    sum(student_insurance) sum_student_insurance')
            ->find();
        return $result;
    }


}