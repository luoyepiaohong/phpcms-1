<?php
/**
 * 获取微信文章接口
 */
defined('IN_CMS') or exit('No permission resources.');

$userid = $_SESSION['userid'] ? $_SESSION['userid'] : (param::get_cookie('_userid') ? param::get_cookie('_userid') : param::get_cookie('userid'));
$siteid = param::get_cookie('siteid');
if(!$siteid) $siteid = get_siteid() ? get_siteid() : 1 ;
$url = urldecode($_GET['url']);
if (!$url) {
	dr_json(0, '微信文章地址不能为空');
}

$html = dr_catcher_data($url);
if (!$html) {
	dr_json(0, '没有获取到任何内容');
}

$preg = '<div class="rich_media_content " id="js_content" style="visibility: hidden;">';
if (preg_match('/'.$preg.'(.+)<\/div>/sU', $html, $mt)) {
	pc_base::load_sys_class('upload','',0);
	$upload = new upload($_GET['module'],intval($_GET['catid']),$siteid);
	$upload->set_userid($userid);
	$body = trim($mt[1]);
	$body = str_replace(array('style="display: none;"', ''), '', $body);
	$body = str_replace('data-src=', 'src=', $body);
	// 下载远程图片
	if (intval($_GET['is_esi']) && preg_match_all("/(src)=([\"|']?)([^ \"'>]+)\\2/i", $body, $imgs)) {
		foreach ($imgs[3] as $img) {
			$ext = get_file_ext($img);
			if (!$ext) {
				continue;
			}
			// 下载图片
			if (intval($_GET['is_esi']) && strpos($img, 'http') === 0) {
				$arr = parse_url($img);
				$domain = $arr['host'];
				if ($domain) {
					$sitedb = pc_base::load_model('site_model');
					$data = $sitedb->select();
					$sites = array();
					foreach ($data as $t) {
						$site_domain = parse_url($t['domain']);
						if ($site_domain['port']) {
							$sites[$site_domain['host'].':'.$site_domain['port']] = $t['siteid'];
						} else {
							$sites[$site_domain['host']] = $t['siteid'];
						}
					}
					if (isset($sites[$domain])) {
						// 过滤站点域名
					} elseif (strpos(SYS_UPLOAD_URL, $domain) !== false) {
						// 过滤附件白名单
					} else {
						if(strpos($img, '://') === false) continue;
						$zj = 0;
						$remote = getcache('attachment', 'commons');
						if ($remote) {
							foreach ($remote as $t) {
								if (strpos($t['url'], $domain) !== false) {
									$zj = 1;
									break;
								}
							}
						}
						if ($zj == 0) {
							// 可以下载文件
							// 下载远程文件
							$rt = $upload->down_file(array(
								'url' => $img,
								'timeout' => 5,
								'watermark' => intval($_GET['watermark']),
								'attachment' => $upload->get_attach_info(($_GET['attachment'] ? intval($_GET['attachment']) : intval(SYS_ATTACHMENT_ATTACH)), intval($_GET['image_reduce'])),
								'file_ext' => $ext,
							));
							if ($rt['code']) {
								$att = $upload->save_data($rt['data']);
								if ($att['code']) {
									// 归档成功
									$body = str_replace($img, $rt['data']['url'], $body);
									$img = $att['code'];
									// 标记附件
									upload_json($data['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
								}
							}
						}
					}
				}
			}
		}
	}
} else {
	echo $url;exit;
	dr_json(0, '没有获取到文章内容');
}
if (preg_match('/<meta property="og:title" content="(.+)"/U', $html, $mt)) {
	$title = trim($mt[1]);
} else {
	dr_json(0, '没有获取到文章标题');
}

dr_json(1, '导入成功', array(
	'title' => $title,
	'keyword' => get_keywords($title),
	'content' => $body,
));
function get_keywords($kw) {
	if (!$kw) {
		return '';
	}
	$cfg_bdqc_qcnum = pc_base::load_config('system', 'baidu_qcnum') ? pc_base::load_config('system', 'baidu_qcnum') : 10;
	if (pc_base::load_config('system', 'keywordapi')==1) {
		$baiduapi = pc_base::load_sys_class('baiduapi');
		$data = array(
			'title' => $kw,
			'content' => $kw,
		);
		$data = mb_convert_encoding(json_encode($data), 'GBK', 'UTF8');
		$baidu = $baiduapi->get_data('https://aip.baidubce.com/rpc/2.0/nlp/v1/keyword', $data, 1);
		if ($baidu && $baidu['data']['items']) {
			$n = 0;
			$resultstr = '';
			foreach ($baidu['data']['items'] as $t) {
				$resultstr .= ','.$t['tag'];
				$n++;
				if( $n >= $cfg_bdqc_qcnum ) break;
			}
		}
		return trim($resultstr, ',');
	} else if (pc_base::load_config('system', 'keywordapi')==2) {
		$XAppid = pc_base::load_config('system', 'xunfei_aid');
		$Apikey = pc_base::load_config('system', 'xunfei_skey');
		$fix = 0; //如果错误日志提示【time out|ilegal X-CurTime】，需要把$fix变量改为 100 、200、300、等等，按实际情况调试，只要是数字都行
		$XParam = base64_encode(json_encode(array(
			"type"=>"dependent",
		)));
		$XCurTime = SYS_TIME - $fix;
		$XCheckSum = md5($Apikey.$XCurTime.$XParam);
		$headers = array();
		$headers[] = 'X-CurTime:'.$XCurTime;
		$headers[] = 'X-Param:'.$XParam;
		$headers[] = 'X-Appid:'.$XAppid;
		$headers[] = 'X-CheckSum:'.$XCheckSum;
		$headers[] = 'Content-Type:application/x-www-form-urlencoded; charset=utf-8';
		$rt = json_decode(file_get_contents("http://ltpapi.xfyun.cn/v1/ke", false, stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => $headers,
				'content' => http_build_query(array(
					'text' => $kw,
				)),
				'timeout' => 15*60
			)
		))), true);
		if (!$rt) {
			//showmessage('讯飞接口访问失败');
			return '';
		} elseif ($rt['code']) {
			//showmessage('讯飞接口: '.$rt['desc']);
			return '';
		} else {
			$n = 0;
			$resultstr = '';
			foreach ($rt['data']['ke'] as $t) {
				$resultstr .= ','.$t['word'];
				$n++;
				if( $n >= $cfg_bdqc_qcnum ) break;
			}
			return trim($resultstr, ',');
		}
	} else {
		$phpanalysis = pc_base::load_sys_class('phpanalysis');
		$phpanalysis = new phpanalysis('utf-8', 'utf-8', false);
		$phpanalysis->LoadDict();
		$phpanalysis->SetSource($kw);
		$phpanalysis->StartAnalysis(true);
		return $phpanalysis->GetFinallyKeywords($cfg_bdqc_qcnum);
	}
	return '';
}
/**
 * 设置upload上传的json格式cookie
 */
function upload_json($aid,$src,$filename,$size) {
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
?>