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
            <div class="platTitle"><h2>统计分析</h2><a href="<?php echo U('admin/student/indexindex');?>"><img src="/Public/Admin/Images/return.png" style="margin-left: 200px"></a></div>
            <i class="refresh" onclick="javascript:location.reload(true);" title="刷新当前页面"></i>
            <div class="recordCou">
                <!--统计分析窗口-->
                <?php if(!empty($charge_standard_name)): ?><form action="<?php echo U('admin/charge/countAnalyseQuery');?>" method="post">

                        <input type="hide" name="s_time" style="width:0px;"><input type="hide" name="e_time" style="width:0px;">
                        <div class="changeDate" style="margin-left: 200px;margin-bottom: 10px;">
                            <div class="input-parent input-container"><input type="text" id="s_time" data-beatpicker="true" data-beatpicker-module="clear" placeholder="收费开始时间" class="beatpicker-input beatpicker-inputnode" readonly="readonly" data-beatpicker-id="beatpicker-0"></div>
                            <div class="input-parent input-container"><input type="text" id="e_time" data-beatpicker="true" data-beatpicker-module="clear" placeholder="收费结束时间" class="beatpicker-input beatpicker-inputnode" readonly="readonly" data-beatpicker-id="beatpicker-1"></div>
                        </div>


                        <h2><?php echo ($charge_standard_name); ?></h2>
                        <input type="hidden" value="<?php echo ($charge_standard_name); ?>" name="charge_standard_name">
                        <div class="selectList">
                            年级<select name="student_grade" class="grade"></select>
                            班级<select name="student_class" class="class"></select>
                            类型<select name="type">
                                    <option value="--">--</option>
                                    <option value="收费">收费</option>
                                    <option value="减免">减免</option>
                                    <option value="退费">退费</option>
                                </select>
                            <input type="text" name="student_id" placeholder="学号">
                            <input type="submit" value="查询" onclick="javascript:format_changeDate('s_time','e_time');" style="background-color: #1a9aec;cursor: pointer;border-radius: 5px;">
                        </div>
                    </form>
                    <?php if(!empty($resultCountAnalyseQuery)): ?><table class="table" style="margin-top: 20px">
                            <thead>
                                <th>学号</th>
                                <th>类型</th>
                                <?php if(!is_null($sum_receive_item[0]['sum_student_tuition'])): ?><th>学费</th><?php endif; ?>
                                <?php if(!is_null($sum_receive_item[0]['sum_student_data'])): ?><th>资料费</th><?php endif; ?>
                                <?php if(!is_null($sum_receive_item[0]['sum_student_accommodation'])): ?><th>住宿费</th><?php endif; ?>
                                <?php if(!is_null($sum_receive_item[0]['sum_student_meal'])): ?><th>包餐费</th><?php endif; ?>
                                <?php if(!is_null($sum_receive_item[0]['sum_student_insurance'])): ?><th>保险费</th><?php endif; ?>
                            </thead>
                                <tr style="font-size: 17px">
                                    <td style="background: blue" colspan="2">总和</td>
                                    <?php if(!is_null($sum_receive_item[0]['sum_student_tuition'])): ?><td style="background: blue"><?php echo ($sum_receive_item[0]['sum_student_tuition']); ?></td><?php endif; ?>
                                    <?php if(!is_null($sum_receive_item[0]['sum_student_data'])): ?><td style="background: blue"><?php echo ($sum_receive_item[0]['sum_student_data']); ?></td><?php endif; ?>
                                    <?php if(!is_null($sum_receive_item[0]['sum_student_accommodation'])): ?><td style="background: blue"><?php echo ($sum_receive_item[0]['sum_student_accommodation']); ?></td><?php endif; ?>
                                    <?php if(!is_null($sum_receive_item[0]['sum_student_meal'])): ?><td style="background: blue"><?php echo ($sum_receive_item[0]['sum_student_meal']); ?></td><?php endif; ?>
                                    <?php if(!is_null($sum_receive_item[0]['sum_student_insurance'])): ?><td style="background: blue"><?php echo ($sum_receive_item[0]['sum_student_insurance']); ?></td><?php endif; ?>
                                </tr>
                                <?php if(is_array($resultCountAnalyseQuery)): $i = 0; $__LIST__ = $resultCountAnalyseQuery;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                        <td><?php echo ($item['student_id']); ?></td>
                                        <td><?php echo ($item['type']); ?></td>

                                        <?php if(!is_null($sum_receive_item[0]['sum_student_tuition'])): ?><td><?php echo ($item['student_tuition']); ?></td><?php endif; ?>
                                        <?php if(!is_null($sum_receive_item[0]['sum_student_data'])): ?><td><?php echo ($item['student_data']); ?></td><?php endif; ?>
                                        <?php if(!is_null($sum_receive_item[0]['sum_student_accommodation'])): ?><td><?php echo ($item['student_accommodation']); ?></td><?php endif; ?>
                                        <?php if(!is_null($sum_receive_item[0]['sum_student_meal'])): ?><td><?php echo ($item['student_meal']); ?></td><?php endif; ?>
                                        <?php if(!is_null($sum_receive_item[0]['sum_student_insurance'])): ?><td><?php echo ($item['student_insurance']); ?></td><?php endif; ?>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            <tbody>
                            </tbody>
                        </table><?php endif; endif; ?>

                <!--展示收费标准列表-->
                <?php if(!empty($result_charge_standard)): ?><table class="table" id="chargeList">
                        <thead>
                        <th>收费项目</th>
                        <th>创建人</th>
                        </thead>
                        <tbody>
                        <?php if(is_array($result_charge_standard)): $i = 0; $__LIST__ = $result_charge_standard;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                <td><a href="<?php echo U('admin/charge/countAnalyse',array('charge_standard_name'=>$item['charge_standard_name']));?>"><?php echo ($item['charge_standard_name']); ?></a></td>
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

        $(function(){

        })
    </script>

</body>
</html>