<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>项城市红旗学校数字化校园管理平台</title>
    <link rel="stylesheet" type="text/css" href="/Public/Admin/Css/style.css">
    <script src="/Public/Admin/Js/jquery-1.11.0.min.js"></script>
    
    <link rel="stylesheet" href="/Public/Admin/Css/BeatPicker.min.css"/>
    <link rel="stylesheet" href="/Public/Admin/Css/hongqi.css"/>
    <script src="/Public/Admin/Js/BeatPicker.min.js"></script>
    <script src="/Public/Admin/Js/hongQi.js"></script>
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
            <div class="platTitle"><h2>减免申请</h2><a href="<?php echo U('admin/student/indexindex');?>"><img src="/Public/Admin/Images/return.png" style="margin-left: 200px"></a></div>
            <i class="refresh" onclick="javascript:location.reload(true);" title="刷新当前页面"></i>
            <div class="recordCou">
                <div style="display: inline-block;width: 50px;margin-left: 3px;">
                    <img src="/Public/Admin/Images/studentSave.png">
                    <input type="button" class="excesssave" value="申请" onclick="saveReduceApply()">
                </div>

                <!--申请页面-->
                <?php if(!empty($charge_standard_name)): ?><form action="<?php echo U('admin/charge/saveReduceApply');?>" method="post" enctype="multipart/form-data" id="saveReduceApply">
                        <table class="table">
                            <thead>
                            <th width="15%">收费项目</th>
                            <th width="10%">学号</th>
                            <th width="10%">户口本照片</th>
                            <th width="10%">低保照片</th>
                            <th width="10%">减免金额</th>
                            <th width="40%">文字说明</th>
                            </thead>
                            <tbody>
                            <tr>
                                <input type="hidden" name="charge_standard_name" value="<?php echo ($charge_standard_name); ?>" >
                                <td><?php echo ($charge_standard_name); ?></td>
                                <td><input style="width: 90%;" name="student_id" id="student_id"><button id="getStudentInformation" type="button">详情</button></td>
                                <td><input type="file" style="width: 140px" accept=".png,.jpeg,.jpg" name="student_id_card_photo"></td>
                                <td><input type="file" style="width: 140px" accept=".png,.jpeg,.jpg" name="student_handicap_photo"></td>
                                <td><input style="width: 90%;" name="reduce_money"></td>
                                <td><textarea rows="4" cols="6" style="width: 100%;" name="text_description"></textarea></td>
                            </tr>
                            </tbody>
                        </table>

                        <div class="selectList" style="margin-top: 20px;" id="selectList">
                            <?php echo ($charge_standard_name); ?><input type="hidden" name="charge_standard_name" value="<?php echo ($charge_standard_name); ?>" id="charge_standard_name">
                            年级<select name="student_grade" class="grade" id="student_grade1"></select>
                            班级<select name="student_class" class="class" id="student_class1"></select>
                            <input type="text" name="student_id" placeholder="学号,姓名" id="student_id2"><b></b>
                            <input  class="excesssave" value="搜索" onclick="getStudentInformation1()"></li>

                        </div>

                        <table class="table" id="tableid">
                        </table>
                    </form>

                        <form id="studentInformation" style="display: none">
                            <table class="table" style="width: 86%;margin-top: 10px;">
                                <tbody>
                                <tr>
                                    <td>姓名</td>
                                    <td id="student_name"></td>
                                    <td>性别</td>
                                    <td id="student_sex"></td>
                                    <td>学号</td>
                                    <td id="student_id1"></td>
                                    <td>民族</td>
                                    <td id="student_nation"></td>
                                </tr>
                                <tr>
                                    <td>出生年月</td>
                                    <td id="student_birthdate"></td>
                                    <td>籍贯</td>
                                    <td id="student_native"></td>
                                    <td>家庭地址</td>
                                    <td colspan="3" id="student_address"></td>
                                </tr>
                                <tr>
                                    <td>隶属年级</td>
                                    <td id="student_grade"></td>
                                    <td>是否住宿</td>
                                    <td id="student_accommodation"></td>
                                    <td>是否包餐</td>
                                    <td id="student_meal"></td>
                                    <td>是否保险</td>
                                    <td id="student_insurance"></td>
                                </tr>
                                <tr>
                                    <td>父亲姓名</td>
                                    <td id="student_father"></td>
                                    <td>联系电话</td>
                                    <td id="student_father_phone"></td>
                                    <td>母亲姓名</td>
                                    <td id="student_mother"></td>
                                    <td>联系电话</td>
                                    <td id="student_mother_phone"></td>
                                </tr>
                                <tr>
                                    <td>隶属班级</td>
                                    <td id="student_class"></td>
                                    <td>寝室编号</td>
                                    <td id="student_dorm_number"></td>
                                    <td>学生卡号</td>
                                    <td id="student_card"></td>
                                    <td>在校状态</td>
                                    <td id="student_status"></td>
                                </tr>
                                <tr>
                                    <td>校区</td>
                                    <td id="student_campus"></td>
                                    <td>宿舍楼</td>
                                    <td id="student_dorm_building"></td>
                                    <td>注册介绍人</td>
                                    <td id="student_introduce"></td>
                                    <td>注册时间</td>
                                    <td id="student_register_date"></td>
                                </tr>
                                <tr>
                                    <td>教师子女</td>
                                    <td id="student_teacher"></td>
                                </tr>
                                </tbody>
                            </table>
                            <img src="" id="student_img" width="200px" height="200px">
                        </form><?php endif; ?>

                <!--展示收费标准列表-->
                <?php if(!empty($result_charge_standard)): ?><table class="table" id="chargeList">
                        <thead>
                        <th>收费项目</th>
                        <th>创建人</th>
                        </thead>
                        <tbody>
                        <?php if(is_array($result_charge_standard)): $i = 0; $__LIST__ = $result_charge_standard;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                <td><a href="<?php echo U('admin/charge/reduceApply',array('charge_standard_name'=>$item['charge_standard_name']));?>"><?php echo ($item['charge_standard_name']); ?></a></td>
                                <td><?php echo ($item['teacher_name']); ?></td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table><?php endif; ?>

            </div>
        </div>
    </div>


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

        function checkSaveReduceApply(){
            //照片大小不能超过300k
        }
        function getStudentInformation1(){
            $("#tableid  tr").html("");
            var data = {'student_grade':$("#student_grade1").val(),'student_class':$("#student_class1").val(),'student_id':$("#student_id2").val()}
            var url ="/admin/" + "student-getstudentinformation.html"
            $.getJSON(url,data,function(response){
                var Thead = $("<thead><th>学号</th><th>姓名</th><th>民族</th><th>性别</th>" +
                "<th>出生年月</th><th>家庭住址</th><th>年级</th><th>班级</th></thead>")
                Thead.appendTo($("#tableid"))
                var Tbody = $("<tbody></tbody>")
                Tbody.appendTo($("#tableid"))
                for(student in response){
                    var Tr = $("<tr><td><a class='student_id' style='cursor: pointer'>"+response[student].student_id+"</a></td><td>"+response[student].student_name + "</td><td>"+response[student].student_nation +"</td><td>"+response[student].student_sex +"</td><td>" +response[student].student_birthdate + "</td><td>" +response[student].student_address + "</td><td>" +response[student].student_grade + "</td><td>" +response[student].student_class +"</td></tr>")
                    Tr.appendTo(Tbody)
                }

                $('.student_id').click(function(){
                    var value = $('#student_id').val()
                    console.log(value);

                    var i = $(this).css('color');
                    var o = i.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
                    delete (o[0]);
                    for (var n = 1; n <= 3; ++n) {
                        o[n] = parseInt(o[n]).toString(16);
                        if (o[n].length == 1) o[n] = '0' + o[n];
                    }
                    var s = o.join('');
                    if(s == 'ff0000'){
                        $(this).css('color','#2e97de')
                    }else{
                        $(this).css('color','red')
                    }

                    if(value==''){
                        value = $(this).text()
                    }else{
                        if($(this).text() == value){
                            value = ''
                        }else{
                            value = $(this).text()
                        }
                    }
                    $('#student_id').val(value)
                })
            })
        }

        function saveReduceApply(){
            $("#saveReduceApply").submit()
        }

        $(function(){
            $("#getStudentInformation").click(function(){
                if(!$('#student_id').val()){
                    alert("请输入学号")
                    return
                }
                var data = {"student_id": $('#student_id').val()};
                var url = "/admin/" + "charge-getstudentinformation.html";
                $.getJSON(url,data,function(response){
                    $("#studentInformation").show()
                    $("#student_name")[0].innerHTML = response['student_name']
                    $("#student_sex")[0].innerHTML = response['student_sex']
                    $("#student_id1")[0].innerHTML = response['student_id']
                    $("#student_nation")[0].innerHTML = response['student_nation']
                    $("#student_birthdate")[0].innerHTML = response['student_birthdate']
                    $("#student_native")[0].innerHTML = response['student_native']
                    $("#student_address")[0].innerHTML = response['student_address']
                    $("#student_grade")[0].innerHTML = response['student_grade']
                    $("#student_accommodation")[0].innerHTML = response['student_accommodation']
                    $("#student_meal")[0].innerHTML = response['student_meal']
                    $("#student_insurance")[0].innerHTML = response['student_insurance']
                    $("#student_father")[0].innerHTML = response['student_father']
                    $("#student_father_phone")[0].innerHTML = response['student_father_phone']
                    $("#student_mother")[0].innerHTML = response['student_mother']
                    $("#student_mother_phone")[0].innerHTML = response['student_mother_phone']
                    $("#student_class")[0].innerHTML = response['student_class']
                    $("#student_dorm_number")[0].innerHTML = response['student_dorm_number']
                    $("#student_card")[0].innerHTML = response['student_card']
                    $("#student_status")[0].innerHTML = response['student_status']
                    $("#student_campus")[0].innerHTML = response['student_campus']
                    $("#student_dorm_building")[0].innerHTML = response['student_dorm_building']
                    $("#student_introduce")[0].innerHTML = response['student_introduce']
                    $("#student_register_date")[0].innerHTML = response['student_register_date']
                    $("#student_teacher")[0].innerHTML = response['student_teacher']
                    $("#student_img").prop('src',"/Uploads"+response['student_photo'])

                })
            })
        })
    </script>

</body>
</html>