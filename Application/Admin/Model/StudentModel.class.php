<?php

namespace Admin\Model;
use Think\Model;

class StudentModel extends Model{

    protected $tableName='student';

    public function getStudentStatus($student_id,$student_status){
        $maps['student_id'] = $student_id;
        $maps['student_status'] = $student_status;
        $result = $this->where($maps)->find();
        return isset($result)?true:false;
    }

    public function getStudentInformation($maps){
        $result = $this->where($maps)->select();
        return $result;
    }
}
