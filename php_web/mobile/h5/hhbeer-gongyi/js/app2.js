/**
 * Created by Vee on 2017/3/31.
 */

top.$.ajax({
    type: 'get',
    url: hlsjs.getRootUrl() + '/user/scan_times',
    success: function (data) {
        console.log(data);
        var total_scan_times = data.data.total_scan_times;
        var user_scan_times = data.data.user_scan_times;
        if (!!total_scan_times && !!user_scan_times) {
            $('#user_drink').html(user_scan_times + '瓶');
            $('#user_gold').html((user_scan_times * 0.01).toFixed(2) + '元');
            $('#total').html((total_scan_times * 0.01).toFixed(2));
        };
    }
});