<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/4
 * Time: 13:55
 */

namespace Admin\Controller;
use Think\Model;

class GradeController extends ChannelsController{

    public function index(){
        $grade = M('grade');
        $model = M();
        $query_select = "select grade, class,
                            case SUBSTRING(class,1,CHAR_LENGTH(class)-1)
                            when '一' then 1
                            when '二' then 2
                            when '三' then 3
                            when '四' then 4
                            when '五' then 5
                            when '六' then 6
                            when '七' then 7
                            when '八' then 8
                            when '九' then 9
                            when '十' then 10
                            when '十一' then 11
                            when '十二' then 12
                            when '十三' then 13
                            when '十四' then 14
                            when '十五' then 15
                            when '十六' then 16
                            when '十七' then 17
                            when '十八' then 18
                            when '十九' then 19
                            when '二十' then 20
                            when '二十一' then 21
                            when '二十二' then 22
                            when '二十三' then 23
                            when '二十四' then 24
                            when '二十五' then 25
                            when '二十六' then 26
                            when '二十七' then 27
                            when '二十八' then 28
                            when '二十九' then 29
                             when '三十' then 30
                            end
                            as cl
                            from grade
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