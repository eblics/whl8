<div class="leftbar">
	<dl>
		<dt>工单设置</dt>
		<dd>
			<a class="<?=uri_string() == 'workorder/w_setting'? 'cur': ''?>" href="/workorder/w_setting">工单配置</a>
            <a class="<?=uri_string() == 'workorder/w_role'? 'cur': ''?>" href="/workorder/w_role">角色管理</a>
            <a class="<?=uri_string() == 'workorder/w_module'? 'cur': ''?>" href="/workorder/w_module">模块管理</a>
		</dd>
	</dl>
    <dl>
        <dt>工单管理</dt>
        <dd>
            <a class="<?=uri_string() == 'workorder'? 'cur': ''?>" href="/workorder">工单列表</a>
            <a class="<?=uri_string() == 'workorder/w_role'? 'cur': ''?>" href="/workorder/w_role">角色管理</a>
            <a class="<?=uri_string() == 'workorder/w_module'? 'cur': ''?>" href="/workorder/w_module">模块管理</a>
        </dd>
    </dl>
    <dl>
    	<dt class="last">用户管理</dt>
    	<dd>
    		<a class="<?=uri_string() == 'merchant/search'? 'cur': ''?>" href="/merchant/search">搜索用户</a>
    		<a class="<?=uri_string() == 'merchant/lock_list'? 'cur': ''?>" href="/merchant/lock_list">锁定列表</a>
    		<a class="<?=uri_string() == 'merchant/appeal_list'? 'cur': ''?>" href="/merchant/appeal_list">申请列表</a>
            <a class="<?=uri_string() == 'merchant/white_list'? 'cur': ''?>" href="/merchant/white_list">白名单</a>
    		<a class="<?=uri_string() == 'merchant/black_list'? 'cur': ''?>" href="/merchant/black_list">黑名单</a>
    	</dd>
    </dl>
</div>