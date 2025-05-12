<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAMY | Support</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-red: #ff2a2a;
            --dark-red: #a00000;
            --neon-glow: 0 0 10px rgba(255, 42, 42, 0.8);
            --bg-dark: #0d0d0d;
            --bg-darker: #080808;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Rajdhani', sans-serif;
            background: #000 url('https://images.unsplash.com/photo-1542751371-adc38448a05e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') no-repeat center center;
            background-size: cover;
            color: white;
            min-height: 100vh;
            position: relative;
            padding-top: 70px;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 0;
        }

    

        .container {
            position: relative;
            z-index: 1;
            max-width: 700px;
            margin: 40px auto;
            background-color: rgba(30, 30, 30, 0.8);
            padding: 40px;
            border-radius: 16px;
            border: 2px solid var(--primary-red);
            box-shadow: var(--neon-glow);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo img {
            width: 120px;
            border-radius: 8px;
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            color: var(--primary-red);
            text-shadow: 0 0 15px var(--primary-red);
            margin-bottom: 30px;
            font-size: 2.5rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-size: 1.1rem;
            color: #ddd;
            display: block;
            margin-bottom: 8px;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            background-color: rgba(20, 20, 20, 0.8);
            border: 1px solid var(--primary-red);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
        }

        textarea {
            height: 120px;
            resize: none;
        }

        input:focus,
        textarea:focus {
            outline: none;
            box-shadow: 0 0 8px var(--primary-red);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-red);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--neon-glow);
        }

        button:hover {
            background-color: var(--dark-red);
            transform: scale(1.05);
        }

        .check-response {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--primary-red);
        }

        .response-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--primary-red);
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .response-icon:hover {
            text-shadow: 0 0 5px var(--primary-red);
        }

        .response-icon i {
            margin-right: 10px;
            font-size: 1.5rem;
        }

        .response-results {
            display: none;
            background-color: rgba(20, 20, 20, 0.8);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid var(--primary-red);
        }

        .response-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #444;
        }

        .response-item:last-child {
            border-bottom: none;
        }

        .response-item h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.3rem;
            color: var(--primary-red);
            margin-bottom: 10px;
        }

        .response-item p {
            font-size: 1rem;
            color: #ddd;
            margin-bottom: 5px;
        }

        .response-date {
            color: #aaa;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .alert-error {
            background-color: rgba(255, 42, 42, 0.15);
            color: #ff9e9e;
            border: 1px solid var(--primary-red);
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--primary-red);
            font-family: 'Rajdhani', sans-serif;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            text-shadow: 0 0 5px var(--primary-red);
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            h1 {
                font-size: 2rem;
            }

            .logo img {
                width: 100px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="logo">
            <img src="logo.png" alt="GAMY Logo">
        </div>
        <h1>Support Request</h1>
        <form action="../../controller/controllerboth.php" method="POST" onsubmit="return validateForm();">
            <div class="form-group">
                <input type="hidden" id="user_id" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            <button type="submit" name="submit_complaint">Send Support Request</button>
        </form>

        <div class="check-response">
            <div class="response-icon" onclick="toggleResponseForm()">
                <i class="fas fa-reply"></i>
                <span>Check your responses</span>
            </div>
            <form id="responseForm" style="display: none;" onsubmit="return checkResponses();">
                <div class="form-group">
                    <label for="check_user_id">Enter your User ID to check responses:</label>
                    <input type="text" id="check_user_id" name="check_user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>" required>
                </div>
                <button type="submit">Check Responses</button>
            </form>
            <div id="responseResults" class="response-results"></div>
        </div>
        <a href="indexfront.php" class="back-link"><i class="fas fa-arrow-left me-1"></i> Retour à la page produits</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateForm() {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!name) {
                alert("Name is required.");
                return false;
            }
            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }
            if (!message) {
                alert("Message is required.");
                return false;
            }

            return true;
        }

        function toggleResponseForm() {
            const form = document.getElementById('responseForm');
            const results = document.getElementById('responseResults');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            results.style.display = 'none';
        }

  function checkResponses() {
    const userId = document.getElementById('check_user_id').value.trim();
    const resultsDiv = document.getElementById('responseResults');

    // Validate user ID (numeric)
    if (!/^\d+$/.test(userId)) {
        resultsDiv.innerHTML = '<p class="alert-error">User ID must be numeric.</p>';
        resultsDiv.style.display = 'block';
        return false;
    }

    // AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../../controller/controllerboth.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.status === 200) {
            console.log('Raw Response:', this.responseText); // Debug raw response
            try {
                const response = JSON.parse(this.responseText);
                console.log('Parsed Response:', response); // Debug parsed response

                if (response.error) {
                    resultsDiv.innerHTML = `<p class="alert-error">${response.error}</p>`;
                } else if (Array.isArray(response) && response.length > 0) {
                    let html = '';
                    response.forEach(item => {
                        html += `
                            <div class="response-item">
                                <h3>Ticket #${item.id}</h3>
                                <p><strong>Your message:</strong> ${item.message}</p>
                                ${item.admin_response ? `
                                    <p><strong>Response:</strong> ${item.admin_response}</p>
                                    <p class="response-date">Responded on: ${item.response_date}</p>
                                ` : '<p><em>No response yet</em></p>'}
                            </div>
                        `;
                    });
                    resultsDiv.innerHTML = html;
                } else {
                    resultsDiv.innerHTML = '<p>No tickets found for this User ID.</p>';
                }
            } catch (e) {
                resultsDiv.innerHTML = '<p class="alert-error">Invalid response from server. Check console for details.</p>';
                console.error('JSON Parse Error:', e, 'Raw Response:', this.responseText);
            }
        } else {
            resultsDiv.innerHTML = `<p class="alert-error">Server error: ${this.status} ${this.statusText}</p>`;
            console.error('Server Error:', this.status, this.statusText, 'Raw Response:', this.responseText);
        }
        resultsDiv.style.display = 'block';
    };

    xhr.onerror = function() {
        resultsDiv.innerHTML = '<p class="alert-error">Network error. Please try again later.</p>';
        resultsDiv.style.display = 'block';
        console.error('Network Error');
    };

    xhr.send('action=check_responses&user_id=' + encodeURIComponent(userId));
    return false;
}
    </script>
</body>
</html>