var shop_edit = {
    init:function(){
        var _this=this;
        $('#deviceIds').multiSelect({
            selectableHeader: "<div class='ms-header'>可选设备：</div>",
            selectionHeader: "<div class='ms-header'>已选设备：</div>"
        });
        $('#sub').click(function(){
            if(beforeSubmitAct()){
                _this.submit($(this).attr('data-id'));
            }
        });
    },
    submit:function(id){
        var data={
            name:$('#name').val(),
            address:$('#address').val(),
            ownerName:$('#ownerName').val(),
            ownerPhoneNum:$('#ownerPhoneNum').val(),
            deviceIds:$('#deviceIds').val()
        };
        /*if(data.name==''){
            common.alert('『门店名称』不能为空');
            return;
        }
        if(data.address==''){
            common.alert('『门店地址』不能为空');
            return;
        }
        if(data.ownerName==''){
            common.alert('『店主姓名』不能为空');
            return;
        }
        if(data.ownerPhoneNum==''){
            common.alert('『店主手机号』不能为空');
            return;
        }*/
        
        $.post("/shop/post_shop_data/"+id,data,function(d){
            if(d.errcode == 0){
                var info='添加成功';
                if(id!=0)
                    info='修改成功';
                common.alert(info,function(e){
                    if(e == 1){
                        location.href = '/shop/index';
                    }
                });
            } else {
                common.alert(d.errmsg);
            }
        });
    }
};
$(function(){
    shop_edit.init();
});