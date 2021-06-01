<?php
/**
 * 获取Word接口
 */
defined('IN_CMS') or exit('No permission resources.');

$userid = $_SESSION['userid'] ? $_SESSION['userid'] : (param::get_cookie('_userid') ? param::get_cookie('_userid') : param::get_cookie('userid'));
$siteid = param::get_cookie('siteid');
if(!$siteid) $siteid = get_siteid() ? get_siteid() : 1 ;

pc_base::load_sys_class('upload','',0);
$module = trim($_GET['module']);
$catid = intval($_GET['catid']);
$upload = new upload($module,$catid,$siteid);
$upload->set_userid($userid);
$site_setting = get_site_setting($siteid);
$upload_maxsize = $site_setting['upload_maxsize'];
$rt = $upload->upload_file(array(
	'path' => '',
	'form_name' => 'file_upload',
	'file_exts' => explode('|', strtolower('docx')),
	'file_size' => ($upload_maxsize/1024) * 1024 * 1024,
	'attachment' => $upload->get_attach_info(($_GET['attachment'] ? intval($_GET['attachment']) : intval(SYS_ATTACHMENT_ATTACH)), 0),
));
if (!$rt['code']) {
	exit(dr_array2string($rt));
}

// 附件归档
$data = $upload->save_data($rt['data']);
if (!$data['code']) {
	exit(dr_array2string($data));
}

if ($rt && $data) {
	$title = $rt['data']['name'];
	upload_json($data['code'],$rt['data']['url'],$title,format_file_size($rt['data']['size']));
} else {
	dr_json(0, '文件上传失败');
}
if (!$rt['data']['path']) {
	dr_json(0, '没有获取到文件内容');
}
if (!$title) {
	dr_json(0, '没有获取到文件标题');
}
$body = readWordToHtml($rt['data']['path']);
if (!$body) {
	dr_json(0, '没有获取到Word内容');
}

dr_json(1, '导入成功', array(
	'file' => $rt['data']['url'],
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
 * 获取站点配置信息
 * @param  $siteid 站点id
 */
function get_site_setting($siteid) {
	$siteinfo = getcache('sitelist', 'commons');
	return string2array($siteinfo[$siteid]['setting']);
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
function readWordToHtml($source) {
	include_once "vendor/autoload.php";
	$phpWord = \PhpOffice\PhpWord\IOFactory::load($source);
	$html = '';
	foreach ($phpWord->getSections() as $section) {
		foreach ($section->getElements() as $ele1) {
			$paragraphStyle = $ele1->getParagraphStyle();
			if ($paragraphStyle) {
				$html .= '<p style="text-align:'. $paragraphStyle->getAlignment() .';text-indent:20px;">';
			} else {
				$html .= '<p>';
			}
			if ($ele1 instanceof \PhpOffice\PhpWord\Element\TextRun) {
				foreach ($ele1->getElements() as $ele2) {
					if ($ele2 instanceof \PhpOffice\PhpWord\Element\Text) {
						$style = $ele2->getFontStyle();
						$fontFamily = mb_convert_encoding($style->getName(), 'GBK', 'UTF-8');
						$fontSize = $style->getSize();
						$isBold = $style->isBold();
						$styleString = '';
						$fontFamily && $styleString .= "font-family:{$fontFamily};";
						$fontSize && $styleString .= "font-size:{$fontSize}px;";
						$isBold && $styleString .= "font-weight:bold;";
						$html .= sprintf('<span style="%s">%s</span>',
							$styleString,
							mb_convert_encoding($ele2->getText(), 'GBK', 'UTF-8')
						);
					} elseif ($ele2 instanceof \PhpOffice\PhpWord\Element\Image) {
						$siteid = param::get_cookie('siteid');
						if(!$siteid) $siteid = get_siteid() ? get_siteid() : 1 ;
						$imageData = $ele2->getImageStringData(true);
						//$imageData = 'data:' . $ele2->getImageType() . ';base64,' . $imageData;
						$module = trim($_GET['module']);
						$upload = new upload(trim($_GET['module']),intval($_GET['catid']),$siteid);
						$upload->set_userid($userid);
						$rt = $upload->base64_image(array(
							'ext' => $ele2->getImageExtension(),
							'content' => base64_decode($imageData),
							'watermark' => intval($_GET['watermark']),
							'attachment' => $upload->get_attach_info(($_GET['attachment'] ? intval($_GET['attachment']) : intval(SYS_ATTACHMENT_ATTACH)), intval($_GET['image_reduce'])),
						));
						upload_json($data['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
						$html .= '<img src="'.$rt['data']['url'].'" title="'.$rt['data']['name'].'" alt="'.$rt['data']['name'].'"/>';
					}
				}
			}
			$html .= '</p>';
		}
	}
	return mb_convert_encoding($html, 'UTF-8', 'GBK');
}
?>