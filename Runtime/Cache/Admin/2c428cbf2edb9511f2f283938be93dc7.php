<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>项城市红旗学校数字化校园管理平台</title>
    <link rel="stylesheet" type="text/css" href="/Public/Admin/Css/style.css">
    <script src="/Public/Admin/Js/jquery-1.11.0.min.js"></script>
    
    <link rel="stylesheet" href="/Public/Admin/Css/BeatPicker.min.css"/>
    <script src="/Public/Admin/Js/BeatPicker.min.js"></script>
    <script src="/Public/Admin/Js/birthday.js"></script>
    <script type="text/javascript" src="/Public/Admin/Js/changeDate.js"></script>
    <style type="text/css">
        .auditingFrame .table input{width: 70px}
        .auditingFrame .table select{width: 70px}
    </style>

</head>

<body>

<div class="wrap">
    <div class="platLeft">
        <div class="platLogo"><a href=""><img src="/Public/Admin/Images/platLogo.png"></a></div>
        <ul class="platNav">
            <li> <i class="icon icon3"></i><a href="" style="color: #FFFFFF">行政办公中心</a></li>
            <li> <i class="icon icon3"></i><a href="" style="color: #FFFFFF">教务管理中心</a></li>
            <li> <i class="icon icon3"></i><a href="" style="color: #FFFFFF">政教管理中心</a></li>
            <li> <i class="icon icon3"></i><a href="<?php echo U('admin/finance/index');?>" style="color: #FFFFFF">财务管理中心</a></li>
            <li> <i class="icon icon3"></i><a href="" style="color: #FFFFFF">总务管理中心</a></li>
            <li> <i class="icon icon3"></i><a href="" style="color: #FFFFFF">系统设置中心</a></li>
        </ul>
    </div>

    <div class="platRight">
        <div class="platTop">
            <div class="Tit">项城市红旗学校数字化校园管理平台</div>
            <div class="TopR">
                <a href="<?php echo U('admin/index/logout');?>">退出</a>
            </div>
            <div style="font-size: 21px;float: right;margin-right: 750px;"><a class="back" onclick="JavaScript:history.go(-1);">后退</a></div>
        </div>
        
    <div class="platCon">
        <div class="platState white">
            <div class="platTitle"><h2>学生档案</h2><a href="<?php echo U('admin/student/indexindex');?>"><img src="/Public/Admin/Images/return.png" style="margin-left: 200px"></a></div>
            <i class="refresh" onclick="javascript:location.reload(true);" title="刷新当前页面"></i>
            <div class="recordCou">
                <div style="display: inline-block;width: 50px;margin-left: 30px;">
                    <img src="/Public/Admin/Images/studentAdd.png">
                    <input type="button" class="excesssave" value="新增" onclick="add()">
                </div>

                <div style="display: inline-block;width: 50px;margin-left: 3px;">
                    <img src="/Public/Admin/Images/studentSave.png">
                    <input type="button" class="excesssave" value="保存" onclick="save()">
                </div>
                    &nbsp; &nbsp; &nbsp;
                <div style="display: inline-block;width: 50px">
                    <img src="/Public/Admin/Images/studentQuery.png">
                    <input type="button" class="excesssave" value="查询" onclick="query()">
                </div>

                <div style="display: inline-block;width: 50px">
                    <img src="/Public/Admin/Images/studentUpdate.png">
                    <input type="button" class="excesssave" value="修改" onclick="updatestudent()">
                </div>

                <div style="display: inline-block;width: 50px">
                    <img src="/Public/Admin/Images/studentDelete.png">
                    <input type="submit" class="excesssave" value="删除" onclick="dele()">
                </div>
                    &nbsp; &nbsp; &nbsp;
                <div style="display: inline-block;width: 50px">
                    <img src="/Public/Admin/Images/studentImport.png">
                    <input type="button" class="excesssave" value="导入" onclick="importstudent()">
                </div>

                <div style="display: inline-block;width: 50px">
                    <img src="/Public/Admin/Images/studentExport.png">
                    <form action="<?php echo U('admin/student/exportStudent');?>" style="display: inline-block;" method="post" id="student_export">
                        <input type="hidden" id="student_id_array" name="student_id_array">
                        <input type="button" class="excesssave" value="导出" onclick="exported()">
                    </form>
                </div>
                <!--学生信息中照片的展示-->
                <?php if(!empty($student_each)): ?><img src="/Uploads<?php echo ($student_each['student_photo']); ?>" id="studentImage" width="200px" height="200px" style="float: left;margin-left: 800px;margin-top: -100px;"><?php endif; ?>

                <!--学生信息的输入-->
                <form action="<?php echo U('admin/student/index');?>" method="post" style="display: none" id="form1" enctype="multipart/form-data" onsubmit="return check()">
                    <table class="table" style="width: 86%;margin-top: 30px;">
                        <tbody>
                        <tr>
                            <td>姓名</td>
                            <td><input type="text" name="student_name" placeholder="张三" id="student_name"></td>
                            <td>性别</td>
                            <td>
                                <label for="5">男</label> <input type="radio" checked="checked" name="student_sex" value="男" id="5" >
                                <label for="6" style="padding-left: 20px">女</label> <input type="radio"  name="student_sex" value="女" id="6" >
                            </td>
                            <td>学号</td>
                            <td><input type="text" name="student_id" placeholder="12121612" id="student_id"></td>
                            <td>民族</td>
                            <td><input type="text" name="student_nation" placeholder="汉族" id="student_nation"></td>
                        </tr>
                        <tr>
                            <td>出生年月</td>
                            <td><select name='student_year' class="sel_year" rel="1993" style="width: 50px"> </select> 年
                                <select name='student_month' class="sel_month" rel="7" style="width: 50px"> </select> 月
                                </li></td>
                            <td>籍贯</td>
                            <td><input type="text" name="student_native" placeholder="项城" id="student_native"></td>
                            <td>家庭地址</td>
                            <td colspan="3">
                                <input type="text" name="student_address" id="student_address" placeholder="**省**市**乡**村" style="width: 300px;">
                            </td>
                        </tr>
                        <tr>
                            <td>隶属年级</td>
                            <td><input type="text" name="student_grade" placeholder="七年级" id="student_grade"></td>
                            <td>是否住宿</td>
                            <td> <label for="13">是</label> <input type="radio" checked="checked" name="student_accommodation" value="是" id="13" >
                                <label for="14" style="padding-left: 20px">否</label> <input type="radio"  name="student_accommodation" value="否" id="14" ></td>
                            <td>是否包餐</td>
                            <td>  <label for="9">是</label> <input type="radio" checked="checked" name="student_meal" value="是" id="9" >
                                <label for="10" style="padding-left: 20px">否</label> <input type="radio"  name="student_meal" value="否" id="10" ></td>
                            <td>是否保险</td>
                            <td>  <label for="7">是</label> <input type="radio" checked="checked" name="student_insurance" value="是" id="7" >
                                <label for="8" style="padding-left: 20px">否</label> <input type="radio"  name="student_insurance" value="否" id="8" >·</td>
                        </tr>
                        <tr>
                            <td>父亲姓名</td>
                            <td><input type="text" name="student_father" placeholder="张父" id="student_father"></td>
                            <td>联系电话</td>
                            <td><input type="text" name="student_father_phone" placeholder="18818217017" id="student_father_phone"></td>
                            <td>母亲姓名</td>
                            <td><input type="text" name="student_mother" placeholder="张母" id="student_mother"></td>
                            <td>联系电话</td>
                            <td><input type="text" name="student_mother_phone" placeholder="18818217017" id="student_mother_phone"></td>
                        </tr>
                        <tr>
                            <td>隶属班级</td>
                            <td><input type="text" name="student_class" placeholder="七班" id="student_class"></td>
                            <td>寝室编号</td>
                            <td><input type="text" name="student_dorm_number" placeholder="203" id="student_dorm_number"></td>
                            <td>学生卡号</td>
                            <td><input type="text" name="student_card" placeholder="12121612" id="student_card"></td>
                            <td>在校状态</td>
                            <td>   <label for="1">在校</label> <input type="radio" checked="checked" name="student_status" value="在校" id="1" >
                                <label for="2" style="padding-left: 10px">退学</label> <input type="radio"  name="student_status" value="退学" id="2" >
                                <label for="3" style="padding-left: 10px">休学</label> <input type="radio"  name="student_status" value="休学" id="3" >
                                <label for="4" style="padding-left: 10px">转学</label> <input type="radio"  name="student_status" value="转学" id="4" ></td>
                        </tr>
                        <tr>
                            <td>校区</td>
                            <td><input type="text" name="student_campus" placeholder="本部" id="student_campus"></td>
                            <td>宿舍楼</td>
                            <td><input type="text" name="student_dorm_building" id="student_dorm_building"></td>
                            <td>注册介绍人</td>
                            <td><input type="text" name="student_introduce"></td>
                            <td>注册时间</td>
                            <input type="hide" name="s_time" style="width:0px;">
                            <td>
                                <input type="text" id="s_time" data-beatpicker="true" data-beatpicker-module="clear" placeholder="日报日期" class="beatpicker-input beatpicker-inputnode" readonly="readonly" data-beatpicker-id="beatpicker-0">
                            </td>
                        </tr>
                        <tr>
                            <td>学生照片</td>
                            <td><input type="file" name="student_photo" accept=".png,.jpeg,.jpg"></td>
                            <td>教师子女</td>
                            <td>
                                <label for="11">是</label> <input type="radio" checked="checked" name="student_teacher" value="是" id="11" >
                                <label for="12" style="padding-left: 20px">否</label> <input type="radio"  name="student_teacher" value="否" id="12" ></td>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
                <!--查询结果-->
                <?php if(!empty($result_student)): ?><table class="table" style="width: 100%;margin-top: 30px;" id="table1">
                            <thead>
                            <th><input type="checkbox" id="all"></th>
                            <th>学号</th>
                            <th>姓名</th>
                            <th >年级</th>
                            <th>班级</th>
                            <th>性别</th>
                            <th>民族</th>
                            <th>出生年月</th>
                            <th>籍贯</th>
                            <th>家庭住址</th>
                            <th>住宿</th>
                            <th>包餐</th>
                            <th>保险</th>
                            <th>父亲</th>
                            <th>父亲电话</th>
                            <th>母亲</th>
                            <th>母亲电话</th>
                            <th>宿舍楼</th>
                            <th>宿舍编号</th>
                            <th>学生卡号</th>
                            <th>在校状态</th>
                            <th>注册介绍人</th>
                            <th>注册日期</th>
                            <th>教师子女</th>
                            </thead>
                        <tbody id="list">
                            <?php if(is_array($result_student)): $i = 0; $__LIST__ = $result_student;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                    <td><input type="checkbox" name="choice" value="<?php echo ($item["student_id"]); ?>"></td>
                                    <td><a href="<?php echo U('admin/student/updatestudent?student_id='.$item['student_id']);?>"><?php echo ($item["student_id"]); ?></a></td>
                                    <td><?php echo ($item["student_name"]); ?></td>
                                    <td><?php echo ($item["student_grade"]); ?></td>
                                    <td><?php echo ($item["student_class"]); ?></td>
                                    <td><?php echo ($item["student_sex"]); ?></td>
                                    <td><?php echo ($item["student_nation"]); ?></td>
                                    <td><?php echo ($item["student_birthdate"]); ?></td>
                                    <td><?php echo ($item["student_native"]); ?></td>
                                    <td><?php echo ($item["student_address"]); ?></td>
                                    <td><?php echo ($item["student_accommodation"]); ?></td>
                                    <td><?php echo ($item["student_meal"]); ?></td>
                                    <td><?php echo ($item["student_insurance"]); ?></td>
                                    <td><?php echo ($item["student_father"]); ?></td>
                                    <td><?php echo ($item["student_father_phone"]); ?></td>
                                    <td><?php echo ($item["student_mother"]); ?></td>
                                    <td><?php echo ($item["student_mother_phone"]); ?></td>
                                    <td><?php echo ($item["student_dorm_building"]); ?></td>
                                    <td><?php echo ($item["student_dorm_number"]); ?></td>
                                    <td><?php echo ($item["student_card"]); ?></td>
                                    <td><?php echo ($item["student_status"]); ?></td>
                                    <td><?php echo ($item["student_introduce"]); ?></td>
                                    <td><?php echo ($item["student_register_date"]); ?></td>
                                    <td><?php echo ($item["student_teacher"]); ?></td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table><?php endif; ?>
                <!--学生信息的修改-->
                <?php if(!empty($student_each)): ?><form action="<?php echo U('admin/student/updatestudent');?>" method="post" id="form2" enctype="multipart/form-data">
                        <table class="table" style="width: 86%;margin-top: 30px;">
                            <tbody>
                            <tr>
                                <td>姓名</td>
                                <td><input type="text" name="student_name" placeholder="张三" id="student_name2" value="<?php echo ($student_each['student_name']); ?>"></td>
                                <td>性别</td>
                                <td>
                                    <label for="33">男</label> <input type="radio" checked="checked" name="student_sex" value="男" id="33" >
                                    <label for="34" style="padding-left: 20px">女</label> <input type="radio"  name="student_sex" value="女" id="34" >
                                </td>
                                <td>学号</td>
                                <td><input type="text" name="student_id" placeholder="12121612" id="student_id2" value="<?php echo ($student_each['student_id']); ?>"></td>
                                <td>民族</td>
                                <td><input type="text" name="student_nation" placeholder="汉族" id="student_nation2" value="<?php echo ($student_each['student_nation']); ?>"></td>
                            </tr>
                            <tr>
                                <td>出生年月</td>
                                <td><select name='student_year' class="sel_year2" rel="1993" style="width: 50px" id="student_year2"> </select> 年
                                    <select name='student_month' class="sel_month2" rel="7" style="width: 50px" id="student_month2"> </select> 月
                                    </li></td>
                                <td>籍贯</td>
                                <td><input type="text" name="student_native" placeholder="项城" id="student_native2" value="<?php echo ($student_each['student_native']); ?>"></td>
                                <td>家庭地址</td>
                                <td colspan="3">
                                    <input type="text" name="student_address" id="student_address2" placeholder="**省**市**乡**村" style="width: 300px;" value="<?php echo ($student_each['student_address']); ?>">
                                </td>
                            </tr>
                            <tr>
                                <td>隶属年级</td>
                                <td><input type="text" name="student_grade" placeholder="七年级" id="student_grade2" value="<?php echo ($student_each['student_grade']); ?>"></td>
                                <td>是否住宿</td>
                                <td> <label for="20">是</label> <input type="radio" checked="checked" name="student_accommodation" value="是" id="20" >
                                    <label for="21" style="padding-left: 20px">否</label> <input type="radio"  name="student_accommodation" value="否" id="21" ></td>
                                <td>是否包餐</td>
                                <td>  <label for="22">是</label> <input type="radio" checked="checked" name="student_meal" value="是" id="22" >
                                    <label for="23" style="padding-left: 20px">否</label> <input type="radio"  name="student_meal" value="否" id="23" ></td>
                                <td>是否保险</td>
                                <td>  <label for="24">是</label> <input type="radio" checked="checked" name="student_insurance" value="是" id="24" >
                                    <label for="25" style="padding-left: 20px">否</label> <input type="radio"  name="student_insurance" value="否" id="25" >·</td>
                            </tr>
                            <tr>
                                <td>父亲姓名</td>
                                <td><input type="text" name="student_father" placeholder="张父" id="student_father2" value="<?php echo ($student_each['student_father']); ?>"></td>
                                <td>联系电话</td>
                                <td><input type="text" name="student_father_phone" placeholder="18818217017" id="student_father_phone2" value="<?php echo ($student_each['student_father_phone']); ?>"></td>
                                <td>母亲姓名</td>
                                <td><input type="text" name="student_mother" placeholder="张母" id="student_mother2" value="<?php echo ($student_each['student_mother']); ?>"></td>
                                <td>联系电话</td>
                                <td><input type="text" name="student_mother_phone" placeholder="18818217017" id="student_mother_phone2" value="<?php echo ($student_each['student_mother_phone']); ?>"></td>
                            </tr>
                            <tr>
                                <td>隶属班级</td>
                                <td><input type="text" name="student_class" placeholder="七班" id="student_class2" value="<?php echo ($student_each['student_class']); ?>"></td>
                                <td>寝室编号</td>
                                <td><input type="text" name="student_dorm_number" placeholder="203" id="student_dorm_number2" value="<?php echo ($student_each['student_dorm_number']); ?>"></td>
                                <td>学生卡号</td>
                                <td><input type="text" name="student_card" placeholder="12121612" id="student_card2" value="<?php echo ($student_each['student_card']); ?>"></td>
                                <td>在校状态</td>
                                <td>   <label for="26">在校</label> <input type="radio" checked="checked" name="student_status" value="在校" id="26" >
                                    <label for="27" style="padding-left: 10px">退学</label> <input type="radio"  name="student_status" value="退学" id="27" >
                                    <label for="28" style="padding-left: 10px">休学</label> <input type="radio"  name="student_status" value="休学" id="28" >
                                    <label for="29" style="padding-left: 10px">转学</label> <input type="radio"  name="student_status" value="转学" id="29" ></td>
                            </tr>
                            <tr>
                                <td>校区</td>
                                <td><input type="text" name="student_campus" placeholder="本部" id="student_campus2" value="<?php echo ($student_each['student_campus']); ?>"></td>
                                <td>宿舍楼</td>
                                <td><input type="text" name="student_dorm_building" id="student_dorm_building2" value="<?php echo ($student_each['student_dorm_building']); ?>"></td>
                                <td>注册介绍人</td>
                                <td><input type="text" name="student_introduce" value="<?php echo ($student_each['student_introduce']); ?>"></td>
                                <td>注册时间</td>
                                <input type="hide" name="s_time2" style="width:0px;" >
                                <td>
                                    <input type="text" id="s_time2" value="<?php echo ($student_each['student_register_date']); ?>" data-beatpicker="true" data-beatpicker-module="clear" placeholder="日报日期" class="beatpicker-input beatpicker-inputnode" readonly="readonly" data-beatpicker-id="beatpicker-0">
                                </td>
                            </tr>
                            <tr>
                                <td>学生照片</td>
                                <td><input type="file" name="student_photo" accept=".png,.jpeg,.jpg"></td>
                                <td>教师子女</td>
                                <td>
                                    <label for="30">是</label> <input type="radio" checked="checked" name="student_teacher" value="是" id="30" >
                                    <label for="31" style="padding-left: 20px">否</label> <input type="radio"  name="student_teacher" value="否" id="31" >·</td>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form><?php endif; ?>
                <!--学生信息导入-->
                <form  action="<?php echo U('admin/student/importstudent');?>" method="post" style="display: none;margin-left: 250px;margin-top: 50px" id="form3" enctype="multipart/form-data" onsubmit="return check3()">
                    <input type="file" name="importStudent" accept=".csv" id="importStudent">
                    <input type="submit" value="提交后台" style="width: 90px;height: 30px;border-radius: 4px;cursor: pointer;">
                </form>

            </div>
        </div>
    </div>

    <div class="auditingFrame">
        <div class="aFtitle">设置查询条件</div>
        <div class="aFcona">
            <form action="<?php echo U('admin/student/querystudent');?>" method="post">
                <table class="table" cellspacing="0" cellpadding="0" style="width: 100%">
                    <tbody>
                    <tr>
                        <td>年级</td>
                        <div class="selectList">
                        <td><select name="student_grade" class="grade">
                            <!--<option value="--">--</option>-->
                            <!--<option value="小班">小班</option>-->
                            <!--<option value="中班">中班</option>-->
                            <!--<option value="大班">大班</option>-->
                            <!--<option value="一年级">一年级</option>-->
                            <!--<option value="二年级">二年级</option>-->
                            <!--<option value="三年级">三年级</option>-->
                            <!--<option value="四年级">四年级</option>-->
                            <!--<option value="五年级">五年级</option>-->
                            <!--<option value="六年级">六年级</option>-->
                            <!--<option value="七年级">七年级</option>-->
                            <!--<option value="八年级">八年级</option>-->
                            <!--<option value="九年级">九年级</option>-->
                        </select></td>
                        <td>班级</td>
                        <td><select name="student_class" class="class">

                        </select>
                            <!--<input name="student_class">-->
                        </td>

                        </div>

                        <td>注册介绍人</td>
                        <td><input name="student_introduce"></td>
                        <td>注册日期</td>
                              <input type="hide" name="s_time1" style="width:0px;">
                        <td>  <input type="text" id="s_time1" data-beatpicker="true" data-beatpicker-module="clear" placeholder="日报日期" class="beatpicker-input beatpicker-inputnode" readonly="readonly" data-beatpicker-id="beatpicker-0"></td>
                    </tr>
                    <tr>
                        <td>性别</td>
                        <td><select name="student_sex">
                            <option value="--">--</option>
                            <option value="男">男</option>
                            <option value="女">女</option>
                        </select></td>

                        <td>住宿</td>
                        <td>
                            <select name="student_accommodation">
                                <option value="--">--</option>
                                <option value="是">是</option>
                                <option value="否">否</option>
                            </select>
                        </td>

                        <td>包餐</td>
                        <td> <select name="student_meal">
                            <option value="--">--</option>
                            <option value="是">是</option>
                            <option value="否">否</option>
                        </select></td>

                        <td>保险</td>
                        <td> <select name="student_insurance">
                            <option value="--">--</option>
                            <option value="是">是</option>
                            <option value="否">否</option>
                        </select></td>
                    </tr>
                    <tr>
                        <td>教师子女</td>
                        <td>
                            <select name="student_teacher">
                                <option value="--">--</option>
                                <option value="是">是</option>
                                <option value="否">否</option>
                            </select>
                        </td>
                        <td>在校状态</td>
                        <td>
                            <select name="student_status">
                                <option value="--">--</option>
                                <option value="在校">在校</option>
                                <option value="退学">退学</option>
                                <option value="休学">休学</option>
                                <option value="转学">转学</option>
                            </select>
                        </td>
                        <td>宿舍楼</td>
                        <td><input type="text" name="student_dorm_building"></td>
                        <td>寝室编号</td>
                        <td><input type="text" name="student_dorm_number"></td>
                    </tr>
                    <tr>
                        <td colspan="4">输入姓名、学号、籍贯、家庭住址、学生卡号</td>
                        <td colspan="2"><input style="width: 100px" name="muohu"></td>
                    </tr>
                    </tbody>
                </table>
                <input  type="submit"  value="查询" class="adopt" onclick="javascript:format_changeDate('s_time1','e_time');">
            </form>
        </div>
        <i class="info_close"></i>
    </div>
    <div class="info_bg"></div>

    </div>
</div>
<script type="text/javascript" src="/Public/Admin/Js/platform.js"></script>
<script>
    $('.sort > a').click(function(){
        var link  = window.location.href.toString();
        var type  = $(this).parent().attr('type');
        var order = $(this).attr('class').split(' ')[0];
        link = link.replace(/([?&-]type[=-])([^&-]*)/gi,"$1"+type);
        link = link.replace(/([?&-]order[=-])([^&-]*)/gi,"$1"+order);
        if(link.match(/[?&-]order[=-]/)==null){
            link += (link.indexOf('?') == -1 ? '?' : '&') + "type="+type+"&order="+order;
        }
        window.location.href = link;
    });
</script>

    <script><?php if(($_REQUEST['status']!= '') OR ($_REQUEST['s_time']!= '') OR ($_REQUEST['e_time']!= '') ): ?>toggel("reSearchM");<?php else: ?>toggel("RCsearch");<?php endif; ?></script>
    <script type="text/javascript">
        //新增按钮
        function add(){
            $("#form1").show()
            $("#form2").hide()
            $("#form3").hide()
            $("#table1").hide()
            $("#studentImage").hide()
        }
        //保存按钮
        function save(){
            format_changeDate('s_time','e_time');
            $("#form1").submit()
        }
        //检查提交学生数据是否合理
        function check(){
          if(!$("#student_id").val()){
              alert("请输入学号")
              return false
          }else{
              return true
          }
        }
        //关闭按钮
        $(".info_close").click(function(){
            $(".info_bg").hide();
            $(".auditingFrame").hide();
        })
        //查询按钮
        function query() {
            $(".info_bg").show();
            $(".auditingFrame").show();
            $(".auditingFrame").css({
                top: function(index, value) {
                    return $(window).scrollTop() + ($(window).height()/2);
                }
            });
        }
        //查询结果全选按钮

        //修改学生信息按钮
        function updatestudent(){
            format_changeDate('s_time2','e_time');
            $("#form2").submit()
        }
        //删除按钮
        function dele(){
            var choice_value = []
            $('input[name="choice"]:checked').each(function(){
                choice_value.push($(this).val())
            })

            var data = {'student_id_array':choice_value}
            var  url = "/admin/" + "student-delete.html"
            $.getJSON(url,data,function(response){
                if(response.code==1){
                    alert('删除成功')
                    $(".info_bg").hide();
                    $(".auditingFrame").hide();
                    window.location.reload()//刷新当前页面.
                }else{
                    alert('删除失败');
                }
            })
        }
        //导出按钮
        function exported(){
            var choice_value = []
            $('input[name="choice"]:checked').each(function(){
                choice_value.push($(this).val())
            })
            $("#student_id_array").val(choice_value)
            $("#student_export").submit()
        }
        //导入按钮
        function importstudent(){
            $("#form1").hide()
            $("#form2").hide()
            $("#table1").hide()
            $("#studentImage").hide()
            $("#form3").show()
        }
        //检查导入文件格式，只能是.csv格式
        function check3(){
            var file = $("#importStudent").val()
            if(file==''){
                alert("请选择文件")
                return false
            }
            var ext = file.substr(file.indexOf("."));
            if(ext !='.csv'){
                alert("上传文件格式不对，必须是csv文件")
                return false
            }else{
                return true
            }
        }

        $(function(){
            if("<?php echo ($student_each['student_sex']); ?>" == '男'){
                $("#33").attr("checked","checked");
            }else{
                $("#34").attr("checked","checked");
            }

            if("<?php echo ($student_each['student_accommodation']); ?>" == '是'){
                $("#20").attr("checked","checked");
            }else{
                $("#21").attr("checked","checked");
            }

            if("<?php echo ($student_each['student_meal']); ?>" == '是'){
                $("#22").attr("checked","checked");
            }else{
                $("#23").attr("checked","checked");
            }

            if("<?php echo ($student_each['student_insurance']); ?>" == '是'){
                $("#24").attr("checked","checked");
            }else{
                $("#25").attr("checked","checked");
            }

            var student_status = "<?php echo ($student_each['student_status']); ?>"
            switch (student_status){
                case '在校':
                    $("#26").attr("checked","checked");
                    break;
                case '退学':
                    $("#27").attr("checked","checked");
                    break;
                case '休学':
                    $("#28").attr("checked","checked");
                    break;
                case '转学':
                    $("#29").attr("checked","checked");
                    break;
            }

            if("<?php echo ($student_each['student_teacher']); ?>" == '是'){
                $("#30").attr("checked","checked");
            }else{
                $("#31").attr("checked","checked");
            }

            //出生日期处理
            var birthdate = "<?php echo ($student_each['student_birthdate']); ?>".split('-')
            var student_year = birthdate[0]
            var student_month = birthdate[1]
            $('#student_year2').attr('rel',student_year)
            $('#student_month2').attr('rel',student_month)
            //下面这个是确定学生信息里面的日期，要修改的表
            $.ms_DatePicker({
                YearSelector: ".sel_year2",
                MonthSelector: ".sel_month2",
                DaySelector: ".sel_day"
            });

            $.ms_DatePicker({
                YearSelector: ".sel_year",
                MonthSelector: ".sel_month",
                DaySelector: ".sel_day"
               });

            $("#all").click(function(){
                if(this.checked){
                    $("#list :checkbox").prop("checked", true);
                }else{
                    $("#list :checkbox").removeProp("checked", false);
                }
            });

            //年级班级二级联动菜单
                var  url = "/admin/" + "grade-index.html"
                var gradeJson;
                //获取json数据
                $.getJSON(url,function(data){
                    gradeJson = data;
                    grade();
                });
                var temp_html = "<option value='--'>--</option>";
                var ograde = $(".grade");
                var oclass = $(".class");
//初始化年级
                var grade = function(){
                    $.each(gradeJson,function(i,grade){
                        temp_html+="<option value='"+grade.g+"'>"+grade.g+"</option>";
                    });
                    ograde.html(temp_html);
                    classf();
                };
//赋值班级
                var classf = function(){
                    temp_html = "<option value='--'>--</option>";
                    var n = ograde.get(0).selectedIndex;
                    if( n == 0){
                    }else{
                        $.each(gradeJson[n-1].c,function(i,cl){
                            temp_html+="<option value='"+cl.cl+"'>"+cl.cl+"</option>";
                        });
                    }

                    oclass.html(temp_html);
                };
//选择年级
                ograde.change(function(){
                    classf();
                });
            });

    </script>

</body>
</html>