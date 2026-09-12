<?php
require_once __DIR__ . '/include/image_webp_helper.php';
echo armor_image_webp_supported() ? "webp=YES\n" : "webp=NO\n";
$dir = __DIR__ . '/images/product';
echo is_dir($dir) ? "dir=OK\n" : "dir=MISSING\n";
$files = glob($dir . '/*.{jpg,jpeg,png,gif,JPG,JPEG,PNG}', GLOB_BRACE);
echo 'count=' . count($files) . "\n";
if (count($files) > 0) {
	$src = $files[0];
	echo 'sample=' . basename($src) . "\n";
	$tmp = $dir . '/_webp_test_' . time() . '.' . pathinfo($src, PATHINFO_EXTENSION);
	copy($src, $tmp);
	$res = armor_convert_file_to_webp($tmp, 80, true);
	print_r($res);
}
