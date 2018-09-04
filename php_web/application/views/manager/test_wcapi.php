<!DOCTYPE html>
<html>
<head>
    <title>测试api</title>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no, address=no">
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script>
        function test_subscribe(){
             $.post('/wcapi/api','<?=$xml_subscribe?>',function(result){
                console.log(result);
            });
        }
        function test_location(){
            $.post('/wcapi/api','<?=$xml_location?>',function(result){
                console.log(result);
            });
        }
        function test_text(){
            $.post('/wcapi/api','<?=$xml_text?>',function(result){
                console.log(result);
            });
        }
    </script>
</head>
<body>
    <a href="javascript:test_location()">test_location</a><br/>
    <a href="javascript:test_text()">test_text</a>
    <a href="javascript:test_subscribe()">test_subscribe</a>
</body>
</html>