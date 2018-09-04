<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/wxmenu.css">
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/wxmenu/wxmenu.js"></script>
    <script>window.mchId=<?=$mchId?></script>
</head>
<body>
    <?php include VIEWPATH .'header.php';?>
    <div class="main">
        <?php include VIEWPATH .'lefter.php';?>
        <div class="rightmain">
            <div class="content" id="wxmenu">
                <ul class="tab">
                    <li class="current noselect">消费者公众号菜单</li>
                    <li class="noselect">供应链公众号菜单</li>
                </ul>
                <div class="tab_con" id="menuA">
                    <div class="add">
                        <span class="btn btn-blue noselect">添加菜单 +</span> 提示：公众号自定义菜单，第1级菜单只能添加3个，第2级菜单只能添加5个。
                    </div>
                    <div class="list"><BR><BR><BR><BR><BR><img src="/static/images/loading.gif"/></div>
                    <div class="save">
                        <div class="btn btn-blue noselect" id="save">保存到微信服务器</div> 
                    </div>
                </div>
                <div class="tab_con" id="menuB" style="display:none">
                    <div class="add">
                        <span class="btn btn-blue noselect">添加菜单 +</span> 提示：公众号自定义菜单，第1级菜单只能添加3个，第2级菜单只能添加5个。
                    </div>
                    <div class="list"><BR><BR><BR><BR><BR><img src="/static/images/loading.gif"/></div>
                    <div class="save">
                        <div class="btn btn-blue noselect" id="save">保存到微信服务器</div> 
                    </div>
                </div>
                <div id="editForm" style="display:none">
                    <div class="table-form">
                        <input type="hidden" id="editKey"/>
                        <label>类型：<select id="editType" maxlength=5 class="input" style="width:322px">
                            <option value="view">链接</option>
                            <option value="scancode_push">扫一扫</option>
                            <!-- <option value="hls_app">欢乐扫应用</option> -->
                            <!--<option value="scancode_waitmsg_waiter_sys">扫一扫（服务员）</option>
                            <option value="scancode_waitmsg_waiter_sq">收券（服务员）</option>
                            <option value="scancode_waitmsg_salesman_sq">收券（业务员）</option>-->
                            </select> <BR></label>
                        <label>名称：<input type="text" id="editName" maxlength=5 class="input" style="width:300px"/> <font color=gray>不超过5个字</font><BR></label>
                        <label style="display:none">链接：<input type="text" id="editUrl" maxlength=255 class="input" style="width:300px"/> <font color=gray>完整的网址</font><br /></label>
                        <label style="display:none">应用：<select id="app_selector" class="input" style="width: 322px">
                        </select></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php';?>
    <ul class="hover-panel" style="display:none;">
        <li title="修改"><i class="iconfont">&#xe602;</i></li>
        <li title="删除" class="last"><i class="iconfont">&#xe603;</i></li>
    </ul>
</body>

</html>