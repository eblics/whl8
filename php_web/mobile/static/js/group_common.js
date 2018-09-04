var common={
	init:function(){
		$('body').on('touchstart',function(){});
	},
	loading: function() {
        var num = $('.loading').length;
        if (num == 0) {
            var html = '<div class="loading"><div class="layer"></div><div class="gif"></div></div>';
            $('body').append(html);
        }
        $('.loading').show();
    },
    unloading: function() {
        $('.loading').hide();
    },
    alert:function(msg){
        $('.weui_dialog_alert').remove();
        var html='<div class="weui_dialog_alert" style="display: none;">';
            html+='        <div class="weui_mask"></div>';
            html+='        <div class="weui_dialog">';
            html+='            <div class="weui_dialog_hd"><strong class="weui_dialog_title">提示</strong></div>';
            html+='            <div class="weui_dialog_bd">'+msg+'</div>';
            html+='            <div class="weui_dialog_ft">';
            html+='                <a class="weui_btn_dialog primary">确定</a>';
            html+='            </div>';
            html+='        </div>';
            html+='    </div>';
        $('body').append(html);
        $('.weui_dialog_alert').show().on('tap', '.weui_btn_dialog', function () {
            $('.weui_dialog_alert').off('tap').remove();
        });
    },
    confirm:function(msg,callback){
        $('.weui_dialog_confirm').remove();
        var html='<div class="weui_dialog_confirm" style="display:none">';
        html+='<div class="weui_mask"></div>';
        html+='<div class="weui_dialog">';
        html+='    <div class="weui_dialog_hd"><strong class="weui_dialog_title">提示</strong></div>';
        html+='    <div class="weui_dialog_bd">'+msg+'</div>';
        html+='    <div class="weui_dialog_ft">';
        html+='        <a href="#" class="weui_btn_dialog default">取消</a>';
        html+='        <a href="#" class="weui_btn_dialog primary">确定</a>';
        html+='    </div>';
        html+='</div>';
        html+='</div>';
        $('body').append(html);
        $('.weui_dialog_confirm').show().on('tap','.weui_btn_dialog.default', function () {
            callback(0);
            $('.weui_dialog_confirm').off('tap').remove();
        });
        $('.weui_dialog_confirm').show().on('tap','.weui_btn_dialog.primary', function () {
            callback(1);
            $('.weui_dialog_confirm').off('tap').remove();
        });
    },
    ajaxUpload: function(feild, upUrl, callback) {
        common.loading();
        var fd = new FormData();
        var fdIf = new FormData();
        var file = feild.get(0).files[0];
        var name = file.name;
        var ext = name.substr(name.lastIndexOf(".") + 1);
        var size = file.size / 1024;
        fd.append("userfile", 1);
        fd.append("userfile", file);
        fd.append("fileSize", size);
        fd.append("fileExt", ext);
        fdIf.append("userfile", 1);
        fdIf.append("userfile", 'if');
        fdIf.append("fileSize", size);
        fdIf.append("fileExt", ext);
        function dopost(data) {
            $.ajax({
                url: upUrl,
                type: "POST",
                processData: false,
                contentType: false,
                data: data,
                success: function(d) {
                    feild.val('');
                    common.unloading();
                    if (d == 'ifok') {
                        dopost(fd);
                    } else {
                        callback(d);
                    }
                },
                error: function(d) {
                    callback(d);
                    common.unloading();
                }
            });
        }
        dopost(fdIf);

    },
    framePage:function(url,nohash){
        var fromUrl=location.href;
        if(fromUrl.indexOf('#')!==-1){
            var fromUrlArr=fromUrl.split('#');
            fromUrl=fromUrlArr[0];
            hash=fromUrlArr[1];
            if(url==hash){
                $('.frame_page').remove();
                $('body').append('<div class="frame_page"><iframe src="'+url+'"></iframe></div>');
            }
        }
        if(typeof nohash!='undefined'){
            $('.frame_page').remove();
            $('body').append('<div class="frame_page"><iframe src="'+url+'"></iframe></div>');
        }else{ 
            location.href=fromUrl+'#'+url;
        }
    },
    framePageMini:function(url){
        $('.frame_page_mini').remove();
        var html = '<div class="frame_page_mini" style="display:none"><div class="layer"></div><div class="con"><iframe src="'+url+'"></iframe></div><div class="close"></div></div>';
        $('body').append(html);
        $('.frame_page_mini').fadeIn('fast');
        $('.frame_page_mini .close').on('tap',function() {
            $('.frame_page_mini').fadeOut(function(){
                $('.frame_page_mini').remove();
            });
        });
    },
    listenHash:function(){
        var _this=this;
        var defaultHash=location.hash.replace('#','');
        if(defaultHash!=''){
            $('.frame_page').remove();
            $('body').append('<div class="frame_page"><iframe src="'+defaultHash+'"></iframe></div>');
        }
        $(window).on('hashchange',function(){
            var toPage=location.hash.replace('#','');
            if(toPage==''){
                if(location.href.indexOf('/group/chat/')){
                    $('.frame_page').remove();
                    _this.refreshTitle('群聊');
                }
                return;
            }
            $('.frame_page').remove();
            $('body').append('<div class="frame_page"><iframe src="'+toPage+'"></iframe></div>');
        });
    },
    refreshTitle:function(title){
        document.title = title+' - '+(localStorage.getItem("groupProductName")?localStorage.getItem("groupProductName"):'好友圈');
        setTimeout(function(){
            var $iframe = $("<iframe style='display:none;' src='/favicon.ico'></iframe>");
            $iframe.on('load',function() {
                setTimeout(function() {
                    $iframe.off('load').remove();
                },0);
            }).appendTo($('body'));
        },10);
    },
    preLoadImg:function(url) {
        var img = new Image();
        img.src = url;
    },
    noOverScroll:function(scrollElement){
        $(document).on('touchmove',function(e){
            e.preventDefault();
        });
        var scrolling = false;
        $('body').on('touchstart',scrollElement,function(e) {
            if (!scrolling) {
                scrolling = true;   
                if (e.currentTarget.scrollTop === 0) {
                    e.currentTarget.scrollTop = 1;
                } else if (e.currentTarget.scrollHeight === e.currentTarget.scrollTop + e.currentTarget.offsetHeight) {
                    e.currentTarget.scrollTop -= 1;
                }
                scrolling = false;
            }
        });
        $('body').on('touchmove',scrollElement,function(e) {
            e.stopPropagation();
        });
    }
}
$(function(){
	common.init();
});