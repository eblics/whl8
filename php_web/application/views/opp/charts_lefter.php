<div class="leftbar">
    <dl>
    <dt>报表管理</dt>
    <dd><a class="<?=uri_string() == 'charts/manage'? 'cur': ''?>" 
        href="/charts/manage">开通管理</a>
        <a class="<?=uri_string() == 'admin'? 'cur': ''?>" 
        href="/admin">报表管理</a></dd>
    </dl>
    <dl>
    <dt>不知道</dt>
    <dd><a class="<?=uri_string() == 'admin/profile'? 'cur': ''?>" 
        href="/admin/profile">账户信息</a>
    	<a class="<?=uri_string() == 'admin/passwd'? 'cur': ''?>" 
        href="/admin/passwd">密码修改</a></dd>
    </dl>
    <dl>
    <dt class="last">系统日志</dt>
    <dd><a class="<?=uri_string() == 'admin/dynamic'? 'cur': ''?>" 
        href="/admin/dynamic?time=today">最近</a></dd>
    </dl>
</div>
