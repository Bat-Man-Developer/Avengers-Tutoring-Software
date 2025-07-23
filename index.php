<!DOCTYPE html>
<html>
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
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #151515;
            font-family: 'Arial', sans-serif;
            overflow: hidden;
        }

        .container {
            text-align: center;
            color: #e23636;
            z-index: 1;
        }

        .equation {
            font-size: 1.5em;
            margin: 15px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.5s ease forwards;
            color: #fff;
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
            background: #333;
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
            background: linear-gradient(90deg, #e23636, #518cca);
            animation: load 5s ease forwards;
        }

        @keyframes load {
            0% { width: 0; }
            100% { width: 100%; }
        }

        .matrix {
            position: absolute;
            color: rgba(255,255,255,0.1);
            font-size: 14px;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .title {
            font-size: 3.5em;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #e23636, #518cca);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                text-shadow: 0 0 5px #e23636, 0 0 10px #e23636;
            }
            to {
                text-shadow: 0 0 10px #518cca, 0 0 20px #518cca;
            }
        }

        .subtitle {
            font-size: 1.2em;
            margin-bottom: 30px;
            color: #fff;
            opacity: 0.8;
        }

        .logo {
            font-size: 4em;
            margin-bottom: 20px;
            color: #e23636;
        }

    </style>
</head>
<body>
    <div class="matrix" id="matrix"></div>
    <div class="container">
        <div class="logo">A</div>
        <h1 class="title">Avengers Tutors</h1>
        <p class="subtitle">Assembling Knowledge, One Student at a Time</p>
        <div class="loading-bar"></div>
        <div id="equations"></div>
    </div>

    <script>
        // Redirect after 5 seconds
        setTimeout(function() {
            window.location.href = 'home.php';
        }, 5000);

        // Matrix rain effect
        const matrix = document.getElementById('matrix');
        const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        function createRain() {
            const column = document.createElement('div');
            column.style.position = 'absolute';
            column.style.left = Math.random() * 100 + '%';
            column.style.animation = `fall ${Math.random() * 10 + 5}s linear infinite`;
            column.style.opacity = '0.5';

            let content = '';
            for (let i = 0; i < 20; i++) {
                content += chars[Math.floor(Math.random() * chars.length)] + '<br>';
            }
            column.innerHTML = content;

            matrix.appendChild(column);
            setTimeout(() => matrix.removeChild(column), 10000);
        }

        setInterval(createRain, 100);

        // Random educational quotes
        const equations = [
            "Knowledge is Power",
            "Study Like a Hero",
            "Excellence is Our Standard",
            "Together We Learn Better",
            "Your Success is Our Mission"
        ];

        const equationsContainer = document.getElementById('equations');
        let currentEquation = 0;

        function showNextEquation() {
            const div = document.createElement('div');
            div.className = 'equation';
            div.textContent = equations[currentEquation];
            equationsContainer.appendChild(div);

            if (equationsContainer.children.length > 3) {
                equationsContainer.removeChild(equationsContainer.children[0]);
            }

            currentEquation = (currentEquation + 1) % equations.length;
        }

        setInterval(showNextEquation, 2000);

        // Add falling animation style
        const fallAnimation = `
            @keyframes fall {
                from { transform: translateY(-100vh); }
                to { transform: translateY(100vh); }
            }
        `;
        const styleElement = document.createElement('style');
        styleElement.textContent = fallAnimation;
        document.head.appendChild(styleElement);
    </script>
</body>
</html>