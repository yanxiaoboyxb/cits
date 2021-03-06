<?php if (!defined('THINK_PATH')) exit();?><div style="text-align:center;padding-top:50px;">
<div style="margin-bottom:15px;">现金券编号：<?php echo ($ticket); ?></div>
<div style="margin-bottom:15px;">发券人姓名：<?php echo ($sender); ?></div>
<div style="margin-bottom:15px;">现金券总价：<span style="color:red;"><?php echo ($worth); ?></span></div>
<div style="margin-bottom:15px;">现金券剩余：<span style="color:blue;"><?php echo ($remain); ?></span></div>

<div>
<div style="margin-bottom:15px;">选择线路：<input id="neworder_second_route"></div>
<div style="margin-bottom:15px;">出发日期：<input id="neworder_second_starttime"></div>
<div style="margin-bottom:15px;">出发省份：<input id="neworder_second_startprovince"></div>
<div style="margin-bottom:15px;">出发城市：<input id="neworder_second_startcity"></div>
<div style="margin-bottom:30px;"><span id="neworder_second_message"></span></div>
<div>
<a href="javascript:void(0)" class="easyui-linkbutton" onClick="neworder_second_submit()">继续制单</a>
</div>
</div>
</div>
<script>
var newordername = $('#center').tabs('getSelected').panel('options').title;
var order_identify = '<?php echo ($identify); ?>';
var order_overdraft = <?php echo ($EXTRA_order_overdraft); ?>;
var order_none = <?php echo ($EXTRA_order_none); ?>;
var order_remain = <?php echo ($remain); ?>;
var route_price = 0;
var maxCustomerNum = 0;

$('#neworder_second_route').combobox({
	valueField:'price',
	textField:'name',
	url:'/webforcits/home/operation/neworder_secondJson/routelist',
	method:'get',
	required:true,
	missingMessage:'此项必选',
	editable:false,
	onChange:function(newValue,oldValue){
		route_price = newValue;
		maxCustomerNum = Math.floor(order_remain / route_price);
		$('#neworder_second_message').html('该线路价格：' + newValue + ' ，至多能报名 ' + maxCustomerNum + ' 位游客');
	}
});

$('#neworder_second_starttime').datebox({
	required:true,
	missingMessage:'此项必选',
	editable:false,
	formatter:function(date){
		var y = date.getFullYear();
		var m = date.getMonth()+1;
		var d = date.getDate();
		return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
	},
	parser:function(s){
		if (!s) return new Date();
		var ss = (s.split('-'));
		var y = parseInt(ss[0],10);
		var m = parseInt(ss[1],10);
		var d = parseInt(ss[2],10);
		if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
			return new Date(y,m-1,d);
		} else {
			return new Date();
		}
	}
});

$('#neworder_second_startprovince').combobox({
	valueField:'id',
	textField:'name',
	url:'/webforcits/home/operation/neworder_secondJson/province',
	method:'get',
	required:true,
	missingMessage:'此项必选',
	editable:false,
	onChange:function(nv,ov){
		$('#neworder_second_startcity').combobox({
			url:'/webforcits/home/operation/neworder_secondJson/city/' + nv,
			method:'get'
		});
	}
});

$('#neworder_second_startcity').combobox({
	valueField:'id',
	textField:'name',
	required:true,
	missingMessage:'此项必选',
	editable:false
});

function neworder_second_submit(){
	if($('#neworder_second_route').combobox('isValid') && $('#neworder_second_starttime').datebox('isValid') && $('#neworder_second_startprovince').combobox('isValid') &&
	$('#neworder_second_startcity').combobox('isValid')){
		if(!order_overdraft && !order_none){
			if(maxCustomerNum == 0){
				$.messager.show({title:'提示',msg:'当前不能透支使用现金券，无法继续制单！'});
				return false;
			}
		}
		var route = $('#neworder_second_route').combobox('getText');
		var starttime = $('#neworder_second_starttime').datebox('getValue');
		var province = $('#neworder_second_startprovince').combobox('getValue');
		var city = $('#neworder_second_startcity').combobox('getValue');
		$.post('/webforcits/home/operation/neworder',{route:route,starttime:starttime,province:province,city:city,action:'second',order_identify:order_identify,identify:identify})
		.success(function(callback){
			if(callback == '1'){
				$('#center').tabs('getTab',newordername).panel('refresh','/webforcits/home/operation/neworder_third');
			}else{
				$.messager.show({title:'提示',msg:callback});
			}
		})
		.error(function(){
			$.messager.show({title:'提示',msg:'无法提交数据，请重试'});
		});
	}
}
</script>