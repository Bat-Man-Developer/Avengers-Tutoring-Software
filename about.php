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

        .page-content {
            padding-top: 80px;
            min-height: 100vh;
            display: none;
        }

        .page-content.active {
            display: block;
        }

        /* About Page Styles */
        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
        }

        .about-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2rem;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .about-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
        }

        .about-card h3 {
            color: #ff6b6b;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .about-card p {
            color: #ddd;
            line-height: 1.6;
        }

        .mission-statement {
            text-align: center;
            margin-bottom: 4rem;
        }

        .mission-statement h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .mission-statement p {
            font-size: 1.2rem;
            color: #ddd;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.8;
        }

        @media (max-width: 768px) {
            .about-grid {
                grid-template-columns: 1fr;
            }

            .mission-statement h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'layouts/navbar.php'; ?>
    <div class="floating-particles"></div>

    <div id="about" class="page-content">
        <div class="about-container">
            <div class="mission-statement">
                <h2>Our Mission</h2>
                <p>At Avengers Tutoring, we believe in unleashing the superhero within every student. Our mission is to provide exceptional educational support that empowers students to overcome academic challenges and achieve their highest potential.</p>
            </div>

            <div class="about-grid">
                <div class="about-card">
                    <h3>Who We Are</h3>
                    <p>We are a team of passionate educators dedicated to transforming the learning experience. Our tutors are carefully selected for their expertise, teaching ability, and commitment to student success.</p>
                </div>

                <div class="about-card">
                    <h3>Our Approach</h3>
                    <p>We provide personalized learning experiences tailored to each student's unique needs. Our innovative teaching methods combine traditional wisdom with modern technology to ensure optimal learning outcomes.</p>
                </div>

                <div class="about-card">
                    <h3>Our Values</h3>
                    <p>Excellence, integrity, and student success are at the core of everything we do. We believe in building confidence, fostering curiosity, and creating an environment where learning is both challenging and enjoyable.</p>
                </div>
            </div>
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