<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>EasyTalk Administrator's Control Panel</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta content="H.Joeson" name="Copyright" /><link rel="stylesheet" href="<?php echo __PUBLIC__;?>/admin/style.css" type="text/css" media="all" /><script src="<?php echo __PUBLIC__;?>/js/jquery.js" type="text/javascript"></script><script src="<?php echo __PUBLIC__;?>/admin/admin.js" type="text/javascript"></script></head><body><div id="bodymain"><div class="title"><?php echo ($position); ?></div><div class="content"><h3>词语过滤</h3><div class="infomation"><b>说明：</b>为不影响程序效率，请不要设置过多不需要的过滤内容。该过滤词语仅对发表广播有效。</div><form action='<?php echo SITE_URL;?>/admin.php?s=/Word/save' method="POST"><table class="table" style="margin:5px 0 20px 0"><tr><td width="90px">&nbsp;</td><td width="250px"><b>不良词语</b></td><td><b>过滤动作</b></td></tr><?php foreach($word as $val){ ?><tr><td><input type="checkbox" name="delid[]" value="<?php echo ($val[id]); ?>" class="checkbox"></td><td><input type="text" name="wordname[<?php echo ($val[id]); ?>]" value="<?php echo ($val[word]); ?>" class="txt_input" style="width:120px"></td><td><select name="optype[<?php echo ($val[id]); ?>]"><option value="1" <?php if($val[type]==1){ ?>selected<?php } ?>>禁止发表</option><option value="2" <?php if($val[type]==2){ ?>selected<?php } ?>>过滤发表</option></select></td></tr><?php } ?><tr id="addbtn"><td>&nbsp;</td><td colspan="3"><a href="javascript:void(0)" onclick="addword()">+ 添加</a></td></tr><tr><td><input type="checkbox" onclick="CheckAll('delid', 'chkall')" id="chkall" name="chkall" class="checkbox"> 删除?</td><td colspan="3"><input type="submit" class="button1" value="提交保存"></td></tr></table></form><script type="text/javascript">
function addword() {
    $('#addbtn').before('<tr><td>&nbsp;</td><td><input type="text" name="n_wordname[]" value="" class="txt_input" style="width:120px"></td><td><select name="n_optype[]"><option value="1">禁止发表</option><option value="2">过滤发表</option></select></td></tr>');
}
</script></div></div></body></html>