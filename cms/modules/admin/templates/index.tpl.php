<?php defined('IN_ADMIN') or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo L('admin_site_title')?></title>
<meta name="author" content="zhaoxunzhiyin" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="stylesheet" href="<?php echo CSS_PATH?>font-awesome/css/font-awesome.min.css" media="all">
<link rel="stylesheet" href="<?php echo JS_PATH?>layui/css/layui.css" media="all">
<link rel="stylesheet" href="<?php echo CSS_PATH?>layuimini/css/layuimini.css" media="all">
<link rel="stylesheet" href="<?php echo CSS_PATH?>layuimini/css/themes/default.css" media="all">
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>Dialog/main.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>styleswitch.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>dialog.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.sgallery.js"></script>
<script src="<?php echo JS_PATH?>jquery.backstretch.min.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>admin_common.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.nicescroll.js"></script>
<script type="text/javascript">
var pc_hash = '<?php echo $_SESSION['pc_hash']?>'
</script>
<!--[if lt IE 9]>
<script src="<?php echo CSS_PATH?>layuimini/js/html5.min.js"></script>
<script src="<?php echo CSS_PATH?>layuimini/js/respond.min.js"></script>
<![endif]-->
<style id="layuimini-bg-color"></style>
</head>
<body class="layui-layout-body layuimini-all">
<div id="ew-lock-screen-group" style="display :<?php if(isset($_SESSION['lock_screen']) && $_SESSION['lock_screen']==0) echo 'none';?>">
    <div class="lock-screen-wrapper">
        <div class="lock-screen-time"></div>
        <div class="lock-screen-date"></div>
        <div class="lock-screen-form">
            <input id="lock_password" placeholder="<?php echo L('lockscreen_status');?>" class="lock-screen-psw" maxlength="20" type="password">
            <i class="layui-icon layui-icon-right lock-screen-enter"></i>
            <br>
            <div class="lock-screen-tip"></div>
        </div>
        <div class="lock-screen-tool">
            <div class="lock-screen-tool-item">
                <i class="layui-icon layui-icon-logout" ew-event="logout" data-confirm="false" data-url="?m=admin&c=index&a=public_logout"></i>
                <div class="lock-screen-tool-tip"><?php echo L('exit_login');?></div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#ew-lock-screen-group').backstretch([
        "<?php echo IMG_PATH?>admin_img/bg-screen1.jpg","<?php echo IMG_PATH?>admin_img/bg-screen2.jpg","<?php echo IMG_PATH?>admin_img/bg-screen3.jpg","<?php echo IMG_PATH?>admin_img/bg-screen4.jpg","<?php echo IMG_PATH?>admin_img/bg-screen5.jpg","<?php echo IMG_PATH?>admin_img/bg-screen6.jpg","<?php echo IMG_PATH?>admin_img/bg-screen7.jpg"], {
        fade: 1000,
        duration: 8000
    });
    // ??????????????????
    var $form = $('.lock-screen-form');
    var $psw = $('.lock-screen-psw');
    var $tip = $('.lock-screen-tip');
    var $time = $('.lock-screen-time');
    var $date = $('.lock-screen-date');
    var $tool = $('.lock-screen-tool-item');

    // ??????enter???
    $(window).keydown(function (event) {
        if (event.keyCode === 13) {
            doVer();
        } else if (event.keyCode === 8 && !$psw.val()) {
            restForm();
            if (event.preventDefault) event.preventDefault();
            if (event.returnValue) event.returnValue = false;
        }
    });

    // ????????????
    $psw.on('input', function () {
        var psw = $psw.val();
        if (psw) {
            $form.removeClass('show-back');
            $tip.text('');
        } else {
            $form.addClass('show-back');
        }
    });

    // ??????????????????
    $form.find('.lock-screen-enter').click(function (e) {
        doVer(true);
    });

    // ????????????
    function doVer(emptyRest) {
        if ($form.hasClass('show-psw')) {
            $psw.focus();
            var psw = $psw.val();
            if (!psw) {
                emptyRest ? restForm() : $tip.text('<?php echo L('lockscreen_status_password');?>');
            } else {
                $.get("?m=admin&c=index&a=public_login_screenlock", { lock_password: psw},function(data){
                    if(data==1) {
                        $('#ew-lock-screen-group').css('display','none');
                        restForm();
                    } else if(data==3) {
                        $psw.val('');
                        $tip.text('<?php echo L('wait_1_hour_lock');?>');
                        $form.addClass('show-back');
                    } else {
                        strings = data.split('|');
                        $psw.val('');
                        $tip.text('<?php echo L('password_error_lock');?>'+strings[1]+'<?php echo L('password_error_lock2');?>');
                        $form.addClass('show-back');
                    }
                });
            }
        } else {
            $form.addClass('show-psw show-back');
            $psw.focus();
        }
    }

    // ??????
    function restForm() {
        $psw.blur();
        $psw.val('');
        $tip.text('');
        $form.removeClass('show-psw show-back');
    }
    
    $tool.on('click', function () {
        var tool = $tool.children().attr("data-url");
        location.href = tool;
    });

    // ???????????????
    function setDate() {
        var FIRSTYEAR = 1998;
        var LASTYEAR = 2031;
        var dateObj = new Date(); //???????????????????????????Date??????
        var year = dateObj.getFullYear(); //????????????????????????????????????
        var month = dateObj.getMonth()+1; //??????????????????????????????
        var date = dateObj.getDate(); //????????????????????????????????????
        var day = dateObj.getDay(); //?????????????????????????????????
        var weeks = ["?????????","?????????","?????????","?????????","?????????","?????????","?????????"];
        var week = weeks[day]; //????????????????????????????????????????????????????????????
        var hour = dateObj.getHours(); //??????????????????????????????
        var minute = dateObj.getMinutes(); //??????????????????????????????
        var second = dateObj.getSeconds(); //??????????????????????????????
        var LunarCal = [
        new tagLunarCal( 27,  5, 3, 43, 1, 0, 0, 1, 0, 0, 1, 1, 0, 1, 1, 0, 1 ),
        new tagLunarCal( 46,  0, 4, 48, 1, 0, 0, 1, 0, 0, 1, 0, 1, 1, 1, 0, 1 ), /* 88 */
        new tagLunarCal( 35,  0, 5, 53, 1, 1, 0, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1 ), /* 89 */
        new tagLunarCal( 23,  4, 0, 59, 1, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 42,  0, 1,  4, 1, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 31,  0, 2,  9, 1, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 0 ),
        new tagLunarCal( 21,  2, 3, 14, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1 ), /* 93 */
        new tagLunarCal( 39,  0, 5, 20, 0, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 28,  7, 6, 25, 1, 0, 1, 0, 1, 0, 1, 0, 1, 1, 0, 1, 1 ),
        new tagLunarCal( 48,  0, 0, 30, 0, 0, 1, 0, 0, 1, 0, 1, 1, 1, 0, 1, 1 ),
        new tagLunarCal( 37,  0, 1, 35, 1, 0, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 1 ), /* 97 */
        new tagLunarCal( 25,  5, 3, 41, 1, 1, 0, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1 ),
        new tagLunarCal( 44,  0, 4, 46, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1 ),
        new tagLunarCal( 33,  0, 5, 51, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 22,  4, 6, 56, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0 ), /* 101 */
        new tagLunarCal( 40,  0, 1,  2, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0 ),
        new tagLunarCal( 30,  9, 2,  7, 0, 1, 0, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 49,  0, 3, 12, 0, 1, 0, 0, 1, 0, 1, 1, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 38,  0, 4, 17, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 1, 0 ), /* 105 */
        new tagLunarCal( 27,  6, 6, 23, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1, 1 ),
        new tagLunarCal( 46,  0, 0, 28, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1, 0 ),
        new tagLunarCal( 35,  0, 1, 33, 0, 1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 0 ),
        new tagLunarCal( 24,  4, 2, 38, 0, 1, 1, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ), /* 109 */
        new tagLunarCal( 42,  0, 4, 44, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 31,  0, 5, 49, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0 ),
        new tagLunarCal( 21,  2, 6, 54, 0, 1, 0, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 40,  0, 0, 59, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 1, 0, 1 ), /* 113 */
        new tagLunarCal( 28,  6, 2,  5, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1, 1, 0 ),
        new tagLunarCal( 47,  0, 3, 10, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 1, 0, 1 ),
        new tagLunarCal( 36,  0, 4, 15, 1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 0, 1 ),
        new tagLunarCal( 25,  5, 5, 20, 1, 1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 0 ), /* 117 */
        new tagLunarCal( 43,  0, 0, 26, 1, 1, 0, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1 ),
        new tagLunarCal( 32,  0, 1, 31, 1, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 0 ),
        new tagLunarCal( 22,  3, 2, 36, 0, 1, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0 ) ];
        /* ???????????????????????? */
        SolarCal = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
        /* ??????????????????????????????, ??????????????? */ 
        SolarDays = [  0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365, 396,  0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366, 397 ];
        AnimalIdx = ["???", "???", "???", "???", "???", "???", "???", "???", "???", "???", "???", "???" ];
        LocationIdx = [ "???", "???", "???", "???" ];
        if ( year <= FIRSTYEAR || year > LASTYEAR ) return 1;
        sm = month - 1;
        if ( sm < 0 || sm > 11 ) return 2;
        leap = GetLeap( year );
        if ( sm == 1 )
        d = leap + 28;
        else
        d = SolarCal[sm];
        if ( date < 1 || date > d ) return 3;
        y = year - FIRSTYEAR;
        acc = SolarDays[ leap*14 + sm ] + date;
        kc = acc + LunarCal[y].BaseKanChih;
        Kan = kc % 10;
        Chih = kc % 12;
        Location = LocationIdx[kc % 4];
        Age = kc % 60;
        if ( Age < 22 )
        Age = 22 - Age;
        else
        Age = 82 - Age;
        Animal = AnimalIdx[ Chih ];
        if ( acc <= LunarCal[y].BaseDays ) {
        y--;
        LunarYear = year - 1;
        leap = GetLeap( LunarYear );
        sm += 12;
        acc = SolarDays[leap*14 + sm] + date;
        }
        else
        LunarYear = year;
        l1 = LunarCal[y].BaseDays;
        for ( i=0; i<13; i++ ) {
        l2 = l1 + LunarCal[y].MonthDays[i] + 29;
        if ( acc <= l2 ) break;
        l1 = l2;
        }
        LunarMonth = i + 1;
        LunarDate = acc - l1;
        im = LunarCal[y].Intercalation;
        if ( im != 0 && LunarMonth > im ) {
        LunarMonth--;
        if ( LunarMonth == im ) LunarMonth = -im;
        }
        if ( LunarMonth > 12 ) LunarMonth -= 12;
        var months = ["???","???","???","???","???","???","???","???","???","???","??????","???"];
        var days = ["??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????","??????"];
        if (LunarMonth < 0) {
        LunarMonth = "???" + months[-LunarMonth-1];
        }else{
        LunarMonth = months[LunarMonth-1];
        }
        LunarDate = days[LunarDate-1];
        var timeValue = "" +((hour >= 12) ? (hour >= 18) ? "????????????" : "????????????" : "????????????" ); //?????????????????????????????????????????????
        newDate = dateFilter(year)+"???"+dateFilter(month)+"???"+dateFilter(date)+"??????"+week+"?????????" + LunarMonth + "???" + LunarDate + "???";
        $time.text(dateFilter(hour)+":"+dateFilter(minute)+":"+dateFilter(second));
        $date.text(timeValue+newDate);
    }
    
    //?????????10??????????????????0
    function dateFilter(date){
        if(date < 10){return "0"+date;}
        return date;
    }
    /* ??????????????????????????????, ?????? 0 ?????????, 1 ????????? */
    function GetLeap( year ) {
        if ( year % 400 == 0 )
        return 1;
        else if ( year % 100 == 0 )
        return 0;
        else if ( year % 4 == 0 )
        return 1;
        else
        return 0;
    }
    function tagLunarCal( d, i, w, k, m1, m2, m3, m4, m5, m6, m7, m8, m9, m10, m11, m12, m13) {
        this.BaseDays = d;         /* ????????? 1 ??? 1 ??????????????????????????????????????? */
        this.Intercalation = i;    /* ????????????. 0==?????????????????? */
        this.Baseday = w;      /* ???????????? 1 ??? 1 ????????????????????? 1 */
        this.BaseKanChih = k;      /* ???????????? 1 ??? 1 ????????????????????? 1 */
        this.MonthDays = [ m1, m2, m3, m4, m5, m6, m7, m8, m9, m10, m11, m12, m13 ]; /* ???????????????????????????, 0==??????(29???), 1==??????(30???) */
    }
    
    setInterval(function () {setDate();}, 1000);

});
</script>
<div class="layui-layout layui-layout-admin">
    <div class="layui-header header">
        <div class="layui-logo layuimini-logo"></div>

        <div class="layuimini-header-content">
            <a>
                <div class="layuimini-tool"><i onmouseover="layer.tips('<?php echo L('spread_or_closed')?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();" class="fa fa-outdent" data-side-fold="1"></i></div>
            </a>

            <!--?????????????????????-->
            <ul class="layui-nav layui-layout-left layuimini-header-menu layuimini-menu-header-pc layuimini-pc-show">
            </ul>

            <!--?????????????????????-->
            <ul class="layui-nav layui-layout-left layuimini-header-menu layuimini-mobile-show">
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="fa fa-list-ul"></i> ????????????</a>
                    <dl class="layui-nav-child layuimini-menu-header-mobile">
                    </dl>
                </li>
            </ul>

            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item layuimini-setting layuimini-unselect">
                    <a href="javascript:;" data-share="<?php echo L('??????')?>"><i class="fa fa-share-alt"></i></a>
                    <dl class="layui-nav-child scroller" style="min-width: 160px;max-width: 275px;width: 275px;height:300px;overflow-x:hidden;overflow-y:auto;">
                        <?php foreach ($sitelist as $key=>$v):?>
                        <dd>
                            <a href="javascript:site_select(<?php echo $v['siteid']?>);" style="<?php if ($siteid==$v['siteid']) {echo 'color:red!important;';}?>border-bottom: 1px solid #EFF2F6!important;" data-title="<?php echo $v['name']?>" data-icon="fa fa-gears"><?php echo $v['name']?><?php if ($siteid==$v['siteid']) {echo '<span class="layui-badge-dot"></span>';}?></a>
                        </dd>
                        <?php endforeach;?>
                    </dl>
                </li>
                <li class="layui-nav-item layuimini-unselect" lay-unselect>
                    <a href="<?php echo $currentsite['domain']?>" target="_blank" data-home="<?php echo L('site_homepage')?>"><i class="fa fa-home"></i></a>
                </li>
                <li class="layui-nav-item layuimini-unselect" lay-unselect>
                    <a href="<?php echo WEB_PATH;?>index.php?m=member" target="_blank" data-member="<?php echo L('member_center')?>"><i class="fa fa-user"></i></a>
                </li>
                <li class="layui-nav-item layuimini-unselect" lay-unselect>
                    <a href="<?php echo WEB_PATH;?>index.php?m=search" target="_blank" id="site_search" data-search="<?php echo L('search')?>"><i class="fa fa-search"></i></a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" data-refresh="<?php echo L('??????')?>"><i class="fa fa-refresh"></i></a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:dr_bfb('<?php echo L('update_backup')?>', 'myform', '?m=admin&c=cache_all&a=init&pc_hash=<?php echo $_SESSION['pc_hash'];?>');" data-cache-all="<?php echo L('update_backup')?>" class="layuimini-clear"><i class="fa fa-trash-o"></i></a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" onclick="lock_screen()" data-lock="<?php echo L('lockscreen')?>"><i class="fa fa-lock"></i></a>
                </li>
                <li class="layui-nav-item mobile layui-hide-xs" lay-unselect>
                    <a href="javascript:;" data-check-screen="full"><i class="fa fa-arrows-alt"></i></a>
                </li>
                <li class="layui-nav-item layuimini-setting">
                    <a href="javascript:;">admin</a>
                    <dl class="layui-nav-child">
                        <dd>
                            <a href="javascript:;" layuimini-content-href="?m=admin&c=admin_manage&a=public_edit_info&pc_hash=<?php echo $_SESSION['pc_hash']?>" data-title="????????????" data-icon="fa fa-gears">????????????<span class="layui-badge-dot"></span></a>
                        </dd>
                        <dd>
                            <a href="javascript:;" layuimini-content-href="?m=admin&c=admin_manage&a=public_edit_pwd&pc_hash=<?php echo $_SESSION['pc_hash']?>" data-title="????????????" data-icon="fa fa-gears">????????????</a>
                        </dd>
                        <dd>
                            <hr>
                        </dd>
                        <dd>
                            <a href="javascript:;" class="login-out">????????????</a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item layuimini-select-bgcolor" lay-unselect>
                    <a href="javascript:;" data-bgcolor="????????????"><i class="fa fa-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
    </div>

    <!--?????????????????????-->
    <div class="layui-side layui-bg-black layuimini-menu-left">
    </div>

    <!--??????????????????-->
    <div class="layuimini-loader">
        <div class="layuimini-loader-inner"></div>
    </div>

    <!--??????????????????-->
    <div class="layuimini-make"></div>

    <!-- ???????????? -->
    <div class="layuimini-site-mobile"><i class="layui-icon">???</i></div>

    <div class="layui-body">

        <div class="layuimini-tab layui-tab-rollTool layui-tab" lay-filter="layuiminiTab" lay-allowclose="true">
            <ul class="layui-tab-title">
                <li class="layui-this" id="layuiminiHomeTabId" lay-id=""></li>
            </ul>
            <div class="layui-tab-control">
                <li class="layuimini-tab-roll-left layui-icon layui-icon-left"></li>
                <li class="layuimini-tab-roll-right layui-icon layui-icon-right"></li>
                <li class="layui-tab-tool layui-icon layui-icon-down">
                    <ul class="layui-nav close-box">
                        <li class="layui-nav-item">
                            <a href="javascript:;"><span class="layui-nav-more"></span></a>
                            <dl class="layui-nav-child">
                                <dd><a href="javascript:;" layuimini-tab-close="current">??? ??? ??? ???</a></dd>
                                <dd><a href="javascript:;" layuimini-tab-close="other">??? ??? ??? ???</a></dd>
                                <dd><a href="javascript:;" layuimini-tab-close="all">??? ??? ??? ???</a></dd>
                            </dl>
                        </li>
                    </ul>
                </li>
            </div>
            <div class="layui-tab-content">
                <div id="layuiminiHomeTabIframe" class="layui-tab-item layui-show"></div>
                <div class="fav-nav">
					<div id="panellist">
						<?php foreach($adminpanel as $v) {?>
								<span>
								<a href="javascript:paneladdclass(this);" layuimini-content-href="<?php echo $v['url'].'&menuid='.$v['menuid'].'&pc_hash='.$_SESSION['pc_hash'];?>" data-title="<?php echo L($v['name'])?>" data-icon="<?php echo $v['icon'];?>"><i class="<?php echo $v['icon'];?>"></i><cite><?php echo L($v['name'])?></cite></a>
								<a class="panel-delete" href="javascript:delete_panel(<?php echo $v['menuid']?>, this);"></a></span>
						<?php }?>
					</div>
					<div id="paneladd"></div>
					<input type="hidden" id="menuid" value="">
				</div>
            </div>
        </div>

    </div>
</div>
<script src="<?php echo JS_PATH?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo CSS_PATH?>layuimini/js/lay-config.js?v=2.0.0" charset="utf-8"></script>
<script>
    layui.use(['jquery', 'layer', 'miniAdmin'], function () {
        var $ = layui.jquery,
            layer = layui.layer,
            miniAdmin = layui.miniAdmin;

        var options = {
            iniUrl: "<?php echo SELF;?>?m=admin&c=index&a=public_menu",    // ???????????????
            clearUrl: "<?php echo SELF;?>", // ??????????????????
            urlHashLocation: true,      // ????????????hash??????
            bgColorDefault: false,      // ??????????????????
            multiModule: true,          // ?????????????????????
            menuChildOpen: false,       // ????????????????????????
            loadingTime: 0,             // ?????????????????????
            pageAnim: true,             // iframe????????????
            maxTabNum: 20,              // ?????????tab????????????
        };
        miniAdmin.render(options);

        $('.login-out').on("click", function () {
            Dialog.confirm('<?php echo L('confirm_exit_login');?>', function(){location.href='?m=admin&c=index&a=public_logout';});
        });
    });
function menu(menuid) {
    $("#menuid").val(menuid);
    $("#paneladd").html('<a class="panel-add" href="javascript:add_panel();"><em><?php echo L('add')?></em></a>');
}
function add_panel() {
    var menuid = $("#menuid").val();
    $.ajax({
        type: "POST",
        url: "?m=admin&c=index&a=public_ajax_add_panel",
        data: "menuid=" + menuid,
        success: function(data){
            if(data) {
                $("#panellist").html(data);
            }
        }
    });
}
function delete_panel(menuid, id) {
    $.ajax({
        type: "POST",
        url: "?m=admin&c=index&a=public_ajax_delete_panel",
        data: "menuid=" + menuid,
        success: function(data){
            $("#panellist").html(data);
        }
    });
}
function paneladdclass(id) {
    $("#panellist span a[class='on']").removeClass();
    $(id).addClass('on')
}
setInterval("session_life()", 160000);
function session_life() {
    $.get("?m=admin&c=index&a=public_session_life");
}
//????????????
function site_select(siteid) {
    Dialog.confirm('???????????????????????????????????????', function() {
        $.get("?m=admin&c=index&a=public_set_siteid&siteid="+siteid,function(data){
            if (data==1){
                location.reload(true);
            }
        });
    });
}
//??????????????????
function lock_screen() {
    $.get("?m=admin&c=index&a=public_lock_screen");
    $('#ew-lock-screen-group').css('display','');
    $('#lock_password').attr("placeholder","<?php echo L('setting_input_password');?>");
}
$(function(){
    <?php if($siteid!=1){?>
    $('#site_search').attr('href', '<?php echo WEB_PATH;?>index.php?m=search&siteid=<?php echo $siteid?>');
    <?php }?>
    // ??????????????????
    $(".scroller").niceScroll({cursorcolor: "#eaeaea",cursorwidth: "8",cursorborder: "none",scrollspeed: 60,mousescrollstep: 55,autohidemode: true,/*background: '#ddd',*/hidecursordelay: 400,cursorfixedheight: false,cursorminheight: 20,enablekeyboard: true,horizrailenabled: true,bouncescroll: false,smoothscroll: true,iframeautoresize: true,touchbehavior: false,zindex: 999});
})
</script>
</body>
</html>