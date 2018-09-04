<div style="width:100%;background: #242e3e;">
<div class="head" style="width:1200px;margin:0 auto">
    <div class="logo noselect"><span><?=PRODUCT_NAME?></span><div class="l"></div><em><?=SYSTEM_NAME?></em></div>
    <?php if(!(get_current_router(1)=='user' && in_array(get_current_router(2),['login','reg','forget']))){ ?>
     <ul class="nav">
        <li class="<?=in_array(get_current_router(1),['index'])? 'cur' : ''?>">
            <a href="/index">
            首页</a></li>
        <?php 
            $modules = $_SESSION['permission_modules'];
            if (in_array('production',      $modules) ||
                in_array('batch',           $modules) ||
                in_array('redpacket',       $modules) ||
                in_array('card',            $modules) ||
                in_array('point',           $modules) ||
                in_array('mixstrategy',     $modules) ||
                in_array('multistrategy',   $modules) ||
                in_array('accumstrategy',   $modules) ||
                in_array('activity',        $modules) ||
                in_array('group',           $modules) ||
                in_array('mall',            $modules) ||
                in_array('wechat',          $modules) ||
                in_array('setting',         $modules) ||
                in_array('salesman',        $modules) ||
                in_array('admin',           $modules) ||
                in_array('userdeal',        $modules) ||
                in_array('tag',             $modules) ||
                $_SESSION['role'] == ROLE_ADMIN_MASTER):
            ?>
            <li class="<?=in_array(get_current_router(1),['product','batch','redpacket','activity','wechat','setting','card','point','mixstrategy','multistrategy','accumstrategy','admin','mchoprlog','group','member','mall','salesman','settle'])? 'cur' : ''?>">
                <a href="/product/category">
                管理中心</a></li>
        <?php endif;?>
        <?php if (has_permission('myapp')): ?>
        <li class="<?=in_array(get_current_router(1),['app'])? 'cur' : ''?>">
            <a href="/app">
            应用中心</a></li>
        <?php endif;?>

        <?php if (has_permission('charts')): ?>
        <li class="<?=in_array(get_current_router(1),['charts','reporting'])? 'cur' : ''?>">
            <a href="/charts/index">
            数据中心</a></li>
        <?php endif;?>

        <li class="<?=in_array(get_current_router(1),['service'])? 'cur' : ''?>">
            <a href="/service/help_read">
            帮助中心</a></li></ul>

    <div class="user">
        <i class="iconfont photo">&#xe601;</i>
            <span><?=isset($_SESSION['username'])?$_SESSION['username']:'管理人员'?></span>
        <i class="iconfont down">&#xe600;</i>
        <ul>
            <li><a href="/user/person">个人信息</a></li>
            <?php if (has_permission('user')): ?>
            <li><a href="/user/company">企业信息</a></li>
            <?php endif;?>
            <?php if($_SESSION['expired']!==null){?>
                <li><a href="/cashier/renew">购买/续费VIP服务</a></li>
            <?php }?>
            <li><a href="/user/logout">退出登录</a></li>
        </ul>
    </div>
    <?php }else{?>
    <div class="user"><a href="/user/login">欢迎登录</a></div>
    <?php }?>
</div>
</div>