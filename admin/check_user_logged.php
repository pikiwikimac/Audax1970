<?php
  # CHECK se utente è loggato
  if(isset($_SESSION['username'])){
    // L'utente è già loggato, mostra il link "ADMIN"
    $link = 'dashboard.php';
    $linkText = "<i class='bi bi-person bx-tada-hover bx-sm text-dark'></i>";

  } else {
    // L'utente non è loggato, mostra il link "LOGIN"
    $link = '../login/login.php';
    $linkText = "<i class='bi bi-box-arrow-in-right bx-tada-hover bx-sm text-dark'></i>";
  }
?>