<div class="head">
    <div class="logo noselect">
        <span><?=PRODUCT_NAME?></span><em><?=SYSTEM_NAME?></em></div>
        <?php if (isset($_SESSION['admin'])): ?>
    <ul class="nav">
        <li class="<?=in_array(get_current_router(1),['merchant','shop','dealer'])? 'cur': ''?>">
            <a href="/merchant">企业管理</a>
        </li>
<!--         <li class="<?=get_current_router(1) == 'charts'? 'cur' : ''?>">
    <a href="/charts">报表管理</a>
</li> -->
        <li class="<?=get_current_router(1) == 'admin'? 'cur' : ''?>">
            <a href="/admin">平台管理</a>
        </li>
        <li class="<?=get_current_router(1) == 'addon'? 'cur' : ''?>">
            <a href="/addon">模块管理</a>
        </li>
        <?php if (isDev()): ?>
        <li class="<?=get_current_router(1) == 'workorder'? 'cur': ''?>">
            <a href="/workorder">工单管理</a>
        </li>
        <?php endif;?>
    </ul>
    <div class="user">
        <i class="iconfont photo">&#xe601;</i><span>
        <?= isset($_SESSION['admin'])? $_SESSION['admin']->realName: ''?></span>
        <i class="iconfont down">&#xe600;</i>
        <ul><li><a href="/admin/profile">帐号信息</a></li>
            <li><a href="/admin/passwd">密码修改</a></li>
            <li><a href="/admin/signout">退出登录</a></li></ul></div>
    <?php else:?>
    <div class="user"><a href="/login">欢迎登录</a></div>
    <?php endif;?>
</div>
