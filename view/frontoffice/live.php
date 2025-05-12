<?php
session_start();
require_once '../../config.php';
require_once '../../model/user.php';
require_once '../../controller/UserController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$errors = [];
$success = false;

$pdo = config::getConnexion();
$userController = new UserController($pdo);
$user = $userController->getUserById($_SESSION['user_id']);

$_SESSION['user'] = $user;

require_once '../../model/Stream.php';

$streamModel = new Stream();
$stream_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$stream = $streamModel->getStreamById($stream_id);

if (!$stream) {
    header('Location: streams.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAMY | Live Stream: <?= htmlspecialchars($stream['title'] ?? 'Unknown Stream') ?></title>
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
            z-index: -1;
        }
        *       /* Navbar Styles */
        .navbar-gaming {
            background-color: var(--bg-darker);
            border-bottom: 1px solid var(--primary-red);
            box-shadow: var(--neon-glow);
            font-family: 'Orbitron', sans-serif;
            padding: 0.8rem 1rem;
        }
        
        .navbar-brand-gaming {
            color: white !important;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .nav-link-gaming {
            color: #ddd !important;
            font-weight: 500;
            letter-spacing: 1px;
            margin: 0 8px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-link-gaming:hover {
            color: white !important;
        }
        
        .nav-link-gaming.active {
            color: white !important;
            text-shadow: 0 0 5px var(--primary-red);
        }
        

      

        h1 {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary-red);
            text-shadow: var(--neon-glow);
        }

        .stream-player iframe {
            width: 100%;
            height: 600px;
            border: none;
        }

        .chat-box {
            height: 300px;
            overflow-y: auto;
            background: #111;
            padding: 15px;
            border: 1px solid var(--primary-red);
            border-radius: 8px;
            margin-top: 20px;
        }

        .chat-message {
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .chat-message strong {
            color: var(--primary-red);
        }

        .chat-form {
            margin-top: 10px;
        }

        .chat-form input {
            background: #222;
            color: white;
            border: 1px solid var(--primary-red);
        }

        .chat-form button {
            background: var(--primary-red);
            border: none;
        }

        .chat-form button:hover {
            background: #cc2222;
        }

        .alert-danger {
            background-color: rgba(255, 0, 0, 0.1);
            border-color: var(--primary-red);
            color: #ff9999;
        }
    </style>
</head>
<body>
    <?php require 'navbar.php' ?>

    <div class="container" style="margin-top:50px">
        <h1><?= htmlspecialchars($stream['title'] ?? 'Unknown Stream') ?></h1>
        <div class="stream-player">
            <iframe 
                src="https://www.youtube.com/embed/<?= htmlspecialchars($stream['stream_id'] ?? '') ?>?enablejsapi=1" 
                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
            </iframe>
        </div>
        <div class="chat-box" id="chat-box"></div>
        <form class="chat-form" id="chat-form">
            <input type="hidden" name="youtube_id" value="<?= htmlspecialchars($stream['stream_id'] ?? '') ?>">
            <div class="input-group">
                <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                <button type="submit" class="btn btn-danger">Send</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const chatForm = document.getElementById('chat-form');
        const chatBox = document.getElementById('chat-box');
        const youtubeId = '<?= htmlspecialchars($stream['stream_id'] ?? '') ?>';
        const chatUrl = '../../controller/ChatController.php';

        async function loadMessages() {
            if (!youtubeId) return;
            try {
                const response = await fetch(`${chatUrl}?youtube_id=${encodeURIComponent(youtubeId)}`);
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}: ${await response.text()}`);
                }
                const messages = await response.json();
                chatBox.innerHTML = '';
                messages.forEach(msg => {
                    const div = document.createElement('div');
                    div.className = 'chat-message';
                    div.innerHTML = `<strong>${msg.username}:</strong> ${msg.message}`;
                    chatBox.prepend(div);
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            } catch (error) {
                console.error('Error loading messages:', error);
                chatBox.innerHTML = '<div class="alert alert-danger">Failed to load messages. Please try again.</div>';
            }
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!youtubeId) {
                alert('Invalid stream ID');
                return;
            }
            const formData = new FormData(chatForm);
            try {
                const response = await fetch(chatUrl, {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}: ${await response.text()}`);
                }
                const data = await response.json();
                if (data.success) {
                    const div = document.createElement('div');
                    div.className = 'chat-message';
                    div.innerHTML = `<strong>${data.message.username}:</strong> ${data.message.text}`;
                    chatBox.prepend(div);
                    chatBox.scrollTop = chatBox.scrollHeight;
                    chatForm.reset();
                } else {
                    alert(data.error);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Error sending message: ' + error.message);
            }
        });

        loadMessages();
        setInterval(loadMessages, 5000);
    </script>
</body>
</html>