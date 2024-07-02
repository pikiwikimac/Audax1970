<?php

  // dichiarare il percorso dei font
  define('FPDF_FONTPATH','./font/');

  //questo file e la cartella font si trovano nella stessa directory
  require('fpdf.php');

  class pdf_html extends FPDF
  {
      function Header()
      {
          // Logo
          $this->Image('../image/logo.jpg',10,6,30);

          // Arial bold 15
          $this->SetFont('Arial','B',15);
          // Move to the right
          $this->Cell(80);
          // Title
          $this->Cell(50,10,'Audax 1970',0,0,'C');
          // Line break
          $this->Ln(20);
          $this->Cell(50,10,'Seminario vescovile - Senigallia 60019',0,0,'C');
          $this->Ln(20);
      }

      // Page footer
      function Footer()
      {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);

        // Arial italic 8
        $this->SetFont('Arial','I',8);

        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
      }

      // Simple table
      function BasicTable($header, $data)
      {
          // Header
          foreach($header as $col)
              $this->Cell(40,7,$col,1);

          $this->Ln();

          // Data
          foreach($data as $row)
          {
              foreach($row as $col)

                  $this->Cell(40,6,$col,1);

              $this->Ln();
          }
      }


      function WriteHTML($html)
      {
        // Strip HTML tags
        $html = strip_tags($html);

        // Decode HTML entities
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

        // Write HTML
        $this->SetFont('Arial', '', 12);
        $this->Write(5, $html);

      }
  }

?>



