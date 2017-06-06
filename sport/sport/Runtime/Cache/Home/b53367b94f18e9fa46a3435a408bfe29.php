<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>首页</title>
    <link rel="stylesheet" href="/sport/Public/bootstrap/css/bootstrap.min.css">
    <script src=" /sport/Public/bootstrap/js/jquery-3.1.1.min.js"></script>
    <script src=" /sport/Public/bootstrap/js/bootstrap.min.js"></script>
    <!--<link rel="stylesheet" href="css/bootstrap.css">  -->
    <!--<script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>-->
    <!--<script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->

    <style type="text/css">
        body {
            background-color: rgba(186, 168, 255, 0.46);
        }
        .form-group{
            float: left;
            margin-right: 10px;
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
        <nav class="navbar navbar-default` navbar-fixed-top navbar-inverse" role="navigation">
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

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div>
                    <h3><strong>公告栏</strong></h3>
                    <div class="panel-group" id="accordion">

                    </div>
                    <div id='page'></div>
                </div>
            </div>
            <div class="col-md-6">
                <h3><strong>今日公告</strong></h3>
                <div style="text-align: center;background-color: rgba(210, 188, 248, 0.57)"><h3><?php echo ($tSport['notice_title']); ?></h3></div>
                <div><p><strong>运动目标距离：</strong><span style="color:#f80e0a"><?php echo ($tSport['notice_run_data']); ?>千米</span></p></div>
                <div><p><strong>运动目标时间：</strong><span style="color:#f80e0a"><?php echo ($tSport['notice_play_time']); ?>小时</span></p></div>
                <p><strong>相关说明：</strong><?php echo ($tSport['notice_content']); ?></p>
            </div>
        </div>


        <hr style="height: 8px;background-color: #00b3ff">
        <!--数据可视化展示-->
        <div class="row">
            <div class="col-md-6">
                <div><h3><strong>年级人数比</strong></h3></div>
                <div id="sport" style="height: 400px; width: 100% "></div>
            </div>

            <div class="col-md-6">
                <div><h3><strong>运动人数统计</strong></h3></div>
                <div id="people_count" style="height: 400px; width: 100% ">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div><h3><strong>每日运动统计</strong><small>统计不分先后</small></h3></div>
                <div id="daysport" style="height: 500px;width: 100%"></div>
                <button class="btn btn-default" style="margin-right: 10px" onclick="lastPage()">上一页</button><button class="btn btn-default" onclick="nextPage()">下一页</button>
            </div>
        </div>
    </div>
    <hr style="height: 8px;background-color: #00b3ff">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div><h3><strong>近一周运动记录</strong><small>按学号查询哟</small></h3></div>
            </div>
        </div>
        <div class="row">
             <div class="col-md-offset-3 col-md-6">
                <div class="form-group">
                    <input type="text" placeholder="请输入学号" name="number" id="number" class="form-control">
                </div>
                 <button class="btn btn-primary" onclick="getWeek_sport()"><span class="glyphicon glyphicon-search"></span>查询</button>
             </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="sports" style="height: 500px;width: 100%"></div>
            </div>
        </div>
    </div>
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
<script src="/sport/Public/bootstrap/js/echarts.min.js"></script>
<script type="text/javascript">
    //年级人数比 饼状图
    var sport = echarts.init(document.getElementById('sport'));
    /*这是变量啊,怎么老是忘了*/
    var option = {
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b}: {c} ({d}%)"
                },
                title: {
                    text: '',
                    x: 'center'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: <?php echo ($ldata); ?>
            },
            toolbox : {
        show : true,
                feature:{
            mark : {
                show: true
            },
            dataView : {
                show: true,
                        eadOnly:
                true
            },
            magicType : {
                show: true,
                        ype:['pie', 'funnel']
            },
            restore : {
                show: true
            },
            saveAsImage : {
                show : true
            }
        }
    },
    series: [
        {
            name: '',
            type: 'pie',
            radius: '55%',
            center: ['40%', '60%'],
            data: <?php echo ($sdata); ?>.sort(function (a, b) {
        return b.value - a.value
    }),
    /*对数据排序*/
        }
    ]};
    sport.setOption(option);
</script>
<script type="text/javascript">
    // 基于准备好的dom，初始化echarts实例
    var myChart1 = echarts.init(document.getElementById('people_count'));

    // 指定图表的配置项和数据
    var option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'line'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                legend: {
                    data: ['运动人数']
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: true},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: [
                        {
                        data: <?php echo ($stype); ?>
                    }
                ],
                yAxis: [
                    {
                        name:'人数'
                    }
                        ],
                series:[
                    {
                    name: '运动人数',
                    type: 'bar',
                    data: <?php echo ($sval); ?>
                    },
            ]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart1.setOption(option);
</script>
<script type="text/javascript">
    //每日运动统计
    var i =1;
    var pages = 1;
    getDay_sport(1);
    function lastPage() {
        i--;
        if(i<1){
            i++;
            alert('已经是第一页了!');
            return ;
        }
        getDay_sport(i);
    }

    function nextPage() {
        i++;
        if(i>pages){
            i--;
            alert('已经是最后一页了！');
            return ;
        }
        getDay_sport(i);
    }
    function getDay_sport() {
        var url = '/sport/index.php/Home/Index/days_sport';
        $.post(url, {p:i}, function (data, status) {
            i=data['p'];
            pages = data['pages'];
            var myChart2 = echarts.init(document.getElementById('daysport'));
            var option = {
                tooltip: {
                    trigger: 'axis'
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: true},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data: ['运动距离', '运动时间']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: data['user']
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '运动距离',
                        min: 0,
                        max: 10,
                        interval: 2,
                        axisLabel: {
                            formatter: '{value} km'
                        }
                    },
                    {
                        type: 'value',
                        name: '运动时间',
                        min: 0,
                        max: 10,
                        interval: 2,
                        axisLabel: {
                            formatter: '{value} h'
                        }
                    },
                ],
                series: [
                    {
                        name: '运动距离',
                        type: 'bar',
                        data: data['sdata'],
                        markLine : {
                            lineStyle: {
                                normal: {
                                    type: 'solid',
                                    color:'#FF0000'
                                }
                            },
                            data : [
                                {
                                    name: '运动距离目标线',
                                    yAxis: data['itemData']
                                }
                            ]
                        }
                    },
                    {
                        yAxisIndex:1,
                        name: '运动时间',
                        type: 'bar',
                        data: data['stime'],
                        markLine : {
                            lineStyle: {
                                normal: {
                                    type: 'solid',
                                    color:'#00FF00'
                                }
                            },
                            data : [
                                {
                                    name: '运动时间目标线',
                                    yAxis: data['itemTime']
                                }
                            ]
                        }
                    },
                ]
            };
            myChart2.setOption(option);
        }, 'json');
    }
</script>
<script type="text/javascript">
    //每周运动统计
    $('#sports').hide();
    function getWeek_sport() {
        var url = '/sport/index.php/Home/Index/week_sport';
        var number = $('#number').val();
        $.post(url, {number:number}, function (data, status) {
            if(data === null){
            alert('查无此人！请检查输入的学号...');
                return ;
        }
           $('#sports').show();
            var myChart3 = echarts.init(document.getElementById('sports'));
            var option = {
                title: {
                    text: data['name']+'的运动记录'
                },
                tooltip: {
                    trigger: 'axis'
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: true},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data: ['运动距离', '运动时间']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: data['date']
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '运动距离',
                        min: 0,
                        max: 40,
                        interval: 4,
                        axisLabel: {
                            formatter: '{value} km'
                        }
                    },
                    {
                        type: 'value',
                        name: '运动时间',
                        min: 0,
                        max: 10,
                        interval: 1,
                        axisLabel: {
                            formatter: '{value} h'
                        }
                    },
                ],
                series: [
                    {
                        name: '运动距离',
                        type: 'bar',
                        data: data['sdata'],
                    },
                    {
                        yAxisIndex:1,
                        name: '运动时间',
                        type: 'bar',
                        data: data['stime'],
                    },
                ]
            };
            myChart3.setOption(option);
        }, 'json');
    }
</script>
<script type="text/javascript">
        getSportNotice(1);
    function getSportNotice(p) {
        i = 1;
        $.post("/sport/index.php/Home/Index/getNotice", {
            p: p
        }, function (data, stutas) {
            $("#accordion").empty();
            if (data !== null) {
                $.each(data, function (index, arr) {
                    showSportNotice(arr);
                });
                $('#page').empty();
                pages1(data['page']);//分页
            } else {
                $("#accordion").empty();
                showEmpty();
            }
        }, 'json');
    }

    function showSportNotice(data) {
        var str = '';
        if(data['notice_id'] != undefined){
            str += "<div class='panel panel-default'><div class='panel-heading'><h4 class='panel-title'>";
            str += "<a data-toggle='collapse' data-parent='#accordion'href='#collapse"+data['notice_id']+"'>";
            str += data['notice_title'] ;
            str += "<span style='color: #CC0000;;float: right;'>"+data['notice_time']+"</span></a>";
            str += "</h4></div><div id='collapse"+data['notice_id']+"' class='panel-collapse collapse'><div class='panel-body'>";
            str += "<h4>"+data['notice_title']+"</h4>";
            str += "<p><strong>运动目标距离:</strong><span style='color: #f8d512'>"+(data['notice_run_data']/1000).toFixed(2)+"千米</span></p>";
            str += "<p><strong>运动目标时间:</strong><span style='color: #f8d512'>"+(data['notice_play_time']/60).toFixed(2)+"小时</span></p>";
            str += "<p><strong>相关说明：</strong>"+data['notice_content']+"</p>";
            str += "</div></div></div>";
        }
        $("#accordion").append(str);
    }
    function showEmpty() {
        var str = '';
        str += "<h2>没有数据哦！</h2>";
        $("#accordion").append(str);
    }

    function pages1(page) {
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
            } */else if (page['p'] + 1 >= page['pages']) {
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
                getSportNotice(1);
            }
        } else if (val === '上一页') {
            if ((p-1) < 1) {
                alert('已经是第一页了！');
            } else {
                getSportNotice(--p);
            }
        } else if (val === '下一页') {
            if ((p+1) > pages) {
                alert('已经是最后一页了！');
            }else  {
                getSportNotice(++p);
            }
        } else if(val === '尾页'){
            if(p === pages){
                alert('已经是最后一页了！');
            }else{
                getSportNotice(pages);
            }
        }else {
            getSportNotice(val);
        }
    }
</script>

</body>
</html>