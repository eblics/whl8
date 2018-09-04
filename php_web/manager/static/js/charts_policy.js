/**
 * 活动评估界面
 * 
 * @author shizq
 */
var Page = {

    init: function() {
        this.bindEvent();

        // 初始化时间条件
        $('#end_time').val(Util.getDate());
        var today = new Date();
        var timestamp = today.getTime();
        timestamp = timestamp - (31 * 24 * 60 * 60 * 1000);
        var date = new Date(timestamp);
        var startTime = [];
        startTime[0] = date.getMonth() + 1;
        startTime[1] = date.getDate();
        startTime[2] = date.getFullYear();
        if (startTime[0] <= 9) startTime[0] = '0' + startTime[0].toString();
        if (startTime[1] <= 9) startTime[1] = '0' + startTime[1].toString();
        $('#start_time').val(startTime[2] + '-' + startTime[0] + '-' + startTime[1]);

        // 获取最后一次查询的活动编号
        var activityIdsStr = localStorage.getItem("Policy_aid_" + $('#mch_id').val());
        var params = {
            "activity_ids_str": activityIdsStr,
            "start_time": $('#start_time').val(),
            "end_time": $('#end_time').val()
        };
        if (! activityIdsStr) {
            activityIdsStr = $("#activity_id").val();
        } else {
            var activityIds = activityIdsStr.split(',');
            $("#activity_id option").each(function() {
                for (var i = 0; i < activityIds.length; i++) {
                    if (activityIds[i] == $(this).val()) {
                        $(this).prop('selected', true);
                    }
                }
            });
        }
        Service.formSubmit(params);

        $('#activity_id').multiselect({
            checkAllText: "全选",
            uncheckAllText: "取消全选",
            selectedText: "选择了 # 项",
            noneSelectedText: "选择活动"
        });
    },

    bindEvent: function() {

        $('#start_time').focus(function() {
            WdatePicker({
                lang:'zh-cn',
                dateFmt: 'yyyy-MM-dd',
            });
        });

        $('#end_time').focus(function() {
            WdatePicker({
                lang:'zh-cn',
                dateFmt: 'yyyy-MM-dd',
                maxDate: '#F{$dp.$D(\'start_time\', {d:+60})}',
                minDate: '#F{$dp.$D(\'start_time\')}',
            });
        });

        // --------------------------------------
        // 查询按钮点击触发
        $("#btn_search").click(function() {
            var activityIds = $("#activity_id").val();
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();
            if (activityIds == null) {
                common.alert('请选择要查询的活动！');
                return;
            }
            activityIdsStr = activityIds.join(',');
            localStorage.setItem("Policy_aid_" + $('#mch_id').val(), activityIdsStr);
            var params = {
                "activity_ids_str": activityIdsStr,
                "start_time": startTime,
                "end_time": endTime
            };
            Service.formSubmit(params);
        });
    },

    /**
     * 创建页面底部的策略项
     * 
     * @param  {array} activityLogs 活动策略变更记录
     * @return void
     */
    createActivityLogCards: function(activityLogs) {
        var self = this;
        $('#activity_logs_container').empty();

        activityLogs.forEach(function(activityLog) {
            // 如果是自己添加的虚拟今天的log，则不做处理
            if (typeof activityLog.policyLevel === 'undefined') {
                return;
            }
            var 
            row = '', color,
            level = Util.policyLevel[activityLog.policyLevel].name,  
            img = "url(/static/images/" + Util.policyLevel[activityLog.policyLevel].img_url + ")";

            row += '<div class="policy_card" t="' + activityLog.id + '"">';
            row += '    <div class="cirle">';
            row += '        <p style="background:' + img + '">' + level + '</p>';
            row += '    </div>';
            row += '    <p><span>' + activityLog.policyName + '</span></p>';
            row += '    <t style="font-size:12px;">(' + activityLog.theTime + ')</t><br>';
            row += '    <t>详情 <img src="/static/images/yjt.png"></t>';
            row += '</div>';
            $('#activity_logs_container').append(row);
        });

        $(".policy_card").on("click", function() {
            var id = $(this).attr('t');
            common.loading();
            $.post("/charts/get_policy", {id: id}, function(data) {
                common.unloading();
                self.showPanle(data);
                common.autoHeight();
            });
        });
    },

    /**
     * 绘制图表
     *
     * @param {array} activityEvaluatings 图表所需数据
     * @param {array} activityLogs 活动策略变更记录
     * @return void
     */
    drawCharts: function(activityEvaluatings, activityLogs) {
        var myChart = echarts.init(document.getElementById('main'));
        var dateArr = [], scanNumArr = [], scanNumTotal = 0, redPacketAmountArr = [], redPacketAmountTotal = 0;
        activityEvaluatings.forEach(function(evaluating) {
            dateArr.push(evaluating.theDate);
            scanNumArr.push(evaluating.scanNum);
            scanNumTotal += parseInt(evaluating.scanNum);
            redPacketAmountArr.push((evaluating.redNum * 0.01).toFixed(2));
            redPacketAmountTotal += parseInt(evaluating.redNum);
        });
        $('#scan_num').text(scanNumTotal);
        $('#redpacket_amount').text((redPacketAmountTotal * 0.01).toFixed(2));

        option = {
            title : {show: false},
            tooltip : {
                show: true,
                trigger: 'axis',
                axisPointer: {
                    lineStyle: {color: '#f9320c'}
                },
                formatter: '{b}<br>{a0}: {c0}<br>{a1}: {c1}'
            },
            legend: {
                data: ['扫码量', '红包'],
                top: 'bottom',
                itemGap: 50,
                align: 'left',
                selectedMode: true,
                textStyle: {fontSize: '16'}
            },
            toolbox: {show: false},
            grid: {
                top:'60',
                left: '3%',
                right: '4%',
                padding: '10',
                borderColor: '#F0FFF0',
                containLabel: true
            },
            calculable : false,
            xAxis: [{
                type : 'category',
                boundaryGap : false,
                axisLine: {show:false},
                axisTick: {show:false},
                splitLine: {show: false},
                axisLabel: {
                    show: true,
                    textStyle: {color: '#999'}
                },
                boundaryGap : false,
                data: dateArr
            }],
            yAxis: [{
                type : 'value',
                axisLine:{show:false},
                axisTick:{show:false},
                splitLine:{
                    show: true,
                    color:'#999'
                },
                axisLabel: {
                    show: true,
                    textStyle: {color: '#999'}
                }
            }],
            dataZoom: [{
                type: 'slider',
                show: false,
                start: 0,
                end: 100,
                handleSize: 8
            }, {
                type: 'inside',
                start: 94,
                end: 100
            }],
            series: [{
                name: '扫码量',
                type: 'line',
                smooth: true,
                z: 1,
                symbolSize: [6, 6],
                itemStyle: {
                    normal: {color: '#63cdef'}
                },
                data: scanNumArr
             }, {
                name: '红包',
                type: 'line',
                smooth: true,
                z: 1,
                symbolSize: [6, 6],
                itemStyle: {
                    normal: {color:'#ff4e50'}
                },
                data: redPacketAmountArr
            }, {
                name: '策略',
                type: 'line',
                smooth: true,
                z: 2,
                symbolSize: [6, 6],
                itemStyle: {
                    normal: {color: '#f00'}
                },
                markArea: {
                    silent: true,
                    data: Service.getChartsMakeAreaData(activityLogs)
                }
            }]
        };
        myChart.setOption(option);
    },

    /**
     * 显示策略详细数据弹窗
     * 
     * @param {object} activityLog 活动策略变更记录
     * @return void
     */
    showPanle: function(activityLog) {
        var level = Util.policyLevel[activityLog.policyLevel].name;
        common.transDialog(function(callback) {
            var html = '';
            html += '<div style="clear:both;height:36px"></div>';
            html += '<div class="close"></div>';
            html += '<div class="title">策略类型：' + level + '&nbsp;&nbsp;';
            html += '所属活动：' + activityLog.Json['name'] + '</div>';
            // 红包策略
            if (activityLog.policyLevel == 0) {
                html = Util.getRedpacketPanelContent(html, activityLog.Json);
            }

            // 乐券策略
            if (activityLog.policyLevel == 2) {
                html = Util.getCardPanelContent(html, activityLog.Json);
            }

            // 积分策略
            if (activityLog.policyLevel == 4) {
                html = Util.getPointPanelContent(html, activityLog.Json);
            }

            // 组合、叠加和累计策略
            if (activityLog.policyLevel == 3 || activityLog.policyLevel == 5 || activityLog.policyLevel == 6) {
                html = Util.getOtherPanelContent(html, activityLog.Json);
            }
            callback(html);
        });
    },
};

/**
 * 业务方法，只在Page对象中调用
 * 
 * @type {Object}
 */
var Service = {

    /**
     * 获取图表的makeArea参数
     * 
     * @param  {array} activityLogs
     * @return array
     */
    getChartsMakeAreaData: function(activityLogs) {
        chartsMakeAreaData = [];
        for (var i = 0; i < activityLogs.length - 1; i++) {
            var objArr = [
                {
                    name: Util.policyLevel[activityLogs[i].policyLevel].level,
                    xAxis: activityLogs[i].theTime,
                    itemStyle: {
                        normal: {
                            color: Util.policyLevel[activityLogs[i].policyLevel].color,
                            opacity: 0.2
                        }
                    }
                }, 
                {
                    xAxis: activityLogs[i + 1].theTime
                }
            ];
            chartsMakeAreaData.push(objArr);
        }
        console.log(chartsMakeAreaData);
        return chartsMakeAreaData;
    },

    /**
     * 根据条件查询数据
     * 
     * @param  {object} params
     * @return {void} 
     */
    formSubmit: function(params) {
        var self = this;
        common.loading();

        var tabIdentity=$('.tab_current').attr('data-identtity');
        params.tab=tabIdentity;

        $.get(common.getRptRootUrl() + "estimate/charts_data", params, function(resp) {
            common.unloading();
            if (resp.errcode == 0) {
                $('#withdraw_amount').text((resp.data.withdraw_amount * 0.01).toFixed(2));
                $('#redpacket_num').text(resp.data.rpt_rows[0].rpt_num);
                if (resp.data.scan_users[0]) {
                    $('#user_num').text(resp.data.scan_users.length);
                } else {
                    $('#user_num').text(0);
                }
                Page.createActivityLogCards(resp.data.activity_logs);

                var dateArr = Util.getDateArr(params.start_time, params.end_time);
                // 如果activity_logs中的日期在activity_evaluatings中是空，则添加一个相应日期的空数据到activity_evaluatings中
                // 否则图表中的markArea会找不到策略的开始或结束日期
                for (var j = 0; j < resp.data.activity_logs.length; j++) {
                    var logsDataExists = false;
                    for (var i = 0; i < resp.data.activity_evaluatings.length; i++) {
                        if (resp.data.activity_evaluatings[i].theDate == resp.data.activity_logs[j].theTime) logsDataExists = true;
                    }
                    if (! logsDataExists) {
                        resp.data.activity_evaluatings.push({theDate: resp.data.activity_logs[j].theTime, scanNum: 0, redNum: 0});
                    }
                }

                for (var j = 0; j < dateArr.length; j++) {
                    var dateItemExists = false;
                    for (var i = 0; i < resp.data.activity_evaluatings.length; i++) {
                        if (resp.data.activity_evaluatings[i].theDate == dateArr[j]) dateItemExists = true;
                    }
                    if (! dateItemExists) {
                        resp.data.activity_evaluatings.push({theDate: dateArr[j], scanNum: 0, redNum: 0});
                    }
                }

                // 如果当天的数据为空，则添加一个虚拟当天的数据
                if (resp.data.activity_evaluatings.length > 0) {
                    resp.data.activity_evaluatings.sort(function(x, y) {
                        if (x.theDate < y.theDate) return -1;
                        if (x.theDate > y.theDate) return 1;
                        if (x.theDate == y.theDate) return 0;
                    });
                } else {
                    resp.data.activity_evaluatings.push({theDate: Util.getDate(), scanNum: 0, redNum: 0});
                }
                // 如果当天的数据为空，则添加一个虚拟当天的数据
                if (resp.data.activity_logs.length > 0) {
                    if (resp.data.activity_logs[resp.data.activity_logs.length - 1].theDate != Util.getDate()) {
                        resp.data.activity_logs.push({theDate: Util.getDate(), scanNum: 0, redNum: 0});
                    }
                } else {
                    resp.data.activity_logs.push({theDate: Util.getDate(), scanNum: 0, redNum: 0});
                }
                Page.drawCharts(resp.data.activity_evaluatings, resp.data.activity_logs);
            } else {
                common.alert(resp.errmsg + '！');
            }
        });
    },
};

var Util = {

    policyLevel: [
        {name: '红包策略', color: "#ffa2a9", img_url: 'hongbao.png'  },
        {name: '乐币策略', color: "#ffa2a9", img_url: 'lebi.png'     }, // deprecated 
        {name: '乐券策略', color: "#3cd0a6", img_url: 'lequan.png'   },
        {name: '组合策略', color: "#bcbcbc", img_url: 'zuhe.png'     },
        {name: '积分策略', color: "#ffbf56", img_url: 'jifen.png'    },
        {name: '叠加策略', color: "#b5cfff", img_url: 'diejia.png'   },
        {name: '累计策略', color: "#329b62", img_url: 'leiji.png'    },
        {name: '未知策略', color: "#a0d762", img_url: 'weidingyi.png'},
    ],

    getDateArr: function(start, end) {
        var startTime = new Date(start);
        var endTime = new Date(end);
        var dateArr = [];
        startTime.setDate(startTime.getDate() - 1);
        while (endTime.getTime() > startTime.getTime()) {
            startTime.setDate(startTime.getDate() + 1);
            var year = startTime.getFullYear();
            var month = startTime.getMonth() + 1;
            if (month <= 9) {
                month = '0' + month.toString();
            }
            var date = startTime.getDate();
            if (date <= 9) {
                date = '0' + date.toString();
            }
            var dateStr = year + '-' + month + '-' + date;
            dateArr.push(year + '-' + month + '-' + date);
        }
        return dateArr;
    },

    getDate: function() {
        var today = new Date();
        var year = today.getFullYear();
        var month = today.getMonth() + 1;
        if (month <= 9) {
            month = '0' + month.toString();
        }
        var date = today.getDate();
        if (date <= 9) {
            date = '0' + date.toString();
        }
        var dateStr = year + '-' + month + '-' + date;
        return dateStr;
    },

    getRedpacketPanelContent: function(html, data) {
        html += '<span class="sub_title">（策略名称：' + data['cont']['name'] + '）</span>';
        html += '<div style="clear:both;height:36px"></div>';
        html += '<div class="top_title"><b>红包名称：</b>' + data['cont']['name'] + '</div>';

        if (data['cont']['levelType'] == 0) { // 不是分级红包
            html += '<table class="table-form">';
            html += '   <tbody>';
            html += '       <tr>';
            html += '           <td class="name">红包名称：</td> '
            html += '           <td class="value"><span>' + data['cont']['name'] + '</span></td>';
            html += '           <td class="name">分级红包：</td>';
            html += '           <td class="value"><span>否</span></td>';
            html += '       </tr>';

            var 
            rpTypeStr = '普通红包',
            amtTypeStr = '固定';
            if (data['cont']['rpType'] != 0) {
                rpTypeStr = '裂变红包';
            }
            if (data['cont']['amtType'] != 0) {
                amtTypeStr = '随机';
            }
            html += '       <tr>';
            html += '           <td class="name">红包类型：</td>';
            html += '           <td class="value"><span>' + rpTypeStr + '</span></td>'
            html += '           <td class="name">额度类型：</td>';
            html += '           <td class="value"><span>' + amtTypeStr + '</span></td>';
            html += '       </tr>';

            html += '       <tr>';
            html += '           <td class="name">红包最小额度：</td>';
            html += '           <td class="value"><span>'+data['cont']['minAmount']+'</span></td>';
            html += '           <td class="name">红包最大额度：</td>';
            html += '           <td class="value"><span>'+data['cont']['maxAmount']+'</span></td>';
            html += '       </tr>';

            var 
            limitTypeStr = '数量',
            total = 0;
            if(data['cont']['limitType'] != 0) {
                limitTypeStr = '金额';
                total = data['cont']['totalAmount'] + '元';
            } else {
                total = data['cont']['totalNum'] + '个';
            }
            html += '       <tr>';
            html += '           <td class="name">上限类型：</td>';
            html += '           <td class="value"><span>' + limitTypeStr + '</span></td>';
            html += '           <td class="name">总' + limitTypeStr + '：</td>';
            html += '           <td class="value"><span>' + total + '</span></td>';
            html += '       </tr>';

            html += '       <tr>';
            html += '           <td class="name">中奖概率：</td>';
            html += '           <td class="value"><span>'+data['cont']['probability']*100+'%</span></td>';
            html += '           <td class="name"></td>';
            html += '           <td class="value"><span></span></td>';
            html += '       </tr>';
            html += '   </tbody>';
            html += '</table>';
        }
        if (data['cont']['levelType'] == 1) { //分级红包
            var priorityStrArr = [
                    '随机',
                    '金额从小到大',
                    '金额从大到小',
                ];
            html += '<table class="table-form">';
            html += '   <tbody>';
            html += '       <tr>';
            html += '           <td class="name">红包名称：</td> '
            html += '           <td class="value"><span>' + data['cont']['name'] + '</span></td>';
            html += '           <td class="name">分级红包：</td>';
            html += '           <td class="value"><span>否</span></td>';
            html += '           <td class="name">分级红包优先级：</td>';
            html += '           <td class="value"><span>' + priorityStrArr[data['cont']['priority']] + '</span></td>';
            html += '       </tr>';

            //循环分级红包子项
            var item;
            for (var i = 0; i < data['cont']['subs'].length; i++) {
                item = data['cont']['subs'][i];
                html += '   <tr>';
                html += '       <td class="name">分级红包额度：</td>';
                html += '       <td class="value"><span>' + item['amount'] / 100 + '元</span></td>';
                html += '       <td class="name">分级红包数量：</td>';
                html += '       <td class="value"><span>' + item['num'] + '个</span></td>';
                html += '       <td class="name">分级红包中奖概率：</td>';
                html += '       <td class="value"><span>' + item['probability'] * 100 + '%</span></td>';
                html += '   </tr>';
            }
            html += '   </tbody>';
            html += '</table>';
        }
        return html;
    },

    getCardPanelContent: function(html, data) {
        html += '<span class="sub_title">（策略名称：'+data['cont']['title']+'）</span>';
        html += '<div style="clear:both;height:36px"></div>';
        html += '<div class="top_title"><b>乐券策略名称：</b>'+data['cont']['title']+'</div>';

        var priorityStrArr = [
                '随机',
                '按中奖概率从小到大',
                '按中奖概率从大到小',
            ];
        html += '<table class="table-form">';
        html += '   <tbody>';
        html += '       <tr>';
        html += '           <td class="name">乐券策略名称：</td>';
        html += '           <td class="value"><span>' + data['cont']['title'] + '</span></td>';
        html += '           <td class="name"></td>';
        html += '           <td class="value"><span></span></td>';
        html += '           <td class="name">乐券优先级：</td>';
        html += '           <td class="value"><span>' + priorityStrArr[data['cont']['priority']] + '</span></td>';
        html += '       </tr>';

        // 循环乐券子项
        var item;
        for (var i = 0; i < data['cont']['subs'].length; i++) {
            item = data['cont']['subs'][i];
            html += '   <tr>';
            html += '       <td class="name">乐券名称：</td>';
            html += '       <td class="value"><span>' + item['title'] + '</span></td>';
            html += '       <td class="name">乐券数量：</td>';
            html += '       <td class="value"><span>' + item['totalNum'] + '张</span></td>';
            html += '       <td class="name">中奖概率：</td>';
            html += '       <td class="value"><span>' + item['probability'] + '%</span></td>';
            html += '   </tr>';
        }
        html += '   </tbody>';
        html += '</table>';
        return html;
    },

    getPointPanelContent: function(html, data) {
        html += '<span class="sub_title">（策略名称：'+data['cont']['name']+'）</span>';
        html += '<div style="clear:both;height:36px"></div>';
        html += '<div class="top_title"><b>积分策略名称：</b>'+data['cont']['name']+'</div>';

        var priorityStrArr = [
                '随机',
                '额度从小到大',
                '额度从大到小',
            ];
        var priorityStr = priorityStrArr[data['cont']['priority']] == undefined ? '未配置' : priorityStrArr[data['cont']['priority']];
        html += '<table class="table-form">';
        html += '   <tbody>';
        html += '       <tr>';
        html += '           <td class="name">积分策略名称：</td>';
        html += '           <td class="value"><span>'+data['cont']['name']+'</span></td>';
        html += '           <td class="name"></td>';
        html += '           <td class="value"><span></span></td>';
        html += '           <td class="name">子策略优先级：</td>'
        html += '           <td class="value"><span>' + priorityStr + '</span></td>';
        html += '       </tr>';

        // 循环乐券子项
        var item;
        for (var i = 0; i < data['cont']['subs'].length; i++) {
            item = data['cont']['subs'][i];
            html += '   <tr>';
            html += '       <td class="name">分级积分额度：</td>';
            html += '       <td class="value"><span>' + item['amount'] + '</span></td>';
            html += '       <td class="name">数量：</td>';
            html += '       <td class="value"><span>' + item['num'] + '个</span></td>';
            html += '       <td class="name">中奖概率：</td>';
            html += '       <td class="value"><span>' + item['probability'] * 100 + '%</span></td>';
            html += '   </tr>';
        }
        html += '   </tbody>';
        html += '</table>';
        return html;
    },

    /**
     * 获取叠加、累计、组合策略的详情界面
     * 
     * @return {string} html content
     */
    getOtherPanelContent: function(html, data) {
        html += '<span class="sub_title">（策略名称：' + data['cont']['name'] + '）</span>';
        html += '<div style="clear:both;height:36px"></div>';

        // 循环乐券子项
        var item;
        for(var i = 0; i < data['cont']['subs'].length; i++) {
            // 红包
            item = data['cont']['subs'][i];
            if (item['strategyType'] == 0) {
                html += '<div class="top_title"><b>红包策略：</b>'+item['content']['name'];
                if (item['weight']) {
                    html += '&nbsp;&nbsp;&nbsp;&nbsp;<b>权重：</b>'+item['weight'];
                }
                html += '</div>';
                if (item['content']['levelType'] == 0) { //不是分级红包
                    html += '<table class="table-form">';
                    html += '   <tbody>';
                    html += '       <tr>';
                    html += '           <td class="name">红包名称：</td>';
                    html += '           <td class="value"><span>'+item['content']['name']+'</span></td>';
                    html += '           <td class="name">分级红包：</td>';
                    html += '           <td class="value"><span>否</span></td>';
                    html += '       </tr>';

                    var 
                    rpTypeStr = '普通红包';
                    amtTypeStr = '固定';
                    if (item['content']['rpType'] != 0) {
                        rpTypeStr = '裂变红包';
                    }
                    if(item['content']['amtType'] != 0) {
                        amtTypeStr = '随机';
                    }
                    html += '       <tr>';
                    html += '           <td class="name">红包类型：</td>';
                    html += '           <td class="value"><span>' + rpTypeStr + '</span></td>';
                    html += '           <td class="name">额度类型：</td>';
                    html += '           <td class="value"><span>' + amtTypeStr + '</span></td>';
                    html += '       </tr>';

                    html += '       <tr>';
                    html += '           <td class="name">红包最小额度：</td>';
                    html += '           <td class="value"><span>'+item['content']['minAmount']+'</span></td>';
                    html += '           <td class="name">红包最大额度：</td>';
                    html += '           <td class="value"><span>'+item['content']['maxAmount']+'</span></td>';
                    html += '       </tr>';

                    var 
                    limitTypeStr = '数量',
                    total = 0;
                    if (item['content']['limitType'] != 0) {
                        limitTypeStr = '金额';
                        total = ['content']['totalNum'] + '个';
                    } else {
                        total = ['content']['totalAmount'] + '元';
                    }
                    html += '       <tr>';
                    html += '           <td class="name">上限类型：</td>';
                    html += '           <td class="value"><span>' + limitTypeStr + '</span></td>';
                    html += '           <td class="name">总' + limitTypeStr + '：</td>';
                    html += '           <td class="value"><span>' + item['content']['totalNum'] + '</span></td>';
                    html += '       </tr>';

                    html += '       <tr>';
                    html += '           <td class="name">中奖概率：</td>';
                    html += '           <td class="value"><span>'+item['content']['probability'] * 100 + '%</span></td>';
                    html += '           <td class="name"></td>';
                    html += '           <td class="value"><span></span></td>';
                    html += '       </tr>';
                    html += '   </tbody>';
                    html += '</table>';
                }

                if (item['content']['levelType'] == 1) { // 分级红包
                    var priorityStrArr = [
                            '随机',
                            '额度从小到大',
                            '额度从大到小',
                        ];
                    html += '<table class="table-form">';
                    html += '   <tbody>';
                    html += '       <tr>';
                    html += '           <td class="name">红包名称：</td>';
                    html += '           <td class="value"><span>' + item['content']['name'] + '</span></td>';
                    html += '           <td class="name">分级红包：</td>';
                    html += '           <td class="value"><span>是</span></td>';
                    html += '           <td class="name">分级红包优先级：</td>';
                    html += '           <td class="value"><span>' + priorityStrArr[item['content']['priority']] + '</span></td>';
                    html += '       </tr>';

                    // 循环分级红包子项
                    var subItem;
                    for(var j = 0; j < item['content']['subs'].length; j++) {
                        subItem = item['content']['subs'][j];
                        html += '   <tr>';
                        html += '       <td class="name">分级红包额度：</td>';
                        html += '       <td class="value"><span>' + subItem['amount'] / 100 + '元</span></td>';
                        html += '       <td class="name">分级红包数量：</td>';
                        html += '       <td class="value"><span>' + subItem['num'] + '个</span></td>';
                        html += '       <td class="name">分级红包中奖概率：</td>';
                        html += '       <td class="value"><span>' + subItem['probability'] * 100 + '%</span></td>';
                        html += '   </tr>';
                    }
                    html += '   </tbody>';
                    html += '</table>';
                }
            }

            // 乐券
            if(item['strategyType'] == 2) {
                html+='<div class="top_title"><b>乐券策略：</b>'+item['content']['title'];
                if(item['weight']){
                    html+="&nbsp;&nbsp;&nbsp;&nbsp;<b>权重：</b>"+item['weight'];
                }
                html+="&nbsp;&nbsp;&nbsp;&nbsp;<b>券组名称：</b>"+item['content']['title'];
                html+='</div>';

                var priorityStrArr = [
                        '随机',
                        '按中奖概率从小到大',
                        '按中奖概率从大到小',
                    ];
                html += '<table class="table-form">';
                html += '   <tbody>';
                html += '       <tr>';
                html += '           <td class="name">乐券策略名称：</td>';
                html += '           <td class="value"><span>'+item['content']['title']+'</span></td>';
                html += '           <td class="name"></td>';
                html += '           <td class="value"><span></span></td>';
                html += '           <td class="name">乐券优先级：</td>';
                html += '           <td class="value"><span>' + priorityStrArr[item['content']['priority']] + '</span></td>';
                html += '       </tr>';

                // 循环乐券子项
                var subItem;
                for (j = 0; j < item['content']['subs'].length; j++) {
                    subItem = item['content']['subs'][j];
                    html += '   <tr>';
                    html += '       <td class="name">乐券名称：</td>';
                    html += '       <td class="value"><span>' + subItem['title'] + '</span></td>';
                    html += '       <td class="name">乐券数量：</td>';
                    html += '       <td class="value"><span>' + subItem['totalNum'] + '张</span></td>';
                    html += '       <td class="name">中奖概率：</td>';
                    html += '       <td class="value"><span>' + subItem['probability'] + '%</span></td>';
                    html += '   </tr>';
                }
                html += '   </tbody>';
                html += '</table>';
            }

            // 积分
            if (item['strategyType'] == 3) {
                html += '<div class="top_title"><b>积分策略：</b>' + item['content']['name'];
                if(item['weight']){
                    html+="&nbsp;&nbsp;&nbsp;&nbsp;<b>权重：</b>"+item['weight'];
                }
                html+="&nbsp;&nbsp;&nbsp;&nbsp;<b>子策略名称：</b>"+item['content']['name'];
                html+='</div>';

                var priorityStrArr = [
                        '随机',
                        '额度从小到大',
                        '额度从大到小',
                    ];
                var priorityStr = priorityStrArr[data['cont']['priority']] == undefined ? '未配置' : priorityStrArr[data['cont']['priority']];
                html += '<table class="table-form">';
                html += '   <tbody>';
                html += '       <tr>';
                html += '           <td class="name">积分策略名称：</td>';
                html += '           <td class="value"><span>'+item['content']['name']+'</span></td>';
                html += '           <td class="name"></td>';
                html += '           <td class="value"><span></span></td>';
                html += '           <td class="name">子策略优先级：</td>';
                html += '           <td class="value"><span>' + priorityStr + '</span></td>';
                html += '       </tr>';

                // 循环积分子项
                var subItem;
                for (j = 0; j < item['content']['subs'].length; j++) {
                    subItem = item['content']['subs'][j];
                    html += '   <tr>';
                    html += '       <td class="name">分级积分额度：</td>';
                    html += '       <td class="value"><span>' + subItem['amount'] + '</span></td>';
                    html += '       <td class="name">数量：</td>';
                    html += '       <td class="value"><span>' + subItem['num'] + '个</span></td>';
                    html += '       <td class="name">中奖概率：</td>';
                    html += '       <td class="value"><span>' + subItem['probability'] * 100 + '%</span></td>';
                    html += '   </tr>';
                }
                html += '   </tbody>';
                html += '</table>';
            }
        }
        return html;
    }
};

$(function(){
	Page.init();
});
