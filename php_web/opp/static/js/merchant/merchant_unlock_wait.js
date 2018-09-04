var unlock = {
    init: function() {
        this.createTable();
    },
    createTable: function() {
        var _this = this;
        var url = "/api/merchant/unlock_wait_list";
        var params = $.extend(hls.common.dataTable, {
            "ajax": {
                "url": url,
                "type": 'POST'
            },
            "serverSide": true,
            "processing":true,
            "searching":true,
            "columns": [{
                class: 'center',
                "data": null,
                render: function(data) {
                    return data.uid;
                }
            },{
                class: 'center',
                "data": null,
                render: function(data) {
                    return data.uname;
                }
            },{
                class:'center',
                "data":null,
                render:function(data){
                    return data.stime;
                }
            },{
                class:'left',
                "data":null,
                render:function(data){
                    return data.logDesc;
                }
            },{
                class:'center',
                "data":null,
                render:function(data){
                    return data.logTime;
                }
            },{
                class:'center',
                "data":null,
                render:function(data){
                    if(data.ustatus == 0){
                        return '正常';
                    }
                    if(data.ustatus == 1){
                        return '封禁';
                    }
                }
            },{
                class:'center',
                "data":null,
                render:function(data){
                    var html = '<img class="loadimg" style="height:80px;width:80px;" src="'+data.simg+'">';
                    return html;
                }
            },{
                class:'center',
                "data":null,
                render:function(data){
                    return data.sreason;
                }
            },
            {
                class:'center',
                "data":null,
                render:function(data){
                    var html = '<a class="btn-text noselect blue look_up" data-userId="'+data.uid+'">查看相关信息</a>&nbsp;&nbsp;&nbsp;<a class="btn-text noselect blue reset" onclick="unlock.unlock(' + data.uid + ')">解禁</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="btn-text noselect blue reset" onclick=unlock.reject(' + data.uid + ',"'+ data.sopenid +'")>驳回</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="btn-text noselect blue reset" onclick="unlock.mark(' + data.sid + ')">备注</a>';
                    return html;
                }
            }],
            "initComplete": function() {
            },
            "drawCallback": function() {
                $('.look_up').click(function(){
                    var userId=$(this).attr('data-userId') || 0;
                    if(userId==0){
                        alert('缺少userId,无法查看！');
                        return false;
                    }
                    common.transDialog(function(callback){
                        common.loading();
                        $.post('/api/merchant/get_userscan_info',{'userId':userId},function(res){
                            common.unloading();
                            var html;
                            html='<!-- 用户的基本信息 -->';
                            html+='<div class="panel panel-default">';
                            html+='  <h3 style="padding:10px;font-size:20px;">用户基本信息</h3>';
                            html+='  <div class="panel-body userinfo">';
                            html+='  <p style="text-align:center">';
                            html+='    <img style="width:80px;border-radius:50%;margin-right:0 auto;" src="'+res.headimgurl+'">';
                            html+='  </p>';
                            // html+='  <p>mchId：'+res.mchId+'</p>';
                            html+='  <p>ID：'+res.userId+'</p>';
                            html+='  <p>昵称：'+res.nickName+'</p>';
                            html+='  <p>OPENID：'+res.openid+'</p>';
                            html+='  <p>地区：'+res.province+'-'+res.city+'-'+res.country+'</p>';
                            html+='  <p>状态：'+(res.commonStatus==0?'正常':res.commonStatus==1?'<b style="color:red">被封禁</b>':'无')+'</p>';
                            html+='  <p>IP：'+res.logIp+'</p>';
                            html+='  <p>理由：'+res.logDesc+'</p>';
                            html+='  <p>来路URL：'+res.referer+'</p>';
                            html+='  <p>发生Url：'+res.logUrl+'</p>';
                            html+='</div>';
                            html+='</div><hr>';
                            html+='<!-- 用户扫码记录 -->';
                            html+='<div class="panel panel-default">';
                            html+='  <h3 style="padding:10px;font-size:20px;">用户扫码记录</h3>';
                            html+='  <div class="panel-body scanlist">';
                            html+='      <table id="userscan_data" class="table">';
                            html+='        <thead>';
                            html+='            <tr>';
                            html+='                <th>扫码ID</th>';
                            html+='                <th>码CODE</th>';
                            html+='                <th>码批次</th>';
                            html+='                <th>商户（mchId）</th>';
                            html+='                <th>用户id</th>';
                            html+='                <th>openId</th>';
                            html+='                <th>扫码时间</th>';
                            html+='            </tr>';
                            html+='        </thead>';
                            html+='      </table>';
                            html+='  </div>';
                            html+='</div>';
                            callback(html);
                            _this.createScanTable(res.scanList);
                            console.log(res);
                        },'json');
                    });
                });
                $(".loadimg").click(function(){
                    var imgUrl=$(this).attr('src');
                    common.transDialog(function(callback){
                        callback('<div style="text-align:center;"><img src="'+imgUrl+'"></div>');
                    });
                })
            }
        });

        $('#userTable').DataTable(params);

    },
    unlock:function(id){
        common.confirm('确认解禁该用户？',function(res){
            if(res == 1){
                $.post('/api/merchant/operation',{id:id},function(result){
                    if(result.data == true){
                        common.alert('处理成功！',function(r){
                            if(r == 1){
                                location.reload();
                                return;
                            }
                        });

                    }
                });
            }
        });
    },
    reject:function(id,openid){
        var title = '拒绝操作';
        var name1 = '申请ID';
        var value1 = id;
        var name2 = '拒绝原因';
        var name3 = '拉黑备注';
        var txt = 1;
        var text = '请填写准确的拒绝原因';
        var text2 = '填写拉黑备注内容';
        common.refuseSec(title, name1, value1, name2, name3, txt,text, text2, function(res,val,mark){
            if(res == 1){
                $.post('/api/merchant/operation_refuse',{id:id,val:val},function(r){
                    if(r.data == true){
                        common.alert('处理成功！',function(result){
                            if(result == 1){
                                location.reload();
                                return;
                            }
                        });
                    }else{
                        common.alert('我就随便提示下吧！这里有错误！');
                        return;
                    }
                });
            }
            if(res == 3){
                $.post('/api/merchant/pull_into_blacklist',{id:id,val:val,openid:openid,mark:mark},function(r){
                    if(r.data == true){
                        common.alert('处理成功！',function(result){
                            if(result == 1){
                                location.reload();
                                return;
                            }
                        });
                    }else{
                        common.alert('都是你的错！');
                        return;
                    }
                });
            }
        });
    },
    mark:function(id){
        var title = '添加备注';
        var name1 = '申请ID';
        var value1 = id;
        var name2 = '备注内容';
        var txt = 1;
        var text = '填写备注内容';
        $.post('/api/merchant/get_mark',{id:id},function(response){
            if(response.data.mark != null){
                text = response.data.mark;
            }
            common.refuseConfirmBro(title, name1, value1, name2, txt, text, function(res,val){
                if(res == 1){
                    $.post('/api/merchant/mark',{id:id,val:val},function(r){
                        if(r.data == true){
                            common.alert('备注成功！',function(result){
                                if(result == 1){
                                    // location.reload();
                                    return;
                                }
                            });

                        }
                    });
                }
            });  
        });
    },
  createScanTable:function(data){
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": false,//加载中
            "info":     true,
            "stateSave": false,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":true,
            "bDestroy": true,
            "lengthChange": false,
            "serverSide":false,//开启服务器分页
            "deferRender": true,
            "data": data,
            "columns": [
                {"data":"scanId","class":"center"},
                {"data":"code","class":"center"},
                {"data":"batchNo","class":"center"},
                {"data":"mchId","class":"center"},
                {"data":"userId","class":"center"},
                {"data":"openid","class":"center"},
                {"data":"scanTime","class":"center"},
                
            ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.unloading();
                common.autoHeight();
            },
            "preDrawCallback": function() {
                common.loading();
            }
        };
        
        this.table=$('#userscan_data').dataTable(config); 
    }
};
$(function() {
    unlock.init();
});