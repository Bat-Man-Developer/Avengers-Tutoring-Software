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
</head>
<body>
    <div class="container">
        <header>
            <h1>Grade 11 Mathematics Test</h1>
            <p>Giving Up Is Not An Option. ~Kay Mudau</p><br>
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

        <div class="loading" id="loading">
            <div class="loading-spinner"></div>
            <p>Saving your test results...</p>
        </div>

        <div class="progress-bar">
            <div class="progress" id="progress"></div>
        </div>

        <div id="quiz-container">
            <!-- Questions will be inserted here -->
        </div>

        <button class="btn" id="submit-btn">Submit Test</button>

        <div class="result" id="result">
            <h2>Test Results</h2>
            <div class="score">Score: <span id="score">0</span>/100</div>
            <button class="btn" id="retry-btn">Retry Test</button>
        </div>
    </div>
    <script>
        const questions = [
            {
                question: "Find the distance between points A(2,3) and B(5,7)",
                options: ["6 units", "5 units", "5√2 units", "4 units"],
                correct: 1
            },
            {
                question: "What is the slope of a line passing through points (1,2) and (4,8)?",
                options: ["1", "3", "2", "4"],
                correct: 2
            },
            {
                question: "The point that divides the line segment joining A(2,3) and B(6,7) in the ratio 1:2 internally is",
                options: ["(5,6)", "(2,4)", "(3.33, 4.33)", "(4,5)"],
                correct: 2
            },
            {
                question: "The center and radius of the circle x² + y² - 6x - 8y + 25 = 0 are",
                options: ["(-3,-4), r=4", "(4,3), r=4", "(3,4), r=4", "(-3,4), r=4"],
                correct: 2
            },
            {
                question: "The equation of a line parallel to y = 2x + 3 passing through point (1,4) is",
                options: ["y = 2x + 1", "y = -2x + 6", "y = 2x + 5", "y = 2x + 2"],
                correct: 2
            },
            {
                question: "The radius of the circle x² + y² = 16 is",
                options: ["8", "16", "2", "4"],
                correct: 3
            },
            {
                question: "The equation of a line perpendicular to 3x + 4y = 12 is",
                options: ["3x - 4y = k", "-3x - 4y = k", "4x + 3y = k", "4x - 3y = k"],
                correct: 3
            },
            {
                question: "The area of a triangle formed by points (0,0), (4,0) and (0,3) is",
                options: ["12 sq units", "8 sq units", "4 sq units", "6 sq units"],
                correct: 3
            },
            {
                question: "The distance of point (2,3) from line 4x - 3y + 5 = 0 is",
                options: ["3 units", "4 units", "|17|/5 units", "2 units"],
                correct: 2
            },
            {
                question: "The equation of a circle with center (2,3) and radius 5 is",
                options: ["(x+2)² + (y+3)² = 25", "(x-2)² + (y-3)² = 5", "(x+2)² + (y-3)² = 25", "(x-2)² + (y-3)² = 25"],
                correct: 3
            },
            {
                question: "The eccentricity of a parabola is",
                options: ["2", "0", "∞", "1"],
                correct: 3
            },
            {
                question: "The angle between lines y = x and y = -x is",
                options: ["45°", "60°", "90°", "30°"],
                correct: 2
            },
            {
                question: "The focus of the parabola y² = 4ax lies at",
                options: ["(0,a)", "(-a,0)", "(0,-a)", "(a,0)"],
                correct: 3
            },
            {
                question: "Lines 2x + 3y = 5 and 4x + 6y = 11 are",
                options: ["Perpendicular", "Parallel", "Coincident", "Intersecting"],
                correct: 1
            },
            {
                question: "The equation of a line passing through (1,2) with slope 3 is",
                options: ["y + 2 = 3(x + 1)", "y - 2 = -3(x - 1)", "y = 3x", "y - 2 = 3(x - 1)"],
                correct: 3
            },
            {
                question: "The coordinates of the vertex of the parabola y = x² + 2x + 3 are",
                options: ["(1,2)", "(-2,1)", "(2,1)", "(-1,2)"],
                correct: 3
            },
            {
                question: "The equation of the directrix of the parabola y² = 4x is",
                options: ["y = 1", "x = -1", "y = -1", "x = 1"],
                correct: 1
            },
            {
                question: "The point of intersection of lines x + y = 5 and x - y = 1 is",
                options: ["(2,3)", "(1,4)", "(4,1)", "(3,2)"],
                correct: 3
            },
            {
                question: "The distance between parallel lines 2x + 3y + 4 = 0 and 2x + 3y + 10 = 0 is",
                options: ["2 units", "3 units", "6/√13 units", "6 units"],
                correct: 2
            },
            {
                question: "The area of the triangle formed by the lines y = 0, x = 2 and y = x is",
                options: ["4 sq units", "6 sq units", "8 sq units", "2 sq units"],
                correct: 3
            }
        ];

        let currentQuestions = [];
        let score = 0;
        let timeLeft = 45 * 60;
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
                    $subject = "Pure Mathematics Test Completed Successfully";
                    $message = "Hello $_SESSION['flduserfirstname']'s Guardian,\n\n$_SESSION['flduserfirstname'] has successfully completed the pure mathematics test. You can view the results on the dashboard.\n\nBest regards,\nAvengers Tutoring Team";
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
                    $subject = "Pure Mathematics Test Completed Successfully";
                    $message = "Hello $_SESSION['flduserfirstname'],\n\nYou have successfully completed the pure mathematics test.\n\nBest regards,\nAvengers Tutoring Team";
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