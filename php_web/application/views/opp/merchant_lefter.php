<div class="leftbar">
	<dl>
		<dt>企业管理</dt>
		<dd>
			<a class="<?=$value == 7? 'cur': ''?>" href="/merchant/add">企业添加</a>
			<a class="<?=$value == 0? 'cur': ''?>" href="/merchant?status=0">企业列表</a>
			<a class="<?=$value == 1? 'cur': ''?>" href="/merchant?status=1">已审列表</a>
			<a class="<?=$value == 4? 'cur': ''?>" href="/merchant?status=4">待审企业</a>
			<a class="<?=$value == 2? 'cur': ''?>" href="/merchant?status=2">驳回企业</a>
			<a class="<?=$value == 3? 'cur': ''?>" href="/merchant?status=3">冻结企业</a>
		</dd>
	</dl>
    <?php if (! isProd()): ?>
    <dl>
		<dt>门店管理</dt>
		<dd>
    		<a class="<?=in_array(get_current_router(1),['shop']) && in_array(get_current_router(2),['index','shop_detail']) ? 'cur': ''?>" href="/shop/index">门店列表</a>
    		<a class="<?=in_array(get_current_router(1),['shop']) && in_array(get_current_router(2),['device']) ? 'cur': ''?>" href="/shop/device">设备列表</a>
    		<a class="<?=in_array(get_current_router(1),['shop']) && in_array(get_current_router(2),['permission','permission_detail']) ? 'cur': ''?>" href="/shop/permission">企业授权</a>
    		<a class="<?=in_array(get_current_router(1),['shop']) && in_array(get_current_router(2),['examine','examine_detail']) ? 'cur': ''?>" href="/shop/examine">门店审批</a>
		</dd>
	</dl>
    <?php endif;?>
    <dl>
        <dt>代理管理</dt>
        <dd>
            <a class="<?=uri_string() == 'dealer/add'? 'cur': ''?>" href="/dealer/add">代理添加</a>
            <a class="<?=uri_string() == 'dealer/lists'? 'cur': ''?>" href="/dealer/lists">代理列表</a>
        </dd>
    </dl>
    <dl>
    	<dt>消息群发</dt>
    	<dd>
    	<a id="getid" class="<?=uri_string() == 'merchant/send_ms'? 'cur': ''?>" 
    	href="/merchant/send_ms">消息发送</a>
    	</dd>
    </dl>
    <dl>
    	<dt class="last">用户管理</dt>
    	<dd>
    		<a class="<?=uri_string() == 'merchant/search'? 'cur': ''?>" href="/merchant/search">搜索用户</a>
    		<!-- <a class="<?=uri_string() == 'merchant/lock_list'? 'cur': ''?>" href="/merchant/lock_list">锁定列表</a> -->
    		<a class="<?=uri_string() == 'merchant/appeal_list'? 'cur': ''?>" href="/merchant/appeal_list">申请列表</a>
            <!-- <a class="<?=uri_string() == 'merchant/white_list'? 'cur': ''?>" href="/merchant/white_list">白名单</a> -->
    		<a class="<?=uri_string() == 'merchant/black_list'? 'cur': ''?>" href="/merchant/black_list">黑名单</a>
    	</dd>
    </dl>
</div>