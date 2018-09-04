var admin = {

	init: function() {
		this.createTable();
	},

	createTable:function(){
		var _this = this;
		var url = '/api/admin/get_admin';
		var params = $.extend(hls.common.dataTable, {
			"ajax": {"url": url},
			"columns": [
                {"data": "id", "class": "center"},
				{"data":"userName", "class": "center"},
				{
					"class":"center",
					"data":"role",
					"render": function(data) {
						if(data == 0){
							return "<b><font color='red'>系统管理员</font></b>";
						}else if(data == 1){
							return "<b>管理员</b>";
						}else if(data == 2){
							return "运营人员";
						}else{
							return "";
						}
					}
				},
				{"data":"phoneNum"},
				{"data":"createTime", "class":"center"},
				{"data":"status","class":"center","render":function(data,type,row){
					if(data == 0){
						return "未激活";
					}else if(data == 1){
						return "<font color='green'>激活</font>";
					}else if(data ==2){
						return "<font color='red'>禁用</font>";
					}else if(data == 3){
						return "<font color='gray'>删除</font>";
					}else{
						return '';
					}
				}},
				{
					"data":null,
					"class":"center",
					"render": function (data) {
						var status = data.status;
						if(status == hls.enum.AdminStatusEnum.Enable){
							var html = '<a class="btn-text noselect blue" onclick=admin.freeze("' + data.id + '")><font color="red">禁用</font></a>';
						}
						if(status == hls.enum.AdminStatusEnum.Disable || 
							status == hls.enum.AdminStatusEnum.Locked) {
							var html = '<a class="btn-text noselect blue" onclick=admin.active("' + data.id + '")><font color="gray">激活</font></a>';
						}
						if(status == hls.enum.AdminStatusEnum.Del) {
							var html = '<a class="btn-text noselect blue")><font color="gray">已删除</font></a>';
						}
						html += '&nbsp;&nbsp;';
						html += '<a class="btn-text noselect blue resetpass" onclick=admin.passwd("' + data.id + '")>重置密码</a>';
						html += '&nbsp;&nbsp;';
						html += '<a class="btn-text noselect blue" href="/admin/edit?id=' + data.id + '">修改</a>';
						html += '&nbsp;&nbsp;';
						html += '<a class="btn-text noselect blue" onclick=admin.del("'+ data.id +'")>删除</a>';
						return html;
					}
				}
			]
		}); 
		$('#opeTable').DataTable(params);
	},

	/**
	 * 禁用管理员账户
	 * 
	 * @param admin_id 管理员ID
	 */
	freeze: function(admin_id) {
		common.confirm('确认禁用此账户？', function(confirm) {
			if (confirm) {
				common.loading();
				hls.api.Admin.freeze(admin_id, function(resp) {
					common.unloading();
					common.alert('操作成功！', function() {
						location.reload();
					});
				}, function(err) {
					common.unloading();
					common.alert(err + '。');
				});
			}
		});
	},

	/**
	 * 激活管理员账户
	 * 
	 * @param admin_id 管理员ID
	 */
	active: function(admin_id) {
		common.confirm('确认激活此账户？', function(confirm) {
			if (confirm) {
				common.loading();
				hls.api.Admin.active(admin_id, function(resp) {
					common.unloading();
					common.alert('操作成功！', function() {
						location.reload();
					});
				}, function(err) {
					common.unloading();
					common.alert(err + '。');
				});
			}
		});
	},

	/**
	 * 重置管理员账户密码
	 * 
	 * @param admin_id 管理员ID
	 */
	passwd: function(admin_id) {
		common.confirm('确认将此帐户密码重置为：123456', function(confirm) {
			if (confirm) {
				common.loading();
				hls.api.Admin.reset_passwd(admin_id, function(resp) {
					common.unloading();
					common.alert('操作成功！', function() {
						// pass
					});
				}, function(err) {
					common.unloading();
					common.alert(err + '。');
				});
			}
		});
	},
	/**
	 * 删除该帐户
	 * 
	 * @param admin_id 管理员ID
	 */
	del:function(admin_id){
		var _this = this;
		common.confirm('确认删除此账号？',function(res){
			if(res){
				hls.api.Admin.del_admin(admin_id,function(resp){	
				$(".sorting_1").each(function(){
					var str = $(this).text();
					if(str == admin_id){
						$(this).parent().remove();
					}
				});
					common.unloading();
					common.alert('操作成功！');
				},function(err){
					common.unloading();
					common.alert(err + '。');
				});
			}
		});
	}

};	
$(function(){
	admin.init();
});