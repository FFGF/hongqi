<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/28
 * Time: 9:16
 */
namespace Admin\Model;
use Think\Model;

class GradeModel extends Model{
    protected $tableName='grade';

    //获得年级实验班数据
    public function getGradeTest(){
        $result_grade = $this->field('grade,class_test,id')->group('grade,class_test')->select();
        return $result_grade;
    }
}
