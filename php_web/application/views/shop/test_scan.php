<?php include 'header.php';?>
<style type="text/css">

</style>
<div class="hls-page">
    <input type="text" name="lecode" id="lecode">
    <button onclick="scan()">Scan</button>
</div>
<script type="text/javascript">
var lat = 0;
var lng = 0;


function scan() {
    var lecode = $('#lecode').val();
    if (lecode === '') {
        alert('No lecode!');
        return;
    }

    $.post('/scan/analyse/' + lecode, {"mch_id": 0, "role": 1, "action": 0}, function(resp) {
        console.log(resp);
        if (resp.errcode !== 0) {
            alert(resp.errmsg);
        } else {
            takeActivity();
        }
    }).fail(function(err) {
        if (err.status !== 500) {
            console.log(err.responseJSON);
        } else {
            console.log(err);
        }
    });
}

function takeActivity() {
    var netErrorCallback = function(err) {
        $('title').text('扫描结果');
        hls.util.Dialog.closeLoading();
        $('#error_message').text('无法连接服务器');
        $('#error_section').show();
    };
    var params = {
        // code: lecode,
        pos: {lat: lat, lng: lng},
        // role: hls.enum.Role.Waiter,
        // openid: openid,
        // common_openid: common_openid
    };
    $.get('/activity/api/activity.match', params, function(resp_) {
        if (resp_.errcode) {
            $('title').text('扫描结果');
            hls.util.Dialog.closeLoading();
            $('#error_message').text(resp_.errmsg);
            $('#error_section').show();
            return;
        }
        $.get('/activity/api/activity.take', params, function(resp) {
            if (typeof resp === 'string') {
                alert(resp.match(/<div.+div>/m));
                return;
            };
            $('title').text('扫描结果');
            hls.util.Dialog.closeLoading();
            if (resp.errcode) {
                hls.util.Dialog.closeLoading();
                $('#error_message').text(resp.errmsg);
                $('#error_section').show();
            } else {
                $('div.hls-page').show();
                var name;
                if (resp.datatype == 0) {
                    name = resp.data.amount + '分';
                    $('.hls-prize-detail .prize-name').text('红包');
                    $('#hls_prize').text(name + '红包');
                } else if (resp.datatype == 3) {
                    name = resp.data.amount + '个';
                    $('.hls-prize-detail .prize-name').text('积分');
                    $('#hls_prize').text(name + '积分');
                } else {
                    name = resp.data.name
                    $('.hls-prize-detail .prize-name').text('乐券');
                    $('#hls_prize').text(name + '');
                }
                if (name.length > 8) {
                    name = name.substring(0, 8) + '</ br>' + name.substring(8, name.length);
                }
                $('.hls-prize-detail .amount').html(name);
            }
        }).fail(netErrorCallback);
    }).fail(netErrorCallback);
}
</script>
<?php include 'footer.php';?>