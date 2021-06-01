<?php $siteinfo = getcache('sitelist', 'commons');$config = string2array($siteinfo[$this->siteid]['setting']);?>
<link href="<?php echo JS_PATH?>jquery-minicolors/jquery.minicolors.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>
<table cellpadding="2" cellspacing="1" width="98%">
    <tr> 
      <td width="220">编辑器样式：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="basic" onclick="$('#bjqms').hide()" checked> 简洁型 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="standard" onclick="$('#bjqms').hide()"> 标准型 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="full" onclick="$('#bjqms').hide()"> 完整型 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="modetool" onclick="$('#bjqms').show()"> 自定义 <span></span></label>
        </div></td>
    </tr>
    <tr id="bjqms" style="display:none;">
      <td>工具栏：</td>
      <td><textarea name="setting[toolvalue]" rows="2" cols="20" id="toolvalue" style="height:100px;width:250px;">'Bold', 'Italic', 'Underline'</textarea><br><?php if (pc_base::load_config('system', 'editor')) {?>必须严格按照CKEditor工具栏格式：'Maximize', 'Source', '-', 'Undo', 'Redo'<?php } else {?>必须严格按照Ueditor工具栏格式：'Fullscreen', 'Source', '|', 'Undo', 'Redo'<?php }?></td>
    </tr>
    <tr>
      <td>默认值：</td>
      <td><textarea name="setting[defaultvalue]" rows="2" cols="20" id="defaultvalue" style="height:100px;width:250px;"></textarea></td>
    </tr>
    <tr<?php if(!$_GET['modelid'] || $_GET['modelid']==-1 || $_GET['modelid']==-2) {echo ' style="display: none;"';}?>> 
      <td>是否启用关联链接：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablekeylink]" value="1">是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablekeylink]" value="0" checked> 否 <span></span></label><input type="text" name="setting[replacenum]" value="1" size="4" class="input-text"> 替换次数 （留空则为替换全部）
          </div></td>
    </tr>
    <tr<?php if(!$_GET['modelid'] || $_GET['modelid']==-1 || $_GET['modelid']==-2) {echo ' style="display: none;"';}?>> 
      <td>关联链接方式：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[link_mode]" value="1" checked> 关键字链接 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[link_mode]" value="0"> 网址链接 <span></span></label>
        </div></td>
    </tr>
    <tr<?php if (!pc_base::load_config('system', 'editor')) {?> style="display: none;"<?php }?>> 
      <td>编辑器颜色：</td>
      <td><input type="text" id="style_color" name="setting[color]" value="" size="6" autocomplete="off" class="input-text" style="height: 22px;"><script type="text/javascript">
      $(function(){
          $("#style_color").minicolors({
              control: $("#style_color").attr("data-control") || "hue",
              defaultValue: $("#style_color").attr("data-defaultValue") || "",
              inline: "true" === $("#style_color").attr("data-inline"),
              letterCase: $("#style_color").attr("data-letterCase") || "lowercase",
              opacity: $("#style_color").attr("data-opacity"),
              position: $("#style_color").attr("data-position") || "bottom left",
              change: function(t, o) {
                  t && (o && (t += ", " + o), "object" == typeof console && console.log(t));
              },
              theme: "bootstrap"
          });
      });
      </script></td>
    </tr>
    <tr<?php if (pc_base::load_config('system', 'editor')) {?> style="display: none;"<?php }?>> 
      <td>编辑器样式：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[theme]" value="default" checked> 默认 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[theme]" value="notadd"> 样式1 <span></span></label>
        </div></td>
    </tr>
    <tr<?php if (pc_base::load_config('system', 'editor')) {?> style="display: none;"<?php }?>> 
      <td>固定编辑器图标栏：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autofloat]" value="1" checked> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autofloat]" value="0"> 否 <span></span></label>
        </div></td>
    </tr>
    <tr<?php if (pc_base::load_config('system', 'editor')) {?> style="display: none;"<?php }?>> 
      <td>自动伸长高度：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autoheight]" value="1"> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autoheight]" value="0" checked> 否 <span></span></label>
        </div></td>
    </tr>
    <?php if ($config['ueditor']) {?>
    <tr> 
      <td>图片水印：</td>
      <td>系统强制开启水印</td>
    </tr>
    <?php } else {?>
    <tr> 
      <td>图片水印：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="1" checked> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="0"> 关闭 <span></span></label>
        </div></td>
    </tr>
    <?php }?>
    <tr<?php if (pc_base::load_config('system', 'editor')) {?> style="display: none;"<?php }?>> 
      <td>将div标签转换为p标签：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[div2p]" value="1" checked> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[div2p]" value="0"> 关闭 <span></span></label>
        </div></td>
    </tr>
    <tr> 
      <td>附件存储策略：</td>
      <td><select class="form-control" name="setting[attachment]">
        <option value="0" selected>本地存储</option>
        <?php foreach ($remote as $i=>$t) {?>
        <option value="<?php echo $i;?>"> <?php echo L($t['name']);?> </option>
        <?php }?>
      </select> 远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败</td>
    </tr>
    <tr> 
      <td>图片压缩大小：</td>
      <td><input type="text" name="setting[image_reduce]" value="" size="20" class="input-text"> 填写图片宽度，例如1000，表示图片大于1000px时进行压缩图片</td>
    </tr>
    <tr> 
      <td>是否保存远程图片：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablesaveimage]" value="1" checked> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablesaveimage]" value="0"> 否 <span></span></label>
        </div></td>
    </tr>
    <tr> 
      <td>编辑器默认高度：</td>
      <td><input type="text" name="setting[height]" value="200" size="4" class="input-text"> px</td>
    </tr>
    <tr> 
      <td>本地图片自动上传：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_img]" value="1" checked> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_img]" value="0" > 否 <span></span></label>
        </div></td>
    </tr>
    <?php if ($config['ueditor']) {?>
    <tr> 
      <td>本地图片上传水印：</td>
      <td>系统强制开启水印</td>
    </tr>
    <?php } else {?>
    <tr> 
      <td>本地图片上传水印：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_watermark]" value="1" checked> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_watermark]" value="0" > 关闭 <span></span></label>
        </div></td>
    </tr>
    <?php }?>
    <tr> 
      <td>本地附件存储策略：</td>
      <td><select class="form-control" name="setting[local_attachment]">
        <option value="0" selected>本地存储</option>
        <?php foreach ($remote as $i=>$t) {?>
        <option value="<?php echo $i;?>"> <?php echo L($t['name']);?> </option>
        <?php }?>
      </select> 远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败</td>
    </tr>
    <tr> 
      <td>本地图片压缩大小：</td>
      <td><input type="text" name="setting[local_image_reduce]" value="" size="20" class="input-text"> 填写图片宽度，例如1000，表示图片大于1000px时进行压缩图片</td>
    </tr>
    <?php if(!$_GET['modelid'] || $_GET['modelid']==-1 || $_GET['modelid']==-2) {?>
    <tr style="display: none;">
      <td>禁止显示编辑器下方的分页符与子标题：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[disabled_page]" value="1" checked> 禁止<span></span></label>
        </div></td>
    </tr>
    <?php } else {?>
    <tr>
      <td>禁止显示编辑器下方的分页符与子标题：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[disabled_page]" value="1"> 禁止 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[disabled_page]" value="0" checked> 显示 <span></span></label>
        </div></td>
    </tr>
    <?php }?>
</table>