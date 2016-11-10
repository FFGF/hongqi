<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/4
 * Time: 9:52
 */
namespace Admin\Controller;
use Think\Model;

class TextController extends ChannelsController{

    public function index(){
        $this->display();
    }



    public function index1(){
        $grade = M('grade');
        $result_grade = $grade->field('grade,class')->select();
        $model = M();
        $query_select = "select grade, class,
                            case SUBSTRING(class,1,CHAR_LENGTH(class)-1)
                            when '一' then 1
                            when '二' then 2
                            when '三' then 3
                            when '七' then 7


                            when '二十' then 20
                            end
                            as cl
                            from grade
                            where grade.grade='小班'
                            order by cl";
        $result_grade = $model->query($query_select);

        $grade = $grade->distinct('true')->field('grade as g')->select();

        $formatData = $this->formatGrade($result_grade,$grade);
        exit(json_encode($formatData));

    }

    public function formatGrade($result_grade,$grade){
        foreach($grade as $key=>&$value){
            $temp = [];
                foreach($result_grade as $k=>$v){
                    if($v['grade'] == $value['g']){
                        $temp[] = array('cl'=>$v['class']);
                        unset($result_grade[$k]);
                    }
                }
            $value['c'] = $temp;
        }
        return $grade;
    }

}