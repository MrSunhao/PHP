<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户注册</title>
    <link rel="stylesheet" href="/sport/Public/bootstrap/css/bootstrap.min.css">
    <script src=" /sport/Public/bootstrap/js/jquery-3.1.1.min.js"></script>
    <script src=" /sport/Public/bootstrap/js/bootstrap.min.js"></script>
    <style>
        body {
            height: 100%;
            width: 100%;
            background-color: #5bc0de;
        }

        h1 {
            text-align: center;
            text-shadow: #0f0f0f;
        }

        .registCon {
            height: 100%;
            width: 100%;
            margin-top: 40px;
        }

        .form-horizontal {
            background-color: gainsboro;
            padding-bottom: 40px;
            border-radius: 15px;
        }

        .form-horizontal .form-group {
            padding: 0 20px;
            margin: 0 0 25px 0;
        }

        #submit {
            margin-left: 20px;
            float: left;
            border-radius: 12px;
        }

        #reset {
            float: right;
            margin-right: 20px;
            border-radius: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="navbar navbar-default " role="navigation">
        <nav class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse"
                            data-target="#example-navbar-collapse">
                        <span class="sr-only">切换导航</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/sport/index.php/Home/Index">首页</a>
                </div>
                <div class="collapse navbar-collapse" id="example-navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="operation"><a href="/sport/index.php/Home/Index/rankList">运动榜</a></li>
                        <li class="operation"><a href="/sport/index.php/Home/Index/dynamic">动态</a></li>
                        <li><a href="/sport/index.php/Home/Index/record">个人记录</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <?php if(isset($_SESSION['user'])): ?><li class="operation"><a
                                    href="/sport/index.php/Home/User/info/id/<?php echo ($_SESSION['user']['user_id']); ?>"><?php echo ($_SESSION['user']['user_number']); ?></a>
                            </li>
                            <li class="operation"><a href="/sport/index.php/Home/User/logout"><span
                                    class="glyphicon glyphicon-log-out">注销</span></a></li>
                            <?php else: ?>
                            <li class="active"><a href="/sport/index.php/Home/Regist"><span
                                    class="glyphicon glyphicon-user"></span> 注册</a></li>
                            <li><a href="/sport/index.php/Home/Login"><span
                                    class="glyphicon glyphicon-log-in"></span> 登录</a></li>
                            <!--<li class="operation"><a href="#">个人信息</a></li>--><?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="registCon">
        <div class="container">
            <div class="row">
                <div class="col-md-offset-4 col-md-4">
                    <form class="form-horizontal" action="/sport/index.php/Home/Regist/register"
                          method="post" onsubmit="return checkInfo()">
                        <h1><strong>用户注册</strong></h1>
                        <div class="form-group" style="padding-top:10px; ">
                            <input type="text" placeholder="学号：如 14100101230" required class="form-control"
                                   name="user_number" id="number">
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="姓名：如 孙杨" required class="form-control" name="user_name">
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="班级：计科1402（卓越）" required class="form-control"
                                   name="user_class">
                        </div>
                        <div class="form-group">
                            <label for="genderMen" class="checkbox-inline">
                                <input type="radio" value="0" required id="genderMen" name="user_gender">男
                            </label>

                            <label for="genderWomen" class="checkbox-inline">
                                <input type="radio" value="1" required id="genderWomen" name="user_gender">女
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="slecteGrade">选择列表</label>
                            <select class="form-control" name="user_grade" id="slecteGrade">
                                <option value="2012">2012级</option>
                                <option value="2013">2013级</option>
                                <option value="2014" selected="selected">2014级</option>
                                <option value="2015">2015级</option>
                                <option value="2016">2016级</option>
                                <option value="2017">2017级</option>
                                <option value="2018">2018级</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="password" placeholder="输入密码 长度为6-16位" required class="form-control"
                                   name="user_password" id="pwd">
                        </div>
                        <div class="form-group">
                            <input type="password" placeholder="确认密码 长度为6-16位" required class="form-control"
                                   name="user_password1" id="pwd1">
                        </div>
                        <input type="submit" class="btn btn-default" value="提交" name="submit" id="submit">
                        <input type="reset" class="btn btn-default" value="重置" name="reset" id="reset">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function checkInfo() {
        var number = $('#number').val();
        var pwd = $('#pwd').val();
        var pwd1 = $('#pwd1').val();

        var reg = /^\d+$/;
        var len = number.length;
        if(!reg.test(number)){
            alert('学号必须是6-11位数字组合哦！');
            return false;
        }
        if(len<6||len>11){
            alert('学号必须是6-11位数字组合哦！');
            return false;
        }
        if(pwd.length < 6 || pwd.length>16){
            alert('密码必须是6-16位！');
            return false;
        }
        if(pwd1.length<6 || pwd1.length>16){
            alert('确认密码必须是6-16位！');
            return false;
        }
        if(pwd!== pwd1){
            alert('密码不一致！');
            return false;
        }
        return true;
    }
    
</script>
</body>
</html>