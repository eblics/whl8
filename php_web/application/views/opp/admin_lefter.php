<div class="leftbar">
    <dl>
    <dt>运营人员管理</dt>
    <dd><a class="<?=uri_string() == 'admin/add'? 'cur': ''?>" 
        href="/admin/add">添加账户</a>
        <a class="<?=uri_string() == 'admin'? 'cur': ''?>" 
        href="/admin">账户列表</a></dd>
    </dl>
    <dl>
    <dt>账户安全</dt>
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
