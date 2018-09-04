
<div class="leftbar">
    <dl class="last">
        <dt>信息</dt>
        <dd>
            <a class="<?=get_current_router(1)=='user' && get_current_router(2)=='person' ? 'cur' : ''?>" 
                href="/user/person">个人信息</a>
            <?php if (in_array('user', $_SESSION['permission_modules']) || $_SESSION['role'] == ROLE_ADMIN_MASTER): ?>
            <a class="<?=get_current_router(1)=='user' && get_current_router(2)=='company' ? 'cur' : ''?>" 
                href="/user/company">企业信息</a>
            <a class="<?=get_current_router(1)=='user' && get_current_router(2)=='wechat' ? 'cur' : ''?>" 
                href="/user/wechat">消费者微信</a>
            <a class="<?=get_current_router(1)=='user' && get_current_router(2)=='weixin' ? 'cur' : ''?>" 
                href="/user/weixin">供应链微信</a>
            <a class="<?=get_current_router(1)=='user' && get_current_router(2)=='tts' ? 'cur' : ''?>" 
                href="/user/tts">TTS接口</a>
            <a class="<?=get_current_router(1)=='user' && get_current_router(2)=='tcloud' ? 'cur' : ''?>" 
                href="/user/tcloud">腾讯云接口</a>
            <?php endif;?>
            <a class="<?=get_current_router(1)=='user' && get_current_router(2)=='safe' ? 'cur' : ''?>" 
                href="/user/safe">帐号安全</a>
            <?php if($_SESSION['expired']!==null){?>
            <a class="<?=get_current_router(1)=='cashier' && in_array(get_current_router(2),['renew']) ? 'cur' : ''?>" href="/cashier/renew">企业续费</a>
            <a class="<?=get_current_router(1)=='wxpay' && in_array(get_current_router(2),['balance']) ? 'cur' : ''?>" href="/wxpay/balance">企业钱包</a>
            <?php }?>
        </dd>
    </dl>
</div>
