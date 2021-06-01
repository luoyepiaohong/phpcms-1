<?php 
defined('IN_CMS') or exit('No permission resources.');
$session_storage = 'session_'.pc_base::load_config('system','session_storage');
pc_base::load_sys_class($session_storage);
if(param::get_cookie('sys_lang')) {
	define('SYS_STYLE',param::get_cookie('sys_lang'));
} else {
	define('SYS_STYLE','zh-cn');
}
class attachments {
	private $att_db;
	function __construct() {
		pc_base::load_app_func('global');
		$this->upload = pc_base::load_sys_class('upload');
		$this->imgext = array('jpg','gif','png','bmp','jpeg');
		$this->userid = $_SESSION['userid'] ? $_SESSION['userid'] : (param::get_cookie('_userid') ? param::get_cookie('_userid') : sys_auth($_POST['userid_h5'],'DECODE'));
		$this->isadmin = $this->admin_username = $_SESSION['roleid'] ? 1 : 0;
		$this->groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		//判断是否登录
		if(empty($this->userid)){
			showmessage(L('please_login','','member'));
		}
	}
	
	/**
	 * 常规上传
	 */
	public function upload() {
		$grouplist = getcache('grouplist','member');
		if($this->isadmin==0 && !$grouplist[$this->groupid]['allowattachment']) return false;
		if($this->isadmin==1) define('IN_ADMIN',true);
		pc_base::load_sys_class('upload','',0);
		$module = trim($_GET['module']);
		$catid = intval($_GET['catid']);
		$siteid = $this->get_siteid();
		$site_setting = get_site_setting($siteid);
		$site_allowext = $site_setting['upload_allowext'];
		$upload_maxsize = $site_setting['upload_maxsize'];
		$watermark = $site_setting['ueditor'] ? 1 : intval($_GET['watermark_enable']);
		$upload = new upload($module,$catid,$siteid);
		$upload->set_userid($this->userid);
		$rt = $upload->upload_file(array(
			'path' => '',
			'form_name' => 'upload',
			'file_exts' => explode('|', strtolower($site_allowext)),
			'file_size' => ($upload_maxsize/1024) * 1024 * 1024,
			'watermark' => $watermark,
			'attachment' => $upload->get_attach_info(($_GET['attachment'] ? intval($_GET['attachment']) : intval(SYS_ATTACHMENT_ATTACH)), intval($_GET['image_reduce'])),
		));
		if (!$rt['code']) {
			$result = array("uploaded"=>false,"error"=>array("message"=>$rt['msg']));
			exit(json_encode($result,JSON_UNESCAPED_UNICODE));
			//exit(dr_array2string($rt));
		}
		
		// 附件归档
		$data = $upload->save_data($rt['data']);
		if (!$data['code']) {
			$result = array("uploaded"=>false,"error"=>array("message"=>$data['msg']));
			exit(json_encode($result,JSON_UNESCAPED_UNICODE));
			//exit(dr_array2string($data));
		}
		
		if($rt && $data){
			$fn = intval($_GET['CKEditorFuncNum']);
			$this->upload_json($data['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
			$result = array("uploaded"=>true,
				"fileName"=>$rt['data']['name'],
				"url"=>$rt['data']['url'],
				"error"=>array(
					"message"=>""
				)
			);
		}else{
			$result = array("uploaded"=>false,"error"=>array("message"=>"上传错误"));
		}
		exit(json_encode($result,JSON_UNESCAPED_UNICODE));
		//exit(dr_array2string(array('code' => 1, 'msg' => L('上传成功'), 'id' => $data['code'], 'info' => $rt['data'])));
	}
	/**
	 * h5upload上传附件
	 */
	public function h5upload(){
		$grouplist = getcache('grouplist','member');
		if(isset($_POST['dosubmit'])){
			if( $_POST['h5_auth_key'] != md5(pc_base::load_config('system','auth_key').$_POST['H5UPLOADSESSID']) || ($_POST['isadmin']==0 && !$grouplist[$_POST['groupid']]['allowattachment'])) exit();
			pc_base::load_sys_class('upload','',0);
			$upload = new upload($_POST['module'],$_POST['catid'],$_POST['siteid']);
			$upload->set_userid($_POST['userid']);
			$siteid = get_siteid();
			$site_setting = get_site_setting($siteid);
			$site_allowext = $site_setting['upload_allowext'];
			$upload_maxsize = $site_setting['upload_maxsize'];
			if ($_POST['filetype_post']) {
				$filetype_post = $_POST['filetype_post'];
			} else {
				$filetype_post = $site_allowext;
			}
			$rt = $upload->upload_file(array(
				'path' => '',
				'form_name' => 'file_upload',
				'file_exts' => explode('|', strtolower($filetype_post)),
				'file_size' => ($upload_maxsize/1024) * 1024 * 1024,
				'watermark' => intval($_POST['watermark_enable']),
				'attachment' => $upload->get_attach_info(($_POST['attachment'] ? intval($_POST['attachment']) : intval(SYS_ATTACHMENT_ATTACH)), (int)$_POST['image_reduce']),
			));
			if (!$rt['code']) {
				exit(dr_array2string($rt));
			}
			
			// 附件归档
			$data = $upload->save_data($rt['data']);
			if (!$data['code']) {
				exit(dr_array2string($data));
			}
			
			//exit(dr_array2string(array('code' => 1, 'msg' => L('上传成功'), 'id' => $data['code'], 'info' => $rt['data'])));
			
			// 缩略图
			if (is_image($rt['data']['path']) && ($_POST['thumb_width'] > 0 || $_POST['thumb_height'] > 0)) {
				thumb($rt['data']['path'], $_POST['thumb_width'], $_POST['thumb_height'] ,$_POST['watermark_enable']);
			}
			
			if($rt && $data) {
				if($upload->uploadedfiles[0]['isimage']) {
					$result['code'] = 1;
					$result['msg'] = L('att_upload_succ');
					$result['id'] = $data['code'];
					$result['src'] = $rt['data']['url'];
					$result['ext'] = 1;
					$result['filename'] = $rt['data']['name'];
					$result['size'] = format_file_size($rt['data']['size']);
					exit(json_encode($result));
				} else {
					$fileext = $rt['data']['ext'];
					if($fileext == 'rar') $fileext = 'rar';
					elseif($fileext == 'zip') $fileext = 'zip';
					elseif($fileext == 'doc' || $fileext == 'docx') $fileext = 'doc';
					elseif($fileext == 'xls' || $fileext == 'xlsx') $fileext = 'xls';
					elseif($fileext == 'ppt' || $fileext == 'pptx') $fileext = 'ppt';
					elseif ($fileext == 'flv' || $fileext == 'swf' || $fileext == 'rm' || $fileext == 'rmvb') $fileext = 'flv';
					else $fileext = 'do';
					$result['code'] = 1;
					$result['msg'] = L('att_upload_succ');
					$result['id'] = $data['code'];
					$result['src'] = $rt['data']['url'];
					$result['ext'] = $fileext;
					$result['filename'] = $rt['data']['name'];
					$result['size'] = format_file_size($rt['data']['size']);
					exit(json_encode($result));
				}
				exit;
			} else {
				$result['code'] = 0;
				$result['msg'] = $rt['msg'];
				exit(json_encode($result));
				echo '0,'.$rt['msg'];
				exit;
			}
		} else {
			if($this->isadmin==0 && !$grouplist[$this->groupid]['allowattachment']) showmessage(L('att_no_permission'));
			$args = $_GET['args'];
			$authkey = $_GET['authkey'];
			if(upload_key($args) != $authkey) showmessage(L('attachment_parameter_error'));
			extract(geth5init($_GET['args']));
			$siteid = $this->get_siteid();
			$site_setting = get_site_setting($siteid);
			$file_size_limit = sizecount($site_setting['upload_maxsize']*1024);		
			$att_not_used = getcache('att_json', 'commons');
			if(empty($att_not_used) || !isset($att_not_used)) $tab_status = ' class="on"';
			if(!empty($att_not_used)) $div_status = ' hidden';
			$userid_h5=sys_auth($this->userid, 'ENCODE');
			include $this->admin_tpl('h5upload');
		}
	}
	/**
	 * 获取临时未处理文件列表
	 */
	public function att_not(){
		$args = $_GET['args'];
		extract(geth5init($_GET['args']));
		//获取临时未处理文件列表
		$att = $this->att_not_used();
		include $this->admin_tpl('att_not');
	}
	
	/**
	 * 加载图片库
	 */
	public function album_load() {
		if(!$this->admin_username) return false;
		$uploadtime= '';
		$this->att_db= pc_base::load_model('attachment_model');
		$siteid = param::get_cookie('siteid');
		if(!$siteid) $siteid = get_siteid() ? get_siteid() : 1 ;
		$site_setting = get_site_setting($siteid);
		$upload_allowext = $site_setting['upload_allowext'];
		if($_GET['args']) extract(geth5init($_GET['args']));
		$args = explode(',',$_GET['args']);
		$site_allowext = ($args[1]!='') ? $args[1] : ($_GET['site_allowext'] ? $_GET['site_allowext'] : $upload_allowext);
		$array_test = explode('|',$site_allowext);
		$length = sizeof($array_test);
		for($i=0;$i<$length;$i++){
			$s_str .= "'".$array_test[$i]."',";
		}
		$s_str = substr($s_str, 0, strlen($s_str) - 1);
		$where = "fileext in (".$s_str.") AND userid=".(int)$this->userid;
		if($_GET['dosubmit']){
			extract($_GET['info']);
			$filename = safe_replace($filename);
			if($filename) $where .= " AND `filename` LIKE '%$filename%' ";
			if($uploadtime) {
				$start_uploadtime = strtotime($uploadtime.' 00:00:00');
				$stop_uploadtime = strtotime($uploadtime.' 23:59:59');
				$where .= " AND `uploadtime` >= '$start_uploadtime' AND  `uploadtime` <= '$stop_uploadtime'";				
			}
		}
		pc_base::load_sys_class('form');
		$page = $_GET['page'] ? $_GET['page'] : '1';
		$infos = $this->att_db->listinfo($where, 'aid DESC', $page, 16,'',5);
		foreach($infos as $n=>$v){
			$ext = fileext($v['filepath']);
			if(in_array($ext,$this->imgext)) {
				$infos[$n]['src']=SYS_UPLOAD_URL.$v['filepath'];
				$infos[$n]['width']='80';
			} else {
				$infos[$n]['src']=file_icon($v['filepath']);
				$infos[$n]['width']='64';
			}
		}
		$pages = $this->att_db->pages;
		include $this->admin_tpl('album_list');
	}
	
	/**
	 * 目录浏览模式添加图片
	 */
	public function album_dir() {
		if(!$this->admin_username) return false;
		if($_GET['args']) extract(geth5init($_GET['args']));
		$dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\','..','.*'), '', trim($_GET['dir'])) : '';
		$filepath = SYS_UPLOAD_PATH.$dir;
		$list = glob($filepath.'/'.'*');
		if(!empty($list)) rsort($list);
		$local = str_replace(array(PC_PATH, CMS_PATH ,DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR), array('','',DIRECTORY_SEPARATOR), $filepath);
		$url = ($dir == '.' || $dir=='') ? SYS_UPLOAD_URL : SYS_UPLOAD_URL.str_replace('.', '', $dir).'/';
		$show_header = true;
		include $this->admin_tpl('album_dir');
	}
	
	/**
	 * 设置upload上传的json格式cookie
	 */
	private function upload_json($aid,$src,$filename,$size) {
		$arr['aid'] = intval($aid);
		$arr['src'] = trim($src);
		$arr['filename'] = urlencode($filename);
		$arr['size'] = $size;
		$json_str = json_encode($arr);
		$att_arr_exist = getcache('att_json', 'commons');
		$att_arr_exist_tmp = explode('||', $att_arr_exist);
		if(is_array($att_arr_exist_tmp) && in_array($json_str, $att_arr_exist_tmp)) {
			return true;
		} else {
			$json_str = $att_arr_exist ? $att_arr_exist.'||'.$json_str : $json_str;
			setcache('att_json', $json_str, 'commons');
			return true;			
		}
	}
	
	/**
	 * 设置h5upload上传的json格式cookie
	 */
	public function h5upload_json() {
		$arr['aid'] = intval($_GET['aid']);
		$arr['src'] = safe_replace(trim($_GET['src']));
		$arr['filename'] = urlencode(safe_replace($_GET['filename']));
		$arr['size'] = $_GET['size'];
		$json_str = json_encode($arr);
		$att_arr_exist = getcache('att_json', 'commons');
		$att_arr_exist_tmp = explode('||', $att_arr_exist);
		if(is_array($att_arr_exist_tmp) && in_array($json_str, $att_arr_exist_tmp)) {
			return true;
		} else {
			$json_str = $att_arr_exist ? $att_arr_exist.'||'.$json_str : $json_str;
			setcache('att_json', $json_str, 'commons');
			return true;			
		}
	}
	
	/**
	 * 删除h5upload上传的json格式cookie
	 */	
	public function h5upload_json_del() {
		$arr['aid'] = intval($_GET['aid']);
		$arr['src'] = trim($_GET['src']);
		$arr['filename'] = urlencode($_GET['filename']);
		$arr['size'] = $_GET['size'];
		$json_str = json_encode($arr);
		$att_arr_exist = getcache('att_json', 'commons');
		$att_arr_exist = str_replace(array($json_str,'||||'), array('','||'), $att_arr_exist);
		$att_arr_exist = preg_replace('/^\|\|||\|\|$/i', '', $att_arr_exist);
		setcache('att_json', $att_arr_exist, 'commons');
	}	

	private function att_not_used() {
		$this->att_db= pc_base::load_model('attachment_model');
		//获取临时未处理文件列表
		if($att_json = getcache('att_json', 'commons')) {
			if($att_json) $att_cookie_arr = explode('||', $att_json);	
			foreach ($att_cookie_arr as $_att_c) $att[] = json_decode($_att_c,true);
			if(is_array($att) && !empty($att)) {
				foreach ($att as $n=>$v) {
					$ext = fileext($v['src']);
					if(in_array($ext,$this->imgext)) {
						$att[$n]['fileimg']=$v['src'];
						$att[$n]['width']='80';
						$att[$n]['filename']=urldecode($v['filename']);
					} else {
						$att[$n]['fileimg']=file_icon($v['src']);
						$att[$n]['width']='64';
						$att[$n]['filename']=urldecode($v['filename']);
					}
					$this->cookie_att .=	'|'.$v['src'];
				}
			}			
		}
		return $att;
	}
	
	/**
	 * 获取站点配置信息
	 * @param  $siteid 站点id
	 */
	private function _get_site_setting($siteid) {
		$siteinfo = getcache('sitelist', 'commons');
		return string2array($siteinfo[$siteid]['setting']);
	}
	
	final public static function admin_tpl($file, $m = '') {
		$m = empty($m) ? ROUTE_M : $m;
		if(empty($m)) return false;
		return PC_PATH.'modules'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$file.'.tpl.php';
	}
	final public static function get_siteid() {
		return get_siteid();
	}	
}
?>