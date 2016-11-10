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
            <div class="platTitle"><h2>新建任务</h2><a href="<?php echo U('admin/student/indexindex');?>"><img src="/Public/Admin/Images/return.png" style="margin-left: 200px"></a></div>
            <i class="refresh" onclick="javascript:location.reload(true);" title="刷新当前页面"></i>
            <div class="recordCou">
                <div style="display: inline-block;width: 50px;margin-left: 30px;">
                    <img src="/Public/Admin/Images/studentAdd.png">
                    <input type="button" class="excesssave" value="新增" onclick="addChargeTask()">
                </div>

                <div style="display: inline-block;width: 50px;margin-left: 3px;">
                    <img src="/Public/Admin/Images/studentSave.png">
                    <input type="button" class="excesssave" value="保存" onclick="saveChargeTask()">
                </div>
                &nbsp; &nbsp; &nbsp;
                <div style="display: inline-block;width: 50px">
                    <img src="/Public/Admin/Images/studentDelete.png">
                    <input type="submit" class="excesssave" value="删除" onclick="deleteChargeStandard()">
                </div>
                &nbsp; &nbsp; &nbsp;

                <div style="display: inline-block;width: 50px">
                    <img src="/Public/Admin/Images/studentExport.png">
                    <form action="<?php echo U('admin/student/exportStudent');?>" style="display: inline-block;" method="post" id="student_export">
                        <input type="hidden" id="student_id_array" name="student_id_array">
                        <input type="button" class="excesssave" value="导出" onclick="exported()">
                    </form>
                </div>

                <!--展示收费标准列表-->
                <?php if(!empty($result_charge_standard)): ?><table class="table">
                        <thead>
                            <th><input type="checkbox" id="all"></th>
                            <th>收费项目</th>
                            <th>创建人</th>
                        </thead>
                        <tbody>
                            <?php if(is_array($result_charge_standard)): $i = 0; $__LIST__ = $result_charge_standard;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                    <td><input type="checkbox" name="choice_item" value="<?php echo ($item["charge_standard_name"]); ?>"></td>
                                    <td><a href="<?php echo U('admin/charge/getChargeStandard',array('charge_standard_name'=>$item['charge_standard_name']));?>"><?php echo ($item['charge_standard_name']); ?></a></td>
                                    <td><?php echo ($item['teacher_name']); ?></td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table><?php endif; ?>
                <!--展示某一次的收费标准-->
                <?php if(!empty($result_charge_standard_some)): ?><table class="table">
                        <thead>
                        <th>收费项目</th>
                        <th>年级</th>
                        <th>班级类别</th>
                        <?php if(!is_null($result_charge_standard_some[0]['student_tuition'])): ?><th>学费</th><?php endif; ?>
                        <?php if(!is_null($result_charge_standard_some[0]['student_accommodation'])): ?><th>住宿费</th><?php endif; ?>
                        <?php if(!is_null($result_charge_standard_some[0]['student_data'])): ?><th>资料费</th><?php endif; ?> <?php if(!is_null($result_charge_standard_some[0]['student_meal'])): ?><th>包餐费</th><?php endif; ?>
                        <?php if(!is_null($result_charge_standard_some[0]['student_insurance'])): ?><th>保险费</th><?php endif; ?>
                        </thead>
                        <tbody>
                        <?php if(is_array($result_charge_standard_some)): $i = 0; $__LIST__ = $result_charge_standard_some;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                <td><?php echo ($item['charge_standard_name']); ?></td>
                                <td><?php echo ($item['student_grade']); ?></td>
                                <td><?php echo (formatclasstest($item['student_test'])); ?></td>

                                <?php if(!is_null($item['student_tuition'])): ?><td><?php echo ($item['student_tuition']); ?></td><?php endif; ?>

                                <?php if(!is_null($item['student_accommodation'])): ?><td><?php echo ($item['student_accommodation']); ?></td><?php endif; ?>

                                <?php if(!is_null($item['student_data'])): ?><td><?php echo ($item['student_data']); ?></td><?php endif; ?>

                                <?php if(!is_null($item['student_meal'])): ?><td><?php echo ($item['student_meal']); ?></td><?php endif; ?>

                                <?php if(!is_null($item['student_insurance'])): ?><td><?php echo ($item['student_insurance']); ?></td><?php endif; ?>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table><?php endif; ?>

                <!--设定收费标准-->
                <?php if(!empty($charge_item)): ?><form action="<?php echo U('admin/charge/saveChargeStandard');?>" method="post" id="chargeStand">
                        <table class="table">
                            <thead>
                            <th>收费项目</th>
                            <th>年级</th>
                            <th>班级类别</th>
                            <?php if(in_array('student_tuition',$charge_item['choice'])): ?><th>学费</th><?php endif; ?>
                            <?php if(in_array('student_accommodation',$charge_item['choice'])): ?><th>住宿费</th><?php endif; ?>
                            <?php if(in_array('student_data',$charge_item['choice'])): ?><th>资料费</th><?php endif; ?>
                            <?php if(in_array('student_meal',$charge_item['choice'])): ?><th>包餐费</th><?php endif; ?>
                            <?php if(in_array('student_insurance',$charge_item['choice'])): ?><th>保险费</th><?php endif; ?>
                            </thead>
                            <tbody>
                            <?php if(is_array($gradeTest)): $i = 0; $__LIST__ = $gradeTest;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                    <td><?php echo ($charge_item['charge_name']); ?></td>
                                    <input name="charge_standard_name<?php echo ($item['id']); ?>" type="hidden" value="<?php echo ($charge_item['charge_name']); ?>"/>
                                    <td><?php echo ($item['grade']); ?></td>
                                    <input name="student_grade<?php echo ($item['id']); ?>" type="hidden" value="<?php echo ($item['grade']); ?>"/>
                                    <td><?php echo (formatclasstest($item['class_test'])); ?></td>
                                    <input name="student_test<?php echo ($item['id']); ?>" type="hidden" value="<?php echo ($item['class_test']); ?>"/>

                                    <?php if(in_array('student_tuition',$charge_item['choice'])): ?><td><input style="width: 50px;" name="student_tuition<?php echo ($item['id']); ?>"></td><?php endif; ?>
                                    <?php if(in_array('student_accommodation',$charge_item['choice'])): ?><td><input style="width: 50px;"  name="student_accommodation<?php echo ($item['id']); ?>"></td><?php endif; ?>
                                    <?php if(in_array('student_data',$charge_item['choice'])): ?><td><input style="width: 50px;"  name="student_data<?php echo ($item['id']); ?>"></td><?php endif; ?>
                                    <?php if(in_array('student_meal',$charge_item['choice'])): ?><td><input style="width: 50px;"  name="student_meal<?php echo ($item['id']); ?>"></td><?php endif; ?>
                                    <?php if(in_array('student_insurance',$charge_item['choice'])): ?><td><input style="width: 50px;"  name="student_insurance<?php echo ($item['id']); ?>"></td><?php endif; ?>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                    </form><?php endif; ?>




            </div>
        </div>
    </div>

    <div class="auditingFrame">
        <div class="aFtitle">选择收费项目</div>
        <div class="aFcona">
            <form action="<?php echo U('admin/charge/createchargetask');?>" method="post" onsubmit="return checkChargeTask()">
                <table class="table" cellspacing="0" cellpadding="0" style="width: 100%;padding-top: 10px">
                    <thead>
                        <th width="30%">收费名称</th>
                        <th width="70%" class="lan">收费项目</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input name="charge_name" style="width: 150px" id="charge_name"></td>
                            <td>
                                <label for="1">学费</label><input type="checkbox" name="choice[]" value="student_tuition" id="1" style="width: 40px;margin-left: -10px;">
                                <label for="2">住宿费</label><input type="checkbox" name="choice[]" value="student_accommodation" id="2" style="width: 40px;margin-left: -10px;">
                                <label for="3">资料费</label><input type="checkbox" name="choice[]" value="student_data" id="3" style="width: 40px;margin-left: -10px;">
                                <label for="4">保险费</label><input type="checkbox" name="choice[]" value="student_insurance" id="4" style="width: 40px;margin-left: -10px;">
                                <label for="5">包餐费</label><input type="checkbox" name="choice[]" value="student_meal" id="5" style="width: 40px;margin-left: -10px;">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input  type="submit"  value="新建" class="adopt" style="margin-left: 280px">
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
        function addChargeTask(){
            $(".info_bg").show();
            $(".auditingFrame").show();
            $(".auditingFrame").css({
                top: function(index, value) {
                    return $(window).scrollTop() + ($(window).height()/2);
                }
            });
        }
        function checkChargeTask(){
            if($("#charge_name").val()){
                if(!$("#1").is(':checked')&&!$("#2").is(':checked')&&!$("#3").is(':checked')&&!$("#4").is(':checked')&&!$("#5").is(':checked')){
                    alert("请至少选择一种收费项目")
                    return false
                }else{
                    return true
                }
            }else{
                alert("请输入收费项目名称")
                return false
            }

        }
        //保存按钮
        function saveChargeTask(){
            $("#chargeStand").submit()
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

        //删除按钮
        function deleteChargeStandard(){
            var choice_value = []
            $('input[name="choice_item"]:checked').each(function(){
                choice_value.push($(this).val())
            })

            var data = {'charge_standard_name_array':choice_value}
            var  url = "/admin/" + "charge-deletechargetask.html"
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


        $(function(){

            $("#all").click(function(){
                if(this.checked){
                    $("input[name='choice_item']").prop("checked", true);
                }else{
                    $("input[name='choice_item']").prop("checked", false);
                }
            });

        })
    </script>

</body>
</html>