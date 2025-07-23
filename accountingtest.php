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
	header('location: login.php?error=User Not Logged In.');
	exit;
}
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
    <link rel="stylesheet" type="text/css" href="assets/styles/home.css">
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
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #000;
        }

        body {
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .timer {
            background: #2c3e50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin: 20px 0;
        }

        a {
            text-decoration: none;
            background-color: #3498db;
            color: #1a1a2e;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            margin: 20px 0;
            transition: all 0.3s ease;
        }

        a:hover {
            background:rgb(45, 110, 171);
            transform: translateY(-2px);
        }

        .student-info {
            margin-bottom: 20px;
        }

        .student-info input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #2c3e50;
            border-radius: 5px;
            font-size: 16px;
            color: #000;
            background-color: #fff;
        }

        .student-info input::placeholder {
            color: #555;
            opacity: 1 !important;
        }

        .student-info input::-webkit-input-placeholder {
            color: #555;
            opacity: 1 !important;
        }

        .student-info input::-moz-placeholder {
            color: #555;
            opacity: 1 !important;
        }

        .student-info input:-ms-input-placeholder {
            color: #555;
            opacity: 1 !important;
        }

        .student-info input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        .error-message {
            color: #e74c3c;
            display: none;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .question-container {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .question {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .option {
            padding: 10px;
            margin: 5px 0;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .option:hover {
            background-color: #f0f0f0;
        }

        .selected {
            background-color: #3498db;
            color: #000;
        }

        .correct {
            background-color: #2ecc71;
            color: white;
        }

        .incorrect {
            background-color: #e74c3c;
            color: #000;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 20px 0;
        }

        .btn:hover {
            background: #34495e;
        }

        .result {
            display: none;
            text-align: center;
        }

        .score {
            font-size: 24px;
            margin: 20px 0;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: #ddd;
            border-radius: 10px;
            margin: 20px 0;
            overflow: hidden;
        }

        .progress {
            width: 0%;
            height: 100%;
            background: #3498db;
            transition: width 0.3s ease;
        }

        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Grade 11 Accounting SBA Test</h1>
            <p>Winning Is A Tough Habit To Break ~Kay Mudau</p><br>
            <a href="dashboard.php">View Results</a>
            <div class="timer">Time Remaining: <span id="time">45:00</span></div>
        </header>

        <div class="student-info">
            <div><?php echo 'Full Name: ' . $_SESSION['flduserfirstname'] . ' ' . $_SESSION['flduserlastname']; ?></div>
            <div><?php echo 'Email: ' . $_SESSION['flduseremail']; ?></div>
            <div><?php echo 'Grade: ' . $_SESSION['fldusergrade']; ?></div>
            <input type="hidden" id="studentName" value="<?php echo $_SESSION['flduserfirstname'] . ' ' . $_SESSION['flduserlastname']; ?>">
            <input type="hidden" id="email" value="<?php echo $_SESSION['flduseremail']; ?>">
            <input type="hidden" id="grade" value="<?php echo $_SESSION['fldusergrade']; ?>">
        </div>

        <div class="progress-bar">
            <div class="progress" id="progress"></div>
        </div>

        <div id="quiz-container"></div>

        <button class="btn" id="submit-btn">Submit Test</button>

        <div class="result" id="result">
            <h2>Test Results</h2>
            <div class="score">Score: <span id="score">0</span>/100</div>
        </div>

        <div class="loading" id="loading">
            <div class="loading-spinner"></div>
            <p>Submitting your test...</p>
        </div>
    </div>

    <script>
        const questions = [
            {
                question: "What is a bank reconciliation statement used to?",
                options: ["Record fixed assets", "Compare bank and cash book balances", "Calculate depreciation", "Record asset disposal"],
                correct: 1
            },
            {
                question: "Which item would appear as an addition in a bank reconciliation?",
                options: ["Bank charges", "Outstanding deposits", "Direct deposits", "Dishonored cheques"],
                correct: 1
            },
            {
                question: "How is the cost of a fixed asset determined?",
                options: ["Purchase price only", "Purchase price plus installation costs", "Market value", "Resale value"],
                correct: 1
            },
            {
                question: "What is the straight-line method used for?",
                options: ["Bank reconciliation", "Asset valuation", "Calculating depreciation", "Recording purchases"],
                correct: 2
            },
            {
                question: "Outstanding cheques are deducted from what balance?",
                options: ["Cash book", "Bank statement", "Asset register", "Trial balance"],
                correct: 1
            },
            {
                question: "What document records all fixed asset details?",
                options: ["Bank statement", "Asset register", "Cash book", "Journal"],
                correct: 1
            },
            {
                question: "Direct debits appearing on bank statement but not in cash book should be:",
                options: ["Ignored", "Added to cash book", "Subtracted from bank", "Recorded next month"],
                correct: 1
            },
            {
                question: "What is the carrying value of an asset?",
                options: ["Original cost", "Cost less depreciation", "Market value", "Replacement cost"],
                correct: 1
            },
            {
                question: "Which is NOT considered a fixed asset?",
                options: ["Building", "Vehicle", "Inventory", "Equipment"],
                correct: 2
            },
            {
                question: "When should bank reconciliation be prepared?",
                options: ["Annually", "Monthly", "Weekly", "Daily"],
                correct: 1
            },
            {
                question: "What affects the depreciation rate of a fixed asset?",
                options: ["Bank balance", "Useful life", "Cash flow", "Bank charges"],
                correct: 1
            },
            {
                question: "What causes a favorable bank reconciliation difference?",
                options: ["Outstanding cheques", "Bank charges", "Unrecorded deposits", "Direct debits"],
                correct: 2
            },
            {
                question: "How are fixed assets recorded in the books?",
                options: ["At cost", "At market value", "At resale value", "At depreciated value"],
                correct: 0
            },
            {
                question: "What document is needed to verify a bank transaction?",
                options: ["Asset register", "Bank statement", "Depreciation schedule", "Fixed asset list"],
                correct: 1
            },
            {
                question: "When should a fixed asset be written off?",
                options: ["After one year", "When fully depreciated", "When damaged beyond repair", "When purchased"],
                correct: 2
            },
            {
                question: "What is the purpose of an asset disposal account?",
                options: ["Record asset sales", "Calculate depreciation", "Record purchases", "Track maintenance"],
                correct: 0
            },
            {
                question: "Which transaction would NOT appear in a bank reconciliation?",
                options: ["Bank charges", "Outstanding cheques", "Asset depreciation", "Direct deposits"],
                correct: 2
            },
            {
                question: "What is the reducing balance method used for?",
                options: ["Bank reconciliation", "Asset disposal", "Depreciation calculation", "Asset purchase"],
                correct: 2
            },
            {
                question: "How often should fixed assets be physically verified?",
                options: ["Never", "Monthly", "Annually", "Daily"],
                correct: 2
            },
            {
                question: "What entry is made when disposing of a fully depreciated asset?",
                options: ["Debit asset account", "Credit accumulated depreciation", "No entry needed", "Debit bank"],
                correct: 2
            }
        ];

        let currentQuestions = [];
        let score = 0;
        let timeLeft = 60 * 60;
        let timerInterval = null;

        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        function startQuiz() {
            // Stop any existing timer
            stopTimer();
            
            // Reset quiz state
            currentQuestions = questions.map(q => ({
                ...q,
                selectedAnswer: null
            }));
            
            // Reset form
            document.getElementById('studentName').value = '';
            document.getElementById('email').value = '';
            document.getElementById('loading').style.display = 'none';

            // Shuffle questions
            shuffleArray(currentQuestions);
            
            // Reset score
            score = 0;
            
            // Display questions
            displayQuestions();
            
            // Start new timer
            startTimer();
            
            // Update UI
            document.getElementById('quiz-container').style.display = 'block';
            document.getElementById('result').style.display = 'none';
            document.getElementById('submit-btn').style.display = 'block';
            updateProgress();
        }

        function displayQuestions() {
            const quizContainer = document.getElementById('quiz-container');
            quizContainer.innerHTML = '';

            currentQuestions.forEach((q, index) => {
                const questionDiv = document.createElement('div');
                questionDiv.className = 'question-container';
                
                const shuffledOptions = [...q.options];
                const correctAnswerText = q.options[q.correct];
                shuffleArray(shuffledOptions);
                
                // Add a unique id to each option for better tracking
                questionDiv.innerHTML = `
                    <div class="question">Question ${index + 1}: ${q.question}</div>
                    <div class="options">
                        ${shuffledOptions.map((option, i) => `
                            <div class="option" 
                                id="option-${index}-${i}"
                                onclick="selectOption(${index}, '${encodeURIComponent(option)}')"
                                data-question="${index}" 
                                data-option="${encodeURIComponent(option)}">
                                ${option}
                            </div>
                        `).join('')}
                    </div>
                `;
                
                quizContainer.appendChild(questionDiv);
            });

            // Trigger MathJax to render the new content
            MathJax.typesetPromise().then(() => {
                console.log('Math rendering complete');
            }).catch((err) => console.log('Error rendering math:', err));
        }

        function selectOption(questionIndex, encodedOption) {
            const selectedOption = decodeURIComponent(encodedOption);
            const questionContainer = document.querySelector(`.question-container:nth-child(${questionIndex + 1})`);
            const options = questionContainer.querySelectorAll('.option');
            
            // Remove selected class from all options in this question
            options.forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            const clickedOption = questionContainer.querySelector(`[data-option="${encodedOption}"]`);
            if (clickedOption) {
                clickedOption.classList.add('selected');
            }

            // Store the selected answer
            currentQuestions[questionIndex].selectedAnswer = selectedOption;
            
            updateProgress();
        }

        function updateProgress() {
            const selected = document.querySelectorAll('.selected').length;
            const progress = (selected / questions.length) * 100;
            document.getElementById('progress').style.width = `${progress}%`;
        }

        function startTimer() {
            // Clear any existing timer first
            if (timerInterval) {
                clearInterval(timerInterval);
            }
            
            // Reset time to 45 minutes
            timeLeft = 45 * 60;
            
            // Update display immediately before starting interval
            updateTimerDisplay();
            
            // Start new timer
            timerInterval = setInterval(function() {
                timeLeft--;
                
                if (timeLeft <= 0) {
                    stopTimer();
                    submitTest();
                    return;
                }
                
                updateTimerDisplay();
            }, 1000);
        }

        function stopTimer() {
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            const timeDisplay = document.getElementById('time');
            if (timeDisplay) {
                timeDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }
        }

        function submitTest() {
            // Stop the timer
            stopTimer();

            // Calculate score
            score = 0;
            currentQuestions.forEach((question, index) => {
                const selectedAnswer = question.selectedAnswer;
                const correctAnswer = question.options[question.correct];
                
                if (selectedAnswer === correctAnswer) {
                    score += 5;
                }
                
                // Mark correct/incorrect answers
                const questionContainer = document.querySelector(`.question-container:nth-child(${index + 1})`);
                if (questionContainer) {
                    const options = questionContainer.querySelectorAll('.option');
                    options.forEach(option => {
                        const optionText = decodeURIComponent(option.dataset.option);
                        if (optionText === selectedAnswer) {
                            option.classList.add(selectedAnswer === correctAnswer ? 'correct' : 'incorrect');
                        }
                        if (optionText === correctAnswer && selectedAnswer !== correctAnswer) {
                            option.classList.add('correct');
                        }
                    });
                }
            });

            // Update UI
            const scoreElement = document.getElementById('score');
            if (scoreElement) {
                scoreElement.textContent = score;
            }
            document.getElementById('result').style.display = 'block';
            document.getElementById('submit-btn').style.display = 'none';

            // Save results
            saveTestResults({
                score: score,
                answers: currentQuestions.map(q => ({
                    question: q.question,
                    selected: q.selectedAnswer,
                    correct: q.options[q.correct]
                }))
            });
        }

        // Initialization code to use both load events
        window.addEventListener('load', function() {
            startQuiz();
            startTimer();
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Set up event listeners
            const submitBtn = document.getElementById('submit-btn');
            const retryBtn = document.getElementById('retry-btn');
            
            if (submitBtn) {
                submitBtn.addEventListener('click', submitTest);
            }
            
            if (retryBtn) {
                retryBtn.addEventListener('click', startQuiz);
            }
            
            // Start timer if it hasn't started yet
            if (!timerInterval) {
                startTimer();
            }
        });

        // Add a fallback timer check
        setTimeout(function() {
            if (!timerInterval) {
                console.log('Timer fallback initiated');
                startTimer();
            }
        }, 1000);

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('option')) {
                updateProgress();
            }
        });

        function saveTestResults(testData) {
            const loading = document.getElementById('loading');
            loading.style.display = 'block';

            const data = {
                studentName: document.getElementById('studentName').value.trim(),
                email: document.getElementById('email').value.trim(),
                grade: document.getElementById('grade').value.trim(),
                score: testData.score,
                timeTaken: (45 * 60) - timeLeft,
                questionsAttempted: document.querySelectorAll('.selected').length,
                correctAnswers: Math.floor(testData.score / 5),
                questions: currentQuestions.map((q, index) => {
                    const selectedElement = document.querySelector(`.option.selected[data-question="${index}"]`);
                    return {
                        questionText: q.question,
                        selectedAnswer: selectedElement ? decodeURIComponent(selectedElement.dataset.option) : '',
                        correctAnswer: q.options[q.correct],
                        isCorrect: selectedElement ? 
                            decodeURIComponent(selectedElement.dataset.option) === q.options[q.correct] : false
                    };
                })
            };

            fetch('server/gettest.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                loading.style.display = 'none';
                if (result.status === 'success') {
                    // Send Email To User Guardian
                    $to = $_SESSION['flduserguardianemail'];
                    $subject = "Accounting Test Completed Successfully";
                    $message = "Hello $_SESSION['flduserfirstname']'s Guardian,\n\n$_SESSION['flduserfirstname'] has successfully completed the accounting test. You can view the results on the dashboard.\n\nBest regards,\nAvengers Tutoring Team";
                    // Additional headers for better email security
                    $headers = array(
                        'From: info@fcsholdix.co.za',
                        'X-Mailer: PHP/' . phpversion(),
                        'MIME-Version: 1.0',
                        'Content-Type: text/plain; charset=UTF-8'
                    );
                    $headers = implode("\r\n", $headers);
                    
                    if(mail($to, $subject, $message, $headers)){
                        
                    }

                    // Send Email To User
                    $to = $_SESSION['flduseremail'];
                    $subject = "Accounting Test Completed Successfully";
                    $message = "Hello $_SESSION['flduserfirstname'],\n\nYou have successfully completed the accounting test.\n\nBest regards,\nAvengers Tutoring Team";
                    // Additional headers for better email security
                    $headers = array(
                        'From: info@fcsholdix.co.za',
                        'X-Mailer: PHP/' . phpversion(),
                        'MIME-Version: 1.0',
                        'Content-Type: text/plain; charset=UTF-8'
                    );
                    $headers = implode("\r\n", $headers);
                    
                    if(mail($to, $subject, $message, $headers)){
                        
                    }

                    alert('Test results saved successfully!');
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 5000);
                } else {
                    alert('Error saving test results: ' + result.message);
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                console.error('Error:', error);
                alert('Error saving test results. Please try again.');
            });
        }
    </script>
</body>
</html>