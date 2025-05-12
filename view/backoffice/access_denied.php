<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #000428, #004e92);
            color: #ffcccc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            text-align: center;
        }

        .container {
            background-color: rgba(255, 0, 0, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(255, 0, 0, 0.7);
            max-width: 400px;
            width: 90%;
        }

        h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        p {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .home-button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #ff4444;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .home-button:hover {
            background-color: #cc0000;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Access Denied</h1>
        <p>You do not have permission to access this page.</p>
        <a href="../frontoffice/update_profile.php" class="home-button">Return to Home</a>
    </div>
    <script src="script.js"></script>
</body>
</html>