/**
 * 业务员核销界面js逻辑
 * 
 * @author shizq
 */
var Page = {

    init: function() {
        this.createTable();
        this.bindEvent();
        
    },

    bindEvent: function() {
        
    },

    /**
     * DataTable表格数据加载初始化
     * 
     */
    createTable: function() {
        var _this = this;
        var data = [
            {
                "data": 'id', "class":"center"
            },
            {
                "data": null, "class":"center",
                render: function(data) {
                    if (data.realName == null) {
                        return '未绑定业务员';
                    }
                    if (data.isDelete === 1 || data.isDelete === '1') {
                        return data.realName + "(已删除)";
                    }
                    return data.realName;
                }
            },
            {
                "data": 'submitTime', "class":"center"
            },
            {
                "data": 'cardNum', "class":"center"
            },
            {
                "data": 'settleCode', "class":"center",
                render: function(data) {
                    if (data == 1) {
                        return '通过';
                    }
                    if (data == 2) {
                        return '拒绝';
                    }
                    return '未审核';
                }
            },
            {
                "data": 'state', "class":"center state",
                render: function(data) {
                    var content = '待处理';
                    if (data == 1) {
                        content = '<span class="gray">已审核</span>';
                    }
                    return content;
                }
            },
            {
                "data": null,
                "class":"right noselect nowrap",
                render: function(data) {
                    var edit = '<a class="btn-text noselect blue" onclick="Page.review(this, ' + data.id + ');" title="'+ data.statementNo +'" href="javascript:void(0);">审核</a>　';
                    var del = '<span class="btn-text noselect del gray" onclick="Page.showCards(' + data.id + ');">查看乐券</span>';
                    return edit + del;
                }
            }
        ];

        var table = $('#settleTable').DataTable({
            "language": {
                "url": "/static/datatables/js/dataTables.language.js"
            },
            "paging": true,
            "ordering": false,
            "order": [[0, 'desc']],
            "info": true,
            "stateSave": false,
            "searching": false,
            "serverSide": true,
            "ajax": {
                "url": "/settle/lists"
            },
            "columns": data,

            initComplete: function() {
                _this.bindEvent();
                common.autoHeight();
                var 
                filter = ' <div id ="filter" class="dataTables_length" style="padding:15px 15px 15px 40px;">';
                filter += '    <label>状态筛选  ';
                filter += '       <select id ="status_select" style="width: 98px;">';
                filter += '         <option index=""  value="">全部</option>';
                filter += '         <option index="0" value="0">待处理</option>';
                filter += '         <option index="1" value="1">已审核</option>';
                filter += '       </select>';
                filter += '    </label>';
                filter += ' </div>';
                $(filter).insertAfter($('#settleTable_length'));

                var 
                filter = ' <div id ="filter" class="dataTables_length" style="padding:15px 15px 15px 40px;">';
                filter += '    <label>业务员筛选  ';
                filter += '       <select id ="salesman_select" style="width: 98px;">';
                filter += '         <option index=""  value="">全部</option>';
                for (var i = 0; i < salesmanLists.length; i++) {
                    filter += '     <option index="' + salesmanLists[i].id + '"  value="' + salesmanLists[i].id + '">' 
                    + salesmanLists[i].realName + '</option>';
                }
                filter += '       </select>';
                filter += '    </label>';
                filter += ' </div>';
                $(filter).insertAfter($('#settleTable_length'));

                $('#status_select').on('change', function() {
                     var val = $(this).val();
                     if (val === "") {
                        table.ajax.url('/settle/lists').load();
                     } else {
                        table.ajax.url('/settle/lists?state=' 
                            + $(this).children(':selected').val()
                            + '&salesman='
                            + $('#salesman_select').children(':selected').val());
                        table.draw(false);
                     }
                });

                $('#salesman_select').on('change', function() {
                     var val = $(this).val();
                     if (val === "") {
                        table.ajax.url('/settle/lists').load();
                     } else {
                        table.ajax.url('/settle/lists?salesman=' 
                            + $(this).children(':selected').val() 
                            + '&state=' 
                            + $('#status_select').children(':selected').val());
                     }
                     table.draw(false);
                });
            },

            drawCallback: function() {
                _this.bindEvent();
                common.autoHeight();
            }
        });
        this.table = table;
    },

    // --------------------------------------
    // 显示核销乐券详细列表
    showCards: function(statementsId) {
        common.loading();
        $.get('/settle/cards/' + statementsId, {}, function(resp) {
            common.unloading();
            if (resp.errcode !== 0) {
                common.alert(resp.errmsg + '！');
                return;
            }
            common.transDialog(function(callback) {
                $('body').css('overflow', 'auto');
                var content = '<ul>';
                content += '<li class="list-item list-title">';
                content += '    <div>乐券名称</div>';
                content += '    <div>乐券数量</div>';
                content += '</li>';
                resp.data.forEach(function(item) {
                    content += '<li class="list-item">';
                    content += '    <div>' + item.title + '</div>';
                    content += '    <div>' + item.cards_num + '张</div>';
                    content += '</li>';
                });
                content += '</ul>';
                callback(content);
            });
        }).fail(function(err) {
            common.unloading();
            common.alert('无法连接服务器！');
        });
    },

    // --------------------------------------
    // 执行审核操作
    review: function(self, statementsId) {
        var _this = this;
        $('.send-confirm').remove();
        var 
        html =  '<div class="send-confirm" style="display:none">';
        html += '   <div class="layer"></div>';
        html += '   <dl class="box" style="width: 460px;margin-left: -230px">';
        html += '       <dt></dt>';
        html += '       <dd>';
        html += '           <div class="condiv">';
        html += '               <div class="s1">核销单号：';
        html += '                   <span class="span1">';
        html += '                       <input type="text" style="width: 300px;" id="textid" value="'+ self.title +'">';
        html += '                   </span>';
        html += '               </div>';
        html += '               <div class="s2">核销说明：';
        html += '                   <span class="span2">';
        html += '                       <textarea cols="2" rows="2" style="width: 300px;"></textarea>';
        html += '                   </span>';
        html += '               </div>';
        html += '           </div>';
        html += '           <div class="btndiv">';
        html += '               <span class="btn btn-blue" data="1">通过</span>';
        html += '               <span class="btn btn-blue" data="2">拒绝</span>';
        html += '               <span class="btn btn-gray" data="0">取消</span>';
        html += '           </div>';
        html += '       </dd>';
        html += '   </dl>';
        html += '</div>';
        $('body').append(html);
        $('textarea').val('审核通过');
        $('.send-confirm dl dt').text('核销审核');
        $('.send-confirm').fadeIn();
        $('.send-confirm .btn').click(function() {
            var save = $(this).attr('data');
            if (save === '0') {
                $('.send-confirm').remove();
                return;
            }
            var textarea = $('textarea').val();
            common.loading();
            var params = {
                "pass": save,
                "content": textarea,
                "statement_id": statementsId
            };
            $.post('/settle/review', params, function(resp) {
                common.unloading();
                if (resp.errcode !== 0) {
                    common.alert(resp.errmsg + '！');
                    return;
                }
                $('.send-confirm').remove();
                _this.table.ajax.reload();
            }).fail(function(err) {
                common.unloading();
                common.alert('无法连接服务器！');
            });
        });
    }

};