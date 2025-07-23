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
if(isset($_SESSION['logged_in'])){
	header('location: account.php');
	exit;
}

include('server/getloginverification.php');
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
            z-index: 999;
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
            z-index: 1000;
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
            z-index: 1000;
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

        .login-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
            display: block;
            margin: auto;
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

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
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

        .submit-btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .links {
            margin-top: 1.5rem;
            text-align: center;
        }

        .links a {
            color: #ff6b6b;
            text-decoration: none;
            margin: 0 0.5rem;
            font-size: 0.9rem;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .countdown {
            text-align: center;
            font-size: clamp(1rem, 4vw, 1.25rem); /* Responsive font size between 16px and 20px */
            color: #ff0000;
            margin-bottom: 1rem; /* Using relative unit instead of fixed pixels */
            padding: 0.5rem; /* Added padding for better spacing on mobile */
            width: 100%;
            max-width: 100%;
            display: block; /* Ensures full width on all devices */
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
    <?php require_once 'layouts/navbar.php'; ?><br><br><br><br><br><br><br><br><br><br>
    <div class="login-container">
        <div class="logo" style="margin-bottom: 2rem;">
            <span class="logo-icon">⚡</span>
            <span class="logo-text">Avengers Tutoring</span>
        </div>
        <div class="countdown" id="countdown"></div>
        <form action="loginverification.php" method="POST">
            <div class="form-group">
                <label for="otpcode">OTP Code</label>
                <input type="number" id="otpcode" name="flduserotpcode" required>
            </div>
            <input type="hidden" name="flduseremail" value="<?php echo $_GET['flduseremail']; ?>">
            <button type="submit" name="loginVerificationBtn" class="submit-btn">Verify</button>
        </form>
    </div><br><br><br><br><br><br><br><br><br><br>
    <script>
        // Function to get the expiry time from localStorage or set a new one
        function getOrSetExpiryTime() {
            let expiryTime = localStorage.getItem('otpExpiryTime');
            
            // If no expiry time is set or if it's expired, set a new one
            if (!expiryTime || new Date().getTime() > parseInt(expiryTime)) {
                expiryTime = new Date().getTime() + (240 * 1000); // 4 minutes from now
                localStorage.setItem('otpExpiryTime', expiryTime);
            }
            
            return parseInt(expiryTime);
        }

        // Get or set the expiry time
        const expiryTime = getOrSetExpiryTime();

        // Update the countdown every second
        const countdownTimer = setInterval(function() {
            // Calculate remaining time
            const currentTime = new Date().getTime();
            const timeLeft = Math.max(0, Math.floor((expiryTime - currentTime) / 1000));
            
            // Calculate minutes and seconds
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            // Display the time remaining
            document.getElementById('countdown').innerHTML = `Time remaining: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            
            // When timer reaches zero
            if (timeLeft <= 0) {
                clearInterval(countdownTimer);
                localStorage.removeItem('otpExpiryTime');
                window.location.href = 'login.php?error=OTP Code Expired. Please Try Again.&bool='+true;
            }
        }, 1000);

        // Clean up function to remove expired timer
        window.onunload = function() {
            const currentTime = new Date().getTime();
            const storedExpiryTime = localStorage.getItem('otpExpiryTime');
            
            if (storedExpiryTime && currentTime > parseInt(storedExpiryTime)) {
                localStorage.removeItem('otpExpiryTime');
            }
        };
    </script>

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