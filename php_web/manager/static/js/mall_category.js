var mallCategory = {
	init:function(){
		if($('.content div').hasClass('main-tip')){
			$('.tip-btn').off().on('click',function(){
				window.location.href="/mall/configure"; 
				return;
			});
		}
		var _this=this;
		_this.createTable();
		$('#btnAdd').click(_this.addRow);
	},
	addRow:function(){
		var t=$('#categoryTable').DataTable();
		var data={
			"parentCategoryId":-1,
			"name": "新建名称",
			"desc": "新建描述",
			"level": 1
		};
		common.loading();
		$.post('/mall/add_category',data,function(r){
			common.unloading();
			if(r.errcode==0){
				data.id=r.result;
				t.row.add(data).draw();
				common.autoHeight();
				mallCategory.editTd();
				mallCategory.delTr();
				mallCategory.addSubRow();
			}else{
				common.alert(r.errmsg);
			}
			
		},'json');
	},
	addSubRow:function(){
		$('#categoryTable tbody td .addsub').off('click').on("click",function(){
			var t=$('#categoryTable').DataTable();
			var _this=$(this);
			var pid=_this.attr('data-id');
			var level=_this.attr('data-level');
            if(Number(level)==10){
                common.alert('只能添加10级分类');
                return false;
            }
			var index=t.row($(this).parent('td').parent('tr')).index();
			var data={
				"parentCategoryId":pid,
				"name": "新建子类名称",
				"desc": "新建子类描述"
			};
			common.loading();
			$.post('/mall/add_category',data,function(r){
				common.unloading();
				if(r.errcode==0){
					data.id=r.result;
					data.level=Number(level)+1;
					var newRow=$(
						'<tr>'+
							'<td class="nowrap"><em class="display-ib grayer txti'+(data.level-1)+'">├─</em> <span title="（提示：双击修改)" class="editfeild" data-id="'+data.id+'" data-name="name">'+data.name+'</span></td>'+
							'<td class="gray"><span title="（提示：双击修改)" class="editfeild" data-id="'+data.id+'" data-name="name">'+data.desc+'</span></td>'+
							'<td class="center"><span class="btn-text noselect addsub blue" data-level="'+data.level+'" data-id="'+data.id+'">添加子类</span> &nbsp;&nbsp; <span class="btn-text noselect del gray" data-id="'+data.id+'">删除</span></td>'+
						'</tr>'
					);
					t.row(index).child(newRow).show();
					var tbData = t.data();
					var newData=[];
					for(var x in tbData) {
						if(!isNaN(x)){
							newData.push(tbData[x]);
							if(x==index){
								newData.push(data);
							}
						}
					};
					t.clear();
					t.rows.add(newData).draw();
					common.autoHeight();
					mallCategory.delTr();
					mallCategory.addSubRow();
					mallCategory.editTd();
				}else{
					common.alert(r.errmsg);
				}
			},'json');
		});
	},
	editTd:function(){
		$('#categoryTable tbody td').off('dblclick').on("dblclick",function(){
            $('#categoryTable').find('.cancel').trigger('click');
			var hasEdit=$(this).find('.editfeild').length || $(this).hasClass('editfeild');
			if(hasEdit){
				var editfeild=$(this).find('.editfeild');
                var maxlength=16;
                if(editfeild.attr('data-name')=='desc'){
                    maxlength=600;                    
                }
				var val='';
				if(editfeild.find('input').length>0){
					val=editfeild.find('input').val();
				}else{
					val=editfeild.text();
					editfeild.html('<input maxlength="'+maxlength+'" data="'+val+'" value="'+val+'"/><span class="save noselect">保存</span><span class="cancel noselect">取消</span>');
                    editfeild.find('input').off().on('keydown',function(e){
                        if(e.keyCode==13){
                            editfeild.find('.save').trigger('click');
                        }
                    }).on('dblclick',function(e){
                        e.stopPropagation();
                    });
				}
				editfeild.find('.save').off().on('click',function(){
					common.loading();
					var _this=$(this);
					var id=_this.parent('.editfeild').attr('data-id');
					var tname=_this.parent('.editfeild').attr('data-name');
					var val=_this.parent('.editfeild').find('input').val();
					var cname = _this.parent().parent().parent().find('td').eq(0).children('span').text();
					var data={
						'id':id,
						cname:cname
					};
					data[tname]=val;
                    if($.trim(val)==''){
                        common.alert('不能为空');
                        common.unloading();
                        return;
                    }
					$.post('/mall/update_category',data,function(r){
						common.unloading();
						if(r.errcode==0){
							_this.parent('.editfeild').html(val);
							var t=$('#categoryTable').DataTable();
							var tbData = t.data();
							for(var x in tbData) {
								if(!isNaN(x)){
									if(tbData[x].id==id){
										tbData[x][tname]=val;
									}
								}
							};
							t.clear();
							t.rows.add(tbData).draw();
							mallCategory.delTr();
							mallCategory.addSubRow();
							mallCategory.editTd();
						}else{
							common.alert(r.errmsg);
						}
					},'json');
				});
				editfeild.find('.cancel').off().on('click',function(){
					var val=$(this).siblings('input').attr('data');
					$(this).parent('.editfeild').html(val);
				});
			}
        });
        
	},
	delTr:function(){
		$('#categoryTable tbody td .del').off().on("click",function(){
			var _this=$(this);
			var id=$(this).attr('data-id');
			var name = $(this).parent().parent().children('td').eq(0).children('span').text();
			common.confirm('确定删除吗？',function(r){
				if(r==1){
					common.loading();
					if(id){
						$.post('/mall/del_category',{'id':id,'name':name},function(d){
							common.unloading();
							if(d.errcode==0){
								// common.alert('删除成功');
								_this.parent('td').parent('tr').addClass('selected');
								var table=$('#categoryTable').DataTable();
								table.row('.selected').remove().draw(false);
								common.autoHeight();
							}else{
								common.alert(d.errmsg);
							}
						},'json');
					}
				}
			});
        });
	},
	createTable:function(){
		var _this=this;
		$('#categoryTable').DataTable({
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
			"paging":   false,
			"ordering": false,
			"info":     false,
			"searching":false,
			"ajax": {
				"url":"/mall/catedata"
			},
			"columns": [
				{
					"data":"name",
					"class":"nowrap",
					"render": function (data,type,row) {
						return '<em class="display-ib grayer txti'+(row.level-1)+'">├─</em> <span title="（提示：双击修改)" class="editfeild" data-id="'+row.id+'" data-name="name">'+data+'</span>';
					}
				},
				{
					"data":"desc",
					"class":"gray",
					"render": function (data,type,row) {
						return '<span title="（提示：双击修改)" class="editfeild" data-id="'+row.id+'" data-name="desc">'+data+'</span>';
					}
				},
				{
					"data":null,
					"class":"center",
					"render": function (data,type,row) {
						// return '<span class="btn-text noselect addsub blue" data-level="'+data.level+'" data-id="'+data.id+'">添加子类</span> &nbsp;&nbsp; <span class="btn-text noselect del gray" data-id="'+data.id+'">删除</span>';
						return '<span class="btn-text noselect del gray" data-id="'+data.id+'">删除</span>';
					}
				}
			],
			"initComplete": function () {
				_this.editTd();
				_this.delTr();
				_this.addSubRow();
				common.autoHeight();
			}
		});
		
	}
};
$(function(){
	mallCategory.init();
});