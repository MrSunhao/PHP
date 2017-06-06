<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户登录</title>
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

        .loginCon {
            padding: 40px;
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
                            <li class="operation"><a href="/sport/index.php/Home/User/logout">注销</a></li>
                            <?php else: ?>
                            <li><a href="/sport/index.php/Home/Regist"><span
                                    class="glyphicon glyphicon-user"></span> 注册</a></li>
                            <li class="active"><a href="/sport/index.php/Home/Login"><span
                                    class="glyphicon glyphicon-log-in"></span> 登录</a></li>
                            <!--<li class="operation"><a href="#">个人信息</a></li>--><?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>
    <div class="loginCon">
        <div class="row">
            <div class="col-md-offset-4 col-md-4">
                <form class="form-horizontal" action="/sport/index.php/Home/Login/login"
                      method="post">
                    <h1><strong>用户登录</strong></h1>
                    <div class="form-group">
                        <span class="glyphicon glyphicon-user"></span>
                        <input type="text" class="form-control" placeholder="请输入账号" required name="user_number">
                    </div>
                    <div class="form-group">
                        <span id="" class="glyphicon glyphicon-lock"></span>
                        <input type="password" class="form-control" placeholder="请输入密码" required name="user_password">
                    </div>
                    <!--<div class="form-group">-->
                        <!--<label for="writePwd" class="checkbox-inline">-->
                            <!--<input type="checkbox" value="1" id="writePwd" name="user_write">记住密码-->
                        <!--</label>-->
                    <!--</div>-->
                    <input type="submit" class="btn btn-default" value="登录" name="submit" id="submit">
                    <input type="reset" class="btn btn-default" value="重置" name="reset" id="reset">
                </form>
            </div>
        </div>
    </div>
</body>
</html>