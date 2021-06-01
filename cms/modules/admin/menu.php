<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class menu extends admin {
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('menu_model');
	}
	
	function init () {
		$tablename = $this->db->db_tablepre.'menu';
		if (!$this->db->field_exists('icon')) {
			$this->db->query('ALTER TABLE `'.$tablename.'` ADD `icon` varchar(255) NULL DEFAULT NULL COMMENT \'图标标示\' AFTER `data`');
		}
		$userid = $_SESSION['userid'];
		$admin_username = param::get_cookie('admin_username');
		$table_name = $this->db->table_name;
		if (IS_POST) {
			$list = $this->db->select('','*','','listorder ASC,id DESC');
			$array = array();
			foreach($list as $r) {
				$rs['id'] = $r['id'];
				$rs['title'] = L($r['name']);
				$rs['pid'] = $r['parentid'];
				$rs['icon'] = $r['icon'];
				$rs['display'] = $r['display'];
				$rs['listorder'] = $r['listorder'];
				$rs['manage'] = '<a href="?m=admin&c=menu&a=add&parentid='.$r['id'].'&menuid='.$_GET['menuid'].'&pc_hash='.$_GET['pc_hash'].'" class="layui-btn layui-btn-xs"><i class="fa fa-plus"></i> '.L('add_submenu').'</a><a href="?m=admin&c=menu&a=edit&id='.$r['id'].'&menuid='.$_GET['menuid'].'&pc_hash='.$_GET['pc_hash'].'" class="layui-btn layui-btn-xs"><i class="fa fa-edit"></i> '.L('modify').'</a><a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="fa fa-trash-o"></i> '.L('delete').'</a>';
				$array[] = $rs;
			}
			echo json_encode(array('code'=>0,'msg'=>L('to_success'),'data'=>$array,'rel'=>1));
			exit();
		}
		include $this->admin_tpl('menu');
	}
	function add() {
		if(isset($_POST['dosubmit'])) {
			$this->db->insert($_POST['info']);
			//开发过程中用于自动创建语言包
			$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.'system_menu.lang.php';
			if(file_exists($file)) {
				$content = file_get_contents($file);
				$content = substr($content,0,-2);
				$key = $_POST['info']['name'];
				$data = $content."\$LANG['$key'] = '$_POST[language]';\r\n?>";
				file_put_contents($file,$data);
			} else {
				
				$key = $_POST['info']['name'];
				$data = "<?php\r\n\$LANG['$key'] = '$_POST[language]';\r\n?>";
				file_put_contents($file,$data);
			}
			//结束
			showmessage(L('add_success'), '?m=admin&c=menu&a=init');
		} else {
			$show_validator = '';
			$tree = pc_base::load_sys_class('tree');
			$result = $this->db->select();
			$array = array();
			foreach($result as $r) {
				$r['cname'] = L($r['name']);
				$r['selected'] = $r['id'] == $_GET['parentid'] ? 'selected' : '';
				$array[] = $r;
			}
			$str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
			$tree->init($array);
			$select_categorys = $tree->get_tree(0, $str);
			$models = pc_base::load_config('model_config');
			include $this->admin_tpl('menu');
		}
	}
	function delete() {
		if(isset($_GET['dosubmit'])) {
			$_POST['id'] = intval($_POST['id']);
			$this->delete_child($_POST['id']);
			$this->db->delete(array('id'=>$_POST['id']));
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	/**
	 * 递归删除
	 * @param $id 要删除的id
	 */
	private function delete_child($id) {
		$id = intval($id);
		if (empty($id)) return false;
		$list = $this->db->select(array('parentid'=>$id));
		foreach($list as $r) {
			$this->delete_child($r['id']);
			$this->db->delete(array('id'=>$r['id']));
		}
		return true;
	}
	
	function edit() {
		if(isset($_POST['dosubmit'])) {
			$id = intval($_POST['id']);
			//print_r($_POST['info']);exit;
			$r = $this->db->get_one(array('id'=>$id));
			$this->db->update($_POST['info'],array('id'=>$id));
			//修改语言文件
			$file = PC_PATH.'languages'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.'system_menu.lang.php';
			require $file;
			$key = $_POST['info']['name'];
			if(!isset($LANG[$key])) {
				$content = file_get_contents($file);
				$content = substr($content,0,-2);
				$data = $content."\$LANG['$key'] = '$_POST[language]';\r\n?>";
				file_put_contents($file,$data);
			} elseif(isset($LANG[$key]) && $LANG[$key]!=$_POST['language']) {
				$content = file_get_contents($file);
				$content = str_replace($LANG[$key],$_POST['language'],$content);
				file_put_contents($file,$content);
			}
			$this->update_menu_models($id, $r, $_POST['info']);
			
			//结束语言文件修改
			showmessage(L('operation_success'), '?m=admin&c=menu&a=init');
		} else {
			$show_validator = '';
			$tree = pc_base::load_sys_class('tree');
			$id = intval($_GET['id']);
			$r = $this->db->get_one(array('id'=>$id));
			if($r) extract($r);
			$result = $this->db->select();
			foreach($result as $r) {
				$r['cname'] = L($r['name']);
				$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
				$array[] = $r;
			}
			$str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
			$tree->init($array);
			$select_categorys = $tree->get_tree(0, $str);
			$models = pc_base::load_config('model_config');
			include $this->admin_tpl('menu');
		}
	}
	
	public function public_icon() {
		$show_header = $show_pc_hash = 1;
		include $this->admin_tpl('menu_icon');
	}
	
	/**
	 * 更新
	 */
	function display() {
		if(isset($_GET['dosubmit'])) {
			if (isset($_POST['display'])) {
				$this->db->update(array('display'=>$_POST['display']),array('id'=>$_POST['id']));
			}
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	
	/**
	 * 排序
	 */
	function listorder() {
		if(isset($_GET['dosubmit'])) {
			if (isset($_POST['listorder'])) {
				$this->db->update(array('listorder'=>$_POST['listorder']),array('id'=>$_POST['id']));
			}
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	
	/**
	 * 更新菜单的所属模式
	 * @param $id INT 菜单的ID
	 * @param $old_data 该菜单的老数据
	 * @param $new_data 菜单的新数据
	 **/
	private function update_menu_models($id, $old_data, $new_data) {
		$models_config = pc_base::load_config('model_config');
		if (is_array($models_config)) {
			foreach ($models_config as $_k => $_m) { 
				if (!isset($new_data[$_k])) $new_data[$_k] = 0;
				if ($old_data[$_k]==$new_data[$_k]) continue; //数据没有变化时继续执行下一项
				$r = $this->db->get_one(array('id'=>$id), 'parentid');
				$this->db->update(array($_k=>$new_data[$_k]), array('id'=>$id));
				if ($new_data[$_k] && $r['parentid']) {
					$this->update_parent_menu_models($r['parentid'], $_k); //如果设置所属模式，更新父级菜单的所属模式
				}
			}
		}
		return true;
	}

	/**
	 * 更新父级菜单的所属模式
	 * @param $id int 菜单ID
	 * @param $field  修改字段名
	 */
	private function update_parent_menu_models($id, $field) {
		$id = intval($id);
		$r = $this->db->get_one(array('id'=>$id), 'parentid');
		$this->db->update(array($field=>1), array('id'=>$id)); //修改父级的所属模式，然后判断父级是否存在父级
		if ($r['parentid']) {
			$this->update_parent_menu_models($r['parentid'], $field);
		}
		return true;
	}
}
?>