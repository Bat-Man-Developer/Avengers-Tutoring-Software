<?php
include('connection.php');

if(isset($_SESSION['logged_in'])){
    if(isset($_POST['profileSettingsBtn'])){
        try {
            // Collect POST data
            $firstName = $_POST['flduserfirstname'];
            $lastName = $_POST['flduserlastname'];
            $email = $_POST['flduseremail'];
            $phoneNumber = $_POST['flduserphonenumber'];
            $grade = $_POST['fldusergrade'];
            $image = $_POST['flduserimage'];
            $streetAddress = $_POST['flduserstreetaddress'];
            $localArea = $_POST['flduserlocalarea'];
            $city = $_POST['fldusercity'];
            $zone = $_POST['flduserzone'];
            $country = $_POST['fldusercountry'];
            $postalCode = $_POST['flduserpostalcode'];

            // Update database
            $stmt = $conn->prepare("UPDATE users SET 
                flduserfirstname = ?,
                flduserlastname = ?,
                flduseremail = ?,
                flduserphonenumber = ?,
                fldusergrade = ?,
                flduserimage = ?,
                flduserstreetaddress = ?,
                flduserlocalarea = ?,
                fldusercity = ?,
                flduserzone = ?,
                fldusercountry = ?,
                flduserpostalcode = ?
                WHERE flduserid = ?");

            $stmt->bind_param("ssssssssssssi", 
                $firstName,
                $lastName,
                $email,
                $phoneNumber,
                $grade,
                $image,
                $streetAddress,
                $localArea,
                $city,
                $zone,
                $country,
                $postalCode,
                $_SESSION['flduserid']
            );

            if($stmt->execute()){
                // Store updated values in session
                $_SESSION['flduserfirstname'] = $firstName;
                $_SESSION['flduserlastname'] = $lastName;
                $_SESSION['flduseremail'] = $email;
                $_SESSION['flduserphonenumber'] = $phoneNumber;
                $_SESSION['fldusergrade'] = $grade;
                $_SESSION['flduserimage'] = $image;
                $_SESSION['flduserstreetaddress'] = $streetAddress;
                $_SESSION['flduserlocalarea'] = $localArea;
                $_SESSION['fldusercity'] = $city;
                $_SESSION['flduserzone'] = $zone;
                $_SESSION['fldusercountry'] = $country;
                $_SESSION['flduserpostalcode'] = $postalCode;

                $stmt->close();
                $conn->close();
                header("Location: profilesettings.php?success=Profile Settings Updated Successfully.");
                exit();
            } else {
                throw new Exception("Failed to update profile settings.");
            }
        } catch (Exception $e) {
            if(isset($stmt)) $stmt->close();
            if(isset($conn)) $conn->close();
            header("Location: profilesettings.php?error=Error: ".$e->getMessage());
            exit();
        }
    }
} else{
    header('location: ../login.php?error=User Not Logged In.');
    exit;
}