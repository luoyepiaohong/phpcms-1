<?php
defined('IN_CMS') or exit('No permission resources.');

pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);

class create_html extends admin {
	private $db;
	public $siteid,$categorys;
	public function __construct() {
		parent::__construct();
		// 生成权限文件
		if (!dr_html_auth(1)) {
			showmessage(L('/cache/html/ 无法写入文件'));
		}
		$this->db = pc_base::load_model('content_model');
		$this->siteid = $this->get_siteid();
		$this->categorys = getcache('category_content_'.$this->siteid,'commons');
		foreach($_GET as $k=>$v) {
			$_POST[$k] = $v;
		}
	}
	
	public function update_urls() {
		if(isset($_POST['dosubmit'])) {
			extract($_POST,EXTR_SKIP);
			$this->url = pc_base::load_app_class('url');

			$modelid = intval($_POST['modelid']);
			$count = 0;
			if($modelid) {
				//设置模型数据表名
				$this->db->set_model($modelid);
				$table_name = $this->db->table_name;

				$where = ' WHERE status=99 ';
				$order = 'ASC';
				
				if(is_array($catids) && $catids[0] > 0)  {
					$catids = implode(',',$catids);
					$where .= " AND catid IN($catids) ";
				}

				if($type == 'date') {
					if($fromdate) {
						$fromtime = strtotime($fromdate.' 00:00:00');
						$where .= " AND `inputtime`>=$fromtime ";
					}
					if($todate) {
						$totime = strtotime($todate.' 23:59:59');
						$where .= " AND `inputtime`<=$totime ";
					}
				} elseif($type == 'id') {
					$fromid = intval($fromid);
					$toid = intval($toid);
					if($fromid) $where .= " AND `id`>=$fromid ";
					if($toid) $where .= " AND `id`<=$toid ";
				}
				if($type == 'lastinput' && $number) {
					$rs = $this->db->query("SELECT * FROM `$table_name` $where ORDER BY `id` DESC LIMIT 0,$number");
				} else {
					$rs = $this->db->query("SELECT * FROM `$table_name` $where ORDER BY `id` $order");
				}
				$data = $this->db->fetch_array($rs);
				foreach($data as $r) {
					if($r['islink'] || $r['upgrade']) continue;
					$insert['modelid'] = $modelid;
					$insert['catid'] = $r['catid'];
					$insert['id'] = $r['id'];
					$count ++;
					$cache_data[] = $insert;

				}
			} else {
				//当没有选择模型时，需要按照栏目来更新
				if(!isset($set_catid)) {
					if($catids[0] != 0) {
						foreach($catids as $catid) {
							$modelid = $this->categorys[$catid]['modelid'];
							$setting = string2array($this->categorys[$catid]['setting']);
							if ($setting['disabled']) continue;
							//设置模型数据表名
							$this->db->set_model($modelid);
							$table_name = $this->db->table_name;
							$where = " WHERE status=99 AND catid='$catid'";
							$order = 'ASC';
							$rs = $this->db->query("SELECT * FROM `$table_name` $where ORDER BY `id` $order");
							$data = $this->db->fetch_array($rs);
							foreach($data as $r) {
								if($r['islink'] || $r['upgrade']) continue;
								$insert['modelid'] = $modelid;
								$insert['catid'] = $r['catid'];
								$insert['id'] = $r['id'];
								$count ++;
								$cache_data[] = $insert;
							}
						}
					} else {
						foreach($this->categorys as $catid=>$cat) {
							$setting = string2array($cat['setting']);
							if ($setting['disabled']) continue;
							if($cat['child'] || $cat['siteid'] != $this->siteid || $cat['type']!=0) continue;
							$modelid = $this->categorys[$catid]['modelid'];
							//设置模型数据表名
							$this->db->set_model($modelid);
							$table_name = $this->db->table_name;
							$where = " WHERE status=99 AND catid='$catid'";
							$order = 'ASC';
							$rs = $this->db->query("SELECT * FROM `$table_name` $where ORDER BY `id` $order");
							$data = $this->db->fetch_array($rs);
							foreach($data as $r) {
								if($r['islink'] || $r['upgrade']) continue;
								$insert['modelid'] = $modelid;
								$insert['catid'] = $r['catid'];
								$insert['id'] = $r['id'];
								$count ++;
								$cache_data[] = $insert;
							}
						}
					}
				}
			}
			$cache = array();
			if ($count > 100) {
				$pagesizes = ceil($count/100);
				for ($i = 1; $i <= 100; $i ++) {
					$cache[$i] = array_slice($cache_data, ($i - 1) * $pagesizes, $pagesizes);
				}
			} else {
				for ($i = 1; $i <= $count; $i ++) {
					$cache[$i] = array_slice($cache_data, ($i - 1), 1);
				}
			}
			setcache('update_url_show-'.$this->siteid.'-'.$_SESSION['userid'], $cache,'content');
			$todo_url = '?m=content&c=create_html&a=public_show_url&set_catid=1&pagesize='.$pagesize.'&dosubmit=1&modelid='.$modelid.'&type='.$type.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid.'&number='.$number;
			include $this->admin_tpl('show_url');
		} else {
			$show_header = $show_dialog  = '';
			$admin_username = param::get_cookie('admin_username');
			$modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : 0;
			
			$tree = pc_base::load_sys_class('tree');
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array();
			if(!empty($this->categorys)) {
				foreach($this->categorys as $catid=>$r) {
					$setting = string2array($r['setting']);
					if ($setting['disabled']) continue;
					if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
					if($modelid && $modelid != $r['modelid']) continue;
					$r['disabled'] = $r['child'] ? 'disabled' : '';
					$categorys[$catid] = $r;
				}
			}
			$str  = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);
			include $this->admin_tpl('update_urls');
		}
	}

	private function urls($id, $catid= 0, $inputtime = 0, $prefix = ''){
		$urls = $this->url->show($id, 0, $catid, $inputtime, $prefix,'','edit');
		//更新到数据库
		$url = $urls[0];
		$this->db->update(array('url'=>$url),array('id'=>$id));
		//echo $id; echo "|";
		return $urls;
	}
	/**
	* 生成内容页
	*/
	public function show() {
		if(isset($_POST['dosubmit'])) {
			extract($_POST,EXTR_SKIP);
			$modelid = intval($_POST['modelid']);
			$catids = $_POST['catids'];
			if ($catids && is_array($catids)) {
				$catids = implode(',', $catids);
			}
			$count_url = '?m=content&c=create_html&a=public_show_count&pagesize='.$pagesize.'&dosubmit=1&modelid='.$modelid.'&catids='.$catids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid.'&number='.$number;
			$todo_url = '?m=content&c=create_html&a=public_show_add&set_catid=1&pagesize='.$pagesize.'&dosubmit=1&modelid='.$modelid.'&catids='.$catids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid.'&number='.$number;
			include $this->admin_tpl('show_html');
		} else {
			$show_header = $show_dialog  = '';
			$admin_username = param::get_cookie('admin_username');
			$this->model_db = pc_base::load_model('sitemodel_model');
			$module = $this->model_db->get_one(array('siteid'=>$this->siteid,'type'=>0,'disabled'=>0),'modelid','modelid');
			$modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : $module['modelid'];
			
			$tree = pc_base::load_sys_class('tree');
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array();
			if(!empty($this->categorys)) {
				foreach($this->categorys as $catid=>$r) {
					$setting = string2array($r['setting']);
					if ($setting['disabled']) continue;
					if($this->siteid != $r['siteid'] || ($r['type']!=0 && $r['child']==0)) continue;
					if($modelid && $modelid != $r['modelid']) continue;
					if($r['child']==0) {
						if(!$setting['content_ishtml']) continue;
					}
					$r['disabled'] = $r['child'] ? 'disabled' : '';
					$categorys[$catid] = $r;
				}
			}
			$str  = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);
			include $this->admin_tpl('create_html_show');
		}

	}
	// 断点内容
	public function public_show_point() {
		$cache_class = pc_base::load_sys_class('cache');
		extract($_POST,EXTR_SKIP);
		$modelid = intval($_POST['modelid']);
		$catids = $_POST['catids'];
		if ($catids && is_array($ids)) {
			$catids = implode(',', $catids);
		}
		$name = 'show-'.$modelid.'-html-file';
		$page = $cache_class->get_auth_data($name.'-error'); // 设置断点
		if (!$page) {
			dr_json(0, L('没有找到上次中断生成的记录'));
		}

		$count_url = '?m=content&c=create_html&a=public_show_point_count&pagesize='.$pagesize.'&dosubmit=1&modelid='.$modelid.'&catids='.$catids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid.'&number='.$number;
		$todo_url = '?m=content&c=create_html&a=public_show_add&set_catid=1&pagesize='.$pagesize.'&dosubmit=1&modelid='.$modelid.'&catids='.$catids.'&fromdate='.$fromdate.'&todate='.$todate.'&fromid='.$fromid.'&toid='.$toid.'&number='.$number;
		include $this->admin_tpl('show_html');
	}
    // 断点内容的数量统计
    public function public_show_point_count() {
		$cache_class = pc_base::load_sys_class('cache');
        extract($_POST,EXTR_SKIP);
		$modelid = intval($_GET['modelid']);
        $name = 'show-'.$modelid.'-html-file';
        $page = $cache_class->get_auth_data($name.'-error'); // 设置断点
        if (!$page) {
            dr_json(0, L('没有找到上次中断生成的记录'));
        } elseif (!$cache_class->get_auth_data($name)) {
            dr_json(0, L('生成记录已过期，请重新开始生成'));
        } elseif (!$cache_class->get_auth_data($name.'-'.$page)) {
            dr_json(0, L('生成记录已过期，请重新开始生成'));
        }

        dr_json(1, 'ok');
    }
    // 内容数量统计
    public function public_show_count() {
		$html = pc_base::load_sys_class('html');
		$html->get_show_data($_GET['modelid'], array(
			'catids' => $_GET['catids'],
			'todate' => $_GET['todate'],
			'fromdate' => $_GET['fromdate'],
			'toid' => $_GET['toid'],
			'fromid' => $_GET['fromid'],
			'pagesize' => $_GET['pagesize'],
			'siteid' => $this->siteid,
			'number' => $_GET['number']
		));
    }
	/**
	* 生成栏目页
	*/
	public function category() {
		if(isset($_POST['dosubmit'])) {
			extract($_POST,EXTR_SKIP);
			$catids = $_POST['catids'];
			if ($catids && is_array($catids)) {
				$catids = implode(',', $catids);
			}
			$pagesize = $_POST['pagesize'];
			$maxsize = $_POST['maxsize'];
			$count_url = '?m=content&c=create_html&a=public_category_count&maxsize='.$maxsize.'&pagesize='.$pagesize.'&dosubmit=1&catids='.$catids;
			$todo_url = '?m=content&c=create_html&a=public_category_add&maxsize='.$maxsize.'&pagesize='.$pagesize.'&dosubmit=1&catids='.$catids;
			include $this->admin_tpl('show_html');
		} else {
			$show_header = $show_dialog  = '';
			$admin_username = param::get_cookie('admin_username');
			$modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : 0;
			
			$tree = pc_base::load_sys_class('tree');
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array();
			if(!empty($this->categorys)) {
				foreach($this->categorys as $catid=>$r) {
					$setting = string2array($r['setting']);
					if ($setting['disabled']) continue;
					if($this->siteid != $r['siteid'] || ($r['type']==2 && $r['child']==0)) continue;
					if($modelid && $modelid != $r['modelid']) continue;
					if($r['child']==0) {
						if(!$r['ishtml']) continue;
					}
					$categorys[$catid] = $r;
				}
			}
			$str  = "<option value='\$catid' \$selected>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);
			include $this->admin_tpl('create_html_category');
		}

	}
    // 断点生成栏目
    public function public_category_point() {
		$cache_class = pc_base::load_sys_class('cache');
		$name = 'category-html-file';
		$page = $cache_class->get_auth_data($name.'-error'); // 设置断点
		if (!$page) {
			dr_json(0, L('没有找到上次中断生成的记录'));
		}

		$catids = $_POST['catids'];
		if ($catids && is_array($catids)) {
			$catids = implode(',', $catids);
		}

		$count_url = '?m=content&c=create_html&a=public_category_point_count&maxsize='.$maxsize.'&pagesize='.$pagesize.'&dosubmit=1&catids='.$catids;
		$todo_url = '?m=content&c=create_html&a=public_category_add&maxsize='.$maxsize.'&pagesize='.$pagesize.'&dosubmit=1&catids='.$catids;
		include $this->admin_tpl('show_html');
    }
	// 断点栏目的数量统计
	public function public_category_point_count() {
		$cache_class = pc_base::load_sys_class('cache');
		$name = 'category-html-file';
		$page = $cache_class->get_auth_data($name.'-error'); // 设置断点
		if (!$page) {
			dr_json(0, L('没有找到上次中断生成的记录'));
		} elseif (!$cache_class->get_auth_data($name)) {
			dr_json(0, L('生成记录已过期，请重新开始生成'));
		} elseif (!$cache_class->get_auth_data($name.'-'.$page)) {
			dr_json(0, L('生成记录已过期，请重新开始生成'));
		}
		dr_json(1, 'ok');
	}
	// 获取生成的栏目
	private function _category_data($catids, $cats) {

		if (!$catids) {
			return $cats;
		}

		$rt = array();
		$arr = explode(',', $catids);
		foreach ($arr as $id) {
			if ($id && $cats[$id]) {
				$rt[$id] = $cats[$id];
			}
		}

		return $rt;
	}
	// 栏目的数量统计
	public function public_category_count() {
		$catids = $_GET['catids'];
		$pagesize = (int)$_GET['pagesize'];
		$maxsize = (int)$_GET['maxsize'];

		$cat = getcache('category_content_'.$this->siteid,'commons');
		$html = pc_base::load_sys_class('html');
		$html->get_category_data($this->_category_data($catids, $cat), $pagesize, $maxsize);
	}
	//生成首页
	public function public_index() {
		$this->html = pc_base::load_app_class('html');
		$this->db = pc_base::load_model('site_model');
		$data = $this->db->get_one(array('siteid'=>$this->siteid));
		if($data['ishtml']==1) {
			$html = $this->html->index();
			showmessage(L('首页更新成功！').$html);
		} else {
			showmessage(L('index_create_close'));
		}
	}
	/**
	* 批量生成内容页
	*/
	public function batch_show() {
		if(isset($_POST['dosubmit'])) {
			$catid = intval($_GET['catid']);
			if(!$catid) dr_json(0, L('missing_part_parameters'));
			$modelid = $this->categorys[$catid]['modelid'];
			$setting = string2array($this->categorys[$catid]['setting']);
			$content_ishtml = $setting['content_ishtml'];
			if(!$content_ishtml) dr_json(0, L('它是动态模式'));
			if($content_ishtml) {
				if(empty($_POST['ids'])) dr_json(0, L('you_do_not_check'));
				$count = 0;
				foreach($_POST['ids'] as $id) {
					$insert['catid']=$catid;
					$insert['id']=$id;
					$count ++;
					$cache_data[] = $insert;
				}
				$cache = array();
				if ($count > 100) {
					$pagesize = ceil($count/100);
					for ($i = 1; $i <= 100; $i ++) {
						$cache[$i] = array_slice($cache_data, ($i - 1) * $pagesize, $pagesize);
					}
				} else {
					for ($i = 1; $i <= $count; $i ++) {
						$cache[$i] = array_slice($cache_data, ($i - 1), 1);
					}
				}
				setcache('update_html_show-'.$this->siteid.'-'.$_SESSION['userid'], $cache,'content');
				dr_json(1, 'ok', array('url' => '?m=content&c=create_html&a=public_batch_show_add&menuid='.$_GET['menuid'].'&pc_hash='.$_GET['pc_hash']));
			}
		}
	}
	/**
	* 批量生成内容页
	*/
	public function public_batch_show_add() {
		$show_header = $show_dialog = $show_pc_hash = '';
		$todo_url = '?m=content&c=create_html&a=public_batch_show&menuid='.$_GET['menuid'].'&pc_hash='.$_GET['pc_hash'];
		include $this->admin_tpl('show_url');
	}
	/**
	* 批量生成内容页
	*/
	public function public_batch_show() {
		$page = max(1, intval($_GET['page']));
		$update_html_show = getcache('update_html_show-'.$this->siteid.'-'.$_SESSION['userid'], 'content');
		if (!$update_html_show) {
			dr_json(0, '数据缓存不存在');
		}

		$cache_data = $update_html_show[$page];
		if ($cache_data) {
			$html = '';
			foreach ($cache_data as $insert) {
				$ok = '完成';
				$class = '';
				$modelid = $this->categorys[$insert['catid']]['modelid'];
				$setting = string2array($this->categorys[$insert['catid']]['setting']);
				$content_ishtml = $setting['content_ishtml'];
				$this->url = pc_base::load_app_class('url');
				$this->db->set_model($modelid);
				$this->html = pc_base::load_app_class('html');
				$rs = $this->db->get_one(array('id'=>$insert['id']));
				if($content_ishtml) {
					if($rs['islink']) {
						$class = 'p_error';
						$ok = '<a class="error" href="'.$rs['url'].'" target="_blank">转向链接</a>';
					} else {
						$this->db->table_name = $this->db->table_name.'_data';
						$r2 = $this->db->get_one(array('id'=>$rs['id']));
						if($r2) $rs = array_merge($rs,$r2);
						//判断是否为升级或转换过来的数据
						if(!$rs['upgrade']) {
							$urls = $this->url->show($rs['id'], '', $rs['catid'],$rs['inputtime']);
						} else {
							$urls[1] = $rs['url'];
						}
						$this->html->show($urls[1],$rs,0,'edit',$rs['upgrade']);
						$class = 'ok';
						$ok = '<a class="ok" href="'.$rs['url'].'" target="_blank">生成成功</a>';
					}
				} else {
					$class = 'p_error';
					$ok = '<a class="error" href="'.$rs['url'].'" target="_blank">它是动态模式</a>';
				}
				$html.= '<p class="'.$class.'"><label class="rleft">(#'.$rs['id'].')'.$rs['title'].'</label><label class="rright">'.$ok.'</label></p>';
			}
			dr_json($page + 1, $html);
		}
		// 完成
		delcache('update_html_show-'.$this->siteid.'-'.$_SESSION['userid'], 'content');
		dr_json(100, '');
	}
	/**
	* 批量批量更新URL
	*/
	public function public_show_url() {
		if(isset($_POST['dosubmit'])) {
			extract($_POST,EXTR_SKIP);
			$page = max(1, intval($_GET['page']));
			$catid_arr = getcache('update_url_show-'.$this->siteid.'-'.$_SESSION['userid'],'content');
			if (!$catid_arr) {
				dr_json(0, '没有可用更新的内容数据');
			}
			
			$cache_data = $catid_arr[$page];
			if ($cache_data) {
				$html = '';
				foreach ($cache_data as $insert) {
					$ok = '完成';
					$class = '';
					//设置模型数据表名
					$this->db->set_model(intval($insert['modelid']));
					$rs = $this->db->get_one(array('id'=>$insert['id']));
					$this->url = pc_base::load_app_class('url');
					if($rs['islink'] || $rs['upgrade']) {
						$class = 'p_error';
						$ok = '<a class="error" href="'.$rs['url'].'" target="_blank">转向链接</a>';
					} else {
						//更新URL链接
						$urls = $this->urls($rs['id'], $rs['catid'], $rs['inputtime'], $rs['prefix']);
						if ($urls[0]==$rs['url']) {
							$class = 'p_error';
							$ok = '<a class="error" href="'.$urls[0].'" target="_blank">没有更新</a>';
						} else {
							$class = 'ok';
							$ok = '<a class="ok" href="'.$urls[0].'" target="_blank">更新成功</a>';
						}
					}
					$html.= '<p class="'.$class.'"><label class="rleft">(#'.$rs['id'].')'.$rs['title'].'</label><label class="rright">'.$ok.'</label></p>';
				}
				dr_json($page + 1, $html);
			}
			// 完成
			//delcache('update_url_show-'.$this->siteid.'-'.$_SESSION['userid'], 'content');
			dr_json(100, '');
		}
	}
	/**
	* 批量生成栏目页
	*/
	public function public_category_add() {
		// 判断权限
		if (!dr_html_auth()) {
			dr_json(0, '权限验证超时，请重新执行生成');
		}
		if(isset($_POST['dosubmit'])) {
			$cache_class = pc_base::load_sys_class('cache');
			$page = max(1, intval($_GET['pp']));
			$this->html = pc_base::load_app_class('html');
			$name2 = 'category-html-file';
			$pcount = $cache_class->get_auth_data($name2);
			if (!$pcount) {
				dr_json(0, '临时缓存数据不存在：'.$name2);
			} elseif ($page > $pcount) {
				// 完成
				$cache_class->del_auth_data($name2);
				dr_json(-1, '');
			}

			$name = 'category-html-file-'.$page;
			$cache = $cache_class->get_auth_data($name);
			if (!$cache) {
				dr_json(0, '临时缓存数据不存在：'.$name);
			}

			if ($cache) {
				$html = '';
				foreach ($cache as $t) {
					$ok = '完成';
					$class = '';
					if (!$t['ishtml']) {
						$class = 'p_error';
						$ok = '<a class="error" href="'.$t['url'].'" target="_blank">它是动态模式</a>';
					} else {
						$this->html->category($t['catid'],$t['page']);
						$cache_class->set_auth_data($name2.'-error', $page); // 设置断点
						$class = 'ok';
						$ok = '<a class="ok" href="'.$t['url'].'" target="_blank">生成成功</a>';
					}
					$html.= '<p class="'.$class.'"><label class="rleft">(#'.$t['catid'].')'.$t['catname'].'</label><label class="rright">'.$ok.'</label></p>';
				}
				// 完成
				//$cache_class->del_auth_data($name);
				dr_json($page + 1, $html, array('pcount' => $pcount + 1));
			}
		}
	}
	/**
	* 批量生成内容页
	*/
	public function public_show_add() {
        // 判断权限
        if (!dr_html_auth()) {
            dr_json(0, '权限验证超时，请重新执行生成');
        }
		if(isset($_POST['dosubmit'])) {
			$cache_class = pc_base::load_sys_class('cache');
			$modelid = intval($_GET['modelid']);
			$page = max(1, intval($_GET['pp']));
			$this->html = pc_base::load_app_class('html');
			$name2 = 'show-'.$modelid.'-html-file';
			$pcount = $cache_class->get_auth_data($name2);
			if (!$pcount) {
				dr_json(0, '临时缓存数据不存在：'.$name2);
			} elseif ($page > $pcount) {
				// 完成
				$cache_class->del_auth_data($name2);
				dr_json(-1, '');
			}

			$name = 'show-'.$modelid.'-html-file-'.$page;
			$cache = $cache_class->get_auth_data($name);
			if (!$cache) {
				dr_json(0, '临时缓存数据不存在：'.$name);
			}
			
			if ($cache) {
				$html = '';
				foreach ($cache as $t) {
					$ok = '完成';
					$class = '';
					//设置模型数据表名
					$this->db->set_model(intval($modelid));
					$setting = string2array($this->categorys[$t['catid']]['setting']);
					$content_ishtml = $setting['content_ishtml'];
					$this->url = pc_base::load_app_class('url');
					if($content_ishtml) {
						if($t['islink']) {
							$class = 'p_error';
							$ok = '<a class="error" href="'.$t['url'].'" target="_blank">转向链接</a>';
						} else {
							if($t['upgrade']) {
								$urls[1] = $t['url'];
							} else {
								$urls = $this->url->show($t['id'], '', $t['catid'],$t['inputtime']);
							}
							$this->html->show($urls[1],$t,0,'edit',$t['upgrade']);
							$cache_class->set_auth_data($name2.'-error', $page); // 设置断点
							$class = 'ok';
							$ok = '<a class="ok" href="'.$t['url'].'" target="_blank">生成成功</a>';
						}
					} else {
						$class = 'p_error';
						$ok = '<a class="error" href="'.$t['url'].'" target="_blank">它是动态模式</a>';
					}
					$html.= '<p class="'.$class.'"><label class="rleft">(#'.$t['id'].')'.$t['title'].'</label><label class="rright">'.$ok.'</label></p>';
				}
				// 完成
				//$cache_class->del_auth_data($name);
				dr_json($page + 1, $html, array('pcount' => $pcount + 1));
			}
		}
	}
}
?>