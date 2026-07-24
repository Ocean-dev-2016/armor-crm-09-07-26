<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP " . PHP_VERSION . "\n";

require __DIR__ . '/bbsales_tracking/PHPExcel/IOFactory.php';

$book = new PHPExcel();
$book->removeSheetByIndex(0);
$sheet = new PHPExcel_Worksheet($book);
$sheet->setTitle('Test Emp');
$book->addSheet($sheet, 0);
$sheet->setCellValue('A1', 'KEY RESULT AREA');
$sheet->setCellValue('I6', 'Total Visit');
$sheet->setCellValue('J6', '01/07/2026');
$sheet->setCellValue('I8', 5);
$sheet->setCellValue('J8', "A\nDone");
$sheet->getStyle('J8')->getFont()->setBold(true)->setSize(16);
$sheet->freezePane('J8');
$book->setActiveSheetIndex(0);

$tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kra_test.xlsx';
$writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
$writer->save($tmp);
echo "OK size=" . filesize($tmp) . " path=" . $tmp . "\n";
