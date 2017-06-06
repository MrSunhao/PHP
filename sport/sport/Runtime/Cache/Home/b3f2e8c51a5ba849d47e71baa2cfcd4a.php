<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>动态</title>
    <link rel="stylesheet" href="/sport/Public/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/sport/Public/bootstrap/css/style.css">
    <script src=" /sport/Public/bootstrap/js/jquery-3.1.1.min.js"></script>
    <script src=" /sport/Public/bootstrap/js/bootstrap.min.js"></script>
    <script src=" /sport/Public/bootstrap/js/jquery.scrollToTop.min.js"></script>

    <!--<link rel="stylesheet" href="css/bootstrap.css">  -->
    <!--<script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>-->
    <!--<script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->

    <style type="text/css">
        body {
            background-color: rgba(186, 168, 255, 0.46);
        }

        #posted {
            position: fixed;
            right: 35px;
            bottom: 50%;
        }

        .fp{
            text-align: center;
        }
    </style>
</head>
<body>
<!--导航栏-->
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
                    <a href="/sport/index.php/Home/Index" class="navbar-brand" >首页</a>
                </div>
                <div class="collapse navbar-collapse" id="example-navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="operation"><a href="/sport/index.php/Home/Index/rankList">运动榜</a></li>
                        <li class="active"><a href="/sport/index.php/Home/Index/dynamic">动态</a></li>
                        <li><a href="/sport/index.php/Home/Index/record">个人记录</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <?php if(isset($_SESSION['user'])): ?><li class="operation"><a
                                    href="/sport/index.php/Home/User/info/id/<?php echo ($_SESSION['user']['user_id']); ?>"><?php echo ($_SESSION['user']['user_number']); ?></a>
                            </li>
                            <li class="operation"><a href="/sport/index.php/Home/User/logout"><span
                                    class="glyphicon glyphicon-log-out">注销</span></a></li>
                            <?php else: ?>
                            <li><a href="/sport/index.php/Home/Regist"><span
                                    class="glyphicon glyphicon-user"></span> 注册</a></li>
                            <li><a href="/sport/index.php/Home/Login"><span
                                    class="glyphicon glyphicon-log-in"></span> 登录</a></li><?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <!-- 动态 -->
    <a href="#Top" id="toTop"><img src="/sport/Public/bootstrap/images/37bOOOPIC23_202.jpg" alt="" height="64px" width="64px"></a>
    <div class="container desc" id="dyna_con">
        <!--内容展示-->
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="nodata" style="text-align: center">

                </div>
            </div>
        </div>
    </div>
</div>
<div id="posted">
    <a href="/sport/index.php/Home/Index/toPosted">
        <button type="button" class="btn btn-primary btn-lg"
                style="height:68px; width:68px; border-radius: 50%;text-shadow: black 5px 3px 3px;"><span
                class="glyphicon glyphicon-plus" style="color: rgb(15, 222, 255); font-size: 36px;"></span></button>
    </a>
</div>
<footer>
<div id="footer" class="container">
    <nav class="navbar navbar-default">
            <div class="container">
                <div class="row" style="margin-top: 10px;font-size: 10px;">
                    <div class="col-md-4">
                        <p class="fp">计算机学院兴趣小组出品</p>
                    </div>
                    <div class="col-md-4">
                        <p class="fp">如果您有更好的建议发送邮件至 <a href="javascript:;" style="font-size: 12px">sun668vip@163.com</a></p>
                    </div>
                    <div class="col-md-4">
                        <p class="fp">©2016 Beyond</p>
                    </div>
                </div>
            </div>
    </nav>
</div>
</footer>

<script>
    $(function () {
        $("#toTop").scrollToTop();
    });
    var i = 1;
    var totalpage = 1; //总页数，防止超过总页数继续滚动
    var winH = $(window).height(); //页面可视区域高度
    $(window).scroll(function () {
        if (i <= totalpage) { // 当滚动的页数小于总页数的时候，继续加载
            var pageH = $(document.body).height();
            var scrollT = $(window).scrollTop(); //滚动条top
            var rate = (pageH - winH - scrollT) / winH;
            if (rate < 0.01) {
                getJson(i);
            }
        } else { //否则显示无数据
            showEmpty();
        }
    });
    getJson(1); //加载第一页
    function getJson(page) {
        $(".nodata").show().html("<img src='/sport/Public/bootstrap/images/loading.gif'/>");
        $.getJSON("/sport/index.php/Home/Index/dynamicPage", {page: i}, function (json) {
            if (json) {
                $.each(json,
                    function (index, array) {
                        showDynamic(array);
                    });
                $(".nodata").hide()
            } else {
                showEmpty(); //数据为空的时候
            }
        });
        i++;
    }
    function showEmpty() {
        $(".nodata").show().html("<span style='color: #6732ff'><strong>别滚啦，已经到底咯。。。</strong></span>");
    }
    function showDynamic(sportInfo) {
        var str = "";
        if (typeof (sportInfo['topic_id']) != "undefined") {
            str += "<div class='dynamic_content'><div class='row'><div class='col-md-offset-2 col-md-8'><div>";
            str += "<a href='/sport/index.php/Home/User/info/id/" + sportInfo['topic_user_id'] + "'>";
            if (sportInfo['user_image'] === null) {

                str += "<img class='img-circle' src='/sport/images/avatar/2016122.jpg' alt='' width='50'height='50'></a>&nbsp;&nbsp;<span>" + sportInfo['user_name'] + "</span>";

            } else {
                str += "<img class='img-circle' src='/sport/images/avatar/" + sportInfo['user_image'] + "' alt='' width='50' height='50'></a>&nbsp;&nbsp;<span>" + sportInfo['user_name'] + "</span>";
            }
            str += "</div><div class='time' style='float: right'><span>" + sportInfo['topic_posted_time'] + "</span></div><hr><div><h3><strong>我的运动:</strong>" + sportInfo['topic_play_type'] + "</h3>";
            if (sportInfo['topic_play_type'] == '跑步') {
                str += "<h4>我今天运动了:<span style='color: red'>" + (sportInfo['topic_play_data']/1000) + "</span>km</h4>";
            } else {

                str += "<h4>我今天运动了:<span style='color: red'>" + Math.floor(sportInfo['topic_play_data'] / 60) + "</span>小时";
                //str += "<span style='color: red'>"+ sportInfo['topic_play_data']-60*Math.floor(sportInfo['topic_play_data']/60) + "</span>分钟</h4>";
                str += "<span style='color: red'>" + (sportInfo['topic_play_data'] - 60 * Math.floor(sportInfo['topic_play_data'] / 60)) + "</span>分钟</h4>";
            }
            str += "<p>";
            var content;
            if (sportInfo['topic_content'] === null) {
                content = "";
            } else {
                content = sportInfo['topic_content'];
            }
            str += content + "</p>";
            str += "</div></div></div><div class='row'> <a href='/sport/index.php/Home/Index/lookRecord/uid/"+sportInfo['topic_user_id']+"'><div class='col-md-offset-2 col-md-8' centered>";
            str += "<img src='/sport/images/sportImg/" + sportInfo['topic_play_image'] + "' alt='' class='img-responsive' height='450' width='300'>";
            str += "</div></a></div><br><div class='row'> <div class='col-md-offset-2 col-md-8' centered><span style='float: right'><a href='/sport/index.php/Home/Comment/index/tid/"+sportInfo['topic_id']+"'>评论("+sportInfo['comm_cnt']+")</a></span></div></div><hr></div>";

            $("#dyna_con").append(str);
        }
        if (typeof (sportInfo['toltalpage']) == "undefined") {
            totalpage = 1;
        } else {
            totalpage = sportInfo['toltalpage'];
        }
    }
</script>
</body>
</html>