<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="pad-10">
<div class="explain-col">
<?php echo L('html_notice');?>

</div>
<div class="bk10"></div>

<div class="table-list">
<table width="100%" cellspacing="0">

<form action="?m=content&c=create_html&a=category" method="post" name="myform" id="myform">
  <input type="hidden" name="dosubmit" value="1"> 
  <input type="hidden" name="type" value="lastinput"> 
<thead>
<tr>
<th align="center" width="386"><?php echo L('select_category_area');?></th>
<th align="center"><?php echo L('select_operate_content');?></th>
</tr>
</thead>
    <tbody height="200" class="nHover td-line">
        <tr>
            <td rowspan="3" colspan="1"><select name='catids[]' id='catids' multiple="multiple" style="height:200px;width:366px;" title="<?php echo L('push_ctrl_to_select');?>">
<option value='0' selected><?php echo L('no_limit_category');?></option>
<?php echo $string;?>
</select></td>
            <td><font color="red"><?php echo L('every_time');?> <input type="text" name="pagesize" value="10" size="4"> <?php echo L('information_items');?></font></td>
        </tr>
        <tr>
            <td><?php echo L('最大分页限制');?> <input type="text" name="maxsize" value="" size="4"><br>当栏目页数过多时，设置此数量可以生成指定的页数，后面页数就不会再生成</td>
        </tr>
        <tr>
            <td><?php echo L('update_all');?> <input type="button" name="dosubmit1" value="<?php echo L('submit_start_update');?> " class="button" onclick="myform.type.value='all';dr_bfb('生成栏目页面', 'myform', '?m=content&c=create_html&a=category')"><button type="button" onclick="dr_bfb('生成栏目页面', 'myform', '?m=content&c=create_html&a=public_category_point')" class="button btn red"> <i class="fa fa-th-large"></i> 上次未执行完毕时继续执行 </button></td>
        </tr>
    </tbody>
	</form>
</table>

</div>
</div>
<script language="JavaScript">
<!--
window.top.$('#display_center_id').css('display','none');
function change_model(modelid) {
	window.location.href='?m=content&c=create_html&a=category&modelid='+modelid+'&pc_hash='+pc_hash;
}
//-->
</script>