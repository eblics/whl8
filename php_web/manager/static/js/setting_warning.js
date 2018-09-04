var settingWarning = {
	init:function(){
        this.btnEvent();
	},
    btnEvent:function(){
        var _this=this;
        $('.tab li:eq(0)').on('click',function(){
           window.location.href='/setting/guard';
        });
        $('.tab li:eq(2)').on('click',function(){
           window.location.href='/setting/user_scan';
        });
        $('.del button').on('click',function(){
            var id = $(this).parent().parent().children().eq(0).text();
            var _this = $(this).parent().parent();
            common.confirm('确认删除？',function(res){
                if(res == 1){
                    $.post('/setting/del_user',{id:id},function(res){
                        console.log(res);
                        if(res.errcode == 0){
                            common.alert('删除成功！');
                            _this.remove();
                        }else{
                            common.alert(res.errmsg);
                            return;
                        }
                    });
                }
            });
            
        });
        $('.btnsave').on('click',function(){
            var tabindex=$('.tab li.current').index();
            _this.saveData(tabindex);
        });
        $('.search-user button').on('click',function(res){
            var val = $(this).parent().children('input').val();
            $('.search-list').children().remove();
            if(val.length == 0){
                common.alert('请输入需要查询的昵称！');
                return;
            }else{
                $.post('/setting/find_user',{nickName:val},function(result){
                    if(result.errcode == 1){
                        common.alert(result.errmsg);
                        return;
                    }else{
                        var length = result.data.length;
                        if(length>10){
                            length = 10;
                            common.alert('返回结果过多，只显示前十条！');
                        }
                        for (var i = 0; i < length; i++) {
                            result.data[i];
                            var html = '<div class="exists-detail-son search-son"><div class="exists-id">'+result.data[i].id+'</div><div class="exists-nickname">'+result.data[i].nickName+'</div><div class="exists-avatar"><img src="'+result.data[i].headimgurl+'"></div><div class="exists-edit add"><button>添加</button></div></div> ';

                            $('.search-list').append(html);
                            settingWarning.add();

                        }
                        return;
                    }
                });
            }

        });
    },
    add:function(){
        $('.search-list .add button').on('click',function(){
            var _this = $(this);
            var id = $(this).parent().parent().children('div').eq(0).html();
            var html = $(this).parent().parent().html();
            html = '<div class="exists-detail-son">'+html+'</div>';
            common.confirm('确定将此用户添加到接收报警用户中？',function(d){
                if(d == 1){
                    var num = $('.exists-users .exists-id').length;
                    if(num >=5){
                        common.alert('最多添加5个用户，请删除后再添加！');
                        return;
                    }
                    $('.exists-users .exists-id').each(function(){
                        if($(this).html() == id){
                            common.alert('该用户已存在！');
                            return false;
                        }
                    });
                    $.post('/setting/add_user',{id:id},function(res){
                        if(res.errcode == 1){
                            // common.alert(res.errmsg);
                            console.log(res.data);
                            return;
                        }else{
                            common.alert('新增成功！');
                            $('.exists-users .exists-detail').append(html);
                            _this.parent().parent().remove();
                            settingWarning.edit();

                        }
                    });
                }
            });
        })
    },
	edit:function(){
        $('.exists-users button').html('删除');
        $('.exists-users .add button').on('click',function(){
            var id = $(this).parent().parent().children().eq(0).text();
            var _this = $(this).parent().parent();
            common.confirm('确认删除？',function(res){
                if(res == 1){
                    $.post('/setting/del_user',{id:id},function(res){
                        console.log(res);
                        if(res.errcode == 0){
                            common.alert('删除成功！');
                            _this.remove();
                        }else{
                            common.alert('res.errmsg');
                            return;
                        }
                    });
                }
            });
            
        });
    }
};
$(function(){
	settingWarning.init();
});