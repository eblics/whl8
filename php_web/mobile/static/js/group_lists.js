var groupLists={
	init:function(){
        if(top.location.hash!=''){
            top.common.refreshTitle('群组');
        }else{
            common.refreshTitle('群组');
        }
		this.event();
        this.searchBar();
        this.list();
        this.scrollSolution();
        
	},
	event:function(){
		$('#create_group').on('tap',this.createGroup);
        $('#join_group').on('tap',this.joinGroup);
        var obj=$('#page_group_list')[0];
        if(obj.scrollHeight>=obj.clientHeight || obj.offsetHeight>=obj.clientHeight){ 
            common.noOverScroll('#page_group_list');
        }else{
            common.noOverScroll('body');
        }
	},
    createGroup:function(){
        location.href='/group/add';
	},
    joinGroup:function(){
        location.href='/group/join';
	},
    list:function(){ 
        if(navigator.userAgent.toLowerCase().indexOf('windowswechat')!=-1){
            $('.group_list dd').off().on('tap',function(){
                var thisId=$(this).attr('data-id');
                location.href='/group/chat/'+thisId;
            });
            return;
        }
        $('.group_list dd').off().on('touchstart',function(){
            window.userMoved=false;
        }).on('touchmove',function(){
            window.userMoved=true;
        }).on('touchend',function(){
            if(!window.userMoved){
                var thisId=$(this).attr('data-id');
                location.href='/group/chat/'+thisId;
            }
        });
	},
    searchBar:function(){
        var _this=this;
        $('#page_group_list').on('focus', '#search_input', function () {
            var $weuiSearchBar = $('#search_bar');
            $weuiSearchBar.addClass('weui_search_focusing');
        }).on('blur', '#search_input', function () {
            var $weuiSearchBar = $('#search_bar');
            $weuiSearchBar.removeClass('weui_search_focusing');
            if ($(this).val()) {
                $('#search_text').hide();
            } else {
                $('#search_text').show();
            }
        }).on('input', '#search_input', function () {
            var $searchShow = $("#search_show");
            var thisVal=$(this).val();
            if (thisVal) {
                $searchShow.show();
                _this.searching(thisVal)
            } else {
                $searchShow.hide();
            }
        }).on('touchend', '#search_cancel', function () {
            $("#search_show").hide();
            $('#search_input').val('');
        }).on('touchend', '#search_clear', function () {
            $("#search_show").hide();
            $('#search_input').val('');
        });
    },
    searching:function(txt){
        $('#search_show').prepend('<div class="search_loading"></div>');
        var data={'keyword':txt};
        $.ajax({
            url: '/group/search',
            type: "POST",
            dataType: 'json',
            data: data,
            success: function(d) {
                $('#search_show .search_loading').remove();
                if(d.errcode!=0){
                    console.log(d.errmsg);
                }else{
                    var html='';
                    for(var i=0;i<d.data.length;i++){
                        html+='<div class="weui_cell"><div class="weui_cell_bd weui_cell_primary"><p>'+d.data[i].groupName+'</p></div></div>';
                    }
                    $('#search_show').html(html);
                    $('#search_show .weui_cell').off().on('tap',function(){
                        location.href='/group/join';
                    });
                }
            },
            error: function(d) {
                $('#search_show .search_loading').remove();
                console.log('请求失败，请重试');
            }
        });
    },
    scrollSolution:function(){
        var u = navigator.userAgent;
        var isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
        if(isIOS){
            $('#page_group_list').css({'position':'absolute','left': 0,'top':0,'right': 0,'bottom': 0,'overflow-x':'hidden','overflow-y':'auto'});
        }
    },
    
};
$(function(){
	groupLists.init();
});
