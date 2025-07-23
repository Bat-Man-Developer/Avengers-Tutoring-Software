<?php
include('connection.php');

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Manual includes for PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if(isset($_POST['resetpasswordVerificationBtn'])){
    // Rate Limiting
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_login_attempt'] = time();
    }

    if ($_SESSION['login_attempts'] > 5 && (time() - $_SESSION['last_login_attempt']) < 300) {
        session_destroy();
        unset($_POST);
        unset($_GET);
        header('location: ../resetpassword.php?error=Too Many Attempts');
        exit;
    }

    $_SESSION['login_attempts']++;
    $_SESSION['last_login_attempt'] = time();

    $useremail = filter_var($_POST['flduseremail'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
        header('location: ../resetpassword.php?error=Invalid Email Format');
        exit;
    }
    
    // When creating/updating password
    $userpassword = $_POST['flduserpassword'];
    $userconfirmpassword = $_POST['flduserconfirmpassword'];

    // Encrypt OTP using SHA256
    $userotpcode = $_POST['flduserotpcode'];
    $userotpcode = hash('sha256', $userotpcode);
    
    $verifyotpcode = $_SESSION['fldverifyotpcode'];

    if($verifyotpcode ==  $userotpcode){
        //1. if password dont match
        if($userpassword !== $userconfirmpassword){
            header('location: ../resetpasswordverification.php?error=Passwords Do Not Match&flduseremail='.$useremail);
            exit;
        }
        else if(strlen($userpassword) < 8)
        {//2. if password is less than 8 characters
            header('location: ../resetpasswordverification.php?error=Password Must Be Atleast 8 Characters&flduseremail='.$useremail);
            exit;
        }
        else{//3. no errors
            // Use PHP's built-in password hashing function
            $userpassword = password_hash($userpassword, PASSWORD_DEFAULT);
            
            //3.1 check whether there is a user with this email or not
            $stmt = $conn->prepare("SELECT flduserid, flduserfirstname FROM users WHERE flduseremail = ? LIMIT 1");
            $stmt->bind_param('s',$useremail);
            if($stmt->execute()){
                $stmt->bind_result($userid, $userfirstname);
                $stmt->store_result();
            }
            else{
                $stmt->close();
                header('../resetpasswordverification.php?error=Something Went Wrong!! Contact Support Team.&flduseremail='.$useremail);
                exit;
            }

            //3.1.1 if there is a user already registered with this email
            if($stmt->num_rows() == 1){
                $stmt->fetch();
                $stmt->close();
                
                $stmt1 = $conn->prepare("UPDATE users SET flduserpassword=? WHERE flduseremail=?");
                $stmt1->bind_param('ss',$userpassword,$useremail);
                if($stmt1->execute()){
                    // Initialize Rate Limit
                    $_SESSION['login_attempts'] = 0;
                    $_SESSION['last_login_attempt'] = time();

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
                        $mail->Subject = "Reset Password Successful";
                        $mail->Body = "Hello $userfirstname,\n\nYou have successfully reset your password. \n\nBest regards,\nNewstuffSA Team";

                        $mail->send();

                        unset($_SESSION['fldverifyotpcode']);
                        unset($_POST);
                        unset($_GET);                

                        $successMessage = urlencode('Password Has Been Reset Succesfully. Login To Account.');
                        header('location: ../login.php?success=' . $successMessage);
                        exit;
                    } catch (Exception $e) {
                        unset($_SESSION['fldverifyotpcode']);
                        unset($_POST);
                        unset($_GET);                
                        header('location: ../login.php?error=' . urlencode('Password Has Been Reset Succesfully. Failed To Send Email Verification: ' . $mail->ErrorInfo));
                        exit;
                    }
                } else{
                    header('location: ../resetpasswordverification.php?error=Something Went Wrong!! Contact Support Team.&flduseremail='.$useremail);
                    exit;
                }
            }
            else{//3.1.2 if no user registered with this email before
                header('location: ../resetpasswordverification.php?error=Email Not Found!&flduseremail='.$useremail);
                exit;
            }
        }
    } else{
        header('location: ../resetpasswordverification.php?error=OTP Code Is Incorrect!&flduseremail='.$useremail);
        exit;
    }
}