<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/setting.css">
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/setting_warning.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="content page_setting">
                <ul class="tab">
                    <li class="noselect">扫码频率</li>
                    <li class="current noselect">安全预警</li>
                    <li class="noselect">扫码次数</li>
                </ul>
                <div class="tab_con" id="freq">
                    <div class="padding">
                        <div class="table-form">
                            <div class="h10"></div>
                            <div class="tip"></div>
                            <div class="exists-users">
                                <div class="exists-title">已添加为接收预警通知的用户</div>
                                <div class="exists-nav">
                                    <div class="exists-n1">ID号</div>
                                    <div class="exists-n2">微信昵称</div>
                                    <div class="exists-n3">微信头像</div>
                                    <div class="exists-n4">编辑</div>
                                </div>
                                <div class="exists-detail">
                                    <?php foreach ($data as $key => $value): ?>
                                    <div class="exists-detail-son">
                                        <div class="exists-id"><?=$value->id?></div>
                                        <div class="exists-nickname"><?=$value->nickName?></div>
                                        <div class="exists-avatar"><img src="<?=$value->headimgurl?>"></div>
                                        <div class="exists-edit del">
                                            <button>删除</button>
                                        </div>                                  
                                    </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                            <div class="h30"></div>
                            <div class="search-title">搜索添加接收预警通知的新用户</div>
                            <div class="h20"></div>
                            <div class="add-user">
                                <div class="search-main">
                                    <div class="search-user"><p>请输入微信昵称:</p><input type="text" name=""><button>搜索</button></div>
                                    <div class="exists-nav search-other">
                                        <div class="exists-n1">ID号</div>
                                        <div class="exists-n2">微信昵称</div>
                                        <div class="exists-n3">微信头像</div>
                                        <div class="exists-n4">编辑</div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="search-result">
                                        <div class="exists-detail search-list">
                                            <!-- <div class="exists-detail-son search-son">
                                                <div class="exists-id">asdfd</div>
                                                <div class="exists-nickname">asdfsdfsdfsdfsfasdfsfsdf</div>
                                                <div class="exists-avatar"><img src="https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=2282382730,4071759632&fm=58"></div>
                                                <div class="exists-edit add">
                                                    <button>添加</button>
                                                </div>                                  
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="h30"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>

</html>