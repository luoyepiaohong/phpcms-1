<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$parentid = $menu_db->insert(array('name'=>'poster', 'parentid'=>29, 'm'=>'poster', 'c'=>'space', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-exclamation-circle', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_space', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'add', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_space', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'del_space', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'poster_list', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'poster', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-exclamation-circle', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'add_poster', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'poster', 'a'=>'add', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_poster', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'poster', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'del_poster', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'poster', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'poster_stat', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'poster', 'a'=>'stat', 'data'=>'', 'icon'=>'fa fa-bar-chart-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'poster_setting', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'setting', 'data'=>'', 'icon'=>'fa fa-cog', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'create_poster_js', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'create_js', 'data'=>'', 'icon'=>'fa fa-refresh', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'poster_template', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'poster_template', 'data'=>'', 'icon'=>'fa fa-desktop', 'listorder'=>0, 'display'=>'1'));

$language = array('poster'=>'??????', 'add_space'=>'????????????', 'edit_space'=>'????????????', 'del_space'=>'????????????', 'poster_list'=>'????????????', 'add_poster'=>'????????????', 'edit_poster'=>'????????????', 'del_poster'=>'????????????', 'poster_stat'=>'????????????', 'poster_setting'=>'????????????', 'create_poster_js'=>'????????????js', 'poster_template'=>'??????????????????');
?>