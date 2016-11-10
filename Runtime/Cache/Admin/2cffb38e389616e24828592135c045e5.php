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
    <script src="/Public/Admin/Js/birthday.js"></script>
    <script src="/Public/Admin/Js/hongQi.js"></script>
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
            <div class="platTitle"><h2>减免费办理</h2><a href="<?php echo U('admin/student/indexindex');?>"><img src="/Public/Admin/Images/return.png" style="margin-left: 200px"></a></div>
            <i class="refresh" onclick="javascript:location.reload(true);" title="刷新当前页面"></i>
            <div class="recordCou">
                <div style="display: inline-block;width: 50px;margin-left: 3px;">
                    <img src="/Public/Admin/Images/studentSave.png">
                    <input type="button" class="excesssave" value="保存" onclick="saveReduceTuition()">
                </div>
                <!--批量减免操作-->
                <?php if(!empty($charge_standard_name)): ?><h2 style="margin-left: 20px"><?php echo ($charge_standard_name); ?></h2>
                    <div style="margin-left: 20px;margin-top: 20px;font-size: 15px;">
                    <label for="1">成绩批量减免</label><input type="radio" name="reduceTuition" value="score" id="1">
                    <label for="2" style="margin-left: 10px;">少数名族批量减免</label><input type="radio" name="reduceTuition" value="nation" id="2">
                    <label for="3" style="margin-left: 10px;">教师子女批量减免</label><input type="radio" name="reduceTuition" id="3">
                    <label for="4" style="margin-left: 10px;">手动减免</label><input type="radio" name="reduceTuition" id="4">
                    </div>
                    <form id="form1" method="post">
                        <input type="hidden" value="<?php echo ($charge_standard_name); ?>" name="charge_standard_name">
                        <table class="table" style="margin-top: 20px;display: none" id="table1" >
                            <thead>
                            <th id="th">成绩批量减免</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td><input name="reduce_money_batch" id="reduce_money_batch"></td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="table" style="margin-top: 20px;display: none" id="table2">
                            <thead>
                            <th width="30%">减免类型</th>
                            <th width="30%">学号</th>
                            <th width="30%">金额</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <select name="type">
                                        <option value="成绩">成绩</option>
                                        <option value="少数名族">少数名族</option>
                                        <option value="教师子女">教师子女</option>
                                    </select>
                                </td>
                                <td><input name="student_id1" id="student_id1" type="text" value=""></td>
                                <td><input name="reduce_money_hand" type="text"></td>
                            </tr>
                            </tbody>
                        </table>

                        <div class="selectList" style="margin-top: 20px;display: none" id="selectList">
                            <?php echo ($charge_standard_name); ?><input type="hidden" name="charge_standard_name" value="<?php echo ($charge_standard_name); ?>" id="charge_standard_name">
                            年级<select name="student_grade" class="grade" id="student_grade"></select>
                            班级<select name="student_class" class="class" id="student_class"></select>
                            <input type="text" name="student_id" placeholder="学号,姓名" id="student_id"><b></b>
                            <input  class="excesssave" value="搜索" onclick="getStudentInformation()"></li>

                        </div>

                        <table class="table" id="tableid">
                        </table>
                    </form><?php endif; ?>
                <!--展示收费标准列表-->
                <?php if(!empty($result_charge_standard)): ?><table class="table" id="chargeList">
                        <thead>
                        <th>收费项目</th>
                        <th>创建人</th>
                        </thead>
                        <tbody>
                        <?php if(is_array($result_charge_standard)): $i = 0; $__LIST__ = $result_charge_standard;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                <td><a href="<?php echo U('admin/charge/reduceTuition',array('charge_standard_name'=>$item['charge_standard_name']));?>"><?php echo ($item['charge_standard_name']); ?></a></td>
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
        function saveReduceTuition(){
            $("#form1").submit()
        }

        function getStudentInformation(){
            $("#student_charge_task").hide()
            $("#tableid  tr").html("");
            var charge_standard_name= $("#charge_standard_name").val()
            var data = {'student_grade':$("#student_grade").val(),'student_class':$("#student_class").val(),'student_id':$("#student_id").val()}
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
                    var value = $('#student_id1').val()
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
                        ss=value.split(",")
                        index=$.inArray($(this).text(),ss)
                        if(index==-1){
                            value = value + ',' + $(this).text()
                        }else{
                            ss.splice(index,1);
                            value=ss.join(",")
                        }
                    }
                    $('#student_id1').val(value)
                })
            })
        }

        $(function(){
            $("#1").click(function(){
                $("#table1").show()
                $("#table2").hide()
                $("#th")[0].innerHTML = "成绩批量减免"
            })

            $("#2").click(function(){
                $("#table1").show()
                $("#table2").hide()
                $("#th")[0].innerHTML = "少数名族批量减免"
                $("#form1").prop('action',"<?php echo U('admin/charge/reduceTuitionNation');?>")
            })

            $("#3").click(function(){
                $("#table1").show()
                $("#table2").hide()
                $("#th")[0].innerHTML = "教师子女批量减免"
                $("#form1").prop('action',"<?php echo U('admin/charge/reduceTuitionTeacher');?>")
            })

            $("#4").click(function(){
                $("#table2").show()
                $("#selectList").show()
                $("#table1").hide()
                $("#form1").prop('action',"<?php echo U('admin/charge/reduceTuitionHand');?>")
            })
        })
    </script>

</body>
</html>