<?php
include('connection.php');

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Manual includes for PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if(isset($_POST['registrationBtn'])){
  $userfirstname = $_POST['flduserfirstname'];
  $userlastname = $_POST['flduserlastname'];
  $userstreetaddress = $_POST['flduserstreetaddress'];
  $userlocalarea = $_POST['flduserlocalarea'];
  $usercity = $_POST['fldusercity'];
  $userzone = $_POST['flduserzone'];
  $usercountry = $_POST['fldusercountry'];
  $userpostalcode = $_POST['flduserpostalcode'];
  
  $useremail = filter_var($_POST['flduseremail'], FILTER_SANITIZE_EMAIL);
  if (!filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
      header('location: ../registration.php?error=Invalid Email Format');
      exit;
  }
  
  $userphonenumber = $_POST['flduserphonenumber'];
  $userpassword = $_POST['flduserpassword'];
  $userconfirmpassword = $_POST['flduserconfirmpassword'];

  //if password dont match
  if($userpassword !== $userconfirmpassword){
    header('location: ../registration.php?error=Passwords Do Not Match');
    exit;
  }
  else if(strlen($userpassword) < 8)
  {//if password is less than 8 characters
    header('location: ../registration.php?error=Password Must Be Atleast 8 Characters');
    exit;
  }
  else{//if there is no error
    //check whether there is a user with this email or not
    $stmt = $conn->prepare("SELECT count(*) FROM users WHERE flduseremail=?");
    $stmt->bind_param('s',$useremail);
    if($stmt->execute()){
      $stmt->bind_result($num_rows);
      $stmt->store_result();
      $stmt->fetch();
    }
    else{
      header('location: ../registration.php?error=Something Went Wrong, Try Again!!');
      exit;
    }

    //if there is a user already registered with this email
    if($num_rows != 0){
      header('location: ../registration.php?error=User With This Email Already Exists!');
      exit;
    }
    else{//if no user registered with this email before
      // Use PHP's built-in password hashing function
      $userpassword = password_hash($userpassword, PASSWORD_DEFAULT);
      
      // Generate a random 6-digit OTP
      $otpcode = rand(100000, 999999);
      // Encrypt OTP using SHA256
      $_SESSION['fldverifyotpcode'] = hash('sha256', $otpcode);
      
      // Create a new PHPMailer instance
      $mail = new PHPMailer(true);

      try {
          // Server settings
          $mail->isSMTP();
          $mail->Host = 'localhost';
          $mail->SMTPAuth = false;
          $mail->Username = '62783780@newstuffsa.com';
          $mail->Password = 'q!@Nd1le';
          $mail->SMTPSecure = false;
          $mail->Port = 25;

          // Recipients
          $mail->setFrom('noreply@newstuffsa.com', 'NewstuffSA Team');
          $mail->addAddress($useremail, $userfirstname);

          // Content
          $mail->isHTML(false);
          $mail->Subject = "Registration OTP Code";
          $mail->Body = "Hello $userfirstname,\n\nHere is your Registration OTP Code: $otpcode. \n\nBest regards,\nNewstuffSA Team";

          $mail->send();

          $successMessage = urlencode('Email Has Been Sent With The OTP Code. Please Enter The OTP Code Before It Expires.');
          header('location: ../registrationverification.php?success=' . $successMessage . '&flduserfirstname='.$userfirstname.'&flduserlastname='.$userlastname.'&fldusercountry='.$usercountry.'&flduserzone='.$userzone.'&fldusercity='.$usercity.'&flduserlocalarea='.$userlocalarea.'&flduserstreetaddress='.$userstreetaddress.'&flduserpostalcode='.$userpostalcode.'&flduseremail='.$useremail.'&flduserphonenumber='.$userphonenumber.'&flduserpassword='.$userpassword);
          exit;
      } catch (Exception $e) {
          header('location: ../registration.php?error=' . urlencode('Failed To Send Email Verification: ' . $mail->ErrorInfo));
          exit;
      }
    }
  }
}