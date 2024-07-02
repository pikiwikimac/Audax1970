<?php
  session_start();
  require_once('../config/db.php');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $superuser=$_POST['superuser'];


    // Check if the username already exists
    $checkQuery = "SELECT * FROM users WHERE username = '$username'";
    $checkResult = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
      $error = "Username already exists";
    } else {
      // Insert the new user into the database
      $insertQuery = "
        INSERT INTO users (firstname, lastname, email, username, password,superuser,accettato)
        VALUES ('$nome', '$cognome', '$email', '$username', '$hashedPassword','$superuser',0)";

      $insertResult = mysqli_query($con, $insertQuery);

      if ($insertResult) {
        // Set other session variables if needed
        header('Location:../login/thanks.php');
        exit;
      } else {
        $error = "Registration failed";
      }
    }
  }

?>