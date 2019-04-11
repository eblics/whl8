<style>
.leftbar{border-top-left-radius: 8px;border-top-right-radius: 8px;}
.tabs{width:100%;height:40px;line-height:40px;background:#f9fafb;border-top-left-radius: 8px;border-top-right-radius: 8px;}
.tabs>li{float: left;display: block;width: 50%;text-align: center;font-size:16px;cursor: pointer;}
.tabs>li:first-child{border-top-left-radius: 8px;}
.tabs>li:last-child{border-top-right-radius: 8px;}
.tab_current{background:#fff;}
</style>
<div class="leftbar">
    <?php if (isDev()):?>
    <ul class="tabs">
        <li class="tab_current" data-identtity="0">消费者</li>
        <li data-identtity="1">服务员</li>
    </ul>
    <?php endif;?>
    <dl>
        <dt>扫码统计</dt>
        <dd>
            <a class="<?=get_current_router(1)=='reporting' || get_current_router(1)=='charts' && in_array(get_current_router(2),['index','userscan','userscanlists','show_user_info','show_scan_info','show_redpack_info','show_trans_info']) ? 'cur' : ''?>" href="/charts/userscan">用户扫码统计</a>
            <a class="<?=get_current_router(1)=='charts' && in_array(get_current_router(2),['period']) ? 'cur' : ''?>" href="/charts/period">时段扫码统计</a>
        </dd>
    </dl>
    <?php if($_SESSION['expired']==null){?>
    <dl>
        <dt>地图分析</dt>
        <dd>
            <a class="<?=get_current_router(1)=='charts' && in_array(get_current_router(2),['region']) ? 'cur' : ''?>" href="/charts/region">区域分布</a>
            <a class="<?=get_current_router(1)=='charts' && in_array(get_current_router(2),['scan']) ? 'cur' : ''?>" href="/charts/scan">热力图</a>
        </dd>
    </dl>
    <dl class="last">
    	<dt>营销分析</dt>
    	<dd>
            <?php if (! isProd() || $_SESSION['mchId'] == 173): ?>
            <a class="<?=get_current_router(1)=='charts' && in_array(get_current_router(2),['wusu_report_for_score']) ? 'cur' : ''?>" href="/charts/wusu_report_for_score">积分核对</a>
            <?php endif;?>
            <a class="<?=get_current_router(1)=='charts' && in_array(get_current_router(2),['business']) ? 'cur' : ''?>" href="/charts/business">业务分析</a>
            <a class="<?=get_current_router(1)=='charts' && in_array(get_current_router(2),['useranalysis']) ? 'cur' : ''?>" href="/charts/useranalysis">新老用户分析</a>
            <a class="<?=get_current_router(1)=='charts' && in_array(get_current_router(2),['userrank']) ? 'cur' : ''?>" href="/charts/userrank">用户排行</a>
            <a class="<?=get_current_router(1)=='charts' && in_array(get_current_router(2),['policy']) ? 'cur' : ''?>" href="/charts/policy">活动评估</a>
            <a class="<?=get_current_router(1)=='charts' && in_array(get_current_router(2),['trend']) ? 'cur' : ''?>" href="/charts/trend">对比分析</a>
            <a class="<?=get_current_router(1)=='charts' && in_array(get_current_router(2),['portrait']) ? 'cur' : ''?>" href="/charts/portrait">消费者画像</a>
        </dd>
    </dl>
    <?php }?>
</div>
