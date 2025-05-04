<?php include PARTIALS_PATH . 'header.php'; ?>

<section class="container admin-section">
    <h2>Admin Panel</h2>

    <!-- Add Video Form -->
    <div class="admin-form-card">
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label for="title">Video Title</label>
                <input type="text" id="title" name="title" placeholder="Enter video title" required>
            </div>
            <div class="form-group">
                <label for="video_url">YouTube URL</label>
                <input type="text" id="video_url" name="video_url" placeholder="Enter YouTube URL" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="admin-btn">Add Video</button>
        </form>
    </div>

    <!-- Video List -->
    <div class="admin-videos-grid">
        <?php foreach ($videos as $video): ?>
            <div class="admin-video-card">
                <div class="video-embed">
                    <?= htmlspecialchars_decode($video['embed_code']) ?>
                </div>
                <div class="video-info">
                    <h3 class="video-title"><?= htmlspecialchars($video['title']) ?></h3>
                    <form method="POST" class="delete-form">
                        <input type="hidden" name="id" value="<?= $video['id'] ?>">
                        <button type="submit" name="delete" class="delete-btn">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include PARTIALS_PATH . 'footer.php'; ?>