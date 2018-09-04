var groupFishing={
	init:function(){
        window.currentPage=1;
        window.loading=false;
		this.event();
	},
	event:function(){
        var _this=this;
		_this.list(1);
        window.currentPage=1;
        $('.fishing_log_more').on('tap',function(){
            _this.list(window.currentPage);
        });
        $('.fishing_log_tit .btn').on('tap',function(){
            location.href=document.referrer;
        });
        
        $('#btnLog').on('tap',_this.laoLog);
        $('.app_fishing_main,.app_fishing_ren').on('touchmove',function(){
            return false;
        });
        $('.amount li').on('tap',function(){
            $(this).addClass('cur').siblings('li').removeClass('cur');
        });
        $('.wraper').scroll(function(){
        　　var scrollTop = $('.wraper')[0].scrollTop;
        　　var scrollHeight = $('.wraper')[0].scrollHeight;
        　　var windowHeight = $('.wraper').height();
        　　if(scrollTop + windowHeight > scrollHeight-100){
                if(!window.loading){
        　　　　    _this.list(window.currentPage);
                }
        　　}
        });
	},
    list:function(){
        window.loading=true;
        var t=setTimeout(function(){
            common.loading();
        },600);
        $('.fishing_log_more').hide();
        $.ajax({
            url: "/hls_app/api/fishing.logs",
            data: {'page':window.currentPage},
            type: 'post',
            dataType: 'json',
        }).done(function(d){
            window.loading=false;
            clearTimeout(t);
            common.unloading();
            if(d.errcode!=0){
                common.alert(d.errmsg);
                return;
            }
            // if(d.data.length>=10){
            //      $('.fishing_log_more').show();
            // }
            if(d.data==null){
                if(window.currentPage>1){
                    if($('.nomore').length==0){
                        $('.fishing_log').append('<li class="nomore">没有更多了</li>');
                    }
                    return;
                }
                $('.fishing_log').append('<li>没有记录</li>');
                return;
            }
            var html='';
            for(var i=0;i<d.data.length;i++){
                html+='<li class="'+d.data[i].style+'"><span>'+d.data[i].createTime+'</span><strong>'+d.data[i].title+'</strong><em>'+d.data[i].data+'</em></li>';
            }
            $('.fishing_log').append(html);
            window.currentPage++;
        }).fail(function(d){
            window.loading=false;
            clearTimeout(t_kai);
            console.log(d);
            common.unloading();
            $('.fishing_log_more').show();
        });
    }
    
};
$(function(){
	groupFishing.init();
});
