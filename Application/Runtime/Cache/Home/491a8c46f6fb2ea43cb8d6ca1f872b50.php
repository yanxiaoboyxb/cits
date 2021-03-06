<?php if (!defined('THINK_PATH')) exit();?><div style="width:210px;height:100%;position:fixed;">
	<div class="easyui-panel" style="width:200px;height:100%;padding:10px;">
	<ul id="actoradminmenu"></ul>
	</div>
</div>

<div style="margin-left:200px;height:100%;">
	<table id="actoradminmain"></table>
	<div id="actoradminmaintoolbar" style="display:none;">
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-ok',plain:true" onclick="actoradmin_ok_clear('ok');">启用</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-clear',plain:true" onclick="actoradmin_ok_clear('clear');">禁用</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-reload',plain:true" onclick="actoradmin_reflash();">刷新</a>
	</div>
</div>
<script>
//初始化目标
var actoradmin_actorid = false;

$('#actoradminmenu').tree({
	url:'/webforcits/home/management/actoradmin/menu',
	method:'get',
	cascadeCheck:false,
	onClick: function(node){
		actoradmin_actorid = node.id;
		$('#actoradminmain').datagrid({
			url:'/webforcits/home/management/actoradminJson/' + node.id,
			method:'get',
			singleSelect:true,
			striped:true,
			height:document.body.clientHeight - 128,
			width:document.body.clientWidth - 428,
			toolbar:'#actoradminmaintoolbar',
			columns:[[{field:'name',title:'权限名称',width:'200'},{field:'value',title:'启用',width:'100'}]]
		});
	}
});

//刷新
function actoradmin_reflash(){
	$('#actoradminmain').datagrid('reload');
}

//启用、禁用
function actoradmin_ok_clear(action){
	var selected = $('#actoradminmain').datagrid('getSelected');
	if(selected){
		if(action == 'ok'){
			if(selected.value == '否'){actoradmin_ajax(selected.id,'totrue')}
		}else if(action == 'clear'){
			if(selected.value == '是'){actoradmin_ajax(selected.id,'tofalse')}
		}
	}
}

//提交修改
function actoradmin_ajax(target,action){
	if(actoradmin_actorid){
		$.post('/webforcits/home/management/actoradmin',{actorid:actoradmin_actorid,col:target,action:action,identify:identify})
		.success(function(callback){
			if(callback == '1'){
				actoradmin_reflash();
			}else{
				$.messager.show({title:'提示',msg:callback});
			}
		})
		.error(function(){
			$.messager.show({title:'提示',msg:'数据无法提交，请重试！'});
		});
	}
}

</script>