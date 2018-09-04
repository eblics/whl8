/**
 * 乐券持有者界面
 *
 * @author shizq
 */
var Page = {

    cardId: $('#card_id').val(),

    init: function() {
        this.bindEvent();
        this.createTable();
    },

    bindEvent: function() {

    },

    createTable: function() {
        var columns = [
            {
                "data": "user_id", "class": "center"
            },
            {
                "data": "role_str", "class": "center"
            },
            {
                "data": "nickname", "class": "center"
            },
            {
                "data": "realname", "class": "center"
            },
            {
                "data": "mobile", "class": "center"
            },
            {
                "data": "num", "class": "center"
            },
        ];
        var config = {
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,
            "paging":   true,
            "order":     [[0, 'desc']],
            "searching": false,
            "stateSave": true,
            "info":      true,
            "serverSide": true,
            "stateSave": true,
            "processing": true,
            "ajax": '/card/holder_list?card_id=' + Page.cardId,
            "columns": columns
        };
        $('#card_holder_table').DataTable(config);
    }
};

var Service = {

    netError: function(err) {
        common.alert('无法连接服务器！');
    },

};
$(function() {
    Page.init();
});