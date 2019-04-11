<?php define('cdn_ver','1.0');
$this->config->set_item('cdn_m_url',$this->config->item('mobile_url'));
?>
<!DOCTYPE html>
<html>
<head>
    <title>红码</title>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no, address=no">
    <link type="text/css" rel="stylesheet" href="<?=$this->config->item('cdn_m_url')?>static/css/common.css?v=<?=cdn_ver?>" />
    <script type="text/javascript" src="<?=$this->config->item('cdn_m_url')?>static/js/jquery-2.1.1.min.js?v=<?=cdn_ver?>"></script>
    <style type="text/css">
        .captcha .error{height:2rem;line-height:2rem;font-size:1.5rem;padding:0.5rem 0;text-align:center;color:#c00;margin-top:2rem;}
        .captcha .txt{height:2rem;line-height:2rem;font-size:1.5rem;padding:0.5rem 0;text-align:center;color:#333;}
        .captcha .img{text-align:center;overflow:hidden;}
        .captcha .img img{width:200px;height:70px;}
        .captcha .refresh{height:1.5rem;line-height:1.5rem;font-size:1rem;text-align:center;}
        .captcha .refresh span{color:#666;}
        .captcha .refresh:active span{color:#c00;}
        .captcha .input{height:3rem;text-align:center;margin:1.5rem 0;}
        .captcha .input input{width:200px;height:3rem;font-size:1rem;border:none;border-bottom:1px solid #ccc;border-radius:0.2rem;text-align:center;}
        .captcha .btn-next{height:3.4rem;line-height:3.4rem;width:200px;margin:4rem auto 0 auto;background:#c00;border-radius:3rem;color:#fff; text-align:center;font-size:1.3rem;}
        .captcha .btn-next:active{background:#900;color:#ddd;}
    </style>
</head>
<body>
<div class="wrapper captcha">
    <form id="form" method="post">
    <div class="error"><?=$errmsg?></div>
    <div class="txt">正确输入以下验证码才能进入活动</div>
    <div class="img"><img src="/code/captcha"/></div>
    <div class="refresh"><span>看不清？点击换一组试试</span></div>
    <div class="input"><input id="input" name="captcha" type="text" placeholder="在此输入验证码"/></div>
    <div class="btn-next">下一步</div>
    </form>
</div>
<script type="text/javascript">
document.addEventListener("touchmove",function(e){
    e.preventDefault();
    e.stopPropagation();
},false);
$('.img,.refresh').on('touchend',function(){
    $('.img img').attr('src','/code/captcha?t='+new Date().getTime());
});
$('.btn-next').on('touchend',function(){
    var val=$('#input').val();
    if($.trim(val)=='') return;
    $('#form').submit();
});
$('#input').on('input',function(){
    $('.error').html('');
});
</script>
</body>
</html>
<?php exit(); ?>
