<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVENGERS TUTORING</title>
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

        .subjects-container {
            padding-top: 150px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 180px 2rem 2rem;
        }

        .subject-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .subject-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.2);
        }

        .subject-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: translateX(-100%);
            transition: 0.5s;
        }

        .subject-card:hover::before {
            transform: translateX(100%);
        }

        .subject-card h2 {
            color: #fff;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .subject-card p {
            color: #ddd;
            font-size: 1rem;
            line-height: 1.5;
        }

        .english {
            border-top: 4px solid #ff6b6b;
        }

        .afrikaans {
            border-top: 4px solid #4ecdc4;
        }

        .mathematics {
            border-top: 4px solid #45b7d1;
        }

        .commerce {
            border-top: 4px solid #96ceb4;
        }

        @media (max-width: 768px) {
            .subjects-container {
                grid-template-columns: 1fr;
                padding: 150px 1rem 1rem;
            }
        }

        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
            animation: float 15s infinite linear;
        }
    </style>
</head>
<body>
    <?php require_once 'layouts/navbar.php'; ?>
    <div class="floating-particles"></div>
    
    <div class="subjects-container">
        <div class="subject-card english" onclick="location.href='#english-home'">
            <h2>English Home Language</h2>
            <p>Master your primary language skills with comprehensive tutoring.</p>
        </div>

        <div class="subject-card english" onclick="location.href='#english-lit'">
            <h2>English Literature</h2>
            <p>Explore and analyze classic and contemporary literature.</p>
        </div>

        <div class="subject-card english" onclick="location.href='#english-essays'">
            <h2>English Essays</h2>
            <p>Develop strong writing skills and essay techniques.</p>
        </div>

        <div class="subject-card afrikaans" onclick="location.href='#afrikaans-fal'">
            <h2>Afrikaans First Additional</h2>
            <p>Build your Afrikaans language proficiency.</p>
        </div>

        <div class="subject-card afrikaans" onclick="location.href='#afrikaans-lit'">
            <h2>Afrikaans Literature</h2>
            <p>Discover the richness of Afrikaans literary works.</p>
        </div>

        <div class="subject-card afrikaans" onclick="location.href='#afrikaans-essays'">
            <h2>Afrikaans Essays</h2>
            <p>Perfect your Afrikaans writing and composition.</p>
        </div>

        <div class="subject-card mathematics" onclick="location.href='puremathsloading.php'">
            <h2>Pure Mathematics</h2>
            <p>Excel in advanced mathematical concepts and problem-solving.</p>
        </div>

        <div class="subject-card commerce" onclick="location.href='accountingloading.php'">
            <h2>Accounting</h2>
            <p>Learn essential accounting principles and practices.</p>
        </div>

        <div class="subject-card commerce" onclick="location.href='businessstudiesloading.php'">
            <h2>Business Studies</h2>
            <p>Understand business concepts and management principles.</p>
        </div>

        <div class="subject-card commerce" onclick="location.href='#economics'">
            <h2>Economics</h2>
            <p>Grasp economic theories and their real-world applications.</p>
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

        // Add hover effect to subject cards
        document.querySelectorAll('.subject-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                card.style.background = `
                    radial-gradient(
                        circle at ${x}px ${y}px,
                        rgba(255, 255, 255, 0.2),
                        rgba(255, 255, 255, 0.1) 40%
                    )
                `;
            });

            card.addEventListener('mouseleave', () => {
                card.style.background = 'rgba(255, 255, 255, 0.1)';
            });
        });

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