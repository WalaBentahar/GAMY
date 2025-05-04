<?php include PARTIALS_PATH . 'header.php'; ?>

<div class="container admin-section">
    <h2>Manage Streams</h2>
    
    <?php include PARTIALS_PATH . 'alerts.php'; ?>
    
    <a href="<?= ADMIN_URL ?>/streams/add" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Add New Stream
    </a>
    
    <div class="admin-videos-grid">
        <?php foreach ($streams as $stream): ?>
        <div class="admin-video-card">
            <div class="video-embed">
                <iframe 
                    width="100%" 
                    height="200" 
                    src="https://www.youtube.com/embed/<?= htmlspecialchars($stream['stream_id']) ?>" 
                    frameborder="0" 
                    allowfullscreen>
                </iframe>
            </div>
            <div class="video-info">
                <div>
                    <h3 class="video-title"><?= htmlspecialchars($stream['title']) ?></h3>
                    <span class="video-category"><?= htmlspecialchars($stream['category_name']) ?></span>
                </div>
                <form method="POST" action="<?= ADMIN_URL ?>/streams/delete/<?= $stream['id'] ?>">
                    <button type="submit" class="delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include PARTIALS_PATH . 'footer.php'; ?>