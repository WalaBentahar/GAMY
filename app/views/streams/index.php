<?php include PARTIALS_PATH . 'header.php'; ?>

<div class="container">
    <h2>Gaming Videos</h2>
    <div class="video-grid">
        <?php foreach ($videos as $video): ?>
            <div class="video-card">
                <div class="video-embed">
                    <?= htmlspecialchars_decode($video['embed_code']) ?>
                </div>
                <div class="video-info">
                    <h3 class="video-title"><?= htmlspecialchars($video['title']) ?></h3>
                    <span class="video-category"><?= htmlspecialchars($video['category_name']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 class="live-streams-heading">Live Streams</h2>
    <div class="video-grid">
        <?php foreach ($streams as $stream): ?>
            <div class="video-card live-stream-card">
                <div class="video-embed">
                    <iframe 
                        width="100%" 
                        height="200" 
                        src="https://www.youtube.com/embed/<?= htmlspecialchars($stream['stream_id']) ?>" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
                <div class="video-info">
                    <h3 class="video-title">
                        <?= htmlspecialchars($stream['title']) ?>
                        <span class="live-badge">LIVE</span>
                    </h3>
                    <span class="video-category"><?= htmlspecialchars($stream['category_name']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include PARTIALS_PATH . 'footer.php'; ?>