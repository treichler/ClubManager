<?php

header("Content-type: application/pdf");

App::import('Vendor','xtcpdf');
$tcpdf = new XTCPDF();


// create new PDF document
$pdf = new XTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(Configure::read('club.name'));
$pdf->SetTitle($title_for_layout);
//$pdf->SetSubject('TCPDF Tutorial');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
//$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
//$pdf->SetFont('dejavusans', '', 14, '', true);
$pdf->SetFont('times', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
$pdf->setTextShadow(array('enabled' => false));

// set border width
//$pdf->SetLineWidth(0.1);


if (!isset($title)) $title = '';
if (!isset($information)) $information = '';

// Print title and information
if ($title || $information) {
  $pdf->SetFont('times', 'B', 16, '', true);
  $pdf->Cell(125, 10, $title, '', 0, 'L', 0, '', 0, false, 'T', 'M');
  $pdf->SetFont('times', '', 10, '', true);
//  $pdf->Cell(0, 8, $information, 'LTRB', 2, 'R', 0, '', 0, false, 'T', 'M');
  $pdf->MultiCell(0, 10, $information, '', 'R');
  $pdf->Cell(0, 2, '', '', 2, 'C', 0, '', 0, false, 'T', 'M');
}


// Print the content (from view)
$pdf->SetFont('times', '', 12, '', true);
$pdf->writeHTMLCell(0, 0, '', '', $content_for_layout, 0, 1, 0, true, '', true);


// ---------------------------------------------------------

if (!isset($file_name) || !$file_name) $file_name = 'default';

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($file_name.'.pdf', 'D');

