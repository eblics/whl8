var module = {
	init:function(){
		var _this=this;
        _this.add();
        _this.dblclick();
	},
    add:function(){
       $("#btnAdd").off().on('click',function(){
            var val = $(".nameinput").val();
            $.post('/workorder/add_littbar',{val:val},function(res){
                if(res.errcode == 0){
                    var html = '<div class="littbar" value="'+res.data.res+'" title="双击删除">'+val+'</div>';
                    $(".tb3border").append(html);
                    var length1 = $(".tb3border").height();
                    var length2 = $(".littbar").length;
                    var length3 = (length2-1)*32+10;
                    if(length1<length3){
                        $(".tb3border").height(length1 +32);
                    }
                    module.dblclick();
                }else{
                    common.alert(res.errmsg);
                    return;
                }
            });
       }); 
    },
    dblclick:function(){
        $(".littbar").dblclick(function(){
            var title = $(this).text();
            var id = $(this).attr("value");
            var _that = $(this);
            common.confirm('确定删除？',function(res){
                if(res == 1){
                    $.post('/workorder/del_littbar',{id:id},function(response){
                        if(response.errcode == 0){
                            common.alert('删除成功',function(r){
                                var height = $(".tb3border").height();
                                _that.remove();
                                if(height < 66){
                                    $(".tb3border").height(33);
                                }
                                return;
                            });
                            return;
                        }else{
                            common.alert(response.errmsg);
                            return;
                        }
                    });
                }
            });
        });
    }
};
$(function(){
	module.init();
});