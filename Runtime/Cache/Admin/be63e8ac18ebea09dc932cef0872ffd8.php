<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>项城市红旗学校数字化校园管理平台</title>
    <link rel="stylesheet" type="text/css" href="/Public/Admin/Css/style.css">
    <script src="/Public/Admin/Js/jquery-1.11.0.min.js"></script>
    <style>/* 资料管理-银行帐户 */

.indexIndex{width: 600px;height: 400px;margin-left: 10%;}
.indexIndex .block{width: 198px;height: 200px;float: left;text-align: center}
/*border: 1px solid indianred;background-color: #ff0000;*/
.indexIndex .block img{margin-top: 20px;width: 50px;}
.indexIndex .block .b{display: block; margin-top: 10px}



.certfBank{ width: 790px; margin-left: 80px; margin-top: 0px; overflow: hidden; margin-bottom: 100px;}
.CBcon{ margin-bottom: 30px;}
.CBcon i{ width: 3px; height: 15px; background: #2e97de; display: inline-block; vertical-align: middle;}
.CBcon span{ width: 75px; margin-left: 15px; font-size: 16px; color: #444; display: inline-block; vertical-align: middle;}
.CBcon input{ width: 200px; height: 35px; border: 1px solid #2e97de; border-radius: 4px; text-indent: 1em; vertical-align: middle; line-height: 35px;}
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
            <div class="platTitle"><h2>财务管理中心</h2></div>
            <div class="recordCou">
                <div class="indexIndex">
                    <div class="block">
                        <a href="<?php echo U('admin/student/indexIndex');?>"><img src="/Public/Admin/Images/toll.png" ></a>
                        <b class="b"><a href="<?php echo U('admin/student/studentFile');?>">收费管理</a></b>
                    </div>

                    <div class="block">
                        <a href=""><img src="/Public/Admin/Images/chargeStandard.png" ></a>
                        <b class="b"><a href="">支出管理</a></b>
                    </div>

                    <div class="block">
                        <a href=''><img src="/Public/Admin/Images/chargeTask.png" ></a>
                        <b class="b"><a href="">工资管理</a></b>
                    </div>

                    <div class="block">
                        <a href=""><img src="/Public/Admin/Images/payment.png" ></a>
                        <b class="b"><a href="">仓库管理</a></b>
                    </div>

                    <div class="block">
                        <a href=""><img src="/Public/Admin/Images/paymentQuery.png" ></a>
                        <b class="b"><a href="">固定资产管理</a></b>
                    </div>
                    <div class="block"></div>
                </div>
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

    <script type="text/javascript" src="/Public/Admin/Js/hongQiRegister.js"></script>
    <script type="text/javascript" src="/Public/Admin/Js/birthday.js"></script>
    <script type="text/javascript">
        $(function(){

        })
    </script>

</body>
</html>