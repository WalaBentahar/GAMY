<?php
include __DIR__ . '/../partials/header.php';

// Récupérer le stream
$stream_id = $_GET['id'] ?? null;
$stream = (new Stream())->getStreamById($stream_id);

if (!$stream) {
    header('Location: ' . BASE_URL . 'streams');
    exit;
}

// Récupérer les messages
require_once APP_PATH . 'controllers/ChatController.php';
$chatController = new ChatController();
$messages = $chatController->getMessages($stream['stream_id']);
?>

<div class="stream-container">
    <!-- Lecteur YouTube -->
    <div class="youtube-player">
        <iframe 
            width="100%" 
            height="600" 
            src="https://www.youtube.com/embed/<?= $stream['stream_id'] ?>" 
            frameborder="0" 
            allowfullscreen>
        </iframe>
    </div>

    <!-- Chat simplifié -->
    <div class="stream-chat">
        <div id="chat-messages">
            <?php foreach ($messages as $msg): ?>
                <div class="message">
                    <strong><?= $msg['username'] ?>:</strong>
                    <span><?= $msg['message'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <form id="chat-form" action="<?= BASE_URL ?>chat/send" method="POST">
            <input type="hidden" name="stream_id" value="<?= $stream['id'] ?>">
            <input type="hidden" name="youtube_id" value="<?= $stream['stream_id'] ?>">
            <input type="text" name="message" placeholder="Écrivez un message..." required>
            <button type="submit">Envoyer</button>
        </form>
    </div>
</div>

<script>
// Envoi de message uniquement
document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('<?= BASE_URL ?>chat/send', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
    if (data.success) {
        this.reset();
        const messagesDiv = document.getElementById('chat-messages');
        
        // Utilisez data.message.text au lieu de data.message.message
        const messageHTML = `
            <div class="message">
                <strong>${data.message.username}:</strong>
                <span>${data.message.text}</span>
            </div>
        `;
        
        messagesDiv.insertAdjacentHTML('afterbegin', messageHTML);
    }
});
});
// Actualisation auto
function loadMessages() {
    fetch(`<?= BASE_URL ?>chat/get?youtube_id=<?= $stream['stream_id'] ?>`)
    .then(response => response.text())
    .then(html => {
        document.getElementById('chat-messages').innerHTML = html;
    });
}

setInterval(loadMessages, 3000);
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>