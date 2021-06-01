<?php
/**
 * 获取二维码接口
 */
defined('IN_CMS') or exit('No permission resources.');

$value = urldecode($_GET['text']);
$thumb = urldecode($_GET['thumb']);
$matrixPointSize = (int)$_GET['size'];
$errorCorrectionLevel = dr_safe_replace($_GET['level']);

if ($value) {
	//生成二维码图片
	require_once CMS_PATH.'api/phpqrcode.php';
	create_folder(CACHE_PATH.'caches_attach/');
	$file = CACHE_PATH.'caches_attach/qrcode-'.md5($value.$thumb.$matrixPointSize.$errorCorrectionLevel).'-qrcode.png';
	if (is_file($file)) {
		$QR = imagecreatefrompng($file);
	} else {
		QRcode::png($value, $file, $errorCorrectionLevel, $matrixPointSize, 3);
		$QR = imagecreatefromstring(file_get_contents($file));
		if ($thumb) {
			$logo = imagecreatefromstring(dr_catcher_data($thumb));
			$QR_width = imagesx($QR);//二维码图片宽度
			$QR_height = imagesy($QR);//二维码图片高度
			$logo_width = imagesx($logo);//logo图片宽度
			$logo_height = imagesy($logo);//logo图片高度
			$logo_qr_width = $QR_width / 4;
			$scale = $logo_width/$logo_qr_width;
			$logo_qr_height = $logo_height/$scale;
			$from_width = ($QR_width - $logo_qr_width) / 2;
			//重新组合图片并调整大小
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
			imagepng($QR, $file);
		}
	}

	// 输出图片
	ob_start();
	ob_clean();
	header("Content-type: image/png");
	ImagePng($QR);
	exit;
}
?>