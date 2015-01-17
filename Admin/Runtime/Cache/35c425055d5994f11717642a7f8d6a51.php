<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>EasyTalk Administrator's Control Panel</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta content="H.Joeson" name="Copyright" /><link rel="stylesheet" href="<?php echo __PUBLIC__;?>/admin/style.css" type="text/css" media="all" /><script src="<?php echo __PUBLIC__;?>/js/jquery.js" type="text/javascript"></script><script src="<?php echo __PUBLIC__;?>/admin/admin.js" type="text/javascript"></script></head><body><div id="bodymain"><div class="title"><?php echo ($position); ?></div><div class="content"><h3>地区管理</h3><div class="infomation"><b>说明：</b>你可以自己编辑地区数据，添加，编辑或删除操作后<b>需要点击“确认提交”按钮才生效</b></div><form method="POST" action="<?php echo SITE_URL;?>/admin.php?s=/District/editnames"><table class="table" style="margin:5px 0 20px 0"><tr><td width="400px">选择地区&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <select id="sprovince"><option>-省份-</option><?php foreach($districts as $val){ if($val['level']==1){ ?><option value="<?php echo ($val['id']); ?>"><?php echo ($val['name']); ?></option><?php } } ?></select><select id="scity"><option>-城市地区-</option><?php foreach($districts as $val){ if($val['level']==2 && $val['upid']==$pid){ ?><option value="<?php echo ($val['id']); ?>"><?php echo ($val['name']); ?></option><?php } } ?></select></td><td>&nbsp;</td></tr><?php foreach($districts as $val){ if(($type=='loadprovince' && $val['level']==1) || ($type=='loadcity' && $val['level']==2 && $val['upid']==$pid)){ ?><tr id="tr<?php echo ($val['id']); ?>"><td><input type="text" id="<?php echo ($val['id']); ?>" name="names[<?php echo ($val['id']); ?>]" value="<?php echo ($val['name']); ?>" class="readonly" readonly style="width:110px"></td><td><a href="javascript:void(0)" onclick="editname(<?php echo ($val['id']); ?>)">编辑</a> | <a href="javascript:void(0)" onclick="delname(<?php echo ($val['id']); ?>)">删除</a></td></tr><?php } } ?><tr id="addbtn"><td colspan="2"><a href="javascript:void(0)" onclick="addname()">+ 添加</a></td></tr><tr><td colspan="2"><input type="hidden" name="level" value="<?php echo ($level); ?>"><input type="hidden" name="pid" value="<?php echo ($pid); ?>"><input type="submit" class="button1" value="确认提交"></td></tr></table></form><script type="text/javascript">
$('#sprovince').change(function(){
    var pid=$('#sprovince').val();
    if (parseInt(pid)>0) {
        window.location.href="admin.php?s=/District/index/type/loadcity/pid/"+pid+"/level/2";
    } else {
        window.location.href="admin.php?s=/District";
    }
});
function editname(id){
    $('#'+id).attr('class','txt_input');
    $('#'+id).removeAttr('readonly');
}
function delname(id){
    if (confirm('是否确定要删除该条名称？')) {
        $('#tr'+id).remove();
    }
}
function addname() {
    $('#addbtn').before('<tr><td colspan="2"><input type="text" name="newnames[]" value="" class="txt_input" style="width:110px"></td></tr>');
}
$('#sprovince').val('<?php echo ($pid); ?>');
</script></div></div></body></html>