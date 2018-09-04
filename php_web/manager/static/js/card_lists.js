/* global common */
var card = {
    init:function(){
        var _this=this;
        _this.createTable();
    },
    state:function(){
        $('#cardTable tbody td .state').off('click').on("click",function(){
            var _this = $(this);
            var thisTxt=$(this).text();
            common.confirm('确定'+ thisTxt +'删除吗？',function(r){
                if(r==1){
                    common.loading();
                    var id=_this.attr('data-id');
                    var url='/card/del';
                    $.post(url,{'id':id},function(d){
                        common.unloading();
                        if(d.errorCode==0){
                            _this.parent('td').parent('tr').addClass('selected');
                            var table=$('#cardTable').DataTable();
                            table.row('.selected').remove().draw(false);
                            common.autoHeight();
                        }else{
                            common.alert(d.errorMsg);
                        }
                    },'json');
                }
            });
        });
    },
    delTr:function(){
        $('#cardTable tbody td .del').off('click').on("click",function(){
            var _this=$(this);
            common.confirm('确定删除吗？',function(r){
                if(r==1){
                    common.loading();
                    var id=_this.attr('data-id');
                    var url='/card/del';
                    $.post(url,{'id':id},function(d){
                        common.unloading();
                        if(d.errorCode==0){
                            _this.parent('td').parent('tr').addClass('selected');
                            var table=$('#cardTable').DataTable();
                            table.row('.selected').remove().draw(false);
                            common.autoHeight();
                        }else{
                            common.alert(d.errorMsg);
                        }
                    },'json');
                }
            });
        });
        $('#cardTable tbody td .cdel').off('click').on('click',function(){
            var _this=$(this);
            common.confirm('确定删除吗？',function(r){
                if(r==1){
                    common.loading();
                    var cid=_this.attr('data-id');
                    var url='/card/del_group';
                    $.post(url,{'cid':cid},function(d){
                        common.unloading();
                        if(d.errcode==0){
                            _this.parent('td').parent('tr').addClass('selected');
                            var table=$('#cardTable').DataTable();
                            table.row('.selected').remove().draw(false);
                            common.autoHeight();
                        }else if(d.errcode == 1){
                            common.alert(d.errmsg);
                            return;
                        }else if(d.errcode == 12007){
                            common.alert(d.errmsg);
                            return;
                        }else if(d.errcode ==3){
                            common.alert(d.errmsg);
                            return;
                        }else if(d.errcode == 2){
                            common.alert(d.errmsg);
                            return;
                        }
                    },'json');
                }
            });
        })
    },
    createTable:function(){
        var _this=this;
        $('#cardTable').on('xhr.dt', function ( e, settings, json, xhr ) {
            window.dataList=json.data;
        }).DataTable({
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "paging":   true,
            "ordering": false,
            "order":[[0,'desc']],
            "info":     true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "ajax": {
                "url":"/card/data"
            },
            "columns": [
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.parentId){
                            return '';
                        }else{
                            return data.id;
                        }
                    }
                },
                {
                    "data":null,"class":"left",
                    "render": function (data,type,row) {
                        if(data.parentId){
                            return '<div style="padding-left:40px;color:#999999;">'+'(ID:'+data.id+') '+ data.title +'</div>';
                        }else{
                            return '<div>'+data.title+'</div>';
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.parentId){
                            return '<div></div>';
                        } else {
                            if (data.hasGroupBonus === '1') {
                                return '<div>√</div>';
                            } else {
                                return '<div></div>';
                            }
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render":function(data,type,row){
                        if(data.parentId){
                            // var val=data.probalbility.toString();
                            return data.probability+'%';
                        }else{
                            var probabilityArr=[];
                            for(var i=0;i<window.dataList.length;i++){
                                if(typeof window.dataList[i].parentId!='undefined'){
                                    if(window.dataList[i].parentId==row.id){
                                        if(window.dataList[i].remainNum>0){
                                            probabilityArr.push(window.dataList[i].probability);
                                        }
                                    }
                                }
                            }
                            var lastPro=1;
                            var xpro=1;
                            for(var i=0;i<probabilityArr.length;i++){
                                xpro*=parseFloat((1-probabilityArr[i]/100).toPrecision(12));
                            }
                            lastPro-=parseFloat(xpro.toPrecision(12));
                            var lastVal='综合（'+(lastPro*100).toFixed(3)+'%）';
                            // console.log(lastVal);
                            val=lastVal;
                            return val;
                        }
                    }
                },
                {
                    "data":null,"class":"center",
                    "render":function(data,type,row){
                        var val=data.totalNum+' / <font color=red>'+data.remainNum+'</font>';
                        if(data.totalNum){

                            return '<div style="color:#999;">'+val+'</div>';
                        }else{
                            var cardarray = [];
                            var cardarray1 = [];
                            for(var i=0;i<window.dataList.length;i++){
                                if(typeof window.dataList[i].parentId!='undefined'){
                                    if(window.dataList[i].parentId==row.id){
                                        if(window.dataList[i].totalNum>0){
                                            cardarray.push(window.dataList[i].totalNum);
                                        }
                                        if(window.dataList[i].remainNum>0){
                                            cardarray1.push(window.dataList[i].remainNum);
                                        }
                                    }
                                }
                            }
                            var total = parseInt(0);
                            var total1 = parseInt(0);
                            for(var i =0;i<cardarray.length;i++){
                                total += parseInt(cardarray[i]);
                            }
                            for(var i =0;i<cardarray1.length;i++){
                                total1 += parseInt(cardarray1[i]);
                            }
                            return '总计（'+ total + ' / '+ total1 +'）';
                        }
                    }
                },
                // },
                // {
                //     "data":"rowStatus","class":"center",
                //     "render":function(data,type,row){
                //         if(data == 0){
                //             return '<div style="color:#999;">未激活</div>';
                //         }
                //         if(data == 1){
                //             return '<div style="color:#008000;">启用</div>';
                //         }
                //     }
                // },
                {
                    "data":null,
                    "class":"right noselect nowrap",
                    "render": function (data,type,row) {
                        if(data.parentId){
                            var holder='<a class="btn-text noselect blue" fid="'+data.id+'" href="/card/holder/'+data.id+'" target="_blank">持有者名单</a> &nbsp;&nbsp; ';
                            var list='<a class="btn-text noselect blue" fid="'+data.id+'" href="/card/winlist/'+data.id+'" target="_blank">中奖名单</a> &nbsp;&nbsp; ';
                            var state = '<a class="btn-text noselect state blue" href="/card/edit/'+data.id+'">修改</a> &nbsp;&nbsp;';
                            var edit='<a class="btn-text noselect blue" href="/card/edit/'+data.id+'">修改</a> &nbsp;&nbsp; ';
                            var del='<span class="btn-text noselect del gray" data-id="'+data.id+'">删除</span>';
                            return holder+list+edit+del;
                        }else{
                            var add='<a class="btn-text noselect blue" fid="'+data.id+'" href="/card/add?fid='+data.id+'">添加乐券</a> &nbsp;&nbsp; ';
                            var edit='<a class="btn-text noselect blue" href="/card/editgroup/'+data.id+'">修改</a> &nbsp;&nbsp; ';
                            var state = '<a class="btn-text noselect state blue" href="/card/edit/'+data.id+'">修改</a> &nbsp;&nbsp;';
                            var del='<span class="btn-text noselect cdel gray" data-id="'+data.id+'">删除</span>';
                            return add+edit+del;
                        }
                    }
                }
            ],
            "initComplete": function () {
                _this.delTr();
                common.autoHeight();
            },
            "drawCallback":function(){
                _this.delTr();
				common.autoHeight();
            }
        });

    },
    edit:function(){
        
    }
};
$(function(){
    card.init();
});
