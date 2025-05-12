<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #0d0d0d;
            font-family: 'Rajdhani', sans-serif;
            color: #fff;
            text-align: center;
            padding: 100px;
            margin: 0;
        }

        .container {
            background-color: #1a1a1a;
            border-radius: 15px;
            padding: 50px;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.6);
            max-width: 600px;
            margin: 0 auto;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo img {
            width: 150px;
            animation: bounce 1.5s infinite;
        }

        h1 {
            font-size: 48px;
            color: #ff0000;
            text-shadow: 0 0 15px rgba(255, 0, 0, 0.7);
            margin-bottom: 20px;
            animation: fadeIn 2s ease-in-out;
        }

        p {
            font-size: 24px;
            color: #ccc;
            animation: fadeIn 2s ease-in-out;
        }

        .cta-btn {
            margin-top: 30px;
            padding: 15px 30px;
            font-size: 20px;
            color: #fff;
            background-color: #ff0000;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cta-btn:hover {
            background-color: #cc0000;
        }

        /* Animations */
        @keyframes bounce {
            0%, 20%, 40%, 60%, 80%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-15px);
            }
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="logo">
            <img src="https://img.canadacasino.ca/400x210/casino/gamix.jpg" alt="Gamix Logo">
        </div>
        <h1>Thank You for Your Support Request!</h1>
        <p>Your support request has been submitted successfully. Our team will get back to you as soon as possible.</p>
        <a href="index.php">
            <button class="cta-btn">Return to Home</button>
        </a>
    </div>

</body>
</html>
