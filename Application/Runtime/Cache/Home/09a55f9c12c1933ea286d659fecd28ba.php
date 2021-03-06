<?php if (!defined('THINK_PATH')) exit();?><div id="groupadmin_toolbar">
	<a href="#" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-filter'" onClick="create();return false;">创建区域</a>
	<a href="#" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-add'" onClick="add();return false;">新增</a>
	<a href="#" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-remove'" onClick="del();return false;">删除</a>
	<a href="#" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-edit'" onClick="edit();">编辑</a>
	<a href="#" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-save'" onClick="save();return false;">保存</a>
	<a href="#" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-cancel'" onClick="cancel();">取消</a>
	<a href="#" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-reload'" onClick="reFlash();">刷新</a>
</div>
<table id="ga" class="easyui-treegrid"
		data-options="
			url: '/webforcits/home/management/groupadminJson',
			method: 'get',
			rownumbers: false,
			idField: 'id',
			treeField: 'name',
			animate: true,
			fit:true,
			toolbar:'#groupadmin_toolbar'
		">
	<thead>
		<tr>
			<th data-options="field:'name',editor:'text'" width="220">名称</th>
			<th data-options="field:'type',align:'center'" width="100">类型</th>
			<th data-options="field:'size',align:'center'" width="100">成员数量</th>
		</tr>
	</thead>
</table>
<script>
//初始化选中项
var editingId = null;

function selectedItem(){
	if(editingId){
		if(editingId != $('#ga').treegrid('getSelected').id){
			return false;
		}else{
			editingId = $('#ga').treegrid('getSelected').id;
			return true;
		}
	}else{
		editingId = $('#ga').treegrid('getSelected').id;
		return true;
	}
}

//刷新表单
function reFlash(){
	$('#ga').treegrid('reload');
	editingId = null;
	return false;
}

//编辑表单
function edit(){
	if(selectedItem()){
		$('#ga').treegrid('beginEdit',editingId);
	}
	return false;
}

//取消编辑
function cancel(){
	if(editingId){
		$('#ga').treegrid('cancelEdit',editingId);
		editingId = null;
	}
	return false;
}

//保存编辑
function save(){
	if(editingId){
		$('#ga').treegrid('endEdit', editingId);
		var groupname = $('#ga').treegrid('select',editingId).treegrid('getSelected').name;
		ajaxPost(editingId,groupname,'save');
	}
}

//新增组
function add(){
	var Selected = $('#ga').treegrid('getSelected');
	if(Selected && Selected.type == '区域'){
		ajaxPost(Selected.id,Selected.name,'add');
	}else{
		$.messager.show({title:'提示',msg:'只有选中[区域]才能新增组'});
	}
}

//创建区域
function create(){
	$.messager.confirm('请确认','是否新创建一个区域？',function(confirm){
		if(confirm){
			ajaxPost(identify,'','create');
		}else{
			return false;
		}
	});
}

//删除组
function del(){
	var Selected = $('#ga').treegrid('getSelected');
	if(Selected){
		if(Selected.size != 0){
			$.messager.show({title:'提示',msg:'成员数量必须为零才能删除'});
			return false;
		}
		if(Selected.type == '区域'){
			var selectedChildren = $('#ga').treegrid('getChildren',Selected.id);
			if(selectedChildren.length){
				$.messager.show({title:'提示',msg:'区域下必须没有组才能删除'});
				return false;
			}
		}
		$.messager.confirm('请确认','是否删除该' + Selected.type + '？',function(confirm){
			if(confirm){
				ajaxPost(Selected.id,Selected.name,'del');
			}else{
				return false;
			}
		});
	}
}

//提交数据
function ajaxPost(groupid,groupname,action){
	$.post('/webforcits/home/management/groupadmin',{groupid:groupid, groupname:groupname, identify:identify, action:action})
	.success(function(callback){
		if(callback == 1){
			reFlash();
		}else{
			$.messager.alert('警告',callback);
		}
	})
	.error(function(){
		$.messager.alert('警告','无法提交数据，请重试！');
	});
}

</script>