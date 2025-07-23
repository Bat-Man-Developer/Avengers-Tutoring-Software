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
    <!-- MathJax -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['\\(', '\\)']],
                displayMath: [['\\[', '\\]']],
                processEscapes: true
            }
        };
    </script>
    <style>
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #1a1a1a;
            font-family: 'Arial', sans-serif;
            overflow: hidden;
        }

        .container {
            text-align: center;
            color: #00d4ff;
        }

        .ledger-entry {
            font-size: 1.2em;
            margin: 20px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.5s ease forwards;
            font-family: 'Courier New', monospace;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading-bar {
            width: 300px;
            height: 4px;
            background: #2a2a2a;
            border-radius: 2px;
            margin: 20px auto;
            position: relative;
            overflow: hidden;
        }

        .loading-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0;
            background: #00d4ff;
            animation: load 5s ease forwards;
        }

        @keyframes load {
            0% { width: 0; }
            100% { width: 100%; }
        }

        .numbers-rain {
            position: absolute;
            color: #00d4ff;
            font-size: 14px;
            width: 100%;
            height: 100%;
            overflow: hidden;
            opacity: 0.2;
        }

        .title {
            font-size: 2.5em;
            margin-bottom: 20px;
            text-shadow: 0 0 10px #00d4ff;
        }

        .subtitle {
            font-size: 1.2em;
            margin-bottom: 30px;
            opacity: 0.8;
        }

        .calculator-icon {
            font-size: 3em;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        @keyframes fall {
            from { transform: translateY(-100vh); }
            to { transform: translateY(100vh); }
        }
    </style>
</head>
<body>
    <div class="numbers-rain" id="numbers-rain"></div>
    <div class="container">
        <div class="calculator-icon">🧮</div>
        <h1 class="title">The World Of Accounting</h1>
        <p class="subtitle">Preparing your assessment...</p>
        <div class="loading-bar"></div>
        <div id="ledger-entries"></div>
    </div>

    <script>
        // Redirect after 5 seconds
        setTimeout(function() {
            window.location.href = 'accountingtest.php';
        }, 5000);

        // Numbers rain effect
        const numbersRain = document.getElementById('numbers-rain');
        const digits = "0123456789R$£€¥₹Dr.Cr.";

        function createRain() {
            const column = document.createElement('div');
            column.style.position = 'absolute';
            column.style.left = Math.random() * 100 + '%';
            column.style.animation = `fall ${Math.random() * 10 + 5}s linear infinite`;
            column.style.opacity = '0.5';

            let content = '';
            for (let i = 0; i < 20; i++) {
                content += digits[Math.floor(Math.random() * digits.length)] + '<br>';
            }
            column.innerHTML = content;

            numbersRain.appendChild(column);
            setTimeout(() => numbersRain.removeChild(column), 10000);
        }

        setInterval(createRain, 100);

        // Accounting entries
        const entries = [
            "Assets = Liabilities + Equity",
            "Debit | Credit",
            "Trial Balance",
            "Income Statement",
            "Balance Sheet"
        ];

        const entriesContainer = document.getElementById('ledger-entries');
        let currentEntry = 0;

        function showNextEntry() {
            const div = document.createElement('div');
            div.className = 'ledger-entry';
            div.textContent = entries[currentEntry];
            entriesContainer.appendChild(div);

            if (entriesContainer.children.length > 3) {
                entriesContainer.removeChild(entriesContainer.children[0]);
            }

            currentEntry = (currentEntry + 1) % entries.length;
        }

        setInterval(showNextEntry, 2000);
    </script>
</body>
</html>