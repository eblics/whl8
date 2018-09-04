<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no, address=no">
    <link type="text/css" rel="stylesheet" href="<?=$this->config->item('cdn_m_url')?>static/css/common.css" />
    <link type="text/css" rel="stylesheet" href="<?=$this->config->item('cdn_m_url')?>static/css/errortip.css" />
    <script type="text/javascript" src="<?=$this->config->item('cdn_m_url')?>static/js/jquery-2.2.0.min.js"></script>
    <script type="text/javascript" src="<?=$this->config->item('cdn_m_url')?>static/js/error.js"></script>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
</head>
<body onstart="">
<form method="post">
<textarea id="menu_data" name="menu_data" style="width:400px;height:400px;"><?=$menu_data?></textarea><br/>
<input type="submit" id="create" name="create" value="创建"></input>
</form>
</body>
</html>
