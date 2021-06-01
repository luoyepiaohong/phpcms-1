<?php $show_header = $show_validator = $show_scroll = 1; include $this->admin_tpl('header', 'attachment');?>
<script src="<?php echo JS_PATH?>assets/ds.min.js"></script>
<link href="<?php echo JS_PATH?>h5upload/h5upload.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo JS_PATH?>layui/css/layui.css" media="all" />
<script type="text/javascript" src="<?php echo JS_PATH?>layui/layui.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo JS_PATH?>h5upload/handlers.js"></script>
<script type="text/javascript">
<?php echo initupload($_GET['module'],$_GET['catid'],$args,$this->userid,$this->groupid,$this->isadmin,$userid_h5)?>
</script>
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_h5_1"<?php echo $tab_status?> onclick="SwapTab('h5','on','',5,1);"><?php echo L('upload_attachment')?></li>
            <li id="tab_h5_2" onclick="SwapTab('h5','on','',5,2);"><?php echo L('net_file')?></li>
            <?php if($allowupload && $this->admin_username && $_SESSION['userid']) {?>
            <li id="tab_h5_3" onclick="SwapTab('h5','on','',5,3);set_iframe('album_list','index.php?m=attachment&c=attachments&a=album_load&args=<?php echo $args?>');"><?php echo L('gallery')?></li>
            <li id="tab_h5_4" onclick="SwapTab('h5','on','',5,4);set_iframe('album_dir','index.php?m=attachment&c=attachments&a=album_dir&args=<?php echo $args?>');"><?php echo L('directory_browse')?></li>
            <?php }?>
            <?php if($att_not_used!='') {?>
            <li id="tab_h5_5" class="on icon" onclick="SwapTab('h5','on','',5,5);set_iframe('att_not','index.php?m=attachment&c=attachments&a=att_not&args=<?php echo $args?>');"><?php echo L('att_not_used')?></li>
            <?php }?>
        </ul>
        <div id="div_h5_1" class="content pad-10<?php echo $div_status?>">
            <div>
                <div id="queue"></div>
                <button type="button" class="layui-btn" id="file_upload"><i class="layui-icon">&#xe67c;</i><?php echo L('select_file')?></button>
                <div id="nameTip" class="onShow"><?php echo L('upload_up_to')?><font color="red"> <?php echo $file_upload_limit?></font> <?php echo L('attachments')?>,<?php echo L('largest')?> <font color="red"><?php echo $file_size_limit?></font></div>
                <div class="bk3"></div>
                <div class="lh24"><?php echo L('supported')?> <font style="font-family: Arial, Helvetica, sans-serif"><?php echo str_replace(array('*.',';'),array('','、'),$file_types)?></font> <?php echo L('formats')?></div>
            </div>
            <div class="bk10"></div>
            <fieldset class="blue pad-10" id="h5upload">
                <legend><?php echo L('lists')?></legend>
                <div id="fsUploadProgress"></div>
                <div class="files" id="fsUpload"></div>
            </fieldset>
        </div>
        <div id="div_h5_2" class="contentList pad-10 hidden">
            <div class="bk10"></div>
            <?php echo L('enter_address')?><div class="bk3"></div><input type="text" name="info[filename]" class="input-text filename" value="" onblur="addonlinefile(this)">
            <div class="bk10"></div>
        </div>
        <?php if($allowupload && $this->admin_username && $_SESSION['userid']) {?>
        <div id="div_h5_3" class="contentList pad-10 hidden">
            <ul class="attachment-list">
                <iframe name="album-list" src="#" frameborder="false" scrolling="auto" style="overflow-x:hidden;border:none" width="100%" height="450" allowtransparency="true" id="album_list"></iframe>
            </ul>
        </div>
        <div id="div_h5_4" class="contentList pad-10 hidden">
            <ul class="attachment-list">
             <iframe name="album-dir" src="#" frameborder="false" scrolling="auto" style="overflow-x:hidden;border:none" width="100%" height="450" allowtransparency="true" id="album_dir"></iframe>
            </ul>
        </div>
        <?php }?>
        <?php if($att_not_used!='') {?>
        <div role="presentation" id="div_h5_5" class="contentList pad-10">
            <script type="text/javascript">
            $(document).ready(function(){
                set_iframe('att_not','index.php?m=attachment&c=attachments&a=att_not&args=<?php echo $args?>');
            })
            </script>
            <ul class="attachment-list">
             <iframe name="att-not" src="#" frameborder="false" scrolling="auto" style="overflow-x:hidden;border:none" width="100%" height="450" allowtransparency="true" id="att_not"></iframe>
            </ul>
        </div>   
        <?php }?>     
    <div id="att-status" class="hidden"></div>
    <div id="att-status-del" class="hidden"></div>
    <div id="att-name" class="hidden"></div>
<!-- h5 -->
</div>
</body>
<script type="text/javascript">
if ($.browser.mozilla) {
    window.onload=function(){
      if (location.href.indexOf("&rand=")<0) {
            location.href=location.href+"&rand="+Math.random();
        }
    }
}
function imgWrap(obj){
    $(obj).hasClass('on') ? $(obj).removeClass("on") : $(obj).addClass("on");
}

function SwapTab(name,cls_show,cls_hide,cnt,cur) {
    for(i=1;i<=cnt;i++){
        if(i==cur){
             $('#div_'+name+'_'+i).show();
             $('#tab_'+name+'_'+i).addClass(cls_show);
             $('#tab_'+name+'_'+i).removeClass(cls_hide);
        }else{
             $('#div_'+name+'_'+i).hide();
             $('#tab_'+name+'_'+i).removeClass(cls_show);
             $('#tab_'+name+'_'+i).addClass(cls_hide);
        }
    }
}

function addonlinefile(obj) {
    var strs = $(obj).val() ? '|'+ $(obj).val() :'';
    $('#att-status').html(strs);
}

function set_iframe(id,src){
    $("#"+id).attr("src",src); 
}
</script>
</html>