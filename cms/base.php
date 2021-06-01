<?php
/**
 *  base.php CMS框架入口文件
 *
 * @copyright			(C) 2005-2010
 * @lastmodify			2010-6-7
 */
define('IN_CMS', true);
define('IN_PHPCMS', true);

// 后台管理标识
!defined('IS_ADMIN') && define('IS_ADMIN', FALSE);

// 移动入口标识
!defined('IS_MOBILE') && define('IS_MOBILE', FALSE);

//CMS框架路径
define('PC_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

if(!defined('CMS_PATH')) define('CMS_PATH', PC_PATH.'..'.DIRECTORY_SEPARATOR);
if(!defined('PHPCMS_PATH')) define('PHPCMS_PATH', PC_PATH.'..'.DIRECTORY_SEPARATOR);

//缓存文件夹地址
define('CACHE_PATH', CMS_PATH.'caches'.DIRECTORY_SEPARATOR);
//主机协议
define('SITE_PROTOCOL', isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://');
//当前访问的主机名
define('SITE_HURL', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
//来源
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

//系统开始时间
define('SYS_START_TIME', microtime());

//加载公用函数库
pc_base::load_sys_func('global');
pc_base::load_sys_func('extention');
pc_base::auto_load_func();

pc_base::load_config('system','errorlog') ? set_error_handler('my_error_handler') : error_reporting(E_ERROR | E_WARNING | E_PARSE);
//设置本地时差
function_exists('date_default_timezone_set') && date_default_timezone_set(pc_base::load_config('system','timezone'));

define('CHARSET' ,pc_base::load_config('system','charset'));
//输出页面字符集
header('Content-type: text/html; charset='.CHARSET);

// 最大栏目数量限制category
!defined('MAX_CATEGORY') && define('MAX_CATEGORY', 100);
//temp目录
define('TEMPPATH', PC_PATH.'temp/');
//是否来自ajax提交
define('IS_AJAX', (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));
//是否来自post提交
define('IS_POST', isset($_POST) && count($_POST) ? TRUE : FALSE);
define('IS_AJAX_POST', IS_POST);
//当前系统时间戳
define('SYS_TIME', $_SERVER['REQUEST_TIME'] ? $_SERVER['REQUEST_TIME'] : time());
//定义网站根路径
define('WEB_PATH',pc_base::load_config('system','web_path'));
//js 路径
define('JS_PATH',pc_base::load_config('system','js_path'));
//css 路径
define('CSS_PATH',pc_base::load_config('system','css_path'));
//img 路径
define('IMG_PATH',pc_base::load_config('system','img_path'));
//手机js 路径
define('MOBILE_JS_PATH',pc_base::load_config('system','mobile_js_path'));
//手机css 路径
define('MOBILE_CSS_PATH',pc_base::load_config('system','mobile_css_path'));
//手机img 路径
define('MOBILE_IMG_PATH',pc_base::load_config('system','mobile_img_path'));
//动态程序路径
define('APP_PATH',pc_base::load_config('system','app_path'));
//动态程序手机路径
define('MOBILE_PATH',pc_base::load_config('system','mobile_path'));
define('ROOT_URL', siteurl(1).'/'); // 主站URL
//站点id
!defined('SITE_ID') && define('SITE_ID', 1);
define('SITE_URL', siteurl(SITE_ID));
define('SITE_MURL', sitemobileurl(SITE_ID));
// 本地附件上传目录和地址
define('SYS_ATTACHMENT_ATTACH',pc_base::load_config('system','sys_attachment_attach'));
define('SYS_ATTACHMENT_PATH',pc_base::load_config('system','sys_attachment_path'));
define('SYS_ATTACHMENT_URL',pc_base::load_config('system','sys_attachment_url'));
define('SYS_ATTACHMENT_SAVE_TYPE',pc_base::load_config('system','sys_attachment_save_type'));
define('SYS_ATTACHMENT_SAVE_DIR',pc_base::load_config('system','sys_attachment_save_dir'));
if (SYS_ATTACHMENT_PATH
	&& (strpos(SYS_ATTACHMENT_PATH, '/') === 0 || strpos(SYS_ATTACHMENT_PATH, ':') !== false)
	&& is_dir(SYS_ATTACHMENT_PATH)) {
	// 相对于根目录
	// 附件上传目录
	define('SYS_UPLOAD_PATH', rtrim(SYS_ATTACHMENT_PATH, DIRECTORY_SEPARATOR).'/');
	// 附件访问URL
	define('SYS_UPLOAD_URL', trim(SYS_ATTACHMENT_URL, '/').'/');
} else {
	// 在当前网站目录
	$path = trim(SYS_ATTACHMENT_PATH ? SYS_ATTACHMENT_PATH : 'uploadfile', '/');
	// 附件上传目录
	define('SYS_UPLOAD_PATH', CMS_PATH.$path.'/');
	// 附件访问URL
	define('SYS_UPLOAD_URL', APP_PATH.$path.'/');
}
if (pc_base::load_config('system','sys_avatar_path')
	&& (strpos(pc_base::load_config('system','sys_avatar_path'), '/') === 0 || strpos(pc_base::load_config('system','sys_avatar_path'), ':') !== false)
	&& is_dir(pc_base::load_config('system','sys_avatar_path'))) {
	// 相对于根目录
	// 附件上传目录
	define('SYS_AVATAR_PATH', rtrim(pc_base::load_config('system','sys_avatar_path'), DIRECTORY_SEPARATOR).'/');
	// 附件访问URL
	define('SYS_AVATAR_URL', trim(pc_base::load_config('system','sys_avatar_url'), '/').'/');
} else {
	// 在当前网站目录
	$avatarpath = trim(pc_base::load_config('system','sys_avatar_path') ? pc_base::load_config('system','sys_avatar_path') : 'avatar', '/');
	// 附件上传目录
	define('SYS_AVATAR_PATH', SYS_UPLOAD_PATH.$avatarpath.'/');
	// 附件访问URL
	define('SYS_AVATAR_URL', SYS_UPLOAD_URL.$avatarpath.'/');
}
if (pc_base::load_config('system','sys_thumb_path')
	&& (strpos(pc_base::load_config('system','sys_thumb_path'), '/') === 0 || strpos(pc_base::load_config('system','sys_thumb_path'), ':') !== false)
	&& is_dir(pc_base::load_config('system','sys_thumb_path'))) {
	// 相对于根目录
	// 附件上传目录
	define('SYS_THUMB_PATH', rtrim(pc_base::load_config('system','sys_thumb_path'), DIRECTORY_SEPARATOR).'/');
	// 附件访问URL
	define('SYS_THUMB_URL', trim(pc_base::load_config('system','sys_thumb_url'), '/').'/');
} else {
	// 在当前网站目录
	$thumbpath = trim(pc_base::load_config('system','sys_thumb_path') ? pc_base::load_config('system','sys_thumb_path') : 'thumb', '/');
	// 附件上传目录
	define('SYS_THUMB_PATH', SYS_UPLOAD_PATH.$thumbpath.'/');
	// 附件访问URL
	define('SYS_THUMB_URL', SYS_UPLOAD_URL.$thumbpath.'/');
}
if (PHP_SAPI === 'cli' || defined('STDIN')) {
	// CLI命令行模式
	 define('ADMIN_URL', 'http://localhost/');
	 define('FC_NOW_URL', 'http://localhost/');
	 define('FC_NOW_HOST', 'http://localhost/');
	 define('DOMAIN_NAME', 'http://localhost/');
	if ($_SERVER["argv"]) {
		foreach ($_SERVER["argv"] as $val) {
			if (strpos($val, '=') !== false) {
				list($name) = explode('=', $val);
				$_GET[$name] = substr($val, strlen($name)+1);
			}
		}
	}
	defined('ENVIRONMENT') && define('ENVIRONMENT', 'testing');
} else {
	// 正常访问模式
	// 当前URL
	$url = 'http';
	if ((defined('IS_HTTPS_FIX') && IS_HTTPS_FIX)
		|| (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
		|| (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')
		|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
		|| (isset($_SERVER['HTTP_FROM_HTTPS']) && $_SERVER['HTTP_FROM_HTTPS'] == 'on')
		|| (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) != 'off')
	) {
		$url.= 's';
	}
	$url.= '://'.$_SERVER['HTTP_HOST'];
	IS_ADMIN && define('ADMIN_URL', $url.'/'); // 优先定义后台域名
	define('FC_NOW_URL', $url.($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']));
	define('FC_NOW_HOST', $url.'/'); // 域名部分
	define('DOMAIN_NAME', strtolower($_SERVER['HTTP_HOST'])); // 当前域名

	// 伪静态字符串
	/*$uu = isset($_SERVER['HTTP_X_REWRITE_URL']) || trim($_SERVER['REQUEST_URI'], '/') == SELF ? trim($_SERVER['HTTP_X_REWRITE_URL'], '/') : ($_SERVER['REQUEST_URI'] ? trim($_SERVER['REQUEST_URI'], '/') : NULL);
	if (defined('FIX_WEB_DIR') && FIX_WEB_DIR && strpos($uu, FIX_WEB_DIR) !== false &&  strpos($uu, FIX_WEB_DIR) === 0) {
		$uu = trim(substr($uu, strlen(FIX_WEB_DIR)), '/');
	}

	// 以index.php或者?开头的uri不做处理
	$uri = strpos($uu, SELF) === 0 || strpos($uu, '?') === 0 ? '' : $uu;

	// 当前URI
	define('CMSURI', $uri);*/
}

//应用静态文件路径
define('PLUGIN_STATICS_PATH',WEB_PATH.'statics/plugin/');

if(pc_base::load_config('system','gzip') && function_exists('ob_gzhandler')) {
	ob_start('ob_gzhandler');
} else {
	ob_start();
}

class pc_base {
	
	/**
	 * 初始化应用程序
	 */
	public static function creat_app() {
		return self::load_sys_class('application');
	}
	/**
	 * 加载系统类方法
	 * @param string $classname 类名
	 * @param string $path 扩展地址
	 * @param intger $initialize 是否初始化
	 */
	public static function load_sys_class($classname, $path = '', $initialize = 1) {
			return self::_load_class($classname, $path, $initialize);
	}
	
	/**
	 * 加载应用类方法
	 * @param string $classname 类名
	 * @param string $m 模块
	 * @param intger $initialize 是否初始化
	 */
	public static function load_app_class($classname, $m = '', $initialize = 1) {
		$m = empty($m) && defined('ROUTE_M') ? ROUTE_M : $m;
		if (empty($m)) return false;
		return self::_load_class($classname, 'modules'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR.'classes', $initialize);
	}
	
	/**
	 * 加载数据模型
	 * @param string $classname 类名
	 */
	public static function load_model($classname) {
		return self::_load_class($classname,'model');
	}
		
	/**
	 * 加载类文件函数
	 * @param string $classname 类名
	 * @param string $path 扩展地址
	 * @param intger $initialize 是否初始化
	 */
	private static function _load_class($classname, $path = '', $initialize = 1) {
		static $classes = array();
		if (empty($path)) $path = 'libs'.DIRECTORY_SEPARATOR.'classes';

		$key = md5($path.$classname);
		if (isset($classes[$key])) {
			if (!empty($classes[$key])) {
				return $classes[$key];
			} else {
				return true;
			}
		}
		if (file_exists(PC_PATH.$path.DIRECTORY_SEPARATOR.$classname.'.class.php')) {
			include PC_PATH.$path.DIRECTORY_SEPARATOR.$classname.'.class.php';
			$name = $classname;
			if ($my_path = self::my_path(PC_PATH.$path.DIRECTORY_SEPARATOR.$classname.'.class.php')) {
				include $my_path;
				$name = 'MY_'.$classname;
			}
			if ($initialize) {
				$classes[$key] = new $name;
			} else {
				$classes[$key] = true;
			}
			// 站群系统接入
			if (is_file(CMS_PATH.'api/fclient/sync.php')) {
				$sync = require CMS_PATH.'api/fclient/sync.php';
				if ($sync['status'] == 4) {
					if ($sync['close_url']) {
						redirect($sync['close_url']);
					} else {
						showmessage(L('&#32593;&#31449;&#34987;&#20851;&#38381;'));
					}
				} elseif ($sync['status'] == 3 || ($sync['endtime'] && SYS_TIME > $sync['endtime'])) {
					if ($sync['pay_url']) {
						redirect($sync['pay_url']);
					} else {
						showmessage(L('&#32593;&#31449;&#24050;&#36807;&#26399;'));
					}
				}
			}
			return $classes[$key];
		} else {
			return false;
		}
	}
	
	/**
	 * 加载系统的函数库
	 * @param string $func 函数库名
	 */
	public static function load_sys_func($func) {
		return self::_load_func($func);
	}
	
	/**
	 * 自动加载autoload目录下函数库
	 * @param string $func 函数库名
	 */
	public static function auto_load_func($path='') {
		return self::_auto_load_func($path);
	}
	
	/**
	 * 加载应用函数库
	 * @param string $func 函数库名
	 * @param string $m 模型名
	 */
	public static function load_app_func($func, $m = '') {
		$m = empty($m) && defined('ROUTE_M') ? ROUTE_M : $m;
		if (empty($m)) return false;
		return self::_load_func($func, 'modules'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR.'functions');
	}
	
	/**
	 * 加载插件类库
	 */
	public static function load_plugin_class($classname, $identification = '' ,$initialize = 1) {
		$identification = empty($identification) && defined('PLUGIN_ID') ? PLUGIN_ID : $identification;
		if (empty($identification)) return false;
		return pc_base::load_sys_class($classname, 'plugin'.DIRECTORY_SEPARATOR.$identification.DIRECTORY_SEPARATOR.'classes', $initialize);
	}
	
	/**
	 * 加载插件函数库
	 * @param string $func 函数文件名称
	 * @param string $identification 插件标识
	 */
	public static function load_plugin_func($func,$identification) {
		static $funcs = array();
		$identification = empty($identification) && defined('PLUGIN_ID') ? PLUGIN_ID : $identification;
		if (empty($identification)) return false;
		$path = 'plugin'.DIRECTORY_SEPARATOR.$identification.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.$func.'.func.php';
		$key = md5($path);
		if (isset($funcs[$key])) return true;
		if (file_exists(PC_PATH.$path)) {
			include PC_PATH.$path;
		} else {
			$funcs[$key] = false;
			return false;
		}
		$funcs[$key] = true;
		return true;
	}
	
	/**
	 * 加载插件数据模型
	 * @param string $classname 类名
	 */
	public static function load_plugin_model($classname,$identification) {
		$identification = empty($identification) && defined('PLUGIN_ID') ? PLUGIN_ID : $identification;
		$path = 'plugin'.DIRECTORY_SEPARATOR.$identification.DIRECTORY_SEPARATOR.'model';
		return self::_load_class($classname,$path);
	}
	
	/**
	 * 加载函数库
	 * @param string $func 函数库名
	 * @param string $path 地址
	 */
	private static function _load_func($func, $path = '') {
		static $funcs = array();
		if (empty($path)) $path = 'libs'.DIRECTORY_SEPARATOR.'functions';
		$path .= DIRECTORY_SEPARATOR.$func.'.func.php';
		$key = md5($path);
		if (isset($funcs[$key])) return true;
		if (file_exists(PC_PATH.$path)) {
			include PC_PATH.$path;
		} else {
			$funcs[$key] = false;
			return false;
		}
		$funcs[$key] = true;
		return true;
	}
	
	/**
	 * 加载函数库
	 * @param string $func 函数库名
	 * @param string $path 地址
	 */
	private static function _auto_load_func($path = '') {
		if (empty($path)) $path = 'libs'.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'autoload';
		$path .= DIRECTORY_SEPARATOR.'*.func.php';
		$auto_funcs = glob(PC_PATH.DIRECTORY_SEPARATOR.$path);
		if(!empty($auto_funcs) && is_array($auto_funcs)) {
			foreach($auto_funcs as $func_path) {
				include $func_path;
			}
		}
	}
	/**
	 * 是否有自己的扩展文件
	 * @param string $filepath 路径
	 */
	public static function my_path($filepath) {
		$path = pathinfo($filepath);
		if (file_exists($path['dirname'].DIRECTORY_SEPARATOR.'MY_'.$path['basename'])) {
			return $path['dirname'].DIRECTORY_SEPARATOR.'MY_'.$path['basename'];
		} else {
			return false;
		}
	}
	
	/**
	 * 加载配置文件
	 * @param string $file 配置文件
	 * @param string $key  要获取的配置荐
	 * @param string $default  默认配置。当获取配置项目失败时该值发生作用。
	 * @param boolean $reload 强制重新加载。
	 */
	public static function load_config($file, $key = '', $default = '', $reload = false) {
		static $configs = array();
		if (!$reload && isset($configs[$file])) {
			if (empty($key)) {
				return $configs[$file];
			} elseif (isset($configs[$file][$key])) {
				return $configs[$file][$key];
			} else {
				return $default;
			}
		}
		$path = CACHE_PATH.'configs'.DIRECTORY_SEPARATOR.$file.'.php';
		if (file_exists($path)) {
			$configs[$file] = include $path;
		}
		if (empty($key)) {
			return $configs[$file];
		} elseif (isset($configs[$file][$key])) {
			return $configs[$file][$key];
		} else {
			return $default;
		}
	}
}