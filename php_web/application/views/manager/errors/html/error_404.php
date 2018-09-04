<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>404</title>
    <style type="text/css">
        body {
            text-align: center;
            padding: 5%;
            background-color: silver;
        }

        .logo {
            position: relative;
            top: 35px;
        }        

        .btn-404 {
            display: block;
            margin: 5% auto;
            cursor: pointer;
        }

        .txt {
            color: white;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 11px;
            position: relative;
            top: 40px;
        }

        .bg {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
        }

    </style>
</head>
<body>
<img class="logo" src="/static/images/404.png">
<p class="txt">):你要访问的页面不存在...</p>
<img class="btn btn-404" src="/static/images/404_to_index.png" onclick="location.href='/'">
<img class="bg" src="/static/images/error_cloud.png">
</body>
</html>