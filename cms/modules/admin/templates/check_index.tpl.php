<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<link rel="stylesheet" href="<?php echo CSS_PATH?>bootstrap/css/bootstrap.min.css" media="all" />
<style type="text/css">
body {background-color: #f5f6f8;font-size: 14px;}
a:hover {text-decoration: underline;}
.note.note-danger {border-radius: 4px;border-left: 4px solid #f0868e;background-color: #ffffff;color: #888;}
.note.note-danger {background-color: #fef7f8;border-color: #f0868e;color: #210406;}
.note {margin: 0 0 20px;padding: 15px 30px 15px 15px;border-left: 5px solid #eee;border-radius: 0 4px 4px 0;}
.note, .tabs-right.nav-tabs>li>a:focus, .tabs-right.nav-tabs>li>a:hover {-webkit-border-radius: 0 4px 4px 0;-moz-border-radius: 0 4px 4px 0;-ms-border-radius: 0 4px 4px 0;-o-border-radius: 0 4px 4px 0;}
.page-container {margin: 0;padding: 0;position: relative;}
.finecms-file-ts, .progress {margin-bottom: 8px!important;}
.progress {border: 0;background-image: none;filter: none;-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;}
.progress {height: 20px;background-color: #fff;border-radius: 4px;}
.embed-responsive, .modal, .modal-open, .progress {overflow: hidden;}
.progress-bar-striped, .progress-striped .progress-bar {background-size: 40px 40px;}
.progress-bar-striped, .progress-striped .progress-bar, .progress-striped .progress-bar-success {background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);}
.progress-bar-striped, .progress-striped .progress-bar, .progress-striped .progress-bar-info, .progress-striped .progress-bar-success, .progress-striped .progress-bar-warning {background-image: -webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image: -o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);}
.progress-bar-success {background-color: #36c6d3;}
.progress-bar {float: left;width: 0;height: 100%;font-size: 12px;line-height: 20px;color: #fff;background-color: #337ab7;-webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);-webkit-transition: width .6s ease;-o-transition: width .6s ease;transition: width .6s ease;}
.badge, .input-group-addon, .label, .pager, .progress-bar {text-align: center;}
.progress-bar {float: left;width: 0;height: 100%;font-size: 12px;line-height: 20px;color: #fff;background-color: #36c6d3;-webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);-webkit-transition: width .6s ease;-o-transition: width .6s ease;transition: width .6s ease;}
.page-content-wrapper {float: left;width: 100%;}
.page-content-wrapper .page-content {margin-top: 0;padding: 25px 20px 10px;}
.text-center {text-align: center;}
.btn {display: inline-block;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none}
.btn.active.focus,.btn.active:focus,.btn.focus,.btn:active.focus,.btn:active:focus,.btn:focus {outline: dotted thin;outline: -webkit-focus-ring-color auto 5px;outline-offset: -2px}
.btn.focus,.btn:focus,.btn:hover {color: #333;text-decoration: none}
.btn.active,.btn:active {outline: 0;-webkit-box-shadow: inset 0 3px 5px rgba(0,0,0,.125);box-shadow: inset 0 3px 5px rgba(0,0,0,.125)}
.btn.disabled,.btn[disabled],fieldset[disabled] .btn {cursor: not-allowed;opacity: .65;filter: alpha(opacity=65);-webkit-box-shadow: none;box-shadow: none}
.btn.green-meadow:not(.btn-outline) {color: #FFF;background-color: #1BBC9B;border-color: #1BBC9B;}
.btn:not(.btn-sm):not(.btn-lg) {line-height: 1.44;}
.btn {outline: 0!important;}
.btn, .form-control {box-shadow: none!important;}
.btn {display: inline-block;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;
font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
.margin-top-20 {margin-top: 20px!important;}
.margin-top-30 {margin-top: 30px!important;}
.btn,.btn-group,.btn-group-vertical,.caret,.checkbox-inline,.radio-inline,img {vertical-align: middle}
.well {background-color: #ffffff!important;}
.well {border: 0;padding: 20px;-webkit-box-shadow: none!important;-moz-box-shadow: none!important;box-shadow: none!important;}
.well {min-height: 20px;margin-bottom: 20px;background-color: #f1f4f7;border-radius: 4px;}
#dr_check_bf p, #dr_check_html p {margin: 10px 0;clear: both;}
#dr_check_html .p_error {color: red;}
#dr_check_html .rleft {float: left;}
#dr_check_bf .rright, #dr_check_html .rright {float: right;}
#dr_check_html .rright .ok {color: green;}
#dr_check_html .rright .error {color: red;}
label {font-weight: 400;}
#dr_check_bf p, #dr_check_html .rright .error {color: red;}
#dr_check_bf p, #dr_check_html p {margin: 10px 0;clear: both;}
.right-card-box {position: relative;display: -webkit-box;display: -ms-flexbox;display: flex;-webkit-box-orient: vertical;-webkit-box-direction: normal;-ms-flex-direction: column;flex-direction: column;min-width: 0;word-wrap: break-word;background-color: #fff;background-clip: border-box;border: 0 solid #f7f7f7;border-radius: .25rem;padding: 1.5rem;}
.badge-success {background-color: #36c6d3;}
</style>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery-3.5.1.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.slimscroll.min.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content  ">
                            <div class="page-body" style="padding-top:0px;margin-bottom:30px;">
<div class="right-card-box">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
    <tr>
        <th width="55"> </th>
        <th width="300"> <?php echo L('检查项目');?> </th>
        <th> <?php echo L('检查结果');?> </th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($list as $id=>$t) {?>
    <tr>
        <td>
            <span class="badge badge-success"> <?php echo $id;?> </span>
        </td>
        <td>
            <?php echo $t;?>
        </td>
        <td id="dr_<?php echo $id;?>_result">
            <img style='height:17px' src='<?php echo JS_PATH;?>layer/theme/default/loading-0.gif'>
        </td>
    </tr>
    <script>
        $(function () {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "?m=admin&c=check&a=public_do_index&id=<?php echo $id;?>",
                success: function (json) {
                    $('#dr_<?php echo $id;?>_result').html(json.msg);
                    if (json.code == 0) {
                        $('#dr_<?php echo $id;?>_result').attr('style', 'color:red');
                    } else {
                        $('#dr_<?php echo $id;?>_result').attr('style', 'color:green');
                    }
                },
                error: function(HttpRequest, ajaxOptions, thrownError) {
                    $('#dr_<?php echo $id;?>_result').attr('style', 'color:red');
                    $('#dr_<?php echo $id;?>_result').html('系统异常，请检查错误日志');
                }
            });
        });
    </script>
    <?php }?>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</body>
</html>