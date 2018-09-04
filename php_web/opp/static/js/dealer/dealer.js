var dealer = {
	init:function(){
		var _this=this;
        _this.createTable();
        $("#btnAdd").on('click',function(){
            window.location.href = '/dealer/add';
        });
	},
    createTable: function() {
        var _this = this;
        var url = "/dealer/get_dealer_data";
        var params = $.extend(hls.common.dataTable, {
            "ajax": {
                "url": url,
                "type": 'POST'
            },
            "searching":true,
            "columns": [{
                class: 'center',
                "data": null,
                render: function(data) {
                    return data.id;
                }
            },{
                class: 'center',
                "data": null,
                render: function(data) {
                    return data.name;
                }
            },{
                class:'center',
                "data":null,
                render:function(data){
                    return data.code;
                }
            },{
                class:'center',
                "data":null,
                render:function(data){
                    return data.contact;
                }
            },{
                class:'center',
                "data":null,
                render:function(data){
                    return data.phoneNum;
                }
            },{
                class:'center',
                "data":null,
                render:function(data){
                    return data.address;
                }
            },{
                class:'center',
                "data":null,
                render:function(data){
                    if(data.status == 0 || data.status == null){
                        return '正常';
                    }else if(data.status == 1){
                        return '<font color="red">锁定</font>';
                    }else if(data.status == 2){
                        return '<font color="gray">待清除</font>';
                    }else{
                        return '？？';
                    }

                }
            },{
                class:'center',
                "data":null,
                render:function(data){
                    var html = '';
                    html += '<a class="btn-text noselect blue resetpass" href="/dealer/edit/'+data.id+'">修改</a>';
                    html += '&nbsp;&nbsp;';
                    html += '<a class="btn-text noselect blue"  onclick=dealer.lockDealer("' + data.id + '")>锁定</a>';
                    return html;
                }
            }],
            "initComplete": function() {
            },
            "drawCallback": function() {
            }
        });

        $('#dealerTable').DataTable(params);
    },
    lockDealer:function(id){
        common.confirm('确认锁定',function(r){
            if(r == 1){       
                $.post('/dealer/lock_dealer',{id:id},function(res){  
                    if(res.errcode === 0){
                        common.alert('成功锁定该经销商',function(result){
                            if(result == 1){
                                document.location.reload();
                            }
                        });
                    }else{
                        common.alert('操作失败');
                        return;
                    }
                }); 
            }   
        });

    }

};
$(function(){
	dealer.init();
});