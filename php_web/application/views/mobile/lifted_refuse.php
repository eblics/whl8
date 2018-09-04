<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no" />
    <link type="text/css" rel="stylesheet" href="/min/?f=static/css/weui.css,static/css/group_common.css,static/css/group.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/lifted.css"/>
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/min/?f=static/js/jquery-2.1.1.min.js,static/js/iscroll-zoom.js,static/js/hammer.js,static/js/jquery.photoClip.js,static/js/group_common.js"></script>
    <script type="text/javascript" src="/static/js/lifted.js?123"></script>
    <title><?=$title?></title>
</head>
<body>
    <div class="lifted-content">
        <?php if($status == 5): ?>
            <p style="margin-top:30px;width:100%;text-align:center;color:#5A5A5D;font-size:15px;">您的帐号异常，不允许提交申诉。</p>
        <?php else: ?>
            <div style="margin-top:10px;width:100%;text-align:center;height:30px;line-height:30px;font-size:16px;">你的申诉没有通过审核，原因如下</div>
            <p style="text-align:center;width:98%;margin:0 auto;border:1px #E0B764 solid;border-radius:3px;color:#E0B764;font-size:1.5rem;font-weight:bold;"><?=$data->refuse?></p>
            <!-- <div style="margin-top:20px;width:100%;height:20px;line-height:20px;text-align:center;font-size:13px;color:#AAA7A7;">你可以点击下面的按钮重新申诉</div> -->
            <div class="lsubmit s-sub">重新提交申诉</div>
        <?php endif; ?>
    </div>
</body>
</html>