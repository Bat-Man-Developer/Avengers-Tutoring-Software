<?php
include('connection.php');

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Manual includes for PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if(isset($_POST['sendOTPCodeBtn'])){
  $useremail = filter_var($_POST['flduseremail'], FILTER_SANITIZE_EMAIL);
  if (!filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
      header('location: ../resetpassword.php?error=Invalid Email Format');
      exit;
  }
  
  //3.1 check whether there is a user with this email or not
  $stmt = $conn->prepare("SELECT flduserid, flduserfirstname FROM users WHERE flduseremail = ? LIMIT 1");
  $stmt->bind_param('s',$useremail);
  if($stmt->execute()){
    $stmt->bind_result($userid,$userfirstName);
    $stmt->store_result();
  } else{
    $stmt->close();
    header('../resetpassword.php?error=Something Went Wrong!! Contact Support Team.');
    exit;
  }

  //3.1.1 if there is a user already registered with this email
  if($stmt->num_rows() == 1){
    $stmt->fetch();
    $stmt->close();

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
        $mail->Subject = "Reset Password OTP Code";
        $mail->Body = "Hello $userfirstname,\n\nHere is your Reset Password OTP Code: $otpcode. \n\nBest regards,\nNewstuffSA Team";

        $mail->send();

        $successMessage = urlencode('Email Has Been Sent With The OTP Code. Please Enter The OTP Code Before It Expires.');
        header('location: ../resetpasswordverification.php?success=' . $successMessage . '&flduseremail=' . urlencode($useremail));
        exit;
    } catch (Exception $e) {
        header('location: ../resetpassword.php?error=' . urlencode('Failed To Send Email Verification: ' . $mail->ErrorInfo));
        exit;
    }
  } else{//3.1.2 if no user registered with this email before
    header('location: ../resetpassword.php?error=Email Not Found!');
    exit;
  }
}