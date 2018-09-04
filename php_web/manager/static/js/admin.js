/**
 * 账户管理界面js逻辑
 * 
 * @author shizq
 * 
 */
var adminList = {

	init: function() {
		this.createTable();
	},

	bindEvent: function() {
		var _this = this;
		$('#admin_list_container tbody td .del').off('click').on("click", function() {
			$('#admin_list_container tr').removeClass('selected');
			$(this).parent('td').parent('tr').addClass('selected');
			var admin_id = $(this).attr('data-id');
			common.confirm('确定删除吗？', function(confirmed) {
				if (confirmed == 1) {
					_this.delAdmin(admin_id);
				}
			});
        });

        $('#admin_list_container tbody td .freeze').off('click').on("click", function() {
			$('#admin_list_container tr').removeClass('selected');
			$(this).parent('td').parent('tr').addClass('selected');
			var admin_id = $(this).attr('data-id');
			var lock = $(this).attr('data-status');
			var msg = '确定锁定此账户吗？';
			if (lock == 0) {
				msg = '确定解锁此账户吗？';
			}
			common.confirm(msg, function(confirmed) {
				if (confirmed == 1) {
					_this.freezeAdmin(admin_id, lock);
				}
			});
        });
	},

	/**
	 * 删除一个管理员账户
	 * 
	 * @param  int admin_id 管理员ID
	 * 
	 */
	delAdmin: function(admin_id) {
		var params = {"admin_id": admin_id};
	    common.loading();
	    $.post('/admin/del', params, function(resp) {
	        common.unloading();
	        if (resp.errcode == 0) {
	            var table = $('#admin_list_container').DataTable();
	            table.row('.selected').remove().draw(false);
	            common.autoHeight();
	        } else {
	            common.alert(resp.errmsg);
	        }
	    });
	},

	/**
	 * 锁定或解锁一个管理员账户
	 * 
	 * @param  int admin_id 管理员ID
	 * 
	 */
	freezeAdmin: function(admin_id, lock) {
		var self = this;
		var params = {"admin_id": admin_id, "lock": lock};
	    common.loading();
	    $.post('/admin/freeze', params, function(resp) {
	        common.unloading();
	        if (resp.errcode == 0) {
	        	if (lock == 0) {
					$('#admin_list_container tr.selected .state span').text('正常');
					$('#admin_list_container tr.selected .freeze').text('锁定');
					$('#admin_list_container tr.selected .freeze').attr('data-status', 2);
	        	}
	        	if (lock == 2) {
	        		$('#admin_list_container tr.selected .state span').text('已锁定');
	        		$('#admin_list_container tr.selected .freeze').text('解锁');
	        		$('#admin_list_container tr.selected .freeze').attr('data-status', 0);
	        	}
	        	$('#admin_list_container tr').removeClass('selected');
	        } else {
	            common.alert(resp.errmsg);
	        }
	    }).error(function(error) {
	    	common.alert('网络错误！');
	    });
	},

	/**
	 * DataTable表格数据加载初始化
	 * 
	 */
	createTable: function() {
		var _this = this;
		var mch = $('#mch').val();
		if(mch == 0){
			var data = [{
	                    "data": 'id', "class":"center"
	                },
	                {
	                    "data": 'realName', "class":"center"
	                }, 
	                {
	                    "data": 'phoneNum', "class":"center"
	                },
		            {
		                "data": 'roleName', "class":"center"
		            },
	                {
	                    "data": 'status', "class":"center state",
	                    render: function(data) {
	                    	var content;
	                    	if (data == 0 || data == 1) {
	                    		content = '<span class="gray">正常</span>';
	                    	}
	                    	if (data == 3) {
	                    		content = '<span class="gray">已删除</span>';
	                    	}
	                    	if (data == 2) {
	                    		content = '<span class="gray">已锁定</span>';
	                    	}
	                    	return content;
	                    }
	                },
	                {
	                	"data":null,"class":"center",render:function(data){
	                		if(data.noSms == 1){
	                			return "是";
	                		}else if(data.noSms == 0){
	                			return '否';
	                		}else{
	                			return '未设置';
	                		}
	                	}
	                },
                	{
	                    "data": null,
	                    "class":"right noselect nowrap",
	                    render: function(data) {
	                        var edit = '<a class="btn-text noselect blue" href="/admin/edit?id=' + data.id + '">修改</a>　';
	                       	var freeze;
	                       	if (data.status == 0 || data.status == 1) {
	                       		freeze = '<span class="btn-text noselect freeze" data-status="2" data-id="' + data.id + '">锁定</span>　';
	                       	}
	                       	if (data.status == 2) {
	                       		freeze = '<span class="btn-text noselect freeze" data-status="0" data-id="' + data.id + '">解锁</span>　';
	                       	}
	                       	var del = '<span class="btn-text noselect del gray" data-id="' + data.id + '">删除</span>';
	                       	return edit + freeze + del;
                    	}
                	}]
		}else{
			var data = [{
	                    "data": 'id', "class":"center"
	                },
	                {
	                    "data": 'realName', "class":"center"
	                }, 
	                {
	                    "data": 'phoneNum', "class":"center"
	                },
		            {
		                "data": 'roleName', "class":"center"
		            },
	                {
	                    "data": 'status', "class":"center state",
	                    render: function(data) {
	                    	var content;
	                    	if (data == 0 || data == 1) {
	                    		content = '<span class="gray">正常</span>';
	                    	}
	                    	if (data == 3) {
	                    		content = '<span class="gray">已删除</span>';
	                    	}
	                    	if (data == 2) {
	                    		content = '<span class="gray">已锁定</span>';
	                    	}
	                    	return content;
	                    }
	                },
                	{
	                    "data": null,
	                    "class":"right noselect nowrap",
	                    render: function(data) {
	                        var edit = '<a class="btn-text noselect blue" href="/admin/edit?id=' + data.id + '">修改</a>　';
	                       	var freeze;
	                       	if (data.status == 0 || data.status == 1) {
	                       		freeze = '<span class="btn-text noselect freeze" data-status="2" data-id="' + data.id + '">锁定</span>　';
	                       	}
	                       	if (data.status == 2) {
	                       		freeze = '<span class="btn-text noselect freeze" data-status="0" data-id="' + data.id + '">解锁</span>　';
	                       	}
	                       	var del = '<span class="btn-text noselect del gray" data-id="' + data.id + '">删除</span>';
	                       	return edit + freeze + del;
                    	}
                	}]
		}
		$('#admin_list_container').DataTable({
            "language": {
            	"url": "/static/datatables/js/dataTables.language.js"
            },
            "paging": true,
            "ordering": false,
            "order": [[0,'desc']],
            "info": true,
            "stateSave": false,
            "searching": true,
            "ajax": {
                "url": "/admin/get"
            },
            "columns": data,

            initComplete: function() {
                _this.bindEvent();
                common.autoHeight();
            },

            drawCallback: function() {
                _this.bindEvent();
                common.autoHeight();
            }
        });
	},

	/**
	 * 编辑界面初始化
	 * 
	 */
	edit: function() {
		var self = this;
		$("#btnSave").on("click", function() {
			if (beforeSubmitAct()) {
				self.save();
			}
		});

		$("#btnAddRole").on("click", function() {
			location.href = '/role/create';
		});
	},

	/**
	 * 保存管理员信息
	 * 
	 */
	save: function() {
		var editId = $('#admin_id_edit').val();
		var params = {};
		$('form input, form select').each(function(){
            var key = $(this).attr('name');
            var val = $(this).val();
            if ($.trim(key) != '') {
                params[key] = val;
            }
        });
        if (editId) {
        	params.admin_id = editId;
        }
        if($("input[type='checkbox']").is(':checked')) {
		    params.freedom = "true";
		}else{
			params.freedom = "false";
		}
		$.post('/admin/save', params, function(resp) {
			if (! resp.errcode) {
 				location.replace('/admin');
			} else {
				common.alert(resp.errmsg);
			}
		}).error(function(error) {
			common.alert('请求失败！');
		});
	}
};