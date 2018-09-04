<div class="leftbar">
    <?php if (has_permission('production')): ?>
    <dl>
        <dt>产品</dt>
        <dd>
            <a class="<?=get_current_router(1)=='product' && get_current_router(2)=='category' ? 'cur' : ''?>" href="/product/category">产品分类</a>
            <a class="<?=get_current_router(1)=='product' && in_array(get_current_router(2),['lists','add','edit']) ? 'cur' : ''?>" href="/product/lists">产品管理</a>
        </dd>
    </dl>
    <?php endif; ?>
    <?php if (has_permission('batch')): ?>
    <dl>
        <dt>红码</dt>
        <dd>
            <a class="<?=get_current_router(1)=='batch' && in_array(get_current_router(2),['lists','edit']) ? 'cur' : ''?>" href="/batch/lists">红码管理</a>
            <a class="<?=get_current_router(1)=='batch' && get_current_router(2)=='add' ? 'cur' : ''?>" href="/batch/add">红码申请</a>
            <?php if($_SESSION['expired']==null){?>
            <a class="<?=get_current_router(1)=='batch' && in_array(get_current_router(2),['order_lists','order_add']) ? 'cur' : ''?>" href="/batch/order_lists">红码入库单</a>
            <a class="<?=get_current_router(1)=='batch' && in_array(get_current_router(2),['order_out_lists','order_out_add']) ? 'cur' : ''?>" href="/batch/order_out_lists">红码出库单</a>
            <?php }?>
        </dd>
    </dl>
    <?php endif; ?>
    <dl class="hls-activity-dl">
        <dt>活动</dt>
        <dd>
            <?php if (has_permission('redpacket')): ?>
            <a class="<?=get_current_router(1)=='redpacket' && in_array(get_current_router(2),['lists','add','edit','addsub','editsub']) ? 'cur' : ''?>" href="/redpacket/lists">红包策略</a>
            <?php endif; ?>
            <?php if($_SESSION['expired']==null){?>
            <?php if (has_permission('card')): ?>
            <a class="<?=get_current_router(1)=='card' && in_array(get_current_router(2),['winlist','lists','add','edit','addsub','editsub','editgroup','addgroup','holder']) ? 'cur' : ''?>" href="/card/lists">乐券策略</a>
            <?php endif; ?>
            <?php if (has_permission('point')): ?>
            <a class="<?=get_current_router(1)=='point' && in_array(get_current_router(2),['lists','add','edit','addsub','editsub']) ? 'cur' : ''?>" href="/point/lists">积分策略</a>
            <?php endif; ?>
            <?php if (has_permission('mixstrategy')): ?>
            <a class="<?=get_current_router(1)=='mixstrategy' && in_array(get_current_router(2),['lists','add','edit','addsub','editsub']) ? 'cur' : ''?>" href="/mixstrategy/lists">组合策略</a>
            <?php endif; ?>
            <?php if (has_permission('multistrategy')): ?>
            <a class="<?=get_current_router(1)=='multistrategy' && in_array(get_current_router(2),['lists','add','edit','addsub','editsub']) ? 'cur' : ''?>" href="/multistrategy/lists">叠加策略</a>
            <?php endif; ?>
            <?php if (has_permission('accumstrategy')): ?>
            <a class="<?=get_current_router(1)=='accumstrategy' && in_array(get_current_router(2),['lists','add','edit','addsub','editsub', 'bonus']) ? 'cur' : ''?>" href="/accumstrategy/lists">累计策略</a>
            <?php endif; ?>
            <?php }?>
            <?php if (has_permission('activity')): ?>
            <a class="<?=get_current_router(1)=='activity' && in_array(get_current_router(2),['lists','add','edit','addsub','editsub']) ? 'cur' : ''?>" href="/activity/lists">活动管理</a>
            <?php endif; ?>
        </dd>
    </dl>
    <script type="text/javascript">
        if ($('.hls-activity-dl a').length === 0) {
            $('.hls-activity-dl').hide();
        }
    </script>
    <?php if($_SESSION['expired']==null){?>
    <dl class="hls-user-dl">
        <dt>用户管理</dt>
        <dd>
            <?php if (has_permission('reporting')): ?>
            <a class="<?=get_current_router(1)=='member' && in_array(get_current_router(2),['memberlist']) ? 'cur' : ''?>" href="/member/memberlist">会员列表</a>
            <?php endif; ?>
            <?php if (has_permission('userdeal')): ?>
            <a class="<?=get_current_router(1)=='userdeal' && in_array(get_current_router(2),['mch_forbidden_users']) ? 'cur' : ''?>" href="/userdeal/mch_forbidden_users">封禁列表</a>
            <?php endif; ?>
            <?php if (has_permission('tag')): ?>
            <a class="<?=get_current_router(1)=='tag' && in_array(get_current_router(2),['lists','edit','add']) ? 'cur' : ''?>" href="/tag/lists">标签管理</a>
            <?php endif; ?>
        </dd>
    </dl>
    <script type="text/javascript">
        if ($('.hls-user-dl a').length === 0) {
            $('.hls-user-dl').hide();
        }
    </script>
    <?php if (has_permission('group')): ?>
    <dl>
        <dt>好友圈</dt>
        <dd>
            <a class="<?=get_current_router(1)=='group' && in_array(get_current_router(2),['setting']) ? 'cur' : ''?>" href="/group/setting">基础设置</a>
            <a class="<?=get_current_router(1)=='group' && in_array(get_current_router(2),['lists']) ? 'cur' : ''?>" href="/group/lists">群组管理</a>
        </dd>
    </dl>
    <?php endif; ?>
    <?php if (has_permission('mall')): ?>
    <dl>
        <dt>积分商城</dt>
        <dd>
            <a class="<?=get_current_router(2)=='configure' && in_array(get_current_router(2),['configure','goods']) ? 'cur' : ''?>" href="/mall/configure">商城配置</a>
            <a class="<?=get_current_router(1)=='mall' && in_array(get_current_router(2),['category']) ? 'cur' : ''?>" href="/mall/category">商品分类</a>
            <a class="<?=get_current_router(1)=='mall' && in_array(get_current_router(2),['goods','edit']) ? 'cur' : ''?>" href="/mall/goods">商品列表</a>
            <a class="<?=get_current_router(2)=='orders' && in_array(get_current_router(2),['configure','goods','edit','orders']) ? 'cur' : ''?>" href="/mall/orders">订单处理</a>
        </dd>
    </dl>
    <?php endif; ?>
    <?php if (has_permission('salesman')): ?>
        <dl>
            <dt>业务员</dt>
            <dd>
                <a class="<?=get_current_router(1)=='salesman' ? 'cur' : ''?>" href="/salesman">业务员管理</a>
                <a class="<?=get_current_router(1)=='settle' ? 'cur' : ''?>" href="/settle">业务员核销</a>
            </dd>
        </dl>
    <?php endif;?>
    <?php if (! isProd() && (in_array('shop', $_SESSION['permission_modules']) || $_SESSION['role'] == ROLE_ADMIN_MASTER)): ?>
        <dl>
            <dt>门店</dt>
            <dd>
                <a class="<?=get_current_router(1)=='shop' ? 'cur' : ''?>" href="/shop/tag_lists">标签管理</a>
            </dd>
        </dl>
    <?php endif;?>
    <?php }?>
    <dl class="hls-setting-dl last">
        <dt>设置</dt>
        <dd>
            <?php if (has_permission('admin')): ?>
            <?php if($_SESSION['expired']==null){?>
            <a class="<?=get_current_router(1)=='wxpay' ? 'cur' : ''?>" href="/wxpay/balance">账户余额</a>
            <a class="<?=get_current_router(1)=='admin' ? 'cur' : ''?>" href="/admin">帐号管理</a>
            <a class="<?=get_current_router(1)=='role' ? 'cur' : ''?>" href="/role">角色管理</a>
            <?php }?>
            <?php endif;?>
            <?php if (has_permission('wechat')): ?>
            <a class="<?=get_current_router(1)=='wechat' && in_array(get_current_router(2),['wxmenu']) ? 'cur' : ''?>" href="/wechat/wxmenu">微信菜单</a>
            <?php endif;?>
            <?php if (has_permission('setting')): ?>
            <a class="<?=get_current_router(1)=='setting' && in_array(get_current_router(2),['guard','warning','user_scan']) ? 'cur' : ''?>" href="/setting/guard">安全防护</a>
            <?php endif;?>
            <?php if($_SESSION['expired']==null){?>
            <?php if ($_SESSION['role'] == ROLE_ADMIN_MASTER): ?>
            <a class="<?=get_current_router(1)=='mchoprlog' ? 'cur' : ''?>" href="/mchoprlog/lists">操作日志</a>
            <?php endif;?>
            <?php }?>
        </dd>
    </dl>
    <script type="text/javascript">
        if ($('.hls-setting-dl a').length === 0) {
            $('.hls-setting-dl').hide();
        }
    </script>
    <?php ?>
</div>
