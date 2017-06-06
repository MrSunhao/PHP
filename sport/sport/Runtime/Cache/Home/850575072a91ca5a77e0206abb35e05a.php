<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>排行榜</title>
    <link rel="stylesheet" href="/sport/Public/bootstrap/css/bootstrap.min.css">
    <script src=" /sport/Public/bootstrap/js/jquery-3.1.1.min.js"></script>
    <script src=" /sport/Public/bootstrap/js/bootstrap.min.js"></script>
    <!--<link rel="stylesheet" href="css/bootstrap.css">  -->
    <!--<link href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">-->
    <!--<script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>-->
    <!--<script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->

    <style type="text/css">
        body {
            background-color: rgba(186, 168, 255, 0.46);
        }

        .form-group {
            float: left;
            margin-right: 10px;
        }

        .tds {
            vertical-align: middle !important;
        }

        .progress {
            margin-bottom: 0;
        }

        #choice {
            margin-top: 40px;
        }

        img {
            height: 50px;
            width: 50px;
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
                    <a class="navbar-brand" href="/sport/index.php/Home/Index">首页</a>
                </div>
                <div class="collapse navbar-collapse" id="example-navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="/sport/index.php/Home/Index/rankList">运动榜</a></li>
                        <li class="operation"><a href="/sport/index.php/Home/Index/dynamic">动态</a></li>
                        <li class="operation"><a href="/sport/index.php/Home/Index/record">个人记录</a></li>
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

    <!--选择排行-->
    <div class="container" id="choice">
        <div class="container">
            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <form action="">
                        <div class="form-group">
                            <label for="sport_type">选择运动类型</label>
                            <select id="sport_type" class="form-control se">
                                <option selected="selected" value="跑步">跑步</option>
                                <option value="篮球">篮球</option>
                                <option value="足球">足球</option>
                                <option value="排球">排球</option>
                                <option value="羽毛球">羽毛球</option>
                                <option value="乒乓球">乒乓球</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sport_grade">选择年级</label>
                            <select id="sport_grade" name="grade" class="form-control se">
                                <option value="All" selected="selected">All</option>
                                <option value="2012">2012级</option>
                                <option value="2013">2013级</option>
                                <option value="2014">2014级</option>
                                <option value="2015">2015级</option>
                                <option value="2016">2016级</option>
                                <option value="2017">2017级</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- 剩余的 -->
        <div class="container">
            <div class="row clearfix">
                <div class="col-md-offset-3 col-md-6 column">
                    <div class="table-responsive" id="noData">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    getRankList(1);
    $('#sport_type').click(function () {
        getRankList(1);
    });
    $('#sport_grade').click(function () {
        getRankList(1);
    });
    var i = 1;
    function getRankList(p) {
        i = 1;
        var sport_type = $('#sport_type').val();
        var grade = $('#sport_grade').val();
        $.post("/sport/index.php/Home/Index/rankListPage", {
            p: p,
            type: sport_type,
            grade: grade
        }, function (data, stutas) {
            if (data !== null) {
                $(function () {
                    $("[data-toggle='tooltip']").tooltip();
                });
                $("#noData").empty();
                i = (data['page']['p']-1)*10+1;
                var thead = "<table class='table table-condensed table-hover'><thead><tr><th>排名</th><th>用户名</th><th>班级</th><th>头像</th><th>运动数据</th><th>完成度</th></tr></thead><tbody id='showList'> </tbody></table><div id='page'></div>";
                $("#noData").append(thead);
                $.each(data, function (index, arr) {
                    showRankList(arr);
                });
                pages(data['page']);//分页
            } else {
                $("#noData").empty();
                showEmpty();
            }
        }, 'json');
    }

    function showRankList(data) {
        var str = '';
        if (data['topic_id'] !== undefined) {
            str += "<tr><td class='tds'>" + (i++) + "</td>";
            str += "<td class='tds'><span style='color: #ffba57'>" + data['user_name'] + "</span></td>";
            str += "<td class='tds'>" + data['user_class'] + "</td>";
            str += "<td class='tds'><a href='/sport/index.php/Home/User/info/id/" + data['topic_user_id'] + "'><img src='/sport/images/avatar/";
            if (data['user_image'] === null) {
                str += "2016122.jpg";
            } else {
                str += data['user_image'];
            }
            str += "' class='img-circle'></a></td>";
            if (data['topic_play_type'] == '跑步') {
                str += "<td class='tds'>" + (data['topic_play_data'] / 1000) + "千米</td>";
                str += "<td class='tds'><div class='progress progress-striped active' data-toggle='tooltip' data-placement='top' title='";
                var val = ((data['topic_play_data'] / data['goal'])*100).toFixed(1);
                if (val > 100) {
                    val = 100;
                }
                str += val + "%'><div class='progress-bar progress-bar-success' role='progressbar'aria-valuenow='60' aria-valuemin='0' aria-valuemax='100'style='width: " + val + "%;'><span class='sr-only'></span></div></div></td></tr>";
            } else {
                str += "<td class='tds'>" + Math.floor(data['topic_play_data'] / 60) + "小时" + (data['topic_play_data'] - 60 * Math.floor(data['topic_play_data'] / 60)) + "分钟</td>";
                str += "<td class='tds'><div class='progress progress-striped active' data-toggle='tooltip' data-placement='top' title='";
                var val2 = ((data['topic_play_data'] / data['goal'])*100).toFixed(1);
                if (val2 > 100) {
                    val2 = 100;
                }
                str += val2 + "%'><div class='progress-bar progress-bar-success' role='progressbar'aria-valuenow='60' aria-valuemin='0' aria-valuemax='100'style='width: " + val2 + "%;'><span class='sr-only'></span></div></div></td></tr>";
            }
            $("#showList").append(str);
        }
    }

    function showEmpty() {
        var str = '';
        str += "<h2>没有数据哦！</h2>";
        $("#noData").append(str);
    }

    function pages(page) {
        var str = '';
        str += "<div class='page collapse navbar-collapse navbar-ex1-collapse'><ul class='pagination'>";
        str += "<li><a href='javascript:;' onclick='pCl(this," + page['p'] + ")'>首页</a></li>";
        str += "<li><a href='javascript:;' onclick='pCl(this," + page['p'] + ")'>上一页</a></li>";
        if (page['pages'] <= 5) {
            for (var i = 1; i <= page['pages']; i++) {
                str += "<li><a href='javascript:;' onclick='pCl(this," + page['p'] + ","+page['pages']+")'>" + i + "</a></li>";
            }
        } else {
            if (page['p'] < 4) {
                for (var j = 1; j <= 5; j++) {
                    str += "<li><a href='javascript:;' onclick='pCl(this," + page['p'] + ","+page['pages']+")'>" + j + "</a></li>";
                }
            } /*else if (data['p'] >= 4) {
                for (var k = data['p']; k <= page['pages']; k++) {
                    str += "<li><a href='javascript:;' onclick='pCl(this," + page['p'] + ","+page['pages']+")'>" + k + "</a></li>";
                }
            }*/ else if (page['p'] + 1 >= page['pages']) {
                for (var l = page['pages'] - 4; l <= page['pages']; l++) {
                    str += "<li><a href='javascript:;' onclick='pCl(this," + page['p'] + ","+page['pages']+")'>" + l + "</a></li>";
                }
            } else {
                for (var n = page['p'] - 2; n <= page['p'] + 2; n++) {
                    str += "<li><a href='javascript:;' onclick='pCl(this," + page['p'] + ","+page['pages']+")'>" + n + "</a></li>";
                }
            }
        }
        str += "<li><a href='javascript:;' onclick='pCl(this," + page['p'] + ","+page['pages']+")'>下一页</a></li>";
        str += "<li><a href='javascript:;' onclick='pCl(this," + page['p'] + ","+page['pages']+")'>尾页</a></li>";
        str += "</ul></div>";
        $("#page").append(str);
    }

    function pCl(obj, p, pages) {
        var val = $(obj).text();
        if (val === '首页') {
            if(p === 1){
                alert('已经是第一页了！');
            }else{
                getRankList(1);
            }
        } else if (val === '上一页') {
            if ((p-1) < 1) {
                alert('已经是第一页了！');
            } else {
                getRankList(--p);
            }
        } else if (val === '下一页') {
            if ((p+1) > pages) {
                alert('已经是最后一页了！');
            }else  {
                getRankList(++p);
            }
        } else if(val === '尾页'){
            if(p === pages){
                alert('已经是最后一页了！');
            }else{
                getRankList(pages);
            }
        }else {
            getRankList(val);
        }
    }
</script>
</body>
</html>