<div class="head">
    <div class="logo noselect"><span><?=PRODUCT_NAME?></span><div class="l"></div><em><?=SYSTEM_NAME?></em></div>
    <?php
    if(!(get_current_router(1)=='user' && in_array(get_current_router(2),['login','reg','forget']))){
    ?>
    <ul class="nav">

            <!-- 报表查看人员只能查看数据中心 - begin -->
            <?php if ($_SESSION['role'] == -1): ?>
            <li class="<?=in_array(get_current_router(1),['charts','reporting'])? 'cur' : ''?>"><a href="/charts/index">数据中心</a></li>
            <?php else:?>
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
            <li class="<?=in_array(get_current_router(1),['product','batch','redpacket','activity','wechat','setting','card','point','mixstrategy','multistrategy','accumstrategy','admin','mchoprlog','group','member','mall','salesman','settle','userdeal','tag'])? 'cur' : ''?>">
                <a href="/product/category">
                管理中心</a></li>
            <?php  endif; ?>
            <?php if (has_permission('myapp')): ?>
            <li class="<?=in_array(get_current_router(1),['app', 'myapp'])? 'cur' : ''?>">
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
                帮助中心</a></li>
            <?php endif;?>
        <!-- 报表查看人员只能查看数据中心 - end -->
    </ul>
    <div class="user">
        <i class="iconfont photo">&#xe601;</i><span><?=isset($_SESSION['username'])?$_SESSION['username']:'管理人员'?></span><i class="iconfont down">&#xe600;</i>
        <ul>
            <?php if ($_SESSION['role'] !== -1): ?>
            <li><a href="/user/person">个人信息</a></li>
                <?php if (has_permission('user')): ?>
            <li><a href="/user/company">企业信息</a></li>
                <?php endif;?>
            <?php endif;?>
            <?php if($_SESSION['expired']!==null){?>
                <li><a href="/cashier/renew">购买/续费VIP服务</a></li>
            <?php }?>
            <li><a href="#" id="sso_login">粉丝营销</a></li>
            <li><a href="/user/logout">退出登录</a></li>
            <li></li>
        </ul>
    </div>
    <?php }else{?>
    <div class="user"><a href="/user/login">欢迎登录</a></div>
    <?php }?>
    <script>
    $(function(){ 
    	var html=$('.cur a').html(); 
    	$('.logo em').html(html);

        $(".leftbar>.tabs>li").click(function(){
            $(".leftbar>.tabs>li").removeClass('tab_current');
            $(this).addClass('tab_current');
        })

        $('#sso_login').click(function(){
            $.post('/utils/api/sso.login', {}, function(resp,obj) {
                console.log(resp);
                window.open("http://scrm.social-touch.com/User/Index/aclogin?cross_domain=1&user="+resp.data);
            }).error(function(error) {
                common.alert('无法连接服务器！');
            });
        });
    });
    </script>
</div>
<?php if(get_current_router(1)=='charts'):?>
<div style="width:100%;height:30px;background:#fefced;text-align:center;line-height:30px;border-bottom:1px solid #edede5;color:#ff5100;font-size:14px;">
  各位用户，平台每日会针对昨日数据重新统计，当日实时数据仅供参考！
</div>
<?php endif; ?>