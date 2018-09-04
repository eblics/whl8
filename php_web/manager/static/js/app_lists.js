/**
 * 应用列表界面
 *
 * @author shizq
 */
$(function() {

	var currentPage = 1,
		lastPage = 0;

	function init() {
		getAllApps();
	}

	function bindEvent() {
		$('#app_container li img, #app_container li .detail').click(function() {
			var id = $(this).prop('id');
			// 跳转到应用详细页面
			location.href = '/app/desc/' + id;
		});

		// ----------------------------------------------
		// 应用APP
		$('#app_container li .apply').click(function() {
			var id = $(this).prop('id');
			applyAppInst(id);
		});

		// ----------------------------------------------
		// 查看APP
		$('#app_container li .view').click(function() {
			var id = $(this).prop('id');
			location.href = '/myapp/';
		});

	}

	// -----------------------------------------------------------------
	// 应用已经添加过的APP
	function applyAppInst(appId) {
		common.confirm('确定安装该应用吗？', function(confirm) {
			if (confirm) {
				$.post('app/re_apply', {"app_id": appId}, function(resp) {
					if (! resp.errcode) {
						common.alert('操作成功！', function() {
							location.reload();
						});
					} else {
						common.alert(resp.errmsg);
					}
				}, 'json').error(function(err) {
					common.alert('无法连接服务器！');
				});
			}
		});
	}

	// -----------------------------------------------------------------
	// 获取应用列表数据
	function getAllApps() {
		var params = {
			"all": 1,
			"current_page": currentPage
		};
		$.get('/app/get', params, function(resp) {
			if (! resp.errcode) {
 				viewAdapter(resp.data);
			} else {
				common.alert(resp.errmsg + '！');
			}
		}, 'json').error(function(err) {
			common.alert('无法连接服务器！');
		});
	}

	// -----------------------------------------------------------------
	// 界面数据渲染
	function viewAdapter(apps) {
		$('#app_container').empty();
		var row, priceStr;
		for (var i = 0; i < apps.length; i++) {
			if (! apps[i].usefull) {
				continue;
			}
			// 根据不同状态设置按钮上的文字
			if (apps[i].hold) {
				priceStr = '<button id="' + apps[i].id + '" class="btn btn-blue view">查看</button>';
			} else if (apps[i].trash) {
				priceStr = '<button id="' + apps[i].id + '" class="btn btn-blue price apply">安装</button>';
			} else {
				if (apps[i].price != 0) {
					priceStr = '<button id="' + apps[i].id + '" class="btn btn-blue price detail">￥' + apps[i].price +'</button>';
				} else {
					priceStr = '<button id="' + apps[i].id + '" class="btn btn-blue price detail">免费</button>';
				}
			}
			
			row =  '<li>';
			if (! apps[i].path) {
				apps[i].path = 'ranking';
			}
			row +=   '<div>';
			row +=   '<img id="' + apps[i].id + '" src="/static/images/' + apps[i].path + '.jpg" title="' + apps[i].desc + '" />';
			row +=     '<h2><span class="app-name">' + apps[i].name + '</span></h2>';
			row +=     '<div>'; 
			row +=       '<p>' + apps[i].desc+  '</p>';
			row +=        priceStr; 
			row +=     '</div>';
			row +=   '</div>';
			row += '</li>';
			$('#app_container').append(row);
		}
		bindEvent();
	}

	init();

});