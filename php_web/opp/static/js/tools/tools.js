$(function() {
	var fileName = '';
	$('#btn_submit').click(function() {
		// 1.创建一个FormData对象，直接把我们的表单传进去  
       	var formData = new FormData(document.forms.namedItem("upload_form"));
       
       	// 2.通过jquery发送出去
       	$.ajax({
           	url: "/api/tools/handle_upload_file",
           	type: "POST",
           	data: formData,
           	processData: false,  // 告诉jQuery不要去处理发送的数据
           	contentType: false   // 告诉jQuery不要去设置Content-Type请求头
       	}).done(function(resp) {
           	if (resp.errcode === 0) {
           		fileName = resp.data.fileName;
           		$('#openid_nums').text('openid数量：' + resp.data.openidNums);
           		alert('上传成功，找到 ' + resp.data.openidNums + ' 个openid！');
           	} else {
           		alert(resp.errmsg + '！');
           	}
       	}).fail(function(err) {
           	alert('无法连接服务器！');
       	});
	});

	$('#btn_seal').click(function() {
		$.get('/api/tools/seal', {"file_name": fileName}, function(resp) {
			if (resp.errcode === 0) {
				alert('成功封杀 ' + resp.data.seal_nums + ' 个openid！');
			} else {
				alert(resp.errmsg + '！');
			}
		}).fail(function(err) {
           	alert('无法连接服务器！');
       	});
	});
});