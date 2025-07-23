<?php
include('connection.php');
if(isset($_SESSION['logged_in'])){
  if(isset($_GET['logout'])){
    //Unset Úser Session
    unset($_SESSION['flduserid']);
    unset($_SESSION['flduserimage']);
    unset($_SESSION['flduserfirstname']);
    unset($_SESSION['flduserlastname']);
    unset($_SESSION['flduserstreetaddress']);
    unset($_SESSION['flduserlocalarea']);
    unset($_SESSION['fldusercity']);
    unset($_SESSION['flduserzone']);
    unset($_SESSION['fldusercountry']);
    unset($_SESSION['flduserpostalcode']);
    unset($_SESSION['flduseremail']);
    unset($_SESSION['flduserphonenumber']);
    unset($_SESSION['fldusergrade']);
    unset($_SESSION['logged_in']);
    
    //Go to login
    header('location: ../login.php?success=Logged Out Successfully.');
    exit;
  }
} else{
  header('location: ../login.php?error=User Not Logged In.');
  exit;
}