var merchant = {

    url: "/api/merchant/get",
    mchUrl: null,

    init: function() {
        var val = $('#type').val();
        $('#btnSend').attr('value', val);
        $('#getid').attr('val', val);
        this.createTable();
    },

    createTable: function() {
        var _this = this;
        var url = _this.url + '?status=' + $('#type').val();
        var params = $.extend(hls.common.dataTable, {
            "ajax": {
                "url": url
            },

            "columns": [{
                class: 'center',
                "data": null,
                render: function(data) {
                    return '<span class="button" onclick="merchant.checkbox()" style="padding-left:10px;" title="点选发送短信">' + data.mid + '  ' + '</span>';
                }
            }, {
                class: 'center',
                "data": null,
                render: function(data) {
                    if (data.mname) {
                        if (!_this.mchUrl) {
                            _this.mchUrl = data.url + 'opp/auth';
                        }
                        return '<a onclick="merchant.go(' + data.mid +
                            ')" href="javascript:void(0)">' +
                            (data.mname==''?'未填写':data.mname) + '</a>';
                    } else {
                        return '<a onclick="merchant.go(' + data.mid +
                            ')" href="javascript:void(0)">' +
                            ($.trim(data.name)==''?'未填写':data.name) + '</a>';
                    }

                }
            }, {
                class: 'center',
                "data": "mcontact"
            }, {
                class: 'center',
                "data": "aphone"
            }, {
                class: 'center',
                "data": "mdate"
            }, {
                class: 'center',
                "data": 'mcdate'
            }, {
                "class": "center",
                "data": "payAccountType",
                "render": function(data,type,row) {
                    if (data == 0) {
                        return '<span data-id="'+row.mid+'" cur-type="'+data+'" class="btn-payaccounttype" style="text-decoration:underline;cursor:pointer">企业自备</span>';
                    }
                    if (data == 1) {
                        return '<span data-id="'+row.mid+'" cur-type="'+data+'" class="btn-payaccounttype" style="text-decoration:underline;cursor:pointer"><font color="red">红码代发</font></span>';
                    }
                }
            }, {
                "class": "center",
                "data": "mamount",
                "render": function(data) {
                    if (data == '' || data==null) {
                        return '0元';
                    }
                    return (data/100)+'元';
                }
            }, {
                "class": "center",
                "data": "mstatus",
                "render": function(data) {
                    if (data == 0) {
                        return '新建';
                    }
                    if (data == 1) {
                        return "<font color='green'>已审核</font>";
                    }
                    if (data == 2) {
                        return "<font color='orange'>已驳回</font>";
                    }
                    if (data == 3) {
                        return "<font color='red'>冻结</font>";
                    }
                    if (data == 4) {
                        return "<font color='gray'>待审核</font>";
                    }
                    if (data == 5) {
                        return "<font color='#990066'>预审核</font>";
                    }

                }
            }, {
                "data": null,
                "class": "center nowrap",
                "render": function(data) {
                    // var html = '<a class="btn-text noselect blue pclick" onclick="merchant.review(' + data.mid + ')">预审核</a>';
                    var html = '<a class="btn-text noselect blue pclick" href="/merchant/pre_review?id=' + data.mid + '">预审核</a>';
                    html += '&nbsp;&nbsp;';
                    html += '<a class="btn-text noselect blue" href="/merchant/review?id=' + data.mid + '">审核</a>';
                    html += '&nbsp;&nbsp;';
                    if (data.mstatus == 3) {
                        html += '<a class="btn-text noselect blue frozen" onclick="merchant.active(' + data.mid + ')">激活</a>';
                    } else {
                        html += '<a class="btn-text noselect blue frozen" onclick="merchant.freeze(' + data.mid + ')">冻结</a>';
                    }
                    html += '&nbsp;&nbsp;';
                    html += '<a class="btn-text noselect blue reset" onclick="merchant.passwd(' + data.mid + ')">重置密码</a>';
                    html += '&nbsp;&nbsp;';
                    html += '<a class="btn-text noselect blue rewhchat" onclick="merchant.rewhchat(' + data.mid + ')">重置授权</a>';
                    html += '&nbsp;&nbsp;<a class="btn-text noselect blue more" data-id="' + data.mid + '">更多</a>';
                    return html;
                }
            }],
            "initComplete": function() {
                _this.more();
            },
            "drawCallback": function() {
                _this.more();
            }
        });

        $('#mchTable').DataTable(params);
        _this.checkbox();

    },
    checkbox: function() {

        window.localStorage.removeItem("phones");
        var table = $('#mchTable').DataTable();
        var selected = 'selected';
        $('#mchTable tbody').on('click', 'tr', function() {
            $(this).toggleClass('selected');
            var storage = window.localStorage;
            var s = new Array();
            var a = table.rows('.selected').data();
            for (var i = 0; i < table.rows('.selected').data().length; i++) {
                s.push(a[i].aphone);
            }
            if (!storage.getItem("phones")) {
                storage.setItem("phones", s);
            } else {
                storage.setItem("phones", s);
            }
            if (storage.getItem("phones").length == 0) {
                $('#btnSend').attr("value", 0);
            } else {
                $('#btnSend').attr("value", 2);
            }
        });
        $('#btnSend').off().on('click', function() {
            var val = $('#btnSend').attr('value');
            window.location.href = "/merchant/send_ms?val=" + val;
        });
    },


    /**
     * 预审核企业账户
     * 
     * @param mch_id 企业ID
     */
    review: function(mch_id) {
        console.log(mch_id);
        // common.confirm('确定预审核该企业？', function(confirm) {
        //     if (confirm) {
        //         common.loading();
        //         hls.api.Merchant.review(mch_id, function(resp) {
        //             common.unloading();
        //             common.alert('操作成功！', function() {
        //                 location.reload();
        //             });
        //         }, function(err) {
        //             common.unloading();
        //             common.alert(err + '！');
        //         });
        //     }
        // });
    },

    /**
     * 冻结企业账户
     * 
     * @param mch_id 企业ID
     */
    freeze: function(mch_id) {
        common.confirm('确定冻结该企业账户？', function(confirm) {
            if (confirm) {
                common.loading();
                hls.api.Merchant.freeze(mch_id, function(resp) {
                    common.unloading();
                    common.alert('操作成功！', function() {
                        location.reload();
                    });
                }, function(err) {
                    common.unloading();
                    common.alert(err + '！');
                });
            }
        });
    },
    /**
     * 微信授权重置
     * 
     * @param mch_id 企业ID
     */
    rewhchat: function(id) {
        common.selectConfirm('选择要操作的微信号', function(e) {
            $.post('/merchant/reset_wechat', {
                id: id,
                e: e
            }, function(result) {
                if (result.errcode == 0) {
                    common.alert('重置成功');
                } else {
                    common.alert(result.errmsg);
                }
            }, 'json');
        });
    },
    /**
     * 激活核企业账户
     * 
     * @param mch_id 企业ID
     */
    active: function(mch_id) {
        common.confirm('确定激活该企业账户？', function(confirm) {
            if (confirm) {
                common.loading();
                hls.api.Merchant.active(mch_id, function(resp) {
                    common.unloading();
                    common.alert('操作成功！', function() {
                        location.reload();
                    });
                }, function(err) {
                    common.unloading();
                    common.alert(err + '！');
                });
            }
        });
    },

    /**
     * 重置企业密码
     * 
     * @param mch_id 企业ID
     */
    passwd: function(mch_id) {
        common.confirm('确定将该企业的密码重置为：123456', function(confirm) {
            if (confirm) {
                common.loading();
                hls.api.Merchant.passwd(mch_id, function(resp) {
                    common.unloading();
                    common.alert('操作成功！新密码为：123456', function() {
                        // pass
                    });
                }, function(err) {
                    common.unloading();
                    common.alert(err + '！');
                });
            }
        });
    },

    /**
     * 前往该企业后台管理
     * 
     * @param mchId 企业ID
     */
    go: function(mchId) {
        var self = this;
        common.loading();
        hls.api.Admin.generate_token(mchId, function(resp) {
            common.unloading();
            var adminId = resp.admin_id;
            var keys = resp.keys;

            // Added by shizq - begin
            if (self.mchUrl == null) {
                alert('该企业没有审核，不能登入！');
                return;
            }
            // Added by shizq - end
            var theurl=self.mchUrl + '?admin_id=' + adminId + '&mch_id=' + mchId + '&keys=' + keys;
            var u = navigator.userAgent;
            var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
            var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
            if(isAndroid||isiOS){
                location.href=theurl;
            }else{
                var merchant = '<a class="hls-to-mch" href="' + theurl + '" target="_blank"><span>&nbsp;</span></a>';
                // window.open(self.mchUrl + '?admin_id=' + adminId + '&mch_id=' + mchId);
                // 上面的代码容易被浏览器屏蔽
                $('.hls-to-mch').remove();
                $('body').append(merchant);
                $('body .hls-to-mch span').trigger('click');
            }
        }, function(err) {
            common.unloading();
            common.alert(err + '！');
        });
    },
    /**
     * 更多操作
     * 
     * @param mch_id 企业ID
     */
    more: function() {
        var html = '<div id="more" style="display:none"><span class="btn-text noselect blue shakearound-reg">申请微信摇一摇周边</span><span class="btn-text noselect blue shakearound-status">查看微信摇一摇审核状态</span></div>';
        var isin = $('#more').length;
        if (isin == 0) {
            $('body').append(html);
        }
        $('#mchTable .more').on('mouseover', function() {
            // clearTimeout(window.moreT);
            $('#more').show();
            var top = $(this).offset().top;
            var left = $(this).offset().left;
            var mchId = $(this).attr('data-id');
            $('#more').css({
                'left': left - $('#more').width() - $(this).width() - 11,
                'top': top - 5,
                'display': 'block'
            });
            $('#more .shakearound-reg').off().on('click', function() {
                $.post('/merchant/shakearound_register/' + mchId).done(function(d) {
                    if (typeof d != 'object')
                        d = $.parseJSON(d);
                    if (typeof d.errcode != 'undefined' && d.errcode != 0) {
                        alert('微信服务器回返错误：' + d.errmsg);
                    } else if (typeof d.errcode != 'undefined' && d.errcode == 0) {
                        alert('成功提交微信服务器');
                    } else {
                        alert('出错了：' + JSON.stringify(d));
                    }
                    console.log(d);
                }).fail(function() {
                    alert('提交失败');
                });
            });
            $('#more .shakearound-status').off().on('click', function() {
                $.post('/merchant/shakearound_auditstatus/' + mchId).done(function(d) {
                    if (typeof d != 'object')
                        d = $.parseJSON(d);
                    if (typeof d.errcode != 'undefined' && d.errcode != 0) {
                        alert('微信服务器回返错误：' + d.errmsg);
                    } else if (typeof d.errcode != 'undefined' && d.errcode == 0) {
                        //审核状态。0：审核未通过、1：审核中、2：审核已通过；
                        if (d.data.audit_status == 0) {
                            alert('审核未通过');
                        }
                        if (d.data.audit_status == 1) {
                            alert('审核中');
                        }
                        if (d.data.audit_status == 2) {
                            alert('审核已通过');
                        }
                    } else {
                        alert('出错了：' + JSON.stringify(d));
                    }
                    console.log(d);
                }).fail(function() {
                    alert('提交失败');
                });
            });

        }).on('mouseout', function() {
            $('#more').hide();
        });
        $('#more').off().on('mouseout', function() {
            $('#more').hide();
        }).on('mouseover', function() {
            $('#more').show();
        });
        $('.btn-payaccounttype').off().on('click', function() {
            var mchId=$(this).attr('data-id');
            var curType=$(this).attr('cur-type');
            var title=curType==1?'切回企业自备':'开启红码代付';
            var toType=curType==1?0:1;
            common.confirm('确定要执行“'+title+'”操作吗？',function(r){
                if(r==1){
                    var t=setTimeout(common.loading,500);
                    $.post('/merchant/edit_payaccounttype',{'mchId':mchId,'payAccountType':toType},function(d){
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
$(function() {
    merchant.init();
});