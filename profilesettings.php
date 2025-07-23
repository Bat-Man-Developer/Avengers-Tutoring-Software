<?php
// Start session with enhanced security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Set security headers
header("Content-Security-Policy: default-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com https://cdnjs.cloudflare.com; script-src 'self' https://cdnjs.cloudflare.com 'unsafe-inline'; style-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
//header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

//if user has already logged in then take user to account page
if(!isset($_SESSION['logged_in'])){
	header('location: login.php');
	exit;
}

if(isset($_GET['bool']) && $_GET['bool'] == true || isset($_SESSION['last_login_attempt']) && (time() - $_SESSION['last_login_attempt']) < 240){
	unset($_SESSION['fldverifyotpcode']);
}

include('server/getprofilesettings.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <!-- Add CSRF token meta -->
    <meta name="csrf-token" content="<?php echo htmlspecialchars(hash_hmac('sha256', session_id(), 'Blackkarmaholyspirit.01234?')); ?>">
    <title>AVENGERS TUTORING</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js" integrity="sha512-vJ3hR5OeYZ5dB6U5/3eBoTEfH9Nz+IQwFOk/7ixBHZY1T4cWlPOZ0QeYqziIFbUGA5g/Kjf/p9zrXr3D5K6JA==" crossorigin="anonymous"></script>
    <!-- Add SRI hashes for local scripts -->
    <script nonce="<?php echo htmlspecialchars(base64_encode(random_bytes(32))); ?>">
        // Add security measures for JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Sanitize all user inputs
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    this.value = DOMPurify.sanitize(this.value);
                });
            });

            // Add CSRF token to all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a1a1a, #2a2a2a);
            color: #fff;
            min-height: 100vh;
        }

        .navbar {
            background: rgba(0, 0, 0, 0.9);
            position: fixed;
            width: 100%;
            z-index: 1000;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-icon {
            font-size: 2rem;
            color: #ff6b6b;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-link {
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: #ff6b6b;
            transition: all 0.3s ease;
        }

        .nav-link:hover::after {
            width: 80%;
        }

        .nav-link:hover {
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
        }

        .menu-btn {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /*-------- website message error / success --------*/
        #webmessage_red {
            background-color: red;
            font-weight: bold;
            text-align: center;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 2000;
            animation: slideIn 0.3s ease-out forwards, slideOut 0.3s ease-out forwards 5s;
        }

        #webmessage_green{
            background-color: green;
            font-weight: bold;
            text-align: center;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 2000;
            animation: slideIn 0.3s ease-out forwards, slideOut 0.3s ease-out forwards 5s;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .account-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 2rem 2rem;
            display: flex;
            gap: 2rem;
        }

        .account-sidebar {
            flex: 1;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2rem;
            height: fit-content;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #ff6b6b;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
        }

        .sidebar-menu {
            list-style: none;
            margin-top: 2rem;
        }

        .sidebar-menu li {
            margin-bottom: 1rem;
        }

        .sidebar-menu a {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            display: block;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ddd;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: 2px solid #ff6b6b;
            background: rgba(255, 255, 255, 0.2);
        }

        .btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .menu-btn {
                display: block;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: rgba(0, 0, 0, 0.95);
                flex-direction: column;
                padding: 1rem;
                gap: 1rem;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-link {
                width: 100%;
                text-align: center;
            }

            .account-container {
                flex-direction: column;
            }
        }

        .account-content {
            flex: 3;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2rem;
        }

        .settings-section {
            margin-bottom: 2rem;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .form-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 2rem 0;
        }

        .profile-upload {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .profile-upload-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px dashed rgba(255, 255, 255, 0.3);
            padding: 1rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-upload-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: #ff6b6b;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid #ff6b6b;
            color: #ff6b6b;
        }

        .btn-secondary:hover {
            background: rgba(255, 107, 107, 0.1);
        }
    </style>
</head>
<body>
    <!--------- Website Message ------------>
    <?php if(isset($_GET['error'])){ ?>
        <p class="text-center" id="webmessage_red"><?php if(isset($_GET['error'])){ echo $_GET['error']; }?></p>
    <?php } ?>
    <?php if(isset($_GET['success'])){ ?>
        <p class="text-center" id="webmessage_green"><?php if(isset($_GET['success'])){ echo $_GET['success']; }?></p>
    <?php } ?>
    <?php require_once 'layouts/navbar.php'; ?>

    <div class="account-container">
        <div class="account-sidebar">
            <div class="profile-picture">👤</div>
            <h2 style="text-align: center; margin-bottom: 1rem;"><?php echo $_SESSION['flduserfirstname'] . ' ' . $_SESSION['flduserlastname']; ?></h2>
            <p style="text-align: center; color: #ddd;">Grade: <?php echo $_SESSION['fldusergrade']; ?></p>
            <ul class="sidebar-menu">
                <li><a href="profilesettings.php">Profile Settings</a></li>
                <li><a href="dashboard.php">Progress Tracking</a></li>
                <li><a href="logout.php?logout=1" style="color: #ff6b6b;">Logout</a></li>
            </ul>
        </div>

        <div class="account-content">
            <h2 style="margin-bottom: 2rem;">Profile Settings</h2>
            
            <form action="profilesettings.php" method="POST" enctype="multipart/form-data">
                <div class="settings-section">
                    <h3 style="margin-bottom: 1rem;">Profile Picture</h3>
                    <div class="profile-upload">
                        <div class="profile-picture">
                            <?php if($_SESSION['flduserimage']): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['flduserimage']); ?>" alt="Profile Picture">
                            <?php else: ?>
                                👤
                            <?php endif; ?>
                        </div>
                        <label class="profile-upload-btn">
                            <input type="file" name="flduserimage" accept="image/*" style="display: none;">
                            Upload New Picture
                        </label>
                    </div>
                </div>

                <div class="form-divider"></div>

                <div class="settings-section">
                    <h3 style="margin-bottom: 1rem;">Personal Information</h3>
                    <div class="settings-grid">
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" id="firstname" name="flduserfirstname" value="<?php echo htmlspecialchars($_SESSION['flduserfirstname']); ?>" placeholder="<?php echo htmlspecialchars($_SESSION['flduserfirstname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" id="lastname" name="flduserlastname" value="<?php echo htmlspecialchars($_SESSION['flduserlastname']); ?>" placeholder="<?php echo htmlspecialchars($_SESSION['flduserlastname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="flduseremail" value="<?php echo htmlspecialchars($_SESSION['flduseremail']); ?>" placeholder="<?php echo htmlspecialchars($_SESSION['flduseremail']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="flduserphonenumber" value="<?php echo htmlspecialchars($_SESSION['flduserphonenumber']); ?>" placeholder="<?php echo htmlspecialchars($_SESSION['flduserphonenumber']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="grade">Grade</label>
                            <input type="text" id="grade" name="fldusergrade" value="<?php echo htmlspecialchars($_SESSION['fldusergrade']); ?>" placeholder="<?php echo htmlspecialchars($_SESSION['fldusergrade']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-divider"></div>

                <div class="settings-section">
                    <h3 style="margin-bottom: 1rem;">Address Information</h3>
                    <div class="settings-grid">
                        <div class="form-group">
                            <label for="street">Street Address</label>
                            <input type="text" id="street" name="flduserstreetaddress" value="<?php echo htmlspecialchars($_SESSION['flduserstreetaddress']); ?>" placeholder="<?php echo htmlspecialchars($_SESSION['flduserstreetaddress']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="area">Local Area</label>
                            <input type="text" id="area" name="flduserlocalarea" value="<?php echo htmlspecialchars($_SESSION['flduserlocalarea']); ?>" placeholder="<?php echo htmlspecialchars($_SESSION['flduserlocalarea']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="fldusercity" value="<?php echo htmlspecialchars($_SESSION['fldusercity']); ?>" placeholder="<?php echo htmlspecialchars($_SESSION['fldusercity']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="zone">Zone</label>
                            <input type="text" id="zone" name="flduserzone" value="<?php echo htmlspecialchars($_SESSION['flduserzone']); ?>" placeholder="<?php echo htmlspecialchars($_SESSION['flduserzone']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" id="country" name="fldusercountry" value="<?php echo htmlspecialchars($_SESSION['fldusercountry']); ?>" placeholder="<?php echo htmlspecialchars($_SESSION['fldusercountry']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="postal">Postal Code</label>
                            <input type="text" id="postal" name="flduserpostalcode" value="<?php echo htmlspecialchars($_SESSION['flduserpostalcode']); ?>" placeholder="<?php echo htmlspecialchars($_SESSION['flduserpostalcode']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="profileSettingsBtn" class="btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Floating particles animation
        function createParticles() {
            const container = document.querySelector('.floating-particles');
            const particleCount = 50;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random size between 2px and 6px
                const size = Math.random() * 4 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Random position
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                
                // Random animation duration and delay
                const duration = Math.random() * 20 + 10;
                const delay = Math.random() * 10;
                particle.style.animation = `float ${duration}s ${delay}s infinite linear`;
                
                container.appendChild(particle);
            }
        }

        // Create floating particles
        createParticles();
        
        // Add keyframe animation for floating particles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float {
                0% {
                    transform: translateY(0) rotate(0deg);
                }
                50% {
                    transform: translateY(-100vh) rotate(180deg);
                }
                100% {
                    transform: translateY(-200vh) rotate(360deg);
                }
            }
        `;
        document.head.appendChild(style);
    </script>

    <!-- Main JS -->
    <script src="js/main.js"></script>
</body>
</html>