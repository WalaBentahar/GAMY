<?php include PARTIALS_PATH . 'header.php'; ?>

<div class="container">
    <?php if(isset($_GET['search_query'])): ?>
        <div class="search-results-header">
            <h2>Search results for "<?= htmlspecialchars($_GET['search_query']) ?>"</h2>
            <a href="<?= BASE_URL ?>streams" class="clear-search"><i class="fas fa-times"></i> Clear search</a>
        </div>
    <?php endif; ?>

    <!-- Add sorting dropdown here -->
    <div class="sorting-options">
    <form method="get" action="<?= BASE_URL ?>streams">
        <?php if(isset($_GET['search_query'])): ?>
            <input type="hidden" name="search_query" value="<?= htmlspecialchars($_GET['search_query']) ?>">
        <?php endif; ?>
        <select name="sort" onchange="this.form.submit()">
            <option value="title_asc" <?= ($_GET['sort'] ?? 'title_asc') === 'title_asc' ? 'selected' : '' ?>>A-Z</option>
            <option value="title_desc" <?= ($_GET['sort'] ?? '') === 'title_desc' ? 'selected' : '' ?>>Z-A</option>
            <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Newest</option>
            <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Oldest</option>
        </select>
    </form>
</div>

    <?php if(!isset($_GET['search_query'])): ?>
        <h2>Gaming Videos</h2>
    <?php endif; ?>
    
    <div class="video-grid">
        <?php if(!empty($videos)): ?>
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
        <?php elseif(isset($_GET['search_query'])): ?>
            <div class="no-results-section">
                <i class="fas fa-video-slash"></i>
                <p>No videos found</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if(!isset($_GET['search_query'])): ?>
        <h2 class="live-streams-heading">Live Streams</h2>
    <?php endif; ?>

    <div class="video-grid">
        <?php if(!empty($streams)): ?>
            <?php foreach ($streams as $stream): ?>
                <a href="<?= BASE_URL ?>streams/live?id=<?= $stream['id'] ?>" class="stream-link">
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
                </a>
            <?php endforeach; ?>
        <?php elseif(isset($_GET['search_query'])): ?>
            <div class="no-results-section">
                <i class="fas fa-broadcast-tower-slash"></i>
                <p>No streams found</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if(isset($_GET['search_query']) && empty($videos) && empty($streams)): ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <p>No results found for "<?= htmlspecialchars($_GET['search_query']) ?>"</p>
        </div>
    <?php endif; ?>
</div>

<?php include PARTIALS_PATH . 'footer.php'; ?>