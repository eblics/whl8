var mallGoods = {
	init:function(){
		var _this=this;
        if($('.content').children('div').hasClass('main-tip')){
            $('.tip-btn').off().on('click',function(){
                window.location.href = "/mall/configure";
            });
        }else{
            _this.createTable();
        }
        window.cacheData = null;
	},
    createTable:function(){
        var _this = this;
        $('#goodsTable').DataTable({
            "searching": true,
            "ordering":  false,
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "paging": true,
            "stateSave": true,
            "ajax": {"url": "/mall/get_goods"},
            "columns": [
                { 
                    "data":null,
                    "class":"center",
                    "render": function(data, type, row){
                        return data.id;
                    }
                },
                {
                    "data":null,
                    "class":"center",
                    "render":function(data, type, row){
                        return '<div gid="'+data.id+'" style="width:150px;">'+data.goodsName+'</div>';
                    }
                },
                {
                    "data":null,
                    "class":"center",
                    "render":function(data, type, row){
                        return '<div><img style="width:50px;height:50px;" src="'+data.path+'"></div>';
                    }
                },
                // {
                //     "data":null,
                //     "class":"center edit oPrice",
                //     "render":function(data, type, row){
                //         return data.oPrice;
                //     }
                // },
                {
                    "data":null,
                    "class":"center edit price",
                    "render":function(data, type, row){
                        if (data.exchangeType == 1) {
                            return '乐券兑换';
                        }
                        return data.price;
                    }
                },
                {
                    "data":null,
                    "class":"center",
                    "render":function(data, type, row){
                        return data.name;
                    } 
                },
                {
                    "data":null,
                    "class":"center",
                    "render":function(data,type,row){
                        return data.createTime;
                    }
                },
                // {
                //     "data":null,
                //     "class":"center",
                //     "render":function(data, type, row){
                //         // return data.rowStatus;
                //         if(data.rowStatus == 0){
                //             return '<font color="green">正常</font>';
                //         }
                //     }  
                // },
                {
                    "data":null,
                    "class":"center",
                    "render":function(data,type,row){
                        return '<a href="/mall/edit/id/'+data.id+'"><font color="blue">编辑</font></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="mallGoods.delete('+data.id+',this)"><font color="blue">删除</font></a>';
                    }
                }
            ],
            "initComplete": function () {
                _this.action();
                common.autoHeight();
            },
            "drawCallback":function(){
                _this.action();
                common.autoHeight();
            }
        });
         
    },
    action:function(){
        $("#goodsTable tbody tr .edit").each(function(){
            if($(this).text()=='乐券兑换'){
                $(this).removeClass('edit');
            }
        });
        $("#goodsTable tbody tr .edit").off().on('dblclick',function(){
            var _this = this;
            if($(this).attr('forbiden') == undefined){
                var text = $(this).text();
                window.cacheData = text;
                var html = '<input type="text" style="border:1px solid #5A8EDD;border-radius:2px;height:20px;width:80px;" id="thisval" value="'+text+'" name="thisval"><button style="border:none;margin-left:3px;background:#5A8EDD;border-radius:2px;width:50px;color:white;line-height:20px;" id="confirm" onclick="mallGoods.confirm(this)">确定</button><button id="cancle" style="border:solid 1px #CCCCCC;background:#EEEEEE;border-radius:2px;width:50px;line-height:20px;color:#999999;margin-left:5px;" onclick="mallGoods.cancle(this)">取消</button>';
                $(this).attr('forbiden','yes');
                $(this).html("");
                $(this).append(html);
            }
        });
        $('#confirm').off().on('click',function(){
            mallGoods.confirm();
        }); 
        $('#cancle').off().on('click',function(){
            mallGoods.cancle();
        });  
    },
    confirm:function(target){
        var reg = /^[0-9]{1,9}$/;
        var id = $(target).parent().parent().children('td:first').text();
        if($(target).parent().hasClass('oPrice')){
            var p = 'oPrice';
        }
        if($(target).parent().hasClass('price')){
            var p = 'price';
        }
        var price = $.trim($(target).parent().children('input').val());
        if(!reg.test(price)){
            common.alert('请填写1-10位的正整数');
            return;
        }
        $("#goodsTable tbody tr .edit").removeAttr('forbiden');
        $.post('/mall/ajax_update_price',{id:id,integralname:p,price:price},function(res){
            if(res.errcode == 0){
                $(target).parent().children().hide();
                $(target).parent().text(price);
                $(target).parent().empty();
            }
            if(res.errcode != 0){
                common.alert(res.errmsg);
                return;
            }
        });
    },
    cancle:function(target){
        var val = $(target).parent().children('input').val();
        $(target).parent().addClass('addClass1');
        $(target).parent().empty();
        if (window.cacheData) {
        	val = window.cacheData;
        }
        $('.addClass1').text(val);
        $('.addClass1').removeClass('addClass1');
        $("#goodsTable tbody tr .edit").removeAttr('forbiden');
    },
    "delete":function(id,target){
        var d = $(target).text();
        var name = $(target).parent().parent().parent().find('td').eq(1).children('div').text();
        common.confirm('确认删除？',function(e){
            if(e == 1){
                common.loading();
                $.post('/mall/delete',{id:id,name:name},function(response){
                    if(response.errcode == 0){
                        common.unloading();
                        $(target).parent().parent().addClass('selected');
                        var table=$('#goodsTable').DataTable();
                        table.row('.selected').remove().draw(false);
                        common.autoHeight();
                    }else{
                        common.unloading();
                        common.alert(response.errmsg);
                    }
                },'json');
            }
        });
    }

}
$(function(){
	mallGoods.init();
});