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
            <div class="platTitle"><h2>学校结构</h2> <a href="<?php echo U('admin/student/indexindex');?>"><img src="/Public/Admin/Images/return.png" style="margin-left: 200px"></a></div>
            <div></div>
            <i class="refresh" onclick="javascript:location.reload(true);" title="刷新当前页面"></i>
            <div class="recordCou">
                <div style="display: inline-block;width: 50px;margin-left: 30px;">
                    <img src="/Public/Admin/Images/studentAdd.png">
                    <input type="button" class="excesssave" value="新增" onclick="addSchoolStructure()">
                </div>

                <div style="display: inline-block;width: 50px;margin-left: 3px;">
                    <img src="/Public/Admin/Images/studentSave.png">
                    <input type="button" class="excesssave" value="保存" onclick="saveSchoolStructure()">
                </div>
                &nbsp; &nbsp; &nbsp;

                <div style="display: inline-block;width: 50px">
                    <form action="<?php echo U('admin/student/querygrade');?>" method="post">
                        <img src="/Public/Admin/Images/studentQuery.png">
                        <input type="submit" class="excesssave" value="查询">
                    </form>
                </div>

                <div style="display: inline-block;width: 50px">
                    <img src="/Public/Admin/Images/studentDelete.png">
                    <input type="submit" class="excesssave" value="删除" onclick="deleteSchoolStructure()">
                </div>

                <div style="margin-left: 30px;margin-top: 30px;display: none" id="radio">
                    <label for="1">校区</label><input type="radio" name="choice" value="campus" id="1" checked="checked">
                    <label for="2" style="margin-left: 20px">年级班级</label><input type="radio" name="choice" value="gradeclass" id="2">
                </div>

                <div>
                    <form id="campus" action="<?php echo U('admin/student/savecampus');?>" method="post" style="display: none" onsubmit="return checkCampus()">
                        <table class="table">
                            <thead>
                            <th>校区名称</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td><input type="text" name="campus_name" id="campus_name"></td>
                            </tr>
                            </tbody>
                        </table>
                    </form>

                    <form id="gradeclass" style="display: none" action="<?php echo U('admin/student/savegrade');?>" method="post" onsubmit="return checkGradeClass()">
                        <table class="table">
                            <thead>
                            <th>校区名称</th>
                            <th>年级</th>
                            <th>班级</th>
                            <th>是否实验班</th>
                            </thead>
                            <tr>
                                <td>
                                    <select name="campus_name">
                                        <?php if(is_array($result_campus)): $i = 0; $__LIST__ = $result_campus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item['campus_name']); ?>"><?php echo ($item['campus_name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="grade">
                                        <option value="小班">小班</option>
                                        <option value="中班">中班</option>
                                        <option value="大班">大班</option>
                                        <option value="一年级">一年级</option>
                                        <option value="二年级">二年级</option>
                                        <option value="三年级">三年级</option>
                                        <option value="四年级">四年级</option>
                                        <option value="五年级">五年级</option>
                                        <option value="六年级">六年级</option>
                                        <option value="七年级">七年级</option>
                                        <option value="八年级">八年级</option>
                                        <option value="九年级">九年级</option>
                                    </select>
                                </td>
                                <td><input type="text" name="class" id="class"></td>
                                <td>
                                    <select name="class_test">
                                        <option value="否">否</option>
                                        <option value="是">是</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>

                <?php if(!empty($result_grade)): ?><table class="table" id="grade_show">
                        <thead>
                        <th><input type="checkbox" id="all"></th>
                        <th>校区</th>
                        <th>年级</th>
                        <th>班级</th>
                        <th>实验班</th>
                        </thead>
                        <tbody>
                        <?php if(is_array($result_grade)): $i = 0; $__LIST__ = $result_grade;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                <td><input type="checkbox" name="choice_item" value="<?php echo ($item["id"]); ?>"></td>
                                <td><?php echo ($item['campus_name']); ?></td>
                                <td><?php echo ($item['grade']); ?></td>
                                <td><?php echo ($item['class']); ?></td>
                                <td><?php echo ($item['class_test']); ?></td>
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
        //新增按钮
        function addSchoolStructure(){
            $("#grade_show").hide()
            $("#radio").show()
            $("#campus").show()
        }
        //保存按钮
        function saveSchoolStructure(){
            var val=$('input:radio[name="choice"]:checked').val();
            if(val == 'campus'){
                $("#campus").submit()
            }
            if(val == 'gradeclass'){
                $("#gradeclass").submit()
            }
        }
        function checkCampus(){
            if($("#campus_name").val()){
                return true
            }else{
                alert("请输入校区名字")
                return false
            }
        }
        function checkGradeClass(){
            if($("#class").val()){
                return true
            }else{
                alert("请输入班级名")
                return false
            }
        }
        function checkAddChargeItem(){
            if($("#charge_item_name").val()){
                return true
            }else{
                alert("请输入收费项目名称")
                return false
            }
        }
        //删除按钮
        function deleteSchoolStructure(){
            var choice_value = []
            $('input[name="choice_item"]:checked').each(function(){
                choice_value.push($(this).val())
            })
            var data = {'grade_id_array':choice_value}
            var  url = "/admin/" + "student-deletegrade.html"
            $.getJSON(url,data,function(response){
                if(response.code==1){
                    alert('删除成功')
                    window.location.reload()//刷新当前页面.
                }else{
                    alert('删除失败');
                }
            })
        }

        $(function(){
            $("#1").click(function(){
                $("#campus").show()
                $("#gradeclass").hide()
            })
            $("#2").click(function(){
                $("#campus").hide()
                $("#gradeclass").show()
            })
            $("#all").click(function(){
                if(this.checked){
                    $("input[name='choice_item']").prop("checked", true);
                }else{
                    $("input[name='choice_item']").removeProp("checked", false);
                }
            });
        })

    </script>

</body>
</html>