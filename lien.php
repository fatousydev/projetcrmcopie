<?php
  
  $host = "localhost";
  $user = "root";
  $pass = "";
  $dbname = "crm";

  $conn = new mysqli($host,$user,$pass,$dbname);

  if($conn->connect_error){

    die("connexion échouée :". $conn->connect_error);
  } 
  //else{

   // echo "connexion réussie !";
  //}
?>