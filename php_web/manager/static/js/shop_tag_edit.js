var tagDetail = {
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
            name:$('#name').val(),
            shopIds:$('#shopIds').val()
        };
        
        $.post("/shop/post_tag_data/"+id,data,function(d){
            if(d.errcode == 0){
                var info='添加成功';
                if(id!=0)
                    info='修改成功';
                common.alert(info,function(e){
                    if(e == 1){
                        location.href = '/shop/tag_lists';
                    }
                });
            } else {
                common.alert(d.errmsg);
            }
        });
    }
};
$(function(){
    tagDetail.init();
});