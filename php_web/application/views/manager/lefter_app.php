
<div class="leftbar">
    <dl>
        <dt>应用中心</dt>
        <dd>
            <a class="<?=get_current_router(1)=='app' ? 'cur' : ''?>" href="/app">应用市场</a>
        </dd>
        <?php if (in_array('myapp', $_SESSION['permission_modules']) || $_SESSION['role'] == ROLE_ADMIN_MASTER): ?>
        <dd>
            <a class="<?=get_current_router(1)=='myapp' ? 'cur' : ''?>" href="/myapp/lists">我的应用</a>
        </dd>
        <?php endif;?>
    </dl>
</div>
