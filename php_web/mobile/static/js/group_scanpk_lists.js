var groupScanpkLists={
	init:function(){
		this.event();
	},
	event:function(){
        var _this=this;
		$('#btnRule').on('tap',function(){
            if($('#rule').is(':hidden')){
                $('#rule').show();
            }else{
                $('#rule').hide();
            }
        });
        $('#rule').on('tap',function(){
            $(this).hide();
        });
        $('#btnAddPK').on('tap',function(){
            $('.wraper').hide();
            $('.app_scanpk_add').show();
        });
        $('#notMyList dd').on('tap',function(){
            var id=$(this).attr('data-id');
            _this.loadScanDetail(id);
            $('.wraper').hide();
            $('.app_scanpk_detail').show();
            if($(this).hasClass('status_1') || $(this).hasClass('status_2')){
                $('#btnJoin').hide();
            }else{
                $('#btnJoin').show();
            }
        });
        $('#myList dd').on('tap',function(){
            var id=$(this).attr('data-id');
            _this.loadScanDetail(id);
            $('.wraper').hide();
            $('.app_scanpk_detail').show();
            $('#btnJoin').hide();
        });
        /* app_scanpk_add */
        $('.app_scanpk_add input[name=input_data_type]').on('click',function(){
            var thisId=$(this).attr('id');
            if(thisId=='input_data_type_0'){
                $('#unit').html('元');
            }
            if(thisId=='input_data_type_1'){
                $('#unit').html('积分');
            }
            $('#input_data_amount').val('');
        });
        $('#input_data_end span').on('tap',function(){
            $(this).addClass('cur').siblings('span').removeClass('cur');
        });
        $('#input_data_amount').on('input',function(){
            var val=$(this).val();
            var unit=$('#unit').html();
            if(unit=='元'){
                $(this).val(val.replace(/[^\d|.]/g,''));
            }
            if(unit=='积分'){
                $(this).val(val.replace(/[^\d]/g,''));
            }
        });
        $('#input_data_amount').on('blur',function(){
            var val=$(this).val();
            var unit=$('#unit').html();
            if(unit=='元'){
                var exp = /^[0-9]\d*(?:.\d{1,2})?$/;
                if(! exp.test(val)){
                    $(this).val('');
                }
            }
            if(Number(val)==0){
                $(this).val('');
            }
        });
        $('#btnToList').on('tap',function(){
            $('.app_scanpk_add').hide();
            $('.app_scanpk_list').show();
        });
        $('#btnPub').on('tap',function(){
            var groupId=$('#groupId').val();
            var pkType=$('#input_data_type_area input:checked').val();
            var pkAmount=$('#input_data_amount').val();
            var pkTime=$('#input_data_end span.cur').attr('data');
            if(pkType=='' || pkAmount=='' || pkTime==''){
                common.alert('信息填写不完整');
                return;
            }
            common.loading();
            if(pkType==0){
                pkAmount=pkAmount*100;
            }
            $.ajax({
                url: "/group_app/scanpk/pubpk",
                data: {'groupId':groupId,'pkType': pkType,'pkAmount':parseInt(pkAmount),'pkTime':pkTime},
                type: 'post',
                dataType: 'json',
            }).done(function(d){
                common.unloading();
                if(d.errcode!=0){
                    common.alert(d.errmsg);
                    return;
                }
                top.socket.emit('message','【扫码PK】我刚发起了一个PK活动，快来加入吧~(左下角+号进入)',1);
                window.location.reload();
            }).fail(function(d){
                console.log(d);
                common.unloading();
                common.alert('发布失败，请重试');
            });

        });
        /* app_scanpk_add end */
        /* app_scanpk_detail */
        $('#btnBackList').on('tap',function(){
            $('.app_scanpk_detail').hide();
            $('.app_scanpk_list').show();
        });
        $('#btnJoin').off().on('tap',function(){
            var id=$.trim($(this).attr('data'));
            if(id==''){
                common.alert('操作失败');
                return;
            }
            var t2=setTimeout(function(){
                common.loading();
            },600);
            $.ajax({
                url: "/group_app/scanpk/joinpk",
                data: {'id':id},
                type: 'post',
                dataType: 'json',
            }).done(function(d){
                clearTimeout(t2);
                common.unloading();
                if(d.errcode!=0){
                    common.alert(d.errmsg);
                    return;
                }
                common.alert('加入成功');
                $('#btnJoin').hide();
                top.socket.emit('message','【扫码PK】我刚加入了一个PK活动，大家一起来~(左下角+号进入)',1);
                _this.loadScanDetail(id);
            }).fail(function(d){
                clearTimeout(t2);
                console.log(d);
                common.unloading();
                common.alert('加入失败，请重试');
            });
        });
        /* app_scanpk_detail end */
	},
    loadScanDetail:function(id){
        var t=setTimeout(function(){
            common.loading();
        },600);
        $('#btnJoin').attr('data',id);
        $('#data_type').html('');
        $('#data_amount').html('');
        $('#data_master').html('');
        $('#data_start').html('');
        $('#data_end').html('');
        $('#data_num').html('');
        $('#data_user').html('');
        $.ajax({
            url: "/group_app/scanpk/detail",
            data: {'id':id},
            type: 'post',
            dataType: 'json',
        }).done(function(d){
            clearTimeout(t);
            common.unloading();
            if(d.errcode!=0){
                common.alert(d.errmsg);
                return;
            }
            var data=d.data;
            var pkType=data.pkType==0?'红包':'';
            pkType=data.pkType==1?'积分':pkType;
            var pkAmount=data.pkType==0?(data.pkAmount)+'元':data.pkAmount+'积分';
            $('#data_type').html(pkType);
            $('#data_amount').html(pkAmount);
            $('#data_master').html(data.pkMasterName);
            $('#data_start').html(data.startTimeFormat);
            $('#data_end').html(data.endTimeFormat);
            $('#data_num').html(data.userNum);
            $('#data_user').append('<h2 class="tit"><i>&nbsp;</i><em>群昵称</em><span>扫码量</span></h2>');
            $(data.users).each(function(i,e){
                i++;
                var thisClass='';
                if(e.winner==1){
                    i='';
                    thisClass='class="iconfont icon-leyingxuanzhong"';
                }
                $('#data_user').append('<h2><i '+thisClass+'>'+i+'</i><em>'+e.nickName+'</em><span>'+e.scanNum+'</span></h2>');
            });
            $('.icon_status').html('<img src="/static/images/icon_status_'+data.status+'.png" />');
        }).fail(function(d){
            clearTimeout(t);
            console.log(d);
            common.unloading();
            common.alert('加载失败，请重试');
        });
    }
};
$(function(){
	groupScanpkLists.init();
});
