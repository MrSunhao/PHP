<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>评论</title>
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
                        <li class="active"><a href="/sport/index.php/Home/Index/dynamic">动态</a></li>
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
</div>
<div class="container desc">
    <div class="dynamic_content">
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
                <!--个人信息-->
                <div>
                    <a href="/sport/index.php/Home/User/info/id/<?php echo ($Info["user_id"]); ?>">
                        <?php if($Info['user_image'] != null): ?><img class="img-circle" src="/sport/images/avatar/<?php echo ($Info["user_image"]); ?>" alt="" width="50"
                                 height="50">
                            <?php else: ?>
                            <img class="img-circle" src="/sport/images/avatar/2016122.jpg" alt="" width="50" height="50"><?php endif; ?>
                    </a>&nbsp;&nbsp;<span><?php echo ($Info["user_name"]); ?></span>
                </div>
                <!--发布时间-->
                <div id="time" style="float: right">
                    <span><?php echo ($Info["topic_posted_time"]); ?></span>
                </div>
                <hr>
                <!--动态内容-->
                <div>
                    <!--运动类型-->
                    <h3><strong>我的运动：</strong><?php echo ($Info["topic_play_type"]); ?></h3>
                    <?php if(($Info['topic_play_type']) == "跑步"): ?><h4>我今天运动了:<span style="color: red"><?php echo ($Info['topic_play_data']/1000); ?></span>km</h4>
                        <?php else: ?>
                        <h4>我今天运动了:<span style="color: red"><?php echo (floor($Info['topic_play_data']/60 )); ?></span>小时<span
                                style="color: red"><?php echo $Info['topic_play_data']-60*floor($Info['topic_play_data']/60);?></span>分钟
                        </h4><?php endif; ?>
                    <p>
                        <?php echo ($Info['topic_content']); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-2 col-md-8" centered>
                <?php if($Info.topic_play_image): ?><a href="/sport/images/sportImg/<?php echo ($Info["topic_play_image"]); ?>" target="_blank"><img src="/sport/images/sportImg/<?php echo ($Info["topic_play_image"]); ?>" alt="" class="img-responsive" height="450" width="300"></a><?php endif; ?>

            </div>
        </div>
        <hr>
        <!--评论展示-->
        <div class="row">
            <div class="col-md-offset-2 col-md-8" centered>
                <?php if(($commList) == "null"): else: ?>
                    <?php if(is_array($commList)): $i = 0; $__LIST__ = $commList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><p>
                            <span onclick="reply(this)"><a href="/sport/index.php/Home/User/info/id/<?php echo ($vo["comment_user_id"]); ?>"><?php echo ($vo["comment_user_name"]); ?></a>评论：
                                <?php echo ($vo["comment_content"]); ?>
                                <input type="hidden" name="CId" value="<?php echo ($vo["comment_id"]); ?>">
                                <input type="hidden" name="CUId" value="<?php echo ($vo["comment_user_id"]); ?>">
                        </span>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if($vo["comment_user_id"] == $_SESSION['user']['user_id']): ?><a href="/sport/index.php/Home/Comment/del/tid/<?php echo ($vo["comment_topic_id"]); ?>/cid/<?php echo ($vo["comment_id"]); ?>">删除</a><?php endif; ?>
                        </p>
                        <?php if(($repList) == "null"): else: ?>
                            <?php if(is_array($repList)): $i = 0; $__LIST__ = $repList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rep): $mod = ($i % 2 );++$i; if($rep['reply_comment_id'] == $vo['comment_id']): ?><p>
                                        <span onclick="reply1(this)"><a
                                                href="/sport/index.php/Home/User/info/id/<?php echo ($rep["reply_start_user_id"]); ?>"><?php echo ($rep["user_name"]); ?></a>回复<a href="/sport/index.php/Home/User/info/id/<?php echo ($rep["reply_end_user_id"]); ?>"><?php echo ($rep["reply_end_user_name"]); ?></a>：
                                            <?php echo ($rep["reply_content"]); ?>
                                            <input type="hidden" name="CId" value="<?php echo ($rep["reply_comment_id"]); ?>">
                                            <input type="hidden" name="CUId" value="<?php echo ($rep["reply_start_user_id"]); ?>">
                                        </span>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php if($rep["reply_start_user_id"] == $_SESSION['user']['user_id']): ?><a href="/sport/index.php/Home/Reply/del/tid/<?php echo ($vo["comment_topic_id"]); ?>/rid/<?php echo ($rep["reply_id"]); ?>">删除</a><?php endif; ?>
                                    </p><?php endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            </div>
        </div>
        <!--发表评论-->
        <div class="row" id="comm">
            <div class="col-md-offset-2 col-md-8" centered>
                <form action="/sport/index.php/Home/Comment/comm" method="post" onsubmit="return checkComm()"
                      class="form-horizontal">
                    <input type="hidden" name="topic_id" value="<?php echo ($Info['topic_id']); ?>">
                    <textarea name="comment_content" id="comment" placeholder="我也说一句......" class="form-control"
                              cols="10" rows="5"></textarea>
                    <input type="submit" name="submit" value="发表评论">
                </form>
            </div>
        </div>
        <!--发表回复-->
        <div class="row" id="rep" style="display: none">
            <div class="col-md-offset-2 col-md-8" centered>
                <form action="/sport/index.php/Home/Reply/index" method="post" onsubmit="return checkRep()"
                      class="form-horizontal">
                    <input type="hidden" name="tid" value="<?php echo ($Info['topic_id']); ?>">
                    <input type="hidden" name="commId" id="commId" value="">
                    <input type="hidden" name="endUId" id="endUId" value="">
                    <textarea name="reply_content" id="reply" placeholder="" class="form-control"
                              cols="10" rows="5"></textarea>
                    <input type="submit" name="submit" value="发表回复">&nbsp;&nbsp;<input type="button" value="返回评论"
                                                                                       onclick="blurs()">
                </form>
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
<script>
    document.getElementById('comment').focus();
    function reply(obj) {
        var ch = obj.children;
        var name = ch[0].innerHTML;
        var CId = ch[1].value;
        var CUId = ch[2].value;
        var info = '回复' + name;
        document.getElementById('comm').style.display = 'none';

        document.getElementById('rep').style.display = 'block';
        document.getElementById('reply').focus();
        document.getElementById('reply').value = '';
        document.getElementById('reply').setAttribute('placeholder', info);
        document.getElementById('commId').value = CId;
        document.getElementById('endUId').value = CUId;
    }
    function reply1(obj) {
        var ch = obj.children;
        var name = ch[0].innerHTML;
        var CId = ch[2].value;
        var CUId = ch[3].value;
        var info = '回复' + name;
        document.getElementById('comm').style.display = 'none';

        document.getElementById('rep').style.display = 'block';
        document.getElementById('reply').focus();
        document.getElementById('reply').value = '';
        document.getElementById('reply').setAttribute('placeholder', info);
        document.getElementById('commId').value = CId;
        document.getElementById('endUId').value = CUId;
    }
    function blurs() {
        document.getElementById('comm').style.display = 'block';
        document.getElementById('rep').style.display = 'none';
    }
    function checkRep() {
        var repCon = document.getElementById('reply').value;
        if (repCon.length === 0) {
            alert('回复不能为空！');
            return false;
        } else {
            return true;
        }
    }
    function checkComm() {
        var comm = document.getElementById('comment').value;
        if (comm.length === 0) {
            alert('评论不能为空！');
            return false;
        } else {
            return true;
        }
    }
</script>
</body>
</html>