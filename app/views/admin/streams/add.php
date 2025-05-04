<?php include PARTIALS_PATH . 'header.php'; ?>

<div class="container mt-4">
    <h2>Add New Stream</h2>
    
    <?php include PARTIALS_PATH . 'alerts.php'; ?>
    
    <form method="POST" action="<?= ADMIN_URL ?>/streams/store">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        
        <div class="form-group">
    <label>YouTube Stream</label>
    <input type="text" 
           name="stream_input" 
           class="form-control" 
           placeholder="URL or ID"
           required
           pattern="(https?://.*youtu.*)|([a-zA-Z0-9_-]{11})"
           title="Valid YouTube URL or 11-character ID">
    <small class="form-text text-muted">
        Supported formats:<br>
        • https://youtube.com/watch?v=ABC123<br>
        • https://youtu.be/ABC123<br>
        • ABCDEFGHIJK (11 characters)
    </small>
</div>
        
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control" required>
                <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Add Stream</button>
    </form>
</div>

<?php include PARTIALS_PATH . 'footer.php'; ?>