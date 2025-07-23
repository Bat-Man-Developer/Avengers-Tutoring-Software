<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVENGERS TUTORING</title>
    <link rel="stylesheet" type="text/css" href="assets/styles/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <?php require_once 'layouts/navbar.php'; ?><br><br><br>
    <div class="dashboard">
        <div class="header">
            <h1>Learning Analytics Dashboard</h1>
            <p>Real-time performance monitoring and analytics</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Students</h3>
                <div class="number" id="totalStudents">0</div>
            </div>
            <div class="stat-card">
                <h3>Average Score</h3>
                <div class="number" id="averageScore">0%</div>
            </div>
            <div class="stat-card">
                <h3>Tests Completed</h3>
                <div class="number" id="testsCompleted">0</div>
            </div>
            <div class="stat-card">
                <h3>Average Time</h3>
                <div class="number" id="averageTime">0 sec</div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-container">
                <h2>Score Distribution</h2>
                <canvas id="scoreDistribution"></canvas>
            </div>
            <div class="chart-container">
                <h2>Performance Trend</h2>
                <canvas id="performanceTrend"></canvas>
            </div>
        </div>

        <div class="student-list">
            <h2>Recent Test Results</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Grade</th>
                            <th>Score</th>
                            <th>Time Taken</th>
                            <th>Progress</th>
                            <th>View Tests</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let scoreDistributionChart = null;
        let performanceTrendChart = null;

        async function fetchDashboardData() {
            try {
                const response = await fetch('server/getdashboard.php');
                const result = await response.json();
                
                if (result.status === 'success') {
                    updateDashboard(result.data);
                } else {
                    console.error('Error from server:', result.message);
                    // Use mock data as fallback
                    const mockData = generateMockData();
                    updateDashboard(mockData);
                }
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
                // Use mock data as fallback
                const mockData = generateMockData();
                updateDashboard(mockData);
            }
        }

        function generateMockData() {
            return {
                totalStudents: 0,
                averageScore: 0,
                testsCompleted: 0,
                averageTime: 0,
                scoreDistribution: [0, 0, 0, 0, 0, 0],
                performanceTrend: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    data: [0, 0, 0, 0, 0, 0]
                },
                recentResults: []
            };
        }

        function updateDashboard(data) {
            // Update statistics
            document.getElementById('totalStudents').textContent = data.totalStudents || 0;
            document.getElementById('averageScore').textContent = (data.averageScore || 0) + '%';
            document.getElementById('testsCompleted').textContent = data.testsCompleted || 0;
            document.getElementById('averageTime').textContent = (data.averageTime || 0) + ' sec';

            // Destroy existing charts
            if (scoreDistributionChart) {
                scoreDistributionChart.destroy();
            }
            if (performanceTrendChart) {
                performanceTrendChart.destroy();
            }

            // Update score distribution chart
            const scoreCtx = document.getElementById('scoreDistribution').getContext('2d');
            scoreDistributionChart = new Chart(scoreCtx, {
                type: 'bar',
                data: {
                    labels: ['0-20', '21-40', '41-60', '61-80', '81-90', '91-100'],
                    datasets: [{
                        label: 'Number of Students',
                        data: data.scoreDistribution || [0, 0, 0, 0, 0, 0],
                        backgroundColor: '#1a73e8',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Update performance trend chart
            const trendCtx = document.getElementById('performanceTrend').getContext('2d');
            performanceTrendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: data.performanceTrend?.labels || [],
                    datasets: [{
                        label: 'Average Score',
                        data: data.performanceTrend?.data || [],
                        borderColor: '#1a73e8',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Update student table
            const tableBody = document.getElementById('studentTableBody');
            tableBody.innerHTML = '';
            
            if (data.recentResults && Array.isArray(data.recentResults)) {
                data.recentResults.forEach(student => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${student.flduserfirstname + ' ' + student.flduserlastname || ''}</td>
                        <td>${student.fldusergrade || ''}</td>
                        <td>${student.fldscore || 0}%</td>
                        <td>${student.fldtimetaken || 0} sec</td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${student.fldscore || 0}%"></div>
                            </div>
                        </td>
                        <td>
                            <div class="btnContainer">
                                <a href="viewresults.php" class="btn" style="text-decoration: none; padding: 5px; border-radius: 5px; background-color:rgb(86, 150, 235); color: #fff">View</a>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }
        }

        // Initial load
        fetchDashboardData();

        // Refresh data every 5 minutes
        setInterval(fetchDashboardData, 300000);
    </script>

    <!-- Main JS -->
    <script src="js/main.js"></script>
</body>
</html>