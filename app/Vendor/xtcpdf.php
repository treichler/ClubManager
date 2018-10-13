<?php

App::import('Vendor','tcpdf/tcpdf');

class XTCPDF extends TCPDF
{
  //Page header
  public function Header() {
    // Logo
    $image_file = K_PATH_IMAGES.'..'.DS.'..'.DS.'webroot'.DS.'img'.DS.'logo_PDF.png';
//    $image_file = K_PATH_IMAGES.'logo_PDF.png';
    $this->Image($image_file, 16, 6, 50, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

    // Set font
//    $this->SetFont('helvetica', 'B', 20);
    $this->SetFont('times', '', 12);

    // Title
    $this->Cell(0,  4, Configure::read('club.building'), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    $this->Cell(0, 15, Configure::read('club.street').', '.Configure::read('club.town'), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    $this->Cell(0, 26, 'ZVR: '.Configure::read('club.id'), 0, false, 'R', 0, '', 0, false, 'T', 'M');

    // Draw line
    $line_x = 15;
    $line_y = 24;
    $style = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
    $this->Line($line_x, $line_y, $this->w - $line_x, $line_y, $style);
  }

  // Page footer
  public function Footer() {
    // Position at 15 mm from bottom
    $this->SetY(-15);

    // Set font
    $this->SetFont('times', '', 8);

    // Page number
    $this->Cell(0, 10, 'Seite '.$this->getAliasNumPage().' von '.$this->getAliasNbPages(), '', false, 'C', 0, '', 0, false, 'T', 'M');

    // Draw line
    $line_x = 15;
    $line_y = $this->h - 14;
    $style = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
    $this->Line($line_x, $line_y, $this->w - $line_x, $line_y, $style);
  }
}

