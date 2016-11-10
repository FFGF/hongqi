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
            <div class="platTitle"><h2>退费办理</h2><a href="<?php echo U('admin/student/indexindex');?>"><img src="/Public/Admin/Images/return.png" style="margin-left: 200px"></a></div>
            <i class="refresh" onclick="javascript:location.reload(true);" title="刷新当前页面"></i>
            <div class="recordCou">
                <div style="display: inline-block;width: 50px;margin-left: 3px;">
                    <img src="/Public/Admin/Images/studentSave.png">
                    <input type="button" class="excesssave" value="保存" onclick="saveReturnMoney()">
                </div>

                <!--退费搜索窗口-->
                <?php if(!empty($charge_standard_name)): ?><!--<form action="<?php echo U('admin/charge/getStudentStatus');?>" method="get">-->

                        <div class="selectList">
                            <?php echo ($charge_standard_name); ?><input type="hidden" name="charge_standard_name" value="<?php echo ($charge_standard_name); ?>" id="charge_standard_name">
                            年级<select name="student_grade" class="grade" id="student_grade"></select>
                            班级<select name="student_class" class="class" id="student_class"></select>
                            <input type="text" name="student_id" placeholder="学号,姓名" id="student_id"><b></b>
                            <input  class="excesssave" value="搜索" onclick="getStudentInformation()"></li>
                        </div>
                        <form action="" method="get">
                            <table class="table" id="tableid">
                            </table>
                        </form>

                    <?php if(!empty($result_charge_task)): ?><h2><?php echo ($result_charge_task['student_name']); ?></h2>
                        <form action="<?php echo U('admin/charge/saveReturnMoney');?>" method="post" id="saveReturnMoney">
                            <input type="hidden" name="charge_standard_name" value="<?php echo ($charge_standard_name); ?>">
                            <input type="hidden" name="student_id" value="<?php echo ($result_charge_task['student_id']); ?>">
                            <input type="hidden" name="charge_task_id" value="<?php echo ($result_charge_task['id']); ?>">
                            <table class="table">
                                <thead>
                                <th width="16%">项目</th>
                                <th width="16%">应交</th>
                                <th width="16%">减免</th>
                                <th width="16%">实交</th>
                                <th width="16%">退费</th>
                                </thead>
                                <tbody>
                                <?php if(!is_null($result_charge_task['student_tuition'])): ?><tr>
                                        <td>学费</td>
                                        <td><?php echo ($result_charge_task['student_tuition']); ?></td>
                                        <td><?php echo ($result_charge_item_reduce['sum_student_tuition']); ?></td>
                                        <td><?php echo ($result_charge_item_receive['sum_student_tuition']); ?></td>
                                        <td><input name="student_tuition"></td>
                                    </tr><?php endif; ?>
                                <?php if(!is_null($result_charge_task['student_data'])): ?><tr>
                                        <td>资料费</td>
                                        <td><?php echo ($result_charge_task['student_data']); ?></td>
                                        <td><?php echo ($result_charge_item_reduce['sum_student_data']); ?></td>
                                        <td><?php echo ($result_charge_item_receive['sum_student_data']); ?></td>
                                        <td><input name="student_data"></td>
                                    </tr><?php endif; ?>

                                <?php if(!is_null($result_charge_task['student_accommodation'])): ?><tr>
                                        <td>住宿费</td>
                                        <td><?php echo ($result_charge_task['student_accommodation']); ?></td>
                                        <td><?php echo ($result_charge_item_reduce['sum_student_accommodation']); ?></td>
                                        <td><?php echo ($result_charge_item_receive['sum_student_accommodation']); ?></td>
                                        <td><input name="student_accommodation"></td>
                                    </tr><?php endif; ?>

                                <?php if(!is_null($result_charge_task['student_meal'])): ?><tr>
                                        <td>包餐费</td>
                                        <td><?php echo ($result_charge_task['student_meal']); ?></td>
                                        <td><?php echo ($result_charge_item_reduce['sum_student_meal']); ?></td>
                                        <td><?php echo ($result_charge_item_receive['sum_student_meal']); ?></td>
                                        <td><input name="student_meal"></td>
                                    </tr><?php endif; ?>

                                <?php if(!is_null($result_charge_task['student_insurance'])): ?><tr>
                                        <td>保险费</td>
                                        <td><?php echo ($result_charge_task['student_insurance']); ?></td>
                                        <td><?php echo ($result_charge_item_reduce['sum_student_insurance']); ?></td>
                                        <td><?php echo ($result_charge_item_receive['sum_student_insurance']); ?></td>
                                        <td><input name="student_insurance"></td>
                                    </tr><?php endif; ?>
                                </tbody>
                            </table>
                        </form>
                        <br/>
                        <table class="table">
                            <thead>
                            <th>类型</th>
                            <th>支付方式</th>
                            <th>付费项目</th>
                            <th>金额</th>
                            <th>操作员</th>
                            <th>日期</th>
                            </thead>
                            <?php if(is_array($result_receive_item)): $i = 0; $__LIST__ = $result_receive_item;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                    <td><?php echo ($item['type']); ?></td>
                                    <td><?php echo ($item['payment_type']); ?></td>
                                    <td><?php echo (formatreceiveitem($item)); ?></td>
                                    <td><?php echo (getreceiveitem($item)); ?></td>
                                    <td><?php echo ($item['teacher_name']); ?></td>
                                    <td><?php echo ($item['create_time']); ?></td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </table><?php endif; endif; ?>
                <!--展示收费标准列表-->
                <?php if(!empty($result_charge_standard)): ?><table class="table" id="chargeList">
                        <thead>
                        <th>收费项目</th>
                        <th>创建人</th>
                        </thead>
                        <tbody>
                        <?php if(is_array($result_charge_standard)): $i = 0; $__LIST__ = $result_charge_standard;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                <td><a href="<?php echo U('admin/charge/returnMoney',array('charge_standard_name'=>$item['charge_standard_name']));?>"><?php echo ($item['charge_standard_name']); ?></a></td>
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
        function saveReturnMoney(){
            $("#saveReturnMoney").submit()
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
                    var Tr = $("<tr><td><a href='/admin/charge-getstudentstatus-charge_standard_name-"+charge_standard_name+"-student_id-"+response[student].student_id+".html'>"+response[student].student_id+"</a></td><td>"+response[student].student_name + "</td><td>"+response[student].student_nation +"</td><td>"+response[student].student_sex +"</td><td>" +response[student].student_birthdate + "</td><td>" +response[student].student_address + "</td><td>" +response[student].student_grade + "</td><td>" +response[student].student_class +"</td></tr>")
                    Tr.appendTo(Tbody)
                }
            })
        }

    </script>

</body>
</html>