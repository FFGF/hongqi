<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/26
 * Time: 15:54
 */
namespace Admin\Controller;
use Think\Auth;
use Think\Model;

class ChargeController extends ChannelsController{
    //收费项目保存
    public function chargeItem(){
        if($_POST){
            $data['charge_item_name'] = I('charge_item_name');
            $data['create_by'] = session('admin')['teacher_id'];
            $charge_item = M("charge_item");

            if($charge_item->add($data)){
                $this->success("数据保存成功");
            }else{
                $this->error("数据保存失败");
            }
        }else{
            $this->display();
        }
    }
    //收费项目查询
    public function queryChargeItem(){
        $charge_item = M("charge_item");
        $result_charge_item = $charge_item->select();
        $this->assign("result_charge_item",$result_charge_item);
        $this->display("Charge/chargeitem");
    }
    //收费项目删除
    public function deleteChargeItem(){
        $student_id_array = I('chargeItem_id_array');
        $charge_item = M('charge_item');
        $charge_item->startTrans();
        $flag = true;
        foreach($student_id_array as $id){
            $maps_charge_item['id'] = $id;
            $result = $charge_item->where($maps_charge_item)->delete();
            if($result == false){
                break;
            }
        }
        if($flag == true){
            $charge_item->commit();
            $json['code'] = 1;
        }else{
            $charge_item->rollback();
            $json['code'] = 0;
        }
        exit(json_encode($json));
    }

    /** 新建收费任务 */

    public function createChargeTask(){
        if($_POST){
            $charge_item = I('post.');
            session('charge_item',$charge_item['choice']);
            $grade = D('Grade');
            $gradeTest = $grade->getGradeTest();
            $this->assign('charge_item',$charge_item);
            $this->assign('gradeTest',$gradeTest);
            $this->display();
        }else{
            $charge_standard = M('charge_standard');
            $result_charge_standard = $charge_standard->join('teacher on charge_standard.create_by = teacher.teacher_id')
                ->field('charge_standard.charge_standard_name,teacher.teacher_name')
                ->group('charge_standard.charge_standard_name')
                ->select();
            $this->assign('result_charge_standard',$result_charge_standard);
            $this->display();
        }
    }
    //生成缴费标准并生成收费任务
    public function saveChargeStandard(){
        $charge_standard_data = $this->formatSaveChargeStandard();
        $charge_stand = M('charge_standard');
        //存储收费标准
        $charge_stand->addAll($charge_standard_data);
        //生成收费任务
        $this->saveChargeTask($charge_standard_data,session('admin')['teacher_id']);
        $this->success("创建成功");
    }
    //生成缴费任务
    public function saveChargeTask($charge_standard_data,$teacher_id){
        $charge_task = D('ChargeTask');
        $charge_task->saveChargeTask($charge_standard_data,$teacher_id);
    }
    //添加单个学生的缴费任务 在收取学费过程中，可能会有新生转校，需要生成该生缴费任务
    public function addSingleStudentChargeTask(){
        $data = I('get.');
        $maps_student['student_id|student_name'] = $data['student_id'];
        $result_student = M('student')->where($maps_student)->select();
        //无此学生
        if(is_null($result_student)){
            $response['flag'] = 3;
            $this->ajaxReturn($response);
        }

        //可能出现学生重名，提示必须输入学号，保证唯一性
        if(count($result_student)>1){
            $response['flag'] = 2;
            $this->ajaxReturn($response);
        }
        //如果已经生成收费任务，这不能生成第二次
        if(M('charge_task')->where($data)->find()){
            $response['flag'] = 0;
            $this->ajaxReturn($response);
        }
        //如果没有收费任务，则新建一个
        $charge_standard_data = D("ChargeStandard")->getChargeStandard($data['charge_standard_name']);
        $charge_task = D('ChargeTask');
        $charge_task->saveChargeTask($charge_standard_data,session('admin')['teacher_id'],$data['student_id']);
        $response['flag'] = 1;
        $this->ajaxReturn($response);
    }

    //获取收费标准
    public function getChargeStandard(){
        $maps_charge_standard['charge_standard_name'] = I('charge_standard_name');
        $charge_standard = M('charge_standard');
        $result_charge_standard = $charge_standard->where($maps_charge_standard)->select();
        $this->assign('result_charge_standard_some',$result_charge_standard);
        $this->display('Charge/createchargetask');
    }
    //删除收费标准
    public function deleteChargeTask(){
        $charge_standard_name_array = I('charge_standard_name_array');
        $Model = M();
        $Model->startTrans();
        $maps['charge_standard_name'] = array('in',$charge_standard_name_array);
        if($Model->table('charge_standard')->where($maps)->delete()&& $Model->table('charge_task')->where($maps)->delete()){
            $Model->commit();
            $json['code'] = 1;
        }else{
            $Model->rollback();
            $json['code'] = 0;
        }
        exit(json_encode($json));
    }
    public function formatSaveChargeStandard(){
        $data = I('post.');
        $length = count(session('charge_item')) + 3;
        $format_data = [];
        $item = [];
        $i = 0;
        foreach($data as $key=>$value){
            $item[$this->pickKey($key)] = $value;
            $i++;
            if($i%$length == 0){
                $item['create_by'] = session('admin')['teacher_id'];
                $format_data[] = $item;
                $item = [];
            }
        }
        session('charge_item',null);
        return $format_data;

    }
    //提取key,去掉字符串中的数字
    public function pickKey($str){
        return preg_replace('|[0-9]|','',$str);
    }

    /** 前台收款 */

    public function receiveItem(){
        if($_GET){
            $charge_standard_name = I('charge_standard_name');
            $this->assign('charge_standard_name',$charge_standard_name);
            $this->display();
        }else{
            $charge_standard = M('charge_standard');
            $result_charge_standard = $charge_standard->join('teacher on charge_standard.create_by = teacher.teacher_id')
                ->field('charge_standard.charge_standard_name,teacher.teacher_name')
                ->group('charge_standard.charge_standard_name')
                ->select();
            $this->assign('result_charge_standard',$result_charge_standard);
            $this->display();
        }
    }
    //获得某个学生的某次收费任务的收款明细
    public function getStudentReceiveItem(){
        $data = I('get.');
        $charge_task = D("ChargeTask");
        $result_charge_task = $charge_task->getChargeTaskStudentInformation($data);
        $receive_item = D("ReceiveItem");
        //收费明细
        $result_receive_item = $receive_item->getChargeTaskItem($result_charge_task['id']);
        //实收金额
        $result_charge_item_receive = $receive_item->getReceiveItemSum($result_charge_task['id'],"收费");
        //减免金额
        $result_charge_item_reduce = $receive_item->getReceiveItemSum($result_charge_task['id'],"减免");
        $this->assign("charge_standard_name",$data['charge_standard_name']);
        $this->assign('result_receive_item',$result_receive_item);
        $this->assign("result_charge_task",$result_charge_task);
        $this->assign("result_charge_item_receive",$result_charge_item_receive);
        $this->assign("result_charge_item_reduce",$result_charge_item_reduce);
        $this->display('Charge/receiveitem');
    }
    //保存某个学生某次收费任务的一次收费
    public function saveStudentReceiveItem(){
        $receive_item = M('receive_item');
        $data_receive_item = I('post.');
        $data_receive_item['create_by'] = session('admin')['teacher_id'];
        $data_receive_item['type'] = '收费';
        $data_receive_item['payment_type'] = '现金';
        if($receive_item->add($data_receive_item)){
            $this->redirect('getStudentReceiveItem',array('student_id'=>$data_receive_item['student_id'],'charge_standard_name'=>$data_receive_item['charge_standard_name']));
        }else{
            $this->error("入库失败");
        }
    }

    /** 减免费办理 */

    public function reduceTuition(){
        if($_GET){
            $charge_standard_name = I('charge_standard_name');
            $this->assign('charge_standard_name',$charge_standard_name);
            $this->display();
        }else{
            $charge_standard = M('charge_standard');
            $result_charge_standard = $charge_standard->join('teacher on charge_standard.create_by = teacher.teacher_id')
                ->field('charge_standard.charge_standard_name,teacher.teacher_name')
                ->group('charge_standard.charge_standard_name')
                ->select();
            $this->assign('result_charge_standard',$result_charge_standard);
            $this->display();
        }
    }
    //成绩批量减免
    public function reduceTuitionScore(){

    }
    //少数名族批量减免
    public function reduceTuitionNation(){
        $data = I('post.');
        $reduce_money = $data['reduce_money_batch'];
        $charge_task = D('ChargeTask');
        $result_charge_task = $charge_task->getReduceTuitionNation($data);
        $result_receive_item = $this->reduceTuitionBatch($result_charge_task,$reduce_money,"少数名族");
        $receive_item = M('receive_item');
        if( $receive_item->addAll($result_receive_item)){
            $this->success("减免成功");
        }else{
            $this->error("减免失败");
        }
    }
    //教师子女批量减免
    public function reduceTuitionTeacher(){
        $data = I('post.');
        $reduce_money = $data['reduce_money_batch'];
        $charge_task = D('ChargeTask');
        $result_charge_task = $charge_task->getReduceTuitionTeacher($data);
        $result_receive_item = $this->reduceTuitionBatch($result_charge_task,$reduce_money,"教师子女");
        $receive_item = M('receive_item');
        if( $receive_item->addAll($result_receive_item)){
            $this->success("减免成功");
        }else{
            $this->error("减免失败");
        }
    }
    //提取批量减免公共部分，写成函数
    public function reduceTuitionBatch($result_charge_task,$reduce_money,$str){
        foreach($result_charge_task as $key=>&$value){
            $value['charge_task_id'] = $value['id'];
            $value['type'] = "减免";
            $value['reduce_batch_reason'] = $str;
            $value['student_tuition'] = $reduce_money;
            $value['create_by'] = session('admin')['teacher_id'];
            unset($value[id]);
        }
        return $result_charge_task;
    }
    //手动减免
    public function reduceTuitionHand(){
        $data  = I('post.');
        $student_id_array  = explode(',',$data['student_id1']);
        $charge_task = M('charge_task');
        $maps_charge_task['charge_standard_name'] = $data['charge_standard_name'];
        $receive_data_array = [];
        foreach($student_id_array as $student_id){
            $receive_data = [];
            $maps_charge_task['student_id'] = $student_id;
            $charge_task_id = $charge_task->where($maps_charge_task)->field('id')->find();
            $receive_data['charge_task_id'] = $charge_task_id['id'];
            $receive_data['type'] = "减免";
            $receive_data['reduce_batch_reason'] = $data['type'];
            $receive_data['student_tuition'] = $data['reduce_money_hand'];
            $receive_data['create_by'] = session('admin')['teacher_id'];
            $receive_data_array[] = $receive_data;
        }
        $receive_item = M('receive_item');
        if($receive_item->addAll($receive_data_array)){
            $this->success("减免成功");
        }else{
            $this->error("减免失败");
        }
    }

    /** 减免费申请 */

    public function reduceApply(){
        if($_GET){
            $charge_standard_name = I('charge_standard_name');
            $this->assign('charge_standard_name',$charge_standard_name);
            $this->display();
        }else{
            $charge_standard = M('charge_standard');
            $result_charge_standard = $charge_standard->join('teacher on charge_standard.create_by = teacher.teacher_id')
                ->field('charge_standard.charge_standard_name,teacher.teacher_name')
                ->group('charge_standard.charge_standard_name')
                ->select();
            $this->assign('result_charge_standard',$result_charge_standard);
            $this->display();
        }
    }
    //保存一条申请
    public function saveReduceApply(){
        $data = I('post.');
        //审核判断 如果正在审核中或者审核已经通过，不能进行二次申请
        $charge_task_id = D('ChargeTask')->getChargeTaskId($data);
        $rightReduceApply = D('ReduceApply')->rightReduceApply($charge_task_id);
        if(!$rightReduceApply){
            $this->error("正在审核中，或者已经审核通过，你暂时不能申请");
        }
        if(!empty($_FILES['student_id_card_photo']['tmp_name']) && !empty($_FILES['student_handicap_photo']['tmp_name'])){
            //如果上传了照片
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =    307200;// 设置附件上传大小300K
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
            $upload->savePath  =      './reduceApply/'; // 设置附件上传目录
            // 上传文件
            $info   =   $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                //$this->success('上传成功！');
            }
            //对文件进行重命名
            if(!changePhotoNameReduceApply($info, $data['student_id'])){
                $this->error('图片重命名失败');
            }
            $data['student_id_card_photo']=$info["student_id_card_photo"]["savepath"].$info["student_id_card_photo"]["savename"];
            $data['student_handicap_photo']=$info["student_handicap_photo"]["savepath"].$info["student_handicap_photo"]["savename"];
        }else{
            $this->error("请上传两张照片");
        }
        $data['charge_task_id'] = D('ChargeTask')->getChargeTaskId($data);
        $data['reduce_money'] = $data['reduce_money'];
        $data['text_description'] = $data['text_description'];
        $data['create_by'] = session('admin')['teacher_id'];
        $data['finish'] = "否";
        $data['examine_finance'] = "审核中";

        $reduce_apply = M('reduce_apply');
        if($reduce_apply->add($data)){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }
    //获得一个学生的详细信息
    public function getStudentInformation(){
        $student_id = I('student_id');
        $maps_student['student_id'] = $student_id;
        $maps_student['student_status'] = '在校';
        $result_student = M('student')->where($maps_student)->find();
        exit(json_encode($result_student));
    }

    /** 减免审核 */
    public function examineReduce(){
        if($_GET){
            $charge_standard_name = I('charge_standard_name');
            $charge_task = D("ChargeTask");
            $result_reduce_apply = $charge_task->getReduceApply($charge_standard_name);
            $this->assign('result_reduce_apply',$result_reduce_apply);
            $this->assign('charge_standard_name',$charge_standard_name);
            $this->display();
        }else{
            $charge_standard = M('charge_standard');
            $result_charge_standard = $charge_standard->join('teacher on charge_standard.create_by = teacher.teacher_id')
                ->field('charge_standard.charge_standard_name,teacher.teacher_name')
                ->group('charge_standard.charge_standard_name')
                ->select();
            $this->assign('result_charge_standard',$result_charge_standard);
            $this->display();
        }
    }
    //需要财务人员审核
    public function getExamineFinance(){
        $auth = new Auth();
        if(!$auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,session('admin')['teacher_id'])){
            $this->error("无此权限");
        }
        $charge_standard_name = I('charge_standard_name');
        $result_examine_finance = D("ReduceApply")->getExamineFinance($charge_standard_name);
        $this->assign('result_examine_finance',$result_examine_finance);
        $this->assign('charge_standard_name',$charge_standard_name);
        $this->display('Charge/examinereduce');
    }
    //需要校长审核
    public function getExamineSchoolmaster(){
        $auth = new Auth();
        if(!$auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,session('admin')['teacher_id'])){
            $this->error("无此权限");
        }
        $charge_standard_name = I('charge_standard_name');
        $result_examine_finance = D("ReduceApply")->getExamineSchoolmaster($charge_standard_name);
        $this->assign('result_examine_schoolmaster',$result_examine_finance);
        $this->assign('charge_standard_name',$charge_standard_name);
        $this->display('Charge/examinereduce');
    }
    //需要董事长审核
    public function getExamineChairman(){
        $auth = new Auth();
        if(!$auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,session('admin')['teacher_id'])){
            $this->error("无此权限");
        }
        $charge_standard_name = I('charge_standard_name');
        $result_examine_chairman = D("ReduceApply")->getExamineChairman($charge_standard_name);
        $this->assign('result_examine_chairman',$result_examine_chairman);
        $this->assign('charge_standard_name',$charge_standard_name);
        $this->display('Charge/examinereduce');
    }
    //具体的审核页面
    public function examineStudent(){
        $reduce_apply_id = I('reduce_apply_id');
        $flag = I('flag');
        $model = M('charge_task');
        $maps['r.id'] = $reduce_apply_id;
        $result_student_information_finance = $model->alias('c')
                        ->join('reduce_apply r on c.id = r.charge_task_id')
                        ->join('student s on c.student_id = s.student_id')
                        ->where($maps)
                        ->field('s.*,r.*,r.id as reduce_apply_id')
                        ->find();
        $this->assign('flag',$flag);
        $this->assign('examine_finance',$result_student_information_finance);
        $this->display('Charge/examinestudent');
    }
    //财务人员具体审核
    public function examineStudentFinance(){
        $data = I('post.');
        $reduce_apply = M('reduce_apply');
        $maps_reduce_apply['id'] = $data['reduce_apply_id'];
        if($data['examine_finance'] == "不通过"){
            //如果财务人员审核不通过
            $data_reduce_apply['examine_finance'] = "不通过";
            $data_reduce_apply['examine_finance_worker'] = session('admin')['teacher_id'];
            $data_reduce_apply['finish'] = "是";//表示此条申请数据结束
            $data_reduce_apply['fail_reason'] = $data['fail_reason'];
            if($reduce_apply->where($maps_reduce_apply)->save($data_reduce_apply)){
                $this->success("数据入库成功");
            }else{
                $this->error("数据入库失败");
            }
        }else{
            //如果财务人员审核通过，那么就传递到下一校长层次
            $data_reduce_apply['examine_finance'] = "通过";
            $data_reduce_apply['examine_finance_worker'] = session('admin')['teacher_id'];
            $data_reduce_apply['examine_schoolmaster'] = "审核中";
            if($reduce_apply->where($maps_reduce_apply)->save($data_reduce_apply)){
                $this->success("数据入库成功");
            }else{
                $this->error("数据入库失败");
            }
        }
    }
    //校长具体审核
    public function examineStudentSchoolmaster(){
        $data = I('post.');
        $reduce_apply = M('reduce_apply');
        $maps_reduce_apply['id'] = $data['reduce_apply_id'];
        $data_reduce_apply['examine_schoolmaster_worker'] = session('admin')['teacher_id'];
        if($data['examine_finance'] == "不通过"){
            //如果校长审核不通过
            $data_reduce_apply['examine_schoolmaster'] = "不通过";

            $data_reduce_apply['finish'] = "是";//表示此条申请数据结束
            $data_reduce_apply['fail_reason'] = $data['fail_reason'];
            if($reduce_apply->where($maps_reduce_apply)->save($data_reduce_apply)){
                $this->success("数据入库成功");
            }else{
                $this->error("数据入库失败");
            }
        }else{
            //如果校长审核通过，那么就传递到下一董事长层次
            $data_reduce_apply['examine_schoolmaster'] = "通过";
            $data_reduce_apply['examine_chairman'] = "审核中";
            if($reduce_apply->where($maps_reduce_apply)->save($data_reduce_apply)){
                $this->success("数据入库成功");
            }else{
                $this->error("数据入库失败");
            }
        }

    }
    //董事长具体审核
    public function examineStudentChairman(){
        $data = I('post.');
        $reduce_apply = M('reduce_apply');
        $maps_reduce_apply['id'] = $data['reduce_apply_id'];
        $data_reduce_apply['examine_chairman_worker'] = session('admin')['teacher_id'];
        $data_reduce_apply['finish'] = "是";//表示此条申请数据结束，不管是否通过此条数据结束
        if($data['examine_finance'] == "不通过"){
            //如果董事长审核不通过
            $data_reduce_apply['examine_chairman'] = "不通过";
            $data_reduce_apply['fail_reason'] = $data['fail_reason'];
            if($reduce_apply->where($maps_reduce_apply)->save($data_reduce_apply)){
                $this->success("数据入库成功");
            }else{
                $this->error("数据入库失败");
            }
        }else{
            //如果董事长审核通过，那么就减免学费
            $data_reduce_apply['examine_chairman'] = "通过";
            $reduce_apply->where($maps_reduce_apply)->save($data_reduce_apply);
            //减免学费
            $receive_item = M('receive_item');
            $data_receive_item['charge_task_id'] = $data['charge_task_id'];
            $data_receive_item['type'] = '减免';
            $data_receive_item['reduce_apply_id'] = $data['reduce_apply_id'];
            $data_receive_item['student_tuition'] = $data['reduce_money'];
            $data_receive_item['create_by'] = session('admin')['teacher_id'];
            if($receive_item->add($data_receive_item)){
                $this->success("数据入库成功");
            }else{
                $this->error("数据入库失败");
            }
        }

    }

    /** 退费办理 */

    public function returnMoney(){
        if($_GET){
            $charge_standard_name = I('charge_standard_name');
            $this->assign('charge_standard_name',$charge_standard_name);
            $this->display();
        }else{
            $charge_standard = M('charge_standard');
            $result_charge_standard = $charge_standard->join('teacher on charge_standard.create_by = teacher.teacher_id')
                ->field('charge_standard.charge_standard_name,teacher.teacher_name')
                ->group('charge_standard.charge_standard_name')
                ->select();
            $this->assign('result_charge_standard',$result_charge_standard);
            $this->display();
        }
    }
    //判断学生是否已经退学
    public function getStudentStatus(){
        $data = I('get.');
        if(D('Student')->getStudentStatus($data['student_id'],"退学")){
            $charge_task = D("ChargeTask");
            $result_charge_task = $charge_task->getChargeTaskStudentInformation($data);
            $receive_item = D("ReceiveItem");
            //收费明细
            $result_receive_item = $receive_item->getChargeTaskItem($result_charge_task['id']);
            //实收金额
            $result_charge_item_receive = $receive_item->getReceiveItemSum($result_charge_task['id'],"收费");
            //减免金额
            $result_charge_item_reduce = $receive_item->getReceiveItemSum($result_charge_task['id'],"减免");
            $this->assign("charge_standard_name",$data['charge_standard_name']);
            $this->assign('result_receive_item',$result_receive_item);
            $this->assign("result_charge_task",$result_charge_task);
            $this->assign("result_charge_item_receive",$result_charge_item_receive);
            $this->assign("result_charge_item_reduce",$result_charge_item_reduce);
            $this->display('Charge/returnmoney');

        }else{
            $this->error("学号错误或者学生状态不是退学，目前无法申请退费");
        }

    }
    //保存退费数据
    public function saveReturnMoney(){
        $receive_item = M('receive_item');
        $data_receive_item = I('post.');
        $data_receive_item['create_by'] = session('admin')['teacher_id'];
        $data_receive_item['type'] = '退费';
        $data_receive_item['payment_type'] = '现金';
        if($receive_item->add($data_receive_item)){
            $this->redirect('getStudentStatus',array('student_id'=>$data_receive_item['student_id'],'charge_standard_name'=>$data_receive_item['charge_standard_name']));
        }else{
            $this->error("入库失败");
        }
    }

    /** 统计分析 */

    public function countAnalyse(){
        if($_GET){
            $charge_standard_name = I('charge_standard_name');
            $this->assign('charge_standard_name',$charge_standard_name);
            $this->display();
        }else{
            $charge_standard = M('charge_standard');
            $result_charge_standard = $charge_standard->join('teacher on charge_standard.create_by = teacher.teacher_id')
                ->field('charge_standard.charge_standard_name,teacher.teacher_name')
                ->group('charge_standard.charge_standard_name')
                ->select();
            $this->assign('result_charge_standard',$result_charge_standard);
            $this->display();
        }
    }
    //统计分析查询
    public function countAnalyseQuery(){
        $charge_task = D("ChargeTask");
        $maps = $this->formatCountAnalyseQuery();
        $result = $charge_task->countAnalyseQuery($maps);
        $this->assign('charge_standard_name',I('charge_standard_name'));
        $this->assign('sum_receive_item',$result[0]);
        $this->assign('resultCountAnalyseQuery',$result[1]);
        $this->display('Charge/countanalyse');
    }
    //格式化统计分析查询条件
    public function formatCountAnalyseQuery(){
        $data = I('post.');
        $maps['charge_task.charge_standard_name']=$data['charge_standard_name'];
        if($data['student_grade'] != '--'){$maps['student.student_grade'] = $data['student_grade'];}
        if($data['student_class'] != '--'){$maps['student.student_class'] = $data['student_class'];}
        if($data['type'] != '--'){$maps['receive_item.type'] = $data['type'];}
        !empty($data['student_id']) && $maps['charge_task.student_id'] = $data['student_id'];
        //只有开始时间
        !empty($data['s_time']) && $maps['receive_item.create_time'] = array('egt',date('Y-m-d 00:00:00',$data['s_time']));
        //只有结束时间
        !empty($data['e_time']) && $maps['receive_item.create_time'] = array('elt',date('Y-m-d 23:59:59',$data['e_time']));
        //开始、结束时间都有
        !empty($data['s_time']) && !empty($data['e_time']) && $maps['receive_item.create_time'] = array(array('egt',date('Y-m-d 00:00:00',$data['s_time'])),array('elt',date('Y-m-d 00:00:00',$data['s_time'])));
        return $maps;
    }
}