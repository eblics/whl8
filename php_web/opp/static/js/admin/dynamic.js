/**
 * 系统日志js
 *
 * @author shizq
 */
var dynamic = {

	init: function() {
		this.createTable();
	},

	createTable: function() {
		var self = this;
		var url = '/api/admin/dynamic';
		var params = $.extend(hls.common.dataTable, {
			"ajax": {"url": url},
			"columns": [
                {"class": "center", "data": "id"},
                {"class": "center", "data": "realName", 
                "render": function(data) {
                	if (! data) {
                		return '未设置';
                	} else {
                		return data;
                	}
                }},
                {"class": "center", "data": "action"},
                {"class": "center", "data": "occTime"},
                {"class": "center", "data": "target"},
            ]
		});
		$('#opeTable').DataTable(params);
	}

};
$(function() {
	dynamic.init();
});