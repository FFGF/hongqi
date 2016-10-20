<?php

namespace Admin\Controller;
use Think\Model;

class ChargeController extends ChannelsController{
    
    public function index(){
        if($_POST){
            $student = M('student');
            $map['student_id'] = I('student_id');
                if($student->where($map)->find()){
                    $this->error("数据库中已经存在，不要重复注册");
                }else{
                    $student_data = $this->formatIndexData();
                    $student->add($student_data);
                    $this->success('注册成功');
                }
        }else{
            $this->display();
        }
    }
    //格式化index接收的数据
    public function formatIndexData(){
        $data = I('post.');
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =    307200;// 设置附件上传大小300K
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =      './StudentPhoto/'; // 设置附件上传目录
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功
            //$this->success('上传成功！');
        }
        //对文件进行重命名
        if(!changePhotoName($info, $data['student_id'])){
            $this->error('图片重命名失败');
        }

        $data['student_photo']=$info["student_photo"]["savepath"].$info["student_photo"]["savename"];

        $data['student_birthdate'] = $data['student_year'].'-'.$data['student_month'].'-'.sprintf("%02d",$data['student_day']);
        unset($data['student_year']);
        unset($data['student_month']);
        unset($data['student_day']);
        $data['create_by'] = session('admin')['teacher_id'];
        $data['student_password'] = $data['student_id'];
        return $data;
    }
    //学生档案
    public function studentFile(){
        if($_POST){
            $maps['student_id'] = I('student_id');
            $student = M('student');
            $result_student = $student->where($maps)->find();
            //如果student表中无此学生，提醒操作人员先去注册。
            if(empty($result_student)){
                $this->error('数据库查无此人，请先去注册');
            }
            $student_file = M('student_file');
            $result_student_file = $student_file->where($maps)->find();
            if(empty($result_student_file)){
                $this->assign('student',$result_student);
                $this->display();
            }else{
                $this->assign('student',$result_student);
                $this->assign('student_file',$result_student_file);
                $this->display('Charge/showstudentdata');
            }
        }else{
            $flag = 1;
            $this->assign('flag',$flag);
            $this->display();
        }
    }
    //保存学生档案数据
    public function saveStudentFile(){
        $data = $this->formatStudentFile();
        $student_file = M('student_file');
        $student_file->add($data);
        $this->success('数据保存成功');
    }
    //格式化学生档案数据
    public function formatStudentFile(){
        $data = I('post.');
        $data['create_by'] = session('admin')['teacher_id'];
        return $data;
    }
    //更新学生数据
    public function updateStudent(){
        $student_name = I('student_name');
        $student = M('student');
        if(empty($student_name)){
            $maps['student_id'] = I('student_id');
            $result_student = $student->where($maps)->find();
            $this->assign('student',$result_student);
            $this->display();
        }else{
            $student_data = $this->formatIndexData();
            $maps['student_id'] = I('student_id');
            $student->where($maps)->save($student_data);
            $this->success("数据更新成功");
        }
    }
    // 更新学生档案数据
    public function updateStudentFile(){
        $student_grade = I('student_grade');
        $student_file = M('student_file');
        if(empty($student_grade)){
            $maps['student_id'] = I('student_id');
            $result_student_file = $student_file->where($maps)->find();
            $this->assign('student_file',$result_student_file);
            $this->display();
        }else{
            $student_file_data = $this->formatStudentFile();
            $maps['student_id'] = I('student_id');
            $student_file->where($maps)->save($student_file_data);
            $this->success("数据更新成功");
        }
    }
    //收费标准设定
    public function setChargeStandard(){
        if($_POST){
            $result_grade = $this->formatSetChargeStandard();
            $result_school_year['school_year'] = I('school_year');
            $result_school_year['school_term'] = I('school_term');
            $this->assign('result_grade',$result_grade);
            $this->assign('result_school_year',$result_school_year);
            $this->display();
        }else{
            $this->display();
        }
    }
    //格式化收费标准数据
    public function formatSetChargeStandard(){
        $data = I('post.');
        $charge_standard = M('charge_standard');
        $maps_charge_standard['school_year'] = $data['school_year'];
        $maps_charge_standard['school_term'] = $data['school_term'];
        $result_charge_standard = $charge_standard->where($maps_charge_standard)->find();

        if(empty($result_charge_standard)){
            //需要制定收费标准
            $grade = M('grade');
            $result_grade = $grade->select();
            return $result_grade;
        }else{
            dump('数据已经存在');
            die();
            //数据已经存在
        }
    }
    public function saveChargeStandard(){
        $charge_standard_data = $this->formatSaveChargeStandard();
        $charge_stand = M('charge_standard');
        $charge_stand->addAll($charge_standard_data);
        $this->success("数据插入成功");
    }
    public function formatSaveChargeStandard(){
        $data = I('post.');
        $format_data = [];
        $item = [];
        $i = 0;
        foreach($data as $key=>$value){
            $item[$this->pickKey($key)] = $value;
            $i++;
            if($i%9 == 0){
                $format_data[] = $item;
                $item = [];
            }
        }
        return $format_data;
    }
    //提取key,去掉字符串中的数字
    public function pickKey($str){
        return preg_replace('|[0-9]|','',$str);
    }

}
