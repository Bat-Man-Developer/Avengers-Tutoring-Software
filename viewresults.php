<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVENGERS TUTORING</title>
    <link rel="stylesheet" type="text/css" href="assets/styles/viewresults.css">
    <!-- MathJax -->
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['\\(', '\\)']],
                displayMath: [['\\[', '\\]']],
                processEscapes: true
            },
            startup: {
                pageReady: () => {
                    return MathJax.startup.defaultPageReady();
                }
            }
        };
    </script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
    <?php require_once 'layouts/navbar.php'; ?>
    <div class="container">
        <header>
            <h1>Test Results</h1>
            <p>Its A Marathon Not A Sprint ~Kay Mudau</p>
        </header>

        <div class="search-container">
            <form class="search-form" id="searchForm">
                <input type="text" id="studentName" placeholder="Search by student name">
                <input type="email" id="studentEmail" placeholder="Search by email">
                <input type="date" id="dateFrom" placeholder="From date">
                <input type="date" id="dateTo" placeholder="To date">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="loading" id="loading">
            <div class="loading-spinner"></div>
            <p>Loading results...</p>
        </div>

        <div class="results-container" id="resultsContainer">
            <!-- Results will be dynamically inserted here -->
        </div>
    </div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetchResults();
        });

        function fetchResults() {
            const loading = document.getElementById('loading');
            const resultsContainer = document.getElementById('resultsContainer');
            
            loading.style.display = 'block';
            resultsContainer.innerHTML = '';

            const searchData = {
                studentName: document.getElementById('studentName').value.trim(),
                email: document.getElementById('studentEmail').value.trim(),
                dateFrom: document.getElementById('dateFrom').value,
                dateTo: document.getElementById('dateTo').value
            };

            fetch('server/getviewresults.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(searchData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // First get the raw text
            })
            .then(text => {
                try {
                    // Try to parse the text as JSON
                    const data = JSON.parse(text);
                    loading.style.display = 'none';
                    
                    if (data.status === 'success') {
                        displayResults(data.results);
                    } else {
                        resultsContainer.innerHTML = `
                            <div class="no-results">
                                ${data.message || 'No results found'}
                            </div>
                        `;
                    }
                } catch (e) {
                    console.error('Server response:', text); // Log the raw response
                    throw new Error('Failed to parse JSON response');
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                resultsContainer.innerHTML = `
                    <div class="no-results">
                        Error loading results: ${error.message}. Please try again.
                    </div>
                `;
                console.error('Error:', error);
            });
        }

        function displayResults(results) {
            const resultsContainer = document.getElementById('resultsContainer');
            
            if (results.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="no-results">
                        No results found
                    </div>
                `;
                return;
            }

            results.forEach(result => {
                const resultCard = document.createElement('div');
                resultCard.className = 'result-card';
                
                const testDate = new Date(result.fldtestdate).toLocaleString();
                const timeSpent = Math.floor(result.fldtimetaken / 60);
                
                resultCard.innerHTML = `
                    <div class="student-info">
                        <h3>${result.flduserfirstname} ${result.flduserlastname}</h3>
                        <span>${testDate}</span>
                    </div>
                    <div class="score-info">
                        <div class="score-item">Score: ${result.fldscore}%</div>
                        <div class="score-item">Time: ${timeSpent} minutes</div>
                        <div class="score-item">Questions: ${result.fldquestionsattempted}/${result.total_questions}</div>
                        <div class="score-item">Correct: ${result.fldcorrectanswers}</div>
                    </div>
                    <button class="toggle-details" onclick="toggleQuestions(this)">Show Details</button>
                    <div class="questions-list">
                        ${result.questions.map(q => `
                            <div class="question-item ${q.fldcorrectbool ? 'correct' : 'incorrect'}">
                                <p><strong>Q:</strong> ${q.fldquestiontext}</p>
                                <p><strong>Selected:</strong> ${q.fldselectedanswer}</p>
                                <p><strong>Correct:</strong> ${q.fldcorrectanswer}</p>
                            </div>
                        `).join('')}
                    </div>
                `;
                
                resultsContainer.appendChild(resultCard);
            });

            // Safely trigger MathJax typesetting
            if (window.MathJax && typeof window.MathJax.typesetPromise === 'function') {
                window.MathJax.typesetPromise()
                    .catch((err) => console.error('MathJax typesetting failed:', err));
            }
        }

        function toggleQuestions(button) {
            const questionsList = button.nextElementSibling;
            const isVisible = questionsList.style.display === 'block';
            
            questionsList.style.display = isVisible ? 'none' : 'block';
            button.textContent = isVisible ? 'Show Details' : 'Hide Details';

            // Safely trigger MathJax typesetting when showing questions
            if (!isVisible && window.MathJax && typeof window.MathJax.typesetPromise === 'function') {
                window.MathJax.typesetPromise()
                    .catch((err) => console.error('MathJax typesetting failed:', err));
            }
        }

        // Initial load
        fetchResults();
    </script>

    <!-- Main JS -->
    <script src="js/main.js"></script>
</body>
</html>