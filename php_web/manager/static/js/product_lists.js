/* global common */
var productLists = {
	init:function(){
		var _this=this;
		_this.createTable();
	},
	delTr:function(){
		$('#productTable tbody td .del').off('click').on("click",function(){
			var _this=$(this);
			common.confirm('确定删除吗？',function(r){
				if(r==1){
					common.loading();
					var id=_this.attr('data-id');
					$.post('/product/del_product',{'id':id},function(d){
						common.unloading();
						if(d.errorCode==0){
							_this.parent('td').parent('tr').addClass('selected');
							var table=$('#productTable').DataTable();
							table.row('.selected').remove().draw(false);
							common.autoHeight();
						}else{
							common.alert(d.errorMsg);
						}
					},'json');
				}
			});
        });
	},
	createTable:function(){
		var _this=this;
		$('#productTable').DataTable({
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
			"paging":   true,
			"ordering": false,
            "order":[[0,'desc']],
			"info":     true,
			"stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
			"searching":true,
			"ajax": {
				"url":"/product/prodata"
			},
			"columns": [
                {"data":"id","class":"center"},
				{"data":"name"},
				{"data":"category"},
				{
					"data":null,
					"class":"center",
					"render": function (data,type,row) {
						return '<a class="btn-text noselect blue" href="/product/edit/'+data.id+'">修改</a> &nbsp;&nbsp; <span class="btn-text noselect del gray" data-id="'+data.id+'">删除</span>';
					}
				}
			],
			"initComplete": function () {
				_this.delTr();
				common.autoHeight();
			},
            "drawCallback":function(){
                _this.delTr();
				common.autoHeight();
            }
		});
		
	}
};
$(function(){
	productLists.init();
});