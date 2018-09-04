var charts = {
	init:function(){
		var _this = this;
		$.post('/charts/manage',{},function(res){
            // var = res.data;
            // console.log(res.data[0]);
            for (var i=0;i<res.data.length;i++){
                console.log(res.data[0][i]);
                var option = "<option val='"+res.data[0][i].id+"'>"+res.data[0][i].name+"</option>";
                $('#chose').append(option);
            }
        },'json');
		
		
	}
};
$(function(){
	charts.init();
});