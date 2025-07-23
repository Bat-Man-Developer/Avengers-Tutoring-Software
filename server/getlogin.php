<?php
include('connection.php');

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Manual includes for PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if(isset($_POST['loginBtn'])){
    // Get the user data from frontend client-side form
    $useremail = filter_var($_POST['flduseremail'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
        header('location: ../login.php?error=Invalid Email Format');
        exit;
    }

    $userpassword = $_POST['flduserpassword'];

    // Get the user data from database
    $stmt = $conn->prepare("SELECT flduserfirstname, flduseremail, flduserpassword FROM users WHERE flduseremail = ? LIMIT 1");
    $stmt->bind_param('s', $useremail);
    
    if($stmt->execute()){
        $stmt->bind_result($userfirstname, $useremail, $hashedPasswordFromDB);
        $stmt->store_result();
        
        if($stmt->num_rows() == 1){
            $stmt->fetch();
            
            // Verify password
            if(password_verify($userpassword, $hashedPasswordFromDB)){
                // Generate Random 6 Digit OTP Code
                $otpcode = rand(100000, 999999);
                // Encrypt the OTP Code & Store in Session
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
                    $mail->isHTML(true);
                    $mail->Subject = "Login OTP Code";

                    $bodyContent = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #333;'>Hello $userfirstname,</h2>

                        <div style='background-color: #f8f8f8; padding: 20px; margin: 20px 0; border-radius: 5px; text-align: center;'>
                            <p style='font-size: 16px; color: #444; margin-bottom: 15px;'>Here is your Login OTP Code:</p>
                            <div style='background-color: #ffffff; padding: 15px; border-radius: 5px; border: 2px dashed #1976d2;'>
                                <span style='font-size: 24px; font-weight: bold; color: #1976d2; letter-spacing: 3px;'>$otpcode</span>
                            </div>
                            <p style='font-size: 12px; color: #666; margin-top: 15px;'>This code will expire shortly. Do not share this code with anyone.</p>
                        </div>

                        <p style='margin-top: 20px; color: #444;'>
                            Best regards,<br>
                            NewstuffSA Team
                        </p>
                    </div>";

                    // Create plain text version for email clients that don't support HTML
                    $mail->AltBody = "Hello $userfirstname,\n\nHere is your Login OTP Code: $otpcode\n\nThis code will expire shortly. Do not share this code with anyone.\n\nBest regards,\nNewstuffSA Team";

                    $mail->Body = $bodyContent;
                    $mail->send();

                    $successMessage = urlencode('Email Has Been Sent With The OTP Code. Please Enter The OTP Code Before It Expires.');
                    header('location: ../loginverification.php?success=' . $successMessage . '&flduseremail=' . urlencode($useremail));
                    exit;
                } catch (Exception $e) {
                    header('location: ../login.php?error=' . urlencode('Failed To Send Email Verification: ' . $mail->ErrorInfo));
                    exit;
                }
            } else {
                header('location: ../login.php?error=' . urlencode('Invalid Password!'));
                exit;
            }
        } else {
            header('location: ../login.php?error=' . urlencode('Invalid Email!'));
            exit;
        }
    } else {
        header('location: ../login.php?error=' . urlencode('Could Not Login At The Moment'));
        exit;
    }
}