<?php include 'common/header.php'; ?></head>
<body><?php include 'common/menus.php';?>
    <style type="text/css">
        input[type=file] {
            border: 1px solid #CCC;
            padding: 5px 10px;
        }
        span {
            color: red;
            font-size: 16px;
            padding: 10px;
        }
        button {
            border: 1px solid;
            color: blue;
            margin: 20px 0;
            cursor: pointer;
        }
        button:hover {
            background-color: blue;
            color: white;
        }
    </style>
    <div class="main">
    <?php include 'admin_lefter.php';?>
    <div class="rightmain">
        <div class="path"><span class="title fleft">禁封账号</span></div>
        <div class="h20"></div>
        <div class="content">
            <form name="upload_form" enctype="multipart/form-data">
            <input type="file" name="upfile" ><span id="openid_nums">openid数量：0</span>
            </form>
            <button id="btn_submit">上传数据文件</button>
            <button id="btn_seal">封杀</button>
        </div>
    </div>
<script type="text/javascript" src="/static/js/tools/tools.js"></script>
<?php include 'common/footer.php';?>