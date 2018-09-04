var shop = {

    init: function() {
        this.createTable();
    },
    
    createTable: function() {
        var _this = this;
        var params = $.extend(hls.common.dataTable, {
            "ajax": {
                "url": '/shop/get_examine_data'
            },
            "columns": [{
                class: 'center',
                "data": "id"
            }, {
                class: 'center',
                "data": "name"
            }, {
                class: 'center',
                "data": "area"
            }, {
                class: 'center',
                "data": "address"
            }, {
                class: 'center',
                "data": 'ownerName'
            }, {
                class: 'center',
                "data": 'ownerPhoneNum'
            }, {
                class: 'center',
                "data": 'createTime'
            }, {
                class: 'center',
                "data": 'state',
                "render": function(data) {
                    if (data == 1) {
                        return "<font color='red'>等待审批</font>";
                    }
                    else if(data==2){
                        return "<font color='green'>审批通过</font>";
                    }
                    return '';
                }
            }, {
                "data": null,
                "class": "center nowrap",
                "render": function(data) {
                    var html = '<a class="btn-text noselect blue pclick" href="/shop/examine_detail/' + data.id + '">'+(data.state==1?'审批':'查看')+'</a>';
                    return html;
                }
            }],
            "initComplete": function() {
                
            },
            "drawCallback": function() {
                
            }
        });

        $('#shopTable').DataTable(params);

    }
};
$(function() {
    shop.init();
});