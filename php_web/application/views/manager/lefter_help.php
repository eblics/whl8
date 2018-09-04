
<div class="leftbar">
    <dl>
        <dt>微信帮助</dt>
        <dd>
            <a class="<?=get_current_router(1)=='service' && get_current_router(2)=='help_read' ? 'cur' : ''?>" href="/service/help_read">必看</a>
            <a class="<?=get_current_router(1)=='service' && get_current_router(2)=='gongzhong' ? 'cur' : ''?>" href="/service/gongzhong">微信公众平台</a>
            <a class="<?=get_current_router(1)=='service' && get_current_router(2)=='renzheng' ? 'cur' : ''?>" href="/service/renzheng">微信认证</a>
            <a class="<?=get_current_router(1)=='service' && get_current_router(2)=='zhifu' ? 'cur' : ''?>" href="/service/zhifu">微信支付</a>
            <a class="<?=get_current_router(1)=='service' && get_current_router(2)=='enable' ? 'cur' : ''?>" href="/service/enable">微信企业支付开通</a>
            <a class="<?=get_current_router(1)=='service' && get_current_router(2)=='limit' ? 'cur' : ''?>" href="/service/limit">支付限额修改</a>
        </dd>
    </dl>
    <dl>
    	<dt>系统帮助</dt>
    	<dd>
            <a class="<?=get_current_router(1)=='service' && get_current_router(2)=='token' ? 'cur' : ''?>" href="/service/token">微信授权</a>
            <a class="<?=get_current_router(1)=='service' && get_current_router(2)=='document' ? 'cur' : ''?>" href="/service/document">用户手册</a>
    		<a class="<?=get_current_router(1)=='service' && get_current_router(2)=='qanda' ? 'cur' : ''?>" href="/service/qanda">Q&A</a>
    	</dd>
    </dl>
</div>
