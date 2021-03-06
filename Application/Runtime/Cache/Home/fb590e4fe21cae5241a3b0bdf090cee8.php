<?php if (!defined('THINK_PATH')) exit();?><div id="neworder_third_table">
	<div id="neworder_third_toolbar" style="display:none;">
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-add'" onClick="neworder_third_add()">新增</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-remove'" onClick="neworder_third_remove()">删除</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-edit'" onClick="neworder_third_edit()">编辑</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-save'" onClick="neworder_third_save()">保存</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-cancel'" onClick="neworder_third_cancel()">取消</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-reload'" onClick="neworder_third_reload()">刷新</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-lock'" onClick="neworder_third_confirm()">确认成单</a>
	</div>
</div>
<script>
$('#neworder_third_table').datagrid({
	url:'/webforcits/home/operation/neworder_thirdJson',
	method:'get',
	striped:true,
	singleSelect:true,
	toolbar:'#neworder_third_toolbar',
	width:document.body.clientWidth - 226,
	height:document.body.clientHeight - 128,
	columns:[[
		{field:'name',title:'姓名',width:100,editor:{type:'textbox',options:{required:true,validType:'checkRealname',missingMessage:'此项必填'}}},
		{field:'idcard',title:'身份证号码',width:150,editor:{type:'textbox',options:{required:true,validType:'checkIdcard',missingMessage:'此项必填'}}},
		{field:'passport',title:'护照',width:100,editor:{type:'textbox',options:{validType:'checkPassport'}}},
		{field:'mobile',title:'手机号码',width:150,editor:{type:'textbox',options:{validType:'checkMobile'}}},
		{field:'email',title:'邮箱',width:200,editor:{type:'textbox',options:{validType:'email',invalidMessage:'邮箱格式错误'}}},
		{field:'address',title:'地址',width:300,editor:{type:'textbox'}},
		{field:'leader',title:'主联系人',width:100,align:'center',editor:{type:'checkbox',options:{on:'是',off:''}}}
	]],
	onClickRow: function(rowIndex, rowData){
		if(neworder_third_editIndex != rowIndex){
			if(neworder_third_endEdit()){
				$('#neworder_third_table').datagrid('cancelEdit',neworder_third_editIndex);
				neworder_third_editIndex = rowIndex;
			}else{
				$('#neworder_third_table').datagrid('selectRow', neworder_third_editIndex);
			}
		}
	},
	onAfterEdit: function(rowIndex, rowData, changes){
		if(neworder_third_action == 'add'){
			var id = 0;
		}else{
			var id = $('#neworder_third_table').datagrid('getSelected').id;
		}
		if(neworder_third_editIndex != undefined){
			neworder_third_ajax(id,rowData,neworder_third_action);
		}
		$('#neworder_third_table').datagrid('unselectAll');
	}
});

//初始化编辑
var newordername = $('#center').tabs('getSelected').panel('options').title;
var neworder_third_editIndex = undefined;
var neworder_third_action = undefined;
var order_identify = '<?php echo ($identify); ?>';
var route_price = <?php echo ($price); ?>;
var order_remain = <?php echo ($remain); ?>;
var order_customernum = <?php echo ($customernum); ?>;
var order_overdraft = <?php echo ($EXTRA_order_overdraft); ?>;
var order_addnum = 0;
var order_maxNum = Math.floor(order_remain / route_price);

function neworder_third_endEdit(){
	if(neworder_third_editIndex == undefined){return true;}
	if($('#neworder_third_table').datagrid('validateRow',neworder_third_editIndex)){
		return true;
	}else{
		return false;
	}
}

//增加
function neworder_third_add(){
	if(neworder_third_endEdit()){
		if(order_overdraft == 0){
			if(order_addnum >= $order_maxNum){
				$.messager.show({title:'提示',msg:'现金券剩余价值过少，无法再增加游客！'});
				return false;
			}
		}
		$('#neworder_third_table').datagrid('cancelEdit',neworder_third_editIndex);
		$('#neworder_third_table').datagrid('appendRow',{});
		neworder_third_editIndex = $('#neworder_third_table').datagrid('getRows').length - 1;
		neworder_third_action = 'add';
		$('#neworder_third_table').datagrid('selectRow', neworder_third_editIndex).datagrid('beginEdit', neworder_third_editIndex);
	}
}

//删除
function neworder_third_remove(){
	if(neworder_third_endEdit()){
		var selectedItem = $('#neworder_third_table').datagrid('getSelected');
		if(selectedItem){
			$.messager.confirm({title:'提示',msg:'是否删除该游客？',fn:function(callback){
				if(callback){
					neworder_third_ajax(selectedItem.id,selectedItem.name,'remove');
				}
			}});
		}
	}
}

//编辑
function neworder_third_edit(){
	if(neworder_third_endEdit() && $('#neworder_third_table').datagrid('getSelected')){
		$('#neworder_third_table').datagrid('selectRow',neworder_third_editIndex);
		$('#neworder_third_table').datagrid('beginEdit',neworder_third_editIndex);
		neworder_third_action = 'edit';
	}
}

//保存
function neworder_third_save(){
	if(neworder_third_editIndex != undefined){
		if(neworder_third_endEdit()){
			if(neworder_third_action == 'add'){
				order_addnum += 1;
			}
			$('#neworder_third_table').datagrid('acceptChanges');
			neworder_third_editIndex = undefined;
			neworder_third_action = undefined;
		}
	}
}

//取消
function neworder_third_cancel(){
	$('#neworder_third_table').datagrid('rejectChanges');
	neworder_third_editIndex = undefined;
	neworder_third_action = undefined;
}

//提交数据
function neworder_third_ajax(id,data,action){
	$.post('/webforcits/home/operation/neworder',{id:id,data:data,mode:action,action:'third',order_identify:order_identify,identify:identify})
	.success(function(callback){
		if(callback != '1'){
			$.messager.show({title:'提示',msg:callback});
		}
		neworder_third_reload();
	})
	.error(function(){
		$.messager.show({title:'提示',msg:'无法提交数据，请重试'});
	});
}

//刷新
function neworder_third_reload(){
	$('#neworder_third_table').datagrid('reload');
	neworder_third_editIndex = undefined;
	neworder_third_action = undefined;
}

//成单
function neworder_third_confirm(){
	$.messager.confirm({title:'请确认',msg:'是否生成订单？',fn:function(callback){
		if(callback){
			$.post('/webforcits/home/operation/neworder',{action:'confirm',order_identify:order_identify,identify:identify})
			.success(function(callback){
				if(callback == '1'){
					$('#center').tabs('getTab',newordername).panel('refresh','/webforcits/home/operation/neworder_confirmed');
				}else{
					$.messager.show({title:'提示',msg:callback,height:1000,width:1000});
				}
			})
			.error(function(){
				$.messager.show({title:'提示',msg:'无法提交数据，请重试'});
			});
		}
	}});
}
</script>