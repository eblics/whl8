<!DOCTYPE html>
<html>
<head>
    <title>红码</title>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no, address=no">
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <style type="text/css">
        .loadwx .error{height:2rem;line-height:2rem;font-size:1.4rem;padding:0.5rem 0;text-align:center;color:#c00;margin-top:5rem;}
        .loadwx .txt{height:2rem;line-height:2rem;font-size:1.4rem;padding:0.5rem 0;text-align:center;color:#333;}
        .loadwx .txt1{font-size:1.1rem;color:#666;}
        .loadwx .btn-next{height:3.4rem;line-height:3.4rem;width:200px;margin:6rem auto 0 auto;background:#c00;border-radius:3rem;color:#fff; text-align:center;font-size:1.3rem;}
        .loadwx .btn-next:active{background:#900;color:#ddd;}
    </style>
</head>
<body>
<div class="wrapper loadwx">
    <div class="error">请使用微信扫描二维码</div>
    <div class="txt txt1">使用方法：打开微信 - 发现 - 扫一扫</div>
    <div class="txt">如果您还没有安装微信，现在就来安装吧</div>
    <div class="btn-next" id="btn-next">去安装微信</div>
    </form>
</div>
<script type="text/javascript">
document.addEventListener("touchmove",function(e){
    e.preventDefault();
    e.stopPropagation();
},false);

document.getElementById('btn-next').ontouchend=function(){
    location.href="http://weixin.qq.com/cgi-bin/readtemplate?t=w_down";
};
</script>
</body>
</html>
<?php
exit;
?>
