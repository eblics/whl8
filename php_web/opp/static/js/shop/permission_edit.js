var shop_edit = {
    init:function(){
        var _this=this;
        $('#shopIds').multiSelect({
            selectableHeader: "<div class='ms-header'>可选门店：</div>",
            selectionHeader: "<div class='ms-header'>已选门店：</div>"
        });
        $('#sub').click(function(){
            if(beforeSubmitAct()){
                _this.submit($(this).attr('data-id'));
            }
        });
    },
    submit:function(id){
        var data={
            shopIds:$('#shopIds').val()
        };
        
        $.post("/shop/post_permission_data/"+id,data,function(d){
            if(d.errcode == 0){
                common.alert('授权成功',function(e){
                    if(e == 1){
                        location.href = '/shop/permission';
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