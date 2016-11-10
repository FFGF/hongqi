<?php

namespace Admin\Controller;
use Think\Auth;
use Think\Model;

class StudentController extends ChannelsController{

    public function indexIndex(){
        $this->display('Student/index/index');
    }

    public function studentFile(){
        $this->display();
    }
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
        $data['student_register_date'] = date('Y-m-d',$data['s_time']);
        if(!empty($_FILES['student_photo']['tmp_name'])){
            //如果上传了照片
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

        }
        $data['student_birthdate'] = $data['student_year'].'-'.$data['student_month'];
        unset($data['student_year']);
        unset($data['student_month']);
        $data['create_by'] = session('admin')['teacher_id'];
        $data['student_password'] = $data['student_id'];
        return $data;
    }
    //学生信息查询
    public function queryStudent(){
        $student = M("student");
        $maps_student = $this->formatQueryStudent();
        $result_student = $student->where($maps_student)->select();
        $this->assign('result_student',$result_student);
        $this->display('Student/studentfile');
    }
    //格式化查询条件
    public function formatQueryStudent(){
        $data = I('post.');
        if($data['student_grade'] != '--'){$maps['student_grade'] = $data['student_grade'];}
        if($data['student_class'] != '--'){$maps['student_class'] = $data['student_class'];}
        !empty($data['s_time1']) && $maps['student_register_date'] = date('Y-m-d',$data['s_time1']);
        !empty($data['student_introduce']) && $maps['student_introduce'] = $data['student_introduce'];
        if($data['student_sex'] != '--'){$maps['student_sex'] = $data['student_sex'];}
        if($data['student_accommodation'] != '--'){$maps['student_accommodation'] = $data['student_accommodation'];}
        if($data['student_meal'] != '--'){$maps['student_meal'] = $data['student_meal'];}
        if($data['student_insurance'] != '--'){$maps['student_insurance'] = $data['student_insurance'];}
        if($data['student_insurance'] != '--'){$maps['student_insurance'] = $data['student_insurance'];}
        if($data['student_status'] != '--'){$maps['student_status'] = $data['student_status'];}
        !empty($data['student_dorm_building']) && $maps['student_dorm_building'] = $data['student_dorm_building'];
        !empty($data['student_dorm_number']) && $maps['student_dorm_number'] = $data['student_dorm_number'];
        $fgf = $data['muohu'];
        !empty($data['muohu']) && $maps['student_id|student_card|student_native|student_name|student_address'] = array('LIKE',"%$fgf%");
        return $maps;
    }
    //删除数据
    public function delete(){
        $student_id_array = I('student_id_array');
        $student = M('student');
        $student->startTrans();
        $flag = true;
        foreach($student_id_array as $student_id){
            $maps_student['student_id'] = $student_id;
            $result = $student->where($maps_student)->delete();
            if($result == false){
                break;
            }
        }
        if($flag == true){
            $student->commit();
            $json['code'] = 1;
        }else{
            $student->rollback();
            $json['code'] = 0;
        }
        exit(json_encode($json));
    }
    //导出学生数据
    public function exportStudent(){
        $student_id_array = explode(',',I('post.')['student_id_array']);
        $student = M('student');
        $maps_student['student_id'] = array('in',$student_id_array);
        $result_student = $student->where($maps_student)->select();
        $_list = array(array('学号','姓名','年级','班级','性别','民族','出生年月','籍贯','家庭住址','住宿','包餐','保险',
            '父亲','父亲电话','母亲','母亲电话','宿舍楼','宿舍编号','学生卡号','在校状态','注册介绍人','注册日期','教师子女'));

        foreach ($result_student as $v) {
            $row = array($v['student_id'],$v['student_name'],$v['student_grade'],$v['student_class'],$v['student_sex'],$v['student_nation'],
                $v['student_birthdate'],$v['student_native'],$v['student_address'],$v['student_accommodation'],$v['student_meal'],$v['student_insurance'],
                $v['student_father'],$v['student_father_phone'],$v['student_mother'],$v['student_mother_phone'],$v['student_dorm_building'],$v['student_dorm_number'],
                $v['student_card'],$v['student_status'],$v['student_introduce'],$v['student_register_date'],$v['student_teacher']);
            array_push($_list,$row);
        }
        exportExcel($_list,'学生信息'.date('Y-m-d-h-i-s'));
    }
    //更新学生信息
    public function updateStudent(){
        //通过判断接收的数据是否有学生性别，来进行数据的展示和修改
        $student_sex = I('student_sex');
        $student = M('student');
        if(empty($student_sex)){
            $maps_student['student_id'] = I('student_id');
            $each_student = $student->where($maps_student)->find();
            $this->assign('student_each',$each_student);
            $this->display('Student/studentfile');
        }else{
            $student_data = $this->formatIndexData();
            $student_data['student_register_date'] = date('Y-m-d',$student_data['s_time2']);;
            unset($student_data['s_time2']);
            unset($student_data['student_password']);
            $maps_student['student_id'] = $student_data['student_id'];
            $result = $student->where($maps_student)->save($student_data);
            if($result){
                $this->success("数据修改成功");
            }else{
                $this->error("数据修改失败");
            }
        }

    }
    //学生信息的csv导入
    public function importStudent(){
        $filename = $_FILES['importStudent']['tmp_name'];
        if (empty ($filename)) {
            echo '请选择要导入的CSV文件！';
            exit;
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =    307200;// 设置附件上传大小300K
        $upload->exts      =     array('csv');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =      './importStudent/'; // 设置附件上传目录
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功
            //$this->success('上传成功！');
        }
        $filename = './Uploads'.$info['importStudent']['savepath'].$info['importStudent']['savename'];
        $handle = fopen($filename, 'r');
        $result = input_csv($handle); //解析csv
        fclose($handle); //关闭指针

        $student = M('student');
        $key = [0=>'student_name',1=>'student_id',2=>'student_sex',3=>'student_nation',4=>'student_birthdate',5=>'student_native',
                6=>'student_address',7=>'student_grade',8=>'student_accommodation',9=>'student_meal',10=>'student_insurance',11=>'student_father',
                12=>'student_father_phone',13=>'student_mother',14=>'student_mother_phone',15=>'student_class',16=>'student_dorm_number',17=>'student_card',
                18=>'student_status',19=>'student_campus',20=>'student_dorm_building',21=>'student_introduce',22=>'student_register_date',23=>'student_teacher'];
        unset($result[0]);
        $student_array = [];
        foreach($result as $value){
            $item = [];
            foreach($value as $k=>$v){
                $item[$key[$k]] = $v;
            }
            $item['create_by'] = session('admin')['teacher_id'];
            $item['student_password'] = $item['student_id'];
            $student_array[] = $item;
        }
        if($student->addAll($student_array)){
            //插入成功后删除csv文件
            unlink($filename);
            $this->success("数据导入成功");
        }else{
            //插入失败后删除csv文件
            unlink($filename);
            $this->error("数据导入失败");
        }
    }

    /** 学校结构设置 */
    public function schoolStructure(){
        $campus = M('campus');
        $result_campus = $campus->select();
        $this->assign('result_campus',$result_campus);
        $this->display();
    }
    //保存校区
    public function saveCampus(){
        $campus = M('campus');
        $data['campus_name'] = I('campus_name');
        $data['create_by'] = session('admin')['teacher_id'];
        if($campus->add($data)){
            $this->success("数据保存成功");
        }else{
            $this->error("数据保存失败");
        }
    }
    //保存班级信息
    public function saveGrade(){
        $data = I('post.');
        $grade = M('grade');
        $data['create_by'] = session('admin')['teacher_id'];
        if($grade->add($data)){
            $this->success("数据保存成功");
        }else{
            $this->error("数据保存失败");
        }
    }
    //查询班级信息
    public function queryGrade(){
        $campus = M('campus');
        $result_campus = $campus->select();
        $this->assign('result_campus',$result_campus);

        $grade = M('grade');
        $result_grade = $grade->select();
        $this->assign('result_grade',$result_grade);
        $this->display('Student/schoolstructure');
    }
    //删除班级信息
    public function deleteGrade(){
        $grade_id_array = I('grade_id_array');
        $grade = M('grade');
        $grade->startTrans();
        $flag = true;
        foreach($grade_id_array as $id){
            $maps_grade['id'] = $id;
            $result = $grade->where($maps_grade)->delete();
            if($result == false){
                break;
            }
        }
        if($flag == true){
            $grade->commit();
            $json['code'] = 1;
        }else{
            $grade->rollback();
            $json['code'] = 0;
        }
        exit(json_encode($json));
    }

    /** 学生信息下拉框查询 */
    public function getStudentInformation(){
        $maps = $this->formatGetStudentInformation();
        $student = D('Student');
        $result = $student->getStudentInformation($maps);
        exit(json_encode($result));
    }

    public function formatGetStudentInformation(){
        $data = I('get.');
        if($data['student_grade'] != '--'){$maps['student_grade'] = $data['student_grade'];}
        if($data['student_class'] != '--'){$maps['student_class'] = $data['student_class'];}
        !empty($data['student_id']) && $maps['student_id|student_name'] = $data['student_id'];
        return $maps;
    }






    //后面都是因为不明白需求白白写的代码，都是泪啊。。。。。
    //学生档案
//    public function studentFile(){
//        if($_POST){
//            $maps['student_id'] = I('student_id');
//            $student = M('student');
//            $result_student = $student->where($maps)->find();
//            //如果student表中无此学生，提醒操作人员先去注册。
//            if(empty($result_student)){
//                $this->error('数据库查无此人，请先去注册');
//            }
//            $student_file = M('student_file');
//            $result_student_file = $student_file->where($maps)->find();
//            if(empty($result_student_file)){
//                $this->assign('student',$result_student);
//                $this->display();
//            }else{
//                $this->assign('student',$result_student);
//                $this->assign('student_file',$result_student_file);
//                $this->display('Student/showstudentdata');
//            }
//        }else{
//            $flag = 1;
//            $this->assign('flag',$flag);
//            $this->display();
//        }
//    }
//    //保存学生档案数据
//    public function saveStudentFile(){
//        $data = $this->formatStudentFile();
//        $student_file = M('student_file');
//        $student_file->add($data);
//        $this->success('数据保存成功');
//    }
//    //格式化学生档案数据
//    public function formatStudentFile(){
//        $data = I('post.');
//        $data['create_by'] = session('admin')['teacher_id'];
//        return $data;
//    }
//    //更新学生数据
//    public function updateStudent1(){
//        $student_name = I('student_name');
//        $student = M('student');
//        if(empty($student_name)){
//            $maps['student_id'] = I('student_id');
//            $result_student = $student->where($maps)->find();
//            $this->assign('student',$result_student);
//            $this->display();
//        }else{
//            $student_data = $this->formatIndexData();
//            $maps['student_id'] = I('student_id');
//            $student->where($maps)->save($student_data);
//            $this->success("数据更新成功");
//        }
//    }
//    // 更新学生档案数据
//    public function updateStudentFile(){
//        $student_grade = I('student_grade');
//        $student_file = M('student_file');
//        if(empty($student_grade)){
//            $maps['student_id'] = I('student_id');
//            $result_student_file = $student_file->where($maps)->find();
//            $this->assign('student_file',$result_student_file);
//            $this->display();
//        }else{
//            $student_file_data = $this->formatStudentFile();
//            $maps['student_id'] = I('student_id');
//            $student_file->where($maps)->save($student_file_data);
//            $this->success("数据更新成功");
//        }
//    }
//    //收费标准设定
//    public function setChargeStandard(){
//        if($_POST){
//            $result_grade = $this->formatSetChargeStandard();
//            $result_school_year['school_year'] = I('school_year');
//            $result_school_year['school_term'] = I('school_term');
//            $this->assign('result_grade',$result_grade);
//            $this->assign('result_school_year',$result_school_year);
//            $this->display();
//        }else{
//            $this->display();
//        }
//    }
//    //格式化收费标准数据
//    public function formatSetChargeStandard(){
//        $data = I('post.');
//        $charge_standard = M('charge_standard');
//        $maps_charge_standard['school_year'] = $data['school_year'];
//        $maps_charge_standard['school_term'] = $data['school_term'];
//        $result_charge_standard = $charge_standard->where($maps_charge_standard)->select();
//
//        if(empty($result_charge_standard)){
//            //需要制定收费标准
//            $grade = M('grade');
//            $result_grade = $grade->select();
//            return $result_grade;
//        }else{
//            //数据已经存在
//            $this->assign('result_charge_standard',$result_charge_standard);
//            $this->display('Student/showchargestandard');
//            die();
//        }
//    }
//    public function saveChargeStandard(){
//        $charge_standard_data = $this->formatSaveChargeStandard();
//        $charge_stand = M('charge_standard');
//        $charge_stand->addAll($charge_standard_data);
//        $this->success("数据插入成功");
//    }
//    public function formatSaveChargeStandard(){
//        $data = I('post.');
//        $format_data = [];
//        $item = [];
//        $i = 0;
//        foreach($data as $key=>$value){
//            $item[$this->pickKey($key)] = $value;
//            $i++;
//            if($i%9 == 0){
//                $item['create_by'] = session('admin')['teacher_id'];
//                $format_data[] = $item;
//                $item = [];
//            }
//        }
//        return $format_data;
//    }
//    //提取key,去掉字符串中的数字
//    public function pickKey($str){
//        return preg_replace('|[0-9]|','',$str);
//    }
//    //创建收费任务
//    public function createAccountBilling(){
//        if($_POST){
//            $charge_standard = M('charge_standard');
//            $receive_data = I('post.');
//            $maps['school_year'] = $receive_data['school_year'];
//            $maps['school_term'] = $receive_data['school_term'];
//            $result_charge_standard = $charge_standard->where($maps)->select();
//            if(empty($result_charge_standard)){
//                $this->error("请先指定收费标准");
//            }else{
//                $student_file = D('Studentfile');
//                $result_charge_standard = $student_file->getGradeStudentNumber($result_charge_standard);
//                $this->assign('result_charge_standard',$result_charge_standard);
//                $this->display('Student/createAccountBilling');
//            }
//        }else{
//            $this->display();
//        }
//    }
//
//    public function saveAccountBilling(){
//        $account_billing = D('Accountbilling');
//        $account_billing->saveAccountBilling(I('post.'),session('admin')['teacher_id']);
//        $this->success("收费任务创建成功");
//    }
//    //缴费办理
//    public function transactPayment(){
//        if($_POST){
//            $data = I('post.');
//            $account_billing = M('account_billing');
//            !empty($data['student_id']) && $maps_account_billing['student_id'] = $data['student_id'];
//            !empty($data['school_year']) && $maps_account_billing['school_year'] = $data['school_year'];
//            !empty($data['school_term']) && $maps_account_billing['school_term'] = $data['school_term'];
//            $result_account_billing = $account_billing->where($maps_account_billing)->select();
//            $this->assign('result_account_billing',$result_account_billing);
//            $this->display();
//        }else{
//            $this->display();
//        }
//    }
//
//    //存储收费，退费，减免
//    public function saveChargeBilling(){
//        $charge_billing = M('charge_billing');
//        $data = I('get.');
//        $data['create_by'] = session('admin')['teacher_id'];
//        $charge_billing_id = $charge_billing->add($data);
//
//        if($charge_billing_id==false){
//            $json['code']=0;//说明插入失败
//        }else{
//            $json['code']=1;//说明插入成功
//        }
//        exit(json_encode($json));
//    }
//    //缴费查询
//    public function queryChargeBilling(){
//        if($_POST){
//            $data = I('post.');
//            $account_billing = M('account_billing');
//            !empty($data['student_id']) && $maps_account_billing['student_id'] = $data['student_id'];
//            !empty($data['school_year']) && $maps_account_billing['school_year'] = $data['school_year'];
//            !empty($data['school_term']) && $maps_account_billing['school_term'] = $data['school_term'];
//
//            $result_account_billing = $account_billing->where($maps_account_billing)
//                    ->field(array('id','student_id','school_year','school_term','student_taition','student_data','student_accommodation','student_meal','student_insurance'))
//                    ->select();
//            $charge_billing = M('charge_billing');
//            foreach($result_account_billing as &$value){
//                //应交额
//                $value['should_sum'] = $value['student_taition'] + $value['student_data'] + $value['student_accommodation'] + $value['student_meal'] + $value['student_insurance'];
//
//                $maps_charge_billing['account_billing_id'] = $value['id'];
//                $maps_charge_billing['type'] = '收费';
//                //点击已交金额进行显示
//                $value['paid_amount_array'] = json_encode($charge_billing->where($maps_charge_billing)->select());
//                //已交金额
//                $paid_amount = $charge_billing->where($maps_charge_billing)->sum('charge_money');
//                empty($paid_amount)?$value['paid_amount'] = 0 : $value['paid_amount'] = $paid_amount;
//                //减免金额
//                $maps_charge_billing['type'] = '减免';
//                $relief_amount = $charge_billing->where($maps_charge_billing)->sum('charge_money');
//                empty($relief_amount)?$value['relief_amount'] = 0 : $value['relief_amount'] = $relief_amount;
//                unset($maps_charge_billing['type']);
//                //欠交金额
//                $value['arrearage_amount'] =  $value['should_sum'] -  $value['paid_amount'] - $maps_charge_billing['type'];
//            }
//            $this->assign('result_account_billing',$result_account_billing);
//            $this->display();
//        }else{
//            $this->display();
//        }
//    }
//
}
