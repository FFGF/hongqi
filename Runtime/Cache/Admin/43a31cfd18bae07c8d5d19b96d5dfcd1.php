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

        }
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
            <div class="platTitle"><h2>减免审核</h2><a href="<?php echo U('admin/student/indexindex');?>"><img src="/Public/Admin/Images/return.png" style="margin-left: 200px"></a></div>
            <i class="refresh" onclick="javascript:location.reload(true);" title="刷新当前页面"></i>
            <div class="recordCou">

                <?php if(!empty($charge_standard_name)): ?><h2 style="margin-left: 20px"><?php echo ($charge_standard_name); ?></h2>
                    <form id="examineReduce" method="post">
                        <input type="hidden" name="charge_standard_name" value="<?php echo ($charge_standard_name); ?>">
                        <input type="button" class="excesssave" value="财务人员审核" style="width: 150px;margin-left: 20px" id="financeExamine">
                        <input type="button" class="excesssave" value="校长审核" style="width: 150px" id="schoolmasterExamine">
                        <input type="button" class="excesssave" value="董事长审核" style="width: 150px" id="chairmanExamine">
                    </form>
                    <?php if(!empty($result_reduce_apply)): ?><table class="table">
                            <thead>
                            <th>学号</th>
                            <th>收费项目</th>
                            <th>减免金额</th>
                            <th>描述</th>
                            <th>财务人员</th>
                            <th>校长</th>
                            <th>董事长</th>
                            <th>不通过原因</th>
                            </thead>
                            <tbody>
                            <?php if(is_array($result_reduce_apply)): $i = 0; $__LIST__ = $result_reduce_apply;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                    <td><?php echo ($item['student_id']); ?></td>
                                    <td><?php echo ($item['charge_standard_name']); ?></td>
                                    <td><?php echo ($item['reduce_money']); ?></td>
                                    <td class="content" title="<?php echo ($item['text_description']); ?>"><?php echo ($item['text_description']); ?></td>
                                    <td><?php echo ($item['examine_finance']); ?></td>
                                    <td><?php echo ($item['examine_schoolmaster']); ?></td>
                                    <td><?php echo ($item['examine_chairman']); ?></td>
                                    <td class="content" title="<?php echo ($item['fail_reason']); ?>"><?php echo ($item['fail_reason']); ?></td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table><?php endif; endif; ?>
                <!--财务审核列表-->
                <?php if(!empty($result_examine_finance)): ?><table class="table">
                        <thead>
                        <th>学号</th>
                        <th>申请减免金额</th>
                        <th>困难描述</th>
                        <th>审核状态</th>
                        </thead>
                        <tbody>
                        <?php if(is_array($result_examine_finance)): $i = 0; $__LIST__ = $result_examine_finance;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                <td><a href="<?php echo U('admin/Charge/examineStudent',array('reduce_apply_id'=>$item['id'],'flag'=>'finance'));?>"><?php echo ($item['student_id']); ?></a></td>
                                <td><?php echo ($item['reduce_money']); ?></td>
                                <td><?php echo ($item['text_description']); ?></td>
                                <td><?php echo ($item['examine_finance']); ?></td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table><?php endif; ?>
                <!--校长审核列表-->
                <?php if(!empty($result_examine_schoolmaster)): ?><table class="table">
                        <thead>
                        <th>学号</th>
                        <th>申请减免金额</th>
                        <th>困难描述</th>
                        <th>审核状态</th>
                        </thead>
                        <tbody>
                        <?php if(is_array($result_examine_schoolmaster)): $i = 0; $__LIST__ = $result_examine_schoolmaster;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                <td><a href="<?php echo U('admin/Charge/examineStudent',array('reduce_apply_id'=>$item['id'],'flag'=>'schoolmaster'));?>"><?php echo ($item['student_id']); ?></a></td>
                                <td><?php echo ($item['reduce_money']); ?></td>
                                <td><?php echo ($item['text_description']); ?></td>
                                <td><?php echo ($item['examine_schoolmaster']); ?></td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table><?php endif; ?>
                <!--董事长审核列表-->
                <?php if(!empty($result_examine_chairman)): ?><table class="table">
                        <thead>
                        <th>学号</th>
                        <th>申请减免金额</th>
                        <th>困难描述</th>
                        <th>审核状态</th>
                        </thead>
                        <tbody>
                        <?php if(is_array($result_examine_chairman)): $i = 0; $__LIST__ = $result_examine_chairman;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                <td><a href="<?php echo U('admin/Charge/examineStudent',array('reduce_apply_id'=>$item['id'],'flag'=>'chairman'));?>"><?php echo ($item['student_id']); ?></a></td>
                                <td><?php echo ($item['reduce_money']); ?></td>
                                <td><?php echo ($item['text_description']); ?></td>
                                <td><?php echo ($item['examine_chairman']); ?></td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table><?php endif; ?>

                <!--展示收费标准列表-->
                <?php if(!empty($result_charge_standard)): ?><table class="table" id="chargeList">
                        <thead>
                        <th>收费项目</th>
                        <th>创建人</th>
                        </thead>
                        <tbody>
                        <?php if(is_array($result_charge_standard)): $i = 0; $__LIST__ = $result_charge_standard;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                <td><a href="<?php echo U('admin/charge/examineReduce',array('charge_standard_name'=>$item['charge_standard_name']));?>"><?php echo ($item['charge_standard_name']); ?></a></td>
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
            $(".content").each(function(){
                var maxwidth=10;
                if($(this).text().length>maxwidth){
                    $(this).text($(this).text().substring(0,maxwidth));
                    $(this).html($(this).html()+'…');
                }

            });

            $("#financeExamine").click(function(){
                $("#examineReduce").prop('action',"<?php echo U('admin/charge/getExamineFinance');?>");
                $("#examineReduce").submit()
            })

            $("#schoolmasterExamine").click(function(){
                $("#examineReduce").prop('action',"<?php echo U('admin/charge/getExamineSchoolmaster');?>");
                $("#examineReduce").submit()
            })

            $("#chairmanExamine").click(function(){
                $("#examineReduce").prop('action',"<?php echo U('admin/charge/getExamineChairman');?>");
                $("#examineReduce").submit()
            })

        })
    </script>

</body>
</html>