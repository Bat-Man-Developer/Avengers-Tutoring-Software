<?php
include('connection.php');

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Manual includes for PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if(isset($_POST['loginVerificationBtn'])){
    // Rate Limiting
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_login_attempt'] = time();
    }

    if ($_SESSION['login_attempts'] > 5 && (time() - $_SESSION['last_login_attempt']) < 300) {
        session_destroy();
        unset($_POST);
        unset($_GET);
        header('location: ../login.php?error=Too Many Attempts');
        exit;
    }

    $_SESSION['login_attempts']++;
    $_SESSION['last_login_attempt'] = time();

    $useremail = filter_var($_POST['flduseremail'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
        header('location: ../login.php?error=Invalid Email Format');
        exit;
    }
    
    // Encrypt OTP using SHA256
    $userotpcode = $_POST['flduserotpcode'];
    $userotpcode = hash('sha256', $userotpcode);
    
    $verifyotpcode = $_SESSION['fldverifyotpcode'];
  
    if($verifyotpcode ==  $userotpcode){
        $stmt = $conn->prepare("SELECT flduserid,flduserimage,flduserfirstname,flduserlastname,flduserstreetaddress,flduserlocalarea,fldusercity,flduserzone,fldusercountry,flduserpostalcode,flduseremail,flduserphonenumber,flduseridnumber,flduserpassword FROM users WHERE flduseremail = ? LIMIT 1");
        $stmt->bind_param('s',$useremail);
        if($stmt->execute()){
            $stmt->bind_result($userid,$userimage,$userfirstname,$userlastname,$userstreetaddress,$userlocalarea,$usercity,$userzone,$usercountry,$userpostalcode,$useremail,$userphonenumber,$useridnumber,$userpassword);
            $stmt->store_result();
            //If user is found in database
            if($stmt->num_rows() == 1){
                $stmt->fetch();
                // Initialize Rate Limit
                $_SESSION['login_attempts'] = 0;
                $_SESSION['last_login_attempt'] = time();

                //Set Users, Billing & Tesimonials/Suggestions Session
                $_SESSION['flduserid'] = $_SESSION['fldbillingid'] = $userid;
                $_SESSION['flduserimage'] = $_SESSION['fldtestimonialsimage'] = $userimage;
                $_SESSION['flduserfirstname'] = $_SESSION['fldbillingfirstname'] = $_SESSION['fldtestimonialsfirstname'] = $userfirstname;
                $_SESSION['flduserlastname'] = $_SESSION['fldbillinglastname'] = $_SESSION['fldtestimonialslastname'] = $userlastname;
                $_SESSION['flduserstreetaddress'] = $_SESSION['fldbillingstreetaddress'] = $userstreetaddress;
                $_SESSION['flduserlocalarea'] = $_SESSION['fldbillinglocalarea'] = $userlocalarea;
                $_SESSION['fldusercity'] = $_SESSION['fldbillingcity'] = $usercity;
                $_SESSION['flduserzone'] = $_SESSION['fldbillingzone'] = $userzone;
                $_SESSION['fldusercountry'] = $_SESSION['fldbillingcountry'] = $_SESSION['fldtestimonialscountry'] = $usercountry;
                $_SESSION['flduserpostalcode'] = $_SESSION['fldbillingpostalcode'] = $userpostalcode;
                $_SESSION['flduseremail'] = $_SESSION['fldbillingemail'] = $_SESSION['fldtestimonialsemail'] = $useremail;
                $_SESSION['flduserphonenumber'] = $_SESSION['fldbillingphonenumber'] = $userphonenumber;
                $_SESSION['logged_in'] = true;
                
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
                    $mail->isHTML(true);
                    $mail->Subject = "Successful Login";

                    $bodyContent = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #333;'>Hello $userfirstname,</h2>

                        <div style='background-color: #e8f5e9; padding: 20px; margin: 20px 0; border-radius: 5px;'>
                            <p style='color: #2e7d32; font-size: 16px; margin: 0;'>
                                <span style='color: #2e7d32; font-size: 20px;'>&#10004;</span>
                                You have successfully logged in to your account.
                            </p>
                        </div>

                        <div style='background-color: #f5f5f5; padding: 15px; margin: 20px 0; border-radius: 5px; font-size: 14px;'>
                            <p style='color: #666; margin: 0;'>
                                If you didn't initiate this login, please:
                                <br>
                                1. <a href='https://newstuffsa.com/resetpassword.php' style='color: #1976d2;'>Change your password immediately</a>
                                <br>
                                2. <a href='https://newstuffsa.com/contact.php' style='color: #1976d2;'>Contact our support team</a>
                            </p>
                        </div>

                        <p style='margin-top: 20px; color: #444;'>
                            Best regards,<br>
                            NewstuffSA Team
                        </p>

                        <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666; text-align: center;'>
                            Always 10 Steps Ahead. We Deliver Success With Ease.
                        </div>
                    </div>";

                    // Create plain text version for email clients that don't support HTML
                    $mail->AltBody = "Hello $userfirstname,\n\nYou have successfully logged in to your account.\n\nIf you didn't initiate this login, please:\n1. Change your password immediately: https://newstuffsa.fcsholdix.co.za/security.php\n2. Contact our support team: https://newstuffsa.fcsholdix.co.za/contact.php\n\nBest regards,\nNewstuffSA Team";

                    $mail->Body = $bodyContent;
                    $mail->send();

                    unset($_SESSION['fldverifyotpcode']);
                    unset($_POST);
                    unset($_GET);            

                    $successMessage = urlencode('Logged In Successfully');
                    header('location: ../account.php?success=' . $successMessage);
                    exit;
                } catch (Exception $e) {
                    unset($_SESSION['fldverifyotpcode']);
                    unset($_POST);
                    unset($_GET);            
                    header('location: ../account.php?error=' . urlencode('Logged In Successfully. Failed To Send Email Verification: ' . $mail->ErrorInfo));
                    exit;
                }
            } else {
                // Email Not Found
                header('location: ../loginverification.php?error=Email Not Found!&flduseremail='.$useremail);
                exit;
            }
        } else{//OTP Code is Wrong
            //Go To Login Verification Page
            header('location: ../loginverification.php?error=Could Not Login At The Moment!&flduseremail='.$useremail);
            exit;
        }
    } else{
        header('location: ../loginverification.php?error=OTP Code Is Incorrect!&flduseremail='.$useremail);
        exit;
    }
}