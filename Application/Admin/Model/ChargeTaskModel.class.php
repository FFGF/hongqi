<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/21
 * Time: 9:25
 */
namespace Admin\Model;
use Think\Model;

class ChargeTaskModel extends Model{

    protected $tableName='charge_task';

    public function saveChargeTask($charge_standard,$teacher_id,$student_id){
        $student = M('student');
        $maps_student_file['student_status'] = '在校';
        $maps_student_file['student_id'] = $student_id;
        //获得所有的在校学生
        $field = ['student.student_id','student.student_grade','student.student_tuition','student.student_accommodation',
                 'student.student_meal','student.student_data','student.student_insurance','grade.class_test'];
        $result_student = $student->join('grade on grade.grade = student.student_grade and grade.class = student.student_class')
                                ->where($maps_student_file)
                                ->field($field)
                                ->select();

        foreach($result_student as &$student){
            $each_student_charge_standard = $this->getEachStudentChargeStandard($student,$charge_standard);
            $student['student_tuition'] = $each_student_charge_standard['student_tuition'];
            $student['student_data'] = $student['student_data'] == '是'?$each_student_charge_standard['student_data']:null;
            $student['student_accommodation'] = $student['student_accommodation'] == '是'?$each_student_charge_standard['student_accommodation']:null;
            $student['student_meal'] = $student['student_meal'] == '是'?$each_student_charge_standard['student_meal']:null;
            $student['student_insurance'] = $student['student_insurance'] == '是'?$each_student_charge_standard['student_insurance']:null;
            $student['charge_standard_name'] = $charge_standard[0]['charge_standard_name'];
            $student['create_by'] = $teacher_id;
            unset($student['class_test']);
        }
        $this->addAll($result_student);
    }
    //获得每个学生的收费标准
    public function getEachStudentChargeStandard($student,$charge_standard){
        foreach($charge_standard as $value){
            if($value['student_grade'] == $student['student_grade'] && $value['student_test'] == $student['class_test']){
                return $value;
            }
        }
        //没有找到收费标准，就返回0
        return [];

    }
    //获得少数名族学生账单
    public function getReduceTuitionNation($data){
        $maps['charge_task.charge_standard_name'] = $data['charge_standard_name'];
        $maps['student.student_nation'] = array('neq',"汉族");
        $result = $this->join('student on charge_task.student_id = student.student_id')
                        ->where($maps)
                        ->field('charge_task.id')
                        ->select();
        return $result;
    }
    //获得教师子女学生账单
    public function getReduceTuitionTeacher($data){
        $maps['charge_task.charge_standard_name'] = $data['charge_standard_name'];
        $maps['student.student_teacher'] = array('eq',"是");
        $result = $this->join('student on charge_task.student_id = student.student_id')
            ->where($maps)
            ->field('charge_task.id')
            ->select();
        return $result;
    }
    //根据学生id和收费标准名字获得收费任务id
    public function  getChargeTaskId($data){
        $maps['student_id'] = $data['student_id'];
        $maps['charge_standard_name'] = $data['charge_standard_name'];
        $charge_task_id = $this->where($maps)->find();
        return $charge_task_id['id'];
    }
    //根据学生id和收费标准名称获得学生信息和收费任务信息
    public function getChargeTaskStudentInformation($data){
        $maps_charge_task['charge_task.student_id'] = $data['student_id'];
        $maps_charge_task['charge_task.charge_standard_name'] = $data['charge_standard_name'];

        $result_charge_task = $this->join('student on charge_task.student_id = student.student_id')
                                          ->field('charge_task.*,student.student_name')
                                          ->where($maps_charge_task)->find();
        return $result_charge_task;
    }
    //统计分析学费
    public function countAnalyseQuery($maps){
        $field = ['charge_task.student_id','receive_item.type','receive_item.student_tuition','receive_item.student_data',
                    'receive_item.student_accommodation','receive_item.student_meal','receive_item.student_insurance'];
        $result_item = $this->join('student on charge_task.student_id = student.student_id')
                        ->join('receive_item on charge_task.id = receive_item.charge_task_id')
                        ->where($maps)
                        ->field($field)
                        ->order('charge_task.student_id')
                        ->select();
        $field_sum = ['sum(receive_item.student_tuition) as sum_student_tuition','sum(receive_item.student_data) as sum_student_data','sum(receive_item.student_accommodation) as sum_student_accommodation',
                        'sum(receive_item.student_meal) as sum_student_meal','sum(receive_item.student_insurance) as sum_student_insurance'];
        $result_sum = $this->join('student on charge_task.student_id = student.student_id')
                        ->join('receive_item on charge_task.id = receive_item.charge_task_id')
                        ->where($maps)
                        ->field($field_sum)
                        ->select();
        $result[] = $result_sum;
        $result[] = $result_item;
        return $result;

    }
    //获得此次收费项目，减免学费申请状态
    public function getReduceApply($charge_standard_name){
        $maps['charge_task.charge_standard_name'] = $charge_standard_name;
        $result = $this->join('reduce_apply on charge_task.id = reduce_apply.charge_task_id')
                        ->where($maps)
                        ->select();
      return $result;
    }
}