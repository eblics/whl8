//执行初始化
$(function(){
    forbiddenUsersPage.init();
});
var forbiddenUsersPage = {
    init:function(){
        this.createTable();
    },
    createTable:function(){
        var _this=this;
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": true,//加载中
            "info":     true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":true,
            "bDestroy": true,
            "serverSide":true,//开启服务器分页
            "ajax":{
                url:'/userdeal/get_mch_forbidden_users',//请求数据地址
                type:"POST",//请求方式
            },
            "columns": [
                        {"data":"userId","class":"center"},
                        {
                            "data":"headimgurl","class":"center",
                            "render": function (data,type,row) {
                                if(data){
                                    return '<img class="headimg" src="'+data+'" />';
                                }else{
                                    return '<div><img class="headimg" src="/static/images/zanwu.png" /></div>';
                                }

                            }
                        },
                        {
                            "data":"nickName","class":"center",
                            "render": function (data,type,row) {
                                if(data){
                                    return data;
                                }else{
                                    return '欢乐扫用户';
                                }

                            }
                        },
                        {
                            "data":"openid","class":"center",
                            "render": function (data,type,row) {
                                return data;
                            }
                        },
                        {
                            "data":"status","class":"center",
                            "render": function (data,type,row) {
                                if(data==0) return '正常';
                                if(data==1) return '<font color=red>封禁</font>';
                            }
                        },
                        {
                            "data":"logDesc","class":"left",
                            "render": function (data,type,row) {
                                if(data!=null || row.lecode!=null){
                                    return (data?data:'')+' <font color=gray>乐码（<a style="text-decoration:underline" href="/tools/search_codes?stype=1&code='+row.lecode+'" target="_blank">'+(row.lecode?row.lecode:'')+'</a>）</font>';
                                }
                                return '';
                            }
                        },
                        {
                            "data":"logTime","class":"center",
                            "render": function (data,type,row) {
                                return data;
                            }
                        },
                        {
                            "data":"applyStatus","class":"center",
                            "render": function (data,type,row) {
                                switch(data){
                                    case '0':
                                    return '<font color=gray>未申诉</font>';
                                    case '1':
                                    return '<font color=black>已申诉</font>';
                                    case '2':
                                    return '<font color=gray>未申诉</font>';
                                    case '3':
                                    return '<font color=orange>已驳回</font>';
                                    case '4':
                                    return '<font color=red>永久驳回</font>';
                                    default:
                                    return '<font color=gray>未申诉</font>';
                                }
                            }
                        },
                        {
                            "data":"applyTime","class":"center",
                            "render": function (data,type,row) {
                                if(row.applyStatus==2) return '<font color=gray>历史申诉<BR>'+data+'</font>';
                                return data;
                            }
                        },
                        {
                            "data":"mark","class":"center",
                            "render": function (data,type,row) {
                                if(data==null) data='';
                                return '<div style="max-width:200px;overflow:hidden;word-wrap: break-word;">'+data+'</div>';
                            }
                        },
                        {
                            "data":null,"class":"center",
                            "render": function (data,type,row) {
                                var html='';
                                if(data.applyTime=='' || data.applyTime==null){
                                    html+='<span class="btn-text noselect blue btn-unlock" data-id="'+row.userId+'">直接解封</span>';
                                }else{
                                    html+='<span class="btn-text noselect blue btn-view" data-id="'+row.userId+'">查看申诉</span>';
                                }
                                return html;
                            }
                        }
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.autoHeight();
                _this.btnEvent();
            }
        };
        $('#forbiddenUsersTable').dataTable(config);
    },
    btnEvent:function(){
        var _this=this;
        $('.btn-view').off().on('click',function(){
            var userId=$(this).attr('data-id');
            common.transDialog(function(callback){
                var t=setTimeout(common.loading,500);
                $.post('/userdeal/get_forbidden_user_apply/'+userId,{},function(d){
                    clearTimeout(t);
                    common.unloading();
                    if(d!=null && d!=''){
                        var html='<table class="apply">';
                        html+='<tr><th colspan=2>昵称：'+d.nickName+' openid：'+d.openid+'</th></tr>';
                        html+='<tr><td class="center bb"><strong>产品包装内二维码</strong></th><td class="center bb"><strong>其他申诉信息</strong></td></tr>';
                        html+='<tr><td width="340"><a href="'+d.QRimg+'" target="_blank"><img src="'+d.QRimg+'" /></a></td><td>';
                        html+='<h2>姓名：'+d.name+'</h2>';
                        html+='<h2>电话：'+d.phoneNum+'</h2>';
                        html+='<h2>说明：'+d.reason+'</h2>';
                        html+='<ul class="admin table-form">';
                        html+='<li><input class="input" type="hidden" placeholder="解封理由" value="无" /> <span data-id="'+d.id+'" data-user="'+d.userId+'" class="btn btn-green noselect btn-evt-unlock">解封帐号</span></li>';
                        html+='<li>备注：<input class="input" type="text" placeholder="备注内容" value="'+(d.mark?d.mark:'')+'" /> <span data-id="'+d.id+'" data-user="'+d.userId+'" class="btn btn-blue noselect btn-evt-mark">保存备注</span></li>';
                        html+='<li>驳回：<input class="input" type="text" placeholder="驳回理由（申诉人可见）" value="'+(d.refuse?d.refuse:'')+'" /> <span data-id="'+d.id+'" data-user="'+d.userId+'" class="btn btn-orange noselect btn-evt-refuse">驳回申诉</span></li>';
                        html+='<li>拉黑：<input class="input" type="text" placeholder="拉黑理由（申诉人不可见）" value="'+(d.remark?d.remark:'')+'" /> <span data-id="'+d.id+'" data-user="'+d.userId+'" class="btn btn-red noselect btn-evt-blacklist">拉黑帐号</span></li>';
                        html+='</ul>';
                        html+='</td></tr></table>';
                        callback(html);
                        _this.adminEvent();
                    }else{
                        callback('<h1 class="center" style="font-size:30px;line-height:150px;">没有待处理申诉</h1>');
                    }
                },'json');
            });
        });
        $('.btn-unlock').off().on('click',function(){
            var userId=$(this).attr('data-id');
            common.confirm('确定直接解封吗？',function(r){
                if(r==1){
                    var t=setTimeout(common.loading,500);
                    $.post('/userdeal/deal_forbidden_user_unlock/'+userId,{},function(d){
                        clearTimeout(t);
                        common.unloading();
                        if(d.errcode==0){
                            window.location.reload();
                            common.alert('解封成功');
                        }else{
                            common.alert(d.errmsg);
                        }
                    },'json');
                }
            });
        });
    },
    adminEvent:function(){
        var _this=this;
        $('.transDialog .admin .btn').off().on('click',function(){
            var value=$(this).siblings('input').val();
            var userId=$(this).attr('data-user');
            var id=$(this).attr('data-id');
            var type='';
            var typeDesc='';
            var title='';
            if($(this).hasClass('btn-evt-unlock')){
                type=0;
                typeDesc='解封理由';
                title='解封';
            }
            if($(this).hasClass('btn-evt-mark')){
                type=1;
                typeDesc='备注内容';
                title='备注';
            }
            if($(this).hasClass('btn-evt-refuse')){
                type=2;
                typeDesc='驳回理由';
                title='驳回';
            }
            if($(this).hasClass('btn-evt-blacklist')){
                type=3;
                typeDesc='拉黑理由';
                title='拉黑';
            }
            if($.trim(value)==''){
                common.alert(typeDesc+'不能为空');
                return;
            }
            common.confirm('确定执行'+title+'操作吗？',function(r){
                if(r==1){
                    var t=setTimeout(common.loading,500);
                    $.post('/userdeal/deal_forbidden_user_apply',{'id':id,'userId':userId,'type':type,'value':value},function(d){
                        clearTimeout(t);
                        common.unloading();
                        if(d.errcode!=0){
                            common.alert(d.errmsg);
                            return;
                        }
                        $('.transDialog').remove();
                        window.location.reload();
                    },'json');
                }
            });
            
        });
    }
    
};