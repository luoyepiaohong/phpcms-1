<?php 
defined('IN_ADMIN') or exit('No permission resources.'); 
$show_header = 1;
include $this->admin_tpl('header','admin');
?>

<div class="subnav"> 
<div class="content-menu ib-a blue line-x">
　<?php if(isset($big_menu)) { foreach($big_menu as $big) { echo '<a class="add fb" href="'.$big[0].'"><em>'.$big[1].'</em></a>　'; } }?>&nbsp;<a class="on" href="?m=special&c=special"><em><?php echo L('special_list')?></em></a></div>
</div>
<div class="pad-10">
<div class="table-list">
<form name="myform" id="myform" action="?m=special&c=content&a=listorder&specialid=<?php echo $_GET['specialid']?>" method="post">
    <table width="100%">
        <thead>
            <tr>
			<th width="40" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('id[]');" />
                        <span></span>
                    </label></th>
            <th width="43"><?php echo L('listorder')?></th>
            <th width="60">ID</th>
			<th><?php echo L('content_title')?></th>
			<th width="120"><?php echo L('for_type')?></th>
            <th width="90"><?php echo L('inputman')?></th>
            <th width="120"><?php echo L('update_time')?></th>
			<th width="200"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
<tbody>
    <?php foreach ($datas as $r) {
    	if ($r['curl']) {
    		$content_arr = explode('|', $r['curl']);
    		$r['url'] = go($content_arr['1'], $content_arr['0']);
    	}
    ?>
        <tr>
		<td align="center" width="40" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="id[]" value="<?php echo $r['id'];?>" />
                        <span></span>
                    </label></td>
        <td align='center' width='43'><input name='listorders[<?php echo $r['id'];?>]' type='text' size='3' value='<?php echo $r['listorder'];?>' class='input-text-c'></td>
		<td align='center' width="60"><?php echo $r['id'];?></td>
		<td><a href="<?php echo $r['url'];?>" target="_blank"><?php echo $r['title'];?></a></td>
		<td align='center' width="120"><?php echo $types[$r['typeid']]['name'];?></td>
		<td align='center' width="90"><?php echo $r['username'];?></td>
		<td align='center' width="120"><?php echo format::date($r['updatetime'],1);?></td>
		<td align='center' width="200"><a href="javascript:;" onclick="javascript:contentopen('?m=special&c=content&a=edit&specialid=<?php echo $r['specialid']?>&id=<?php echo $r['id']?>','')"><?php echo L('content_edit')?></a> </td>
	</tr>
     <?php }?>
</tbody>
     </table>
    <div class="btn"><label for="check_box"><?php echo L('selected_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" value="<?php echo L('listorder')?>" /> <input type="button" class="button" value="<?php echo L('delete')?>" onclick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){document.myform.action='?m=special&c=content&a=delete&specialid=<?php echo $_GET['specialid']?>';$('#myform').submit();});"/></div>
    <div id="pages"><?php echo $pages;?></div>
</form>
</div>
</div>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript">
setcookie('refersh_time', 0);
function refersh_window() {
	var refersh_time = getcookie('refersh_time');
	if(refersh_time==1) {
		window.location.reload(true);
	}
}

setInterval("refersh_window()", 5000);
</script>
</body>
</html>