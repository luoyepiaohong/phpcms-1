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

<form action="?m=content&c=create_html&a=show" method="post" name="myform" id="myform">
  <input type="hidden" name="dosubmit" value="1"> 
  <input type="hidden" name="type" value="lastinput"> 
<thead>
<tr>
<th align="center" width="150"><?php echo L('according_model');?></th>
<th align="center" width="386"><?php echo L('select_category_area');?></th>
<th align="center"><?php echo L('select_operate_content');?></th>
</tr>
</thead>
    <tbody height="200" class="nHover td-line">
        <tr>
            <td <?php if($modelid) { ?>rowspan="5" <?php } else { ?>rowspan="2" <?php } ?> colspan="1"><?php
			$models = getcache('model','commons');
			$model_datas = array();
			foreach($models as $_k=>$_v) {
				if($_v['siteid']!=$this->siteid) continue;
				$model_datas[$_v['modelid']] = $_v['name'];
			}
			echo form::select($model_datas,$modelid,'name="modelid" size="2" style="height:200px;width:130px;" onclick="change_model(this.value)"');
		?></td>
            <td <?php if($modelid) { ?>rowspan="5" <?php } else { ?>rowspan="2" <?php } ?>colspan="1"><select name='catids[]' id='catids' multiple="multiple" style="height:200px;width:366px;" title="<?php echo L('push_ctrl_to_select');?>">
<option value='0' selected><?php echo L('no_limit_category');?></option>
<?php echo $string;?>
</select></td>
            <td><font color="red"><?php echo L('every_time');?> <input type="text" name="pagesize" value="10" size="4"> <?php echo L('information_items');?></font></td>
        </tr>
	<?php if($modelid) { ?>
    <tr> 
      <td><?php echo L('last_information');?> <input type="text" name="number" value="" size="5"> <?php echo L('information_items');?></td>
    </tr>
	<tr> 
      <td><?php echo L('update_time_from');?> <?php echo form::date('fromdate');?> <?php echo L('to');?> <?php echo form::date('todate');?><?php echo L('in_information');?></td>
    </tr>
	<tr> 
      <td><?php echo L('update_id_from');?> <input type="text" name="fromid" value="0" size="8"> <?php echo L('to');?> <input type="text" name="toid" size="8"></td>
    </tr>
	<?php } ?>
        <tr>
            <td><input type="button" name="dosubmit1" value=" <?php echo L('submit_start_update');?> " class="button" onclick="myform.type.value='all';dr_bfb('生成内容页面', 'myform', '?m=content&c=create_html&a=show');"><button type="button" onclick="dr_bfb('生成内容页面', 'myform', '?m=content&c=create_html&a=public_show_point')" class="button btn red"> <i class="fa fa-th-large"></i> 上次未执行完毕时继续执行 </button></td>
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
	window.location.href='?m=content&c=create_html&a=show&modelid='+modelid+'&pc_hash='+pc_hash;
}
//-->
</script>