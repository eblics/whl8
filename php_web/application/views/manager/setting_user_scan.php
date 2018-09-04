<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/setting.css">
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="content page_setting">
                <ul class="tab">
                    <li class="noselect">扫码频率</li>
                    <li class="noselect">安全预警</li>
                    <li class="current noselect">扫码次数</li>
                </ul>
                <div class="tab_con" id="freq">
                    <div class="padding">
                        <div class="table-form">
                            <div class="h10"></div>
                            <div class="tip"></div>
                            <div class="h20"></div>
                            <div class="tr">
                                <input id="mch_id" type="hidden" name="id" value="<?=$mch_id?>" />
                                	扫码他人的码次数不得超过
                                <input id="scan_times_input" type="text" name="times" class="input" value="<?=$times?>" style="width:60px; text-align:center;"/> 次
                                &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-extra text-red">（超过设定次数将做封号处理）</span>
                                <p style="color: #999;margin-top: 10px;">注：如果不做限制此处请填写0</p>
                            </div>
                            <div class="h30"></div>
                            <div class="btn btn-blue noselect btnsave">保存</div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php';?>

    <script type="text/javascript">
        $('.tab li:eq(0)').on('click',function(){
           window.location.href='/setting/guard';
        });
        $('.tab li:eq(1)').on('click',function(){
           window.location.href='/setting/warning';
        });

        $('#scan_times_input').on('keydown keyup paste blur focus', function() {
            var val = $(this).val();
            if (isNaN(val) || val.indexOf('.') != -1 || val.indexOf(' ') != -1) {
                $(this).val('');
            }
        });

        $('.btnsave').click(function() {
            var times = $('#scan_times_input').val();
            if (times === '') {
                common.alert('请输入正确的限制次数！');
            } else {
                save({"mch_id": $('#mch_id').val(), "times": times});
            }
        });

        function save(params) {
            $.post('/setting/save_user_scan', params, function(resp) {
                if (resp.errcode === 0) {
                    common.alert('保存成功！');
                } else {
                    common.alert(resp.errmsg + '！');
                }
            }).fail(function(err) {
                common.alert('无法连接服务器！');
            });
        }
    </script>
</body>
</html>