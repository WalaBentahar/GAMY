<?php
session_start();
require_once '../../config.php';
require_once '../../model/user.php';
require_once '../../controller/UserController.php';

// Suppress notices for AJAX responses
error_reporting(E_ALL & ~E_NOTICE);

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current user data
$pdo = config::getConnexion();
$userController = new UserController($pdo);
$user = $userController->getUserById($_SESSION['user_id']);
$_SESSION['user'] = $user;

// Load controllers
require_once '../../controller/PostsController.php';
require_once '../../controller/CommentsController.php';
require_once '../../controller/LikesController.php';

$postsController = new PostsController();
$commentsController = new CommentsController();
$likesController = new LikesController();

// Handle form submissions and AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's an AJAX JSON request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        if ($action === 'toggle-like') {
            header('Content-Type: application/json');
            $response = $likesController->toggleLike();
            echo json_encode($response);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Action AJAX invalide.']);
        exit;
    }

    // Handle regular form submissions
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'add-post':
            $postsController->addPost();
            break;
        case 'delete-post':
            $postsController->deletePost();
            break;
        case 'update-post':
            $postsController->updatePost();
            break;
        case 'add-comment':
            $commentsController->addComment();
            break;
        case 'delete-comment':
            $commentsController->deleteComment();
            break;
        case 'update-comment':
            $commentsController->updateComment();
            break;
        default:
            $_SESSION['error'] = 'Action inconnue.';
            break;
    }
    header('Location: ?filter=' . urlencode($_GET['filter'] ?? 'all') . '&search=' . urlencode($_GET['search'] ?? ''));
    exit;
}

// Fetch posts
$posts = $postsController->index();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAMY | CyberForum</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Roboto:wght@400;500&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-red: #ff3333;
            --neon-red-dark: #cc0000;
            --neon-red-light: #ff6666;
            --dark-bg: #0a0a0a;
            --dark-surface: #1c1c1c;
            --border-color: #333333;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --card-shadow: 0 8px 32px rgba(0, 0, 0, 0.7);
            --transition: all 0.3s ease;
        }


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
               /* Navbar Styles */
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
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }


      

        .sticky-header {
            position: sticky;
            top: 0;
            background: rgba(28, 28, 28, 0.95);
            border-bottom: 2px solid var(--neon-red);
            padding: 15px;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .search-filter-bar {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .form-control, select {
            background: #2a2a2a;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
            width: 100%;
            max-width: 350px;
            transition: var(--transition);
            font-family: 'Montserrat', sans-serif;
        }

        .form-control:focus, select:focus {
            outline: none;
            border-color: var(--neon-red);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
            font-style: italic;
        }

        .btn-neon {
            background: var(--neon-red);
            border: none;
            color: #000;
            padding: 12px 24px;
            border-radius: 8px;
            font-family: 'Orbitron', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-neon:hover {
            background: var(--neon-red-dark);
            transform: translateY(-2px);
        }

        .btn-neon::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-neon:hover::after {
            left: 100%;
        }

        .btn-danger, .btn-warning {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: var(--transition);
            font-family: 'Montserrat', sans-serif;
        }

        .btn-danger {
            background: #dc3545;
            color: var(--text-primary);
        }

        .btn-warning {
            background: #ffc107;
            color: #000;
        }

        .btn-danger:hover, .btn-warning:hover {
            transform: translateY(-2px);
        }

        .post-card {
            background: var(--dark-surface);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 25px;
            transition: var(--transition);
            overflow: hidden;
        }

        .post-card:hover {
            transform: translateY(-5px);
        }

        .post-header {
            padding: 20px;
            cursor: pointer;
            background: linear-gradient(180deg, #2a2a2a, var(--dark-surface));
        }

        .post-header h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.6rem;
            margin: 0 0 8px;
            text-shadow: 0 0 5px var(--neon-red);
        }

        .post-body {
            padding: 20px;
            display: none;
        }

        .post-body.active {
            display: block;
        }

        .post-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid var(--neon-red);
        }

        .post-description {
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 20px;
            color: var(--text-secondary);
        }

        .category-badge {
            background: var(--neon-red);
            color: #000;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
        }

        .like-button {
            background: #1a1a1a;
            border: 1px solid var(--neon-red);
            color: var(--text-primary);
            padding: 8px 16px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Montserrat', sans-serif;
        }

        .like-button:hover {
            background: var(--neon-red-dark);
            color: #000;
            transform: translateY(-2px);
        }

        .like-button.active {
            background: var(--neon-red);
            color: #000;
        }

        .comment {
            background: #252525;
            border-left: 4px solid var(--neon-red);
            padding: 15px;
            margin: 15px 0 15px 25px;
            border-radius: 6px;
            transition: var(--transition);
        }

        .comment:hover {
        }

        .alert {
            background: rgba(255, 51, 51, 0.2);
            border: 1px solid var(--neon-red);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            animation: fadeIn 0.5s ease-out;
            font-family: 'Montserrat', sans-serif;
        }

        .alert-success {
            background: rgba(0, 255, 0, 0.2);
            border-color: #4ade80;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-out;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--dark-surface);
            border: 2px solid var(--neon-red);
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            padding: 25px;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            color: var(--neon-red);
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            text-shadow: 0 0 5px var(--neon-red);
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--neon-red);
            font-size: 2rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-close:hover {
            color: var(--neon-red-light);
            transform: scale(1.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }

        textarea.form-control {
            min-height: 120px;
            width: 100%;
            resize: vertical;
        }

        @media (max-width: 768px) {
            .container {
                margin: 15px auto;
                padding: 0 10px;
            }

            .search-filter-bar {
                flex-direction: column;
                gap: 10px;
            }

            .form-control, select {
                max-width: 100%;
            }

            .post-header h3 {
                font-size: 1.3rem;
            }

            .modal-content {
                width: 95%;
                padding: 15px;
            }
        }
    </style>
    <script>
        function toggleLike(contentType, contentId) {
            const userId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;
            if (!userId) {
                alert('Vous devez être connecté pour aimer.');
                return;
            }

            console.log(`Sending like request: userId=${userId}, contentType=${contentType}, contentId=${contentId}`);

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    action: 'toggle-like',
                    user_id: userId,
                    content_type: contentType,
                    content_id: contentId
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text(); // Get raw text first for debugging
            })
            .then(text => {
                console.log('Raw response:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        const likeCount = document.querySelector(`#${contentType}-${contentId}-likes`);
                        const button = document.querySelector(`[data-content-id="${contentId}"][data-content-type="${contentType}"]`);
                        if (likeCount) {
                            likeCount.textContent = data.counts.likes;
                        } else {
                            console.warn(`Like count element not found: #${contentType}-${contentId}-likes`);
                        }
                        if (button) {
                            button.classList.toggle('active', data.action === 'added');
                        } else {
                            console.warn(`Like button not found for contentId=${contentId}, contentType=${contentType}`);
                        }
                    } else {
                        console.error('Server error:', data.error);
                        alert(data.error || 'Erreur lors du like.');
                    }
                } catch (e) {
                    console.error('JSON parse error:', e.message, 'Raw text:', text);
                    throw new Error('Réponse non-JSON reçue.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error.message);
                alert('Échec du like : ' + error.message);
            });
        }

        function applyFilterSearch() {
            const filter = document.querySelector('#filter-select').value;
            const search = document.querySelector('#search-input').value;
            const url = new URL(window.location.href);
            url.searchParams.set('filter', filter);
            url.searchParams.set('search', search);
            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const postsContainer = doc.querySelector('#posts-container');
                if (postsContainer) {
                    document.querySelector('#posts-container').innerHTML = postsContainer.innerHTML;
                    rebindEventListeners();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors du filtrage/recherche.');
            });
        }

        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function hideModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function rebindEventListeners() {
            document.querySelectorAll('.post-header').forEach(header => {
                header.removeEventListener('click', togglePostBody);
                header.addEventListener('click', togglePostBody);
            });

            document.querySelectorAll('.edit-toggle').forEach(button => {
                button.removeEventListener('click', toggleEditForm);
                button.addEventListener('click', toggleEditForm);
            });

            document.querySelectorAll('.modal-btn').forEach(button => {
                button.removeEventListener('click', handleModalButton);
                button.addEventListener('click', handleModalButton);
            });

            document.querySelectorAll('.modal-close').forEach(button => {
                button.removeEventListener('click', handleModalClose);
                button.addEventListener('click', handleModalClose);
            });
        }

        function togglePostBody(e) {
            const body = e.currentTarget.nextElementSibling;
            body.classList.toggle('active');
        }

        function toggleEditForm(e) {
            const formId = e.currentTarget.dataset.form;
            const form = document.getElementById(formId);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function handleModalButton(e) {
            const modalId = e.currentTarget.dataset.modal;
            showModal(modalId);
        }

        function handleModalClose(e) {
            const modal = e.currentTarget.closest('.modal');
            hideModal(modal.id);
        }

        document.addEventListener('DOMContentLoaded', () => {
            rebindEventListeners();

            document.querySelector('#filter-select').addEventListener('change', applyFilterSearch);
            document.querySelector('#search-form').addEventListener('submit', e => {
                e.preventDefault();
                applyFilterSearch();
            });

            document.querySelectorAll('#addPostForm, .edit-post-form').forEach(form => {
                form.addEventListener('submit', e => {
                    const title = form.querySelector('[name="title"]')?.value.trim();
                    const author = form.querySelector('[name="author"]')?.value.trim();
                    const category = form.querySelector('[name="category"]')?.value;
                    const photoUrl = form.querySelector('[name="photo_url"]')?.value.trim();
                    if (!title || !author || !category) {
                        alert('Les champs titre, pseudo et catégorie sont requis.');
                        e.preventDefault();
                    } else if (photoUrl && !/^(https?:\/\/).+\.(jpg|jpeg|png|gif)$/i.test(photoUrl)) {
                        alert('Veuillez entrer une URL d’image valide (jpg, jpeg, png, gif).');
                        e.preventDefault();
                    }
                });
            });

            document.querySelectorAll('.comment-form, .edit-comment-form').forEach(form => {
                form.addEventListener('submit', e => {
                    const author = form.querySelector('[name="author"]')?.value.trim();
                    const content = form.querySelector('[name="content"]')?.value.trim();
                    if (!author || !content) {
                        alert('Pseudo et contenu requis.');
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</head>
<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container">
        <!-- Alerts -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php elseif (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Sticky Search/Filter Bar -->
        <div class="sticky-header">

        </div>

        <!-- Add Post Modal -->
        <div id="addPostModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Créer un Nouveau Post</h2>
                    <button class="modal-close">×</button>
                </div>
                <form id="addPostForm" method="POST" action="">
                    <input type="hidden" name="action" value="add-post">
                    <div class="form-group">
                        <label for="post-title">Titre</label>
                        <input type="text" id="post-title" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="hidden" id="post-author" name="author" class="form-control" 
                               value="<?php echo htmlspecialchars($user->getNom() ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="post-category">Catégorie</label>
                        <select id="post-category" name="category" class="form-control" required>
                            <option value="">Choisir...</option>
                            <option value="FPS">FPS</option>
                            <option value="RPG">RPG</option>
                            <option value="MOBA">MOBA</option>
                            <option value="Survie">Survie</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="post-photo">URL de la Photo</label>
                        <input type="url" id="post-photo" name="photo_url" class="form-control" 
                               placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="form-group">
                        <label for="post-description">Description</label>
                        <textarea id="post-description" name="description" class="form-control" 
                                  placeholder="Décrivez votre post..."></textarea>
                    </div>
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <button type="submit" class="btn-neon">Publier</button>
                </form>
            </div>
        </div>

        <!-- Posts List -->
        <div id="posts-container">
                        <div class="search-filter-bar">
                <form id="search-form">
                    <input id="search-input" type="text" name="search" class="form-control" 
                           placeholder="Rechercher par titre ou auteur" 
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <button type="submit" class="btn-neon">Rechercher</button>
                </form>
                <select id="filter-select" class="form-control">
                    <option value="all" <?php echo ($_GET['filter'] ?? 'all') === 'all' ? 'selected' : ''; ?>>Tous</option>
                    <option value="FPS" <?php echo ($_GET['filter'] ?? '') === 'FPS' ? 'selected' : ''; ?>>FPS</option>
                    <option value="RPG" <?php echo ($_GET['filter'] ?? '') === 'RPG' ? 'selected' : ''; ?>>RPG</option>
                    <option value="MOBA" <?php echo ($_GET['filter'] ?? '') === 'MOBA' ? 'selected' : ''; ?>>MOBA</option>
                    <option value="Survie" <?php echo ($_GET['filter'] ?? '') === 'Survie' ? 'selected' : ''; ?>>Survie</option>
                </select>
                <button class="btn-neon modal-btn" data-modal="addPostModal">Nouveau Post</button>
            </div>
            <?php if (empty($posts)): ?>
                <div class="alert alert-info text-center">
                    Aucun post trouvé. Créez le premier post !
                </div>
            <?php else: ?>
<?php foreach ($posts as $post): ?>
    <?php
    $comments = $commentsController->getCommentsByPost($post['id']);
    $likeCounts = $likesController->getLikeCounts('post', $post['id']);
    $userLike = $likesController->getUserLike($_SESSION['user_id'], 'post', $post['id']);
    ?>
    <div class="post-card" data-id="<?php echo $post['id']; ?>">
        <div class="post-header">
            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
            <p class="mb-0">
                Par: <strong><?php echo htmlspecialchars($post['nom']); ?></strong> |
                Catégorie: <span class="category-badge"><?php echo htmlspecialchars($post['category']); ?></span> |
                <small><?php echo htmlspecialchars($post['created_at']); ?></small>
            </p>
        </div>
        <div class="post-body">
            <?php if (!empty($post['photo_url'])): ?>
                <img src="<?php echo htmlspecialchars($post['photo_url']); ?>" 
                     alt="Post Image" class="post-image">
            <?php endif; ?>
            <?php if (!empty($post['description'])): ?>
                <p class="post-description"><?php echo htmlspecialchars($post['description']); ?></p>
            <?php endif; ?>
            <div class="d-flex gap-2 mb-3">
                <button class="like-button <?php echo $userLike === 'liked' ? 'active' : ''; ?>" 
                        data-content-id="<?php echo $post['id']; ?>" data-content-type="post"
                        onclick="toggleLike('post', <?php echo $post['id']; ?>)">
                    <i class="fas fa-heart"></i> 
                    <span id="post-<?php echo $post['id']; ?>-likes" class="like-count">
                        <?php echo $likeCounts['likes']; ?>
                    </span>
                </button>
            </div>
            <div class="d-flex gap-2 mb-3">
                <form class="delete-post-form" method="POST" action="">
                    <input type="hidden" name="action" value="delete-post">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <button type="submit" class="btn-danger btn-sm">
                        <i class="mdi mdi-trash-can"></i> Supprimer
                    </button>
                </form>
                <button class="btn-warning btn-sm edit-toggle" 
                        data-form="edit-post-<?php echo $post['id']; ?>">
                    <i class="mdi mdi-pencil"></i> Modifier
                </button>
            </div>
            <!-- Edit Post Form -->
            <div id="edit-post-<?php echo $post['id']; ?>" style="display: none;" class="mb-3">
                <form class="edit-post-form" method="POST" action="">
                    <input type="hidden" name="action" value="update-post">
                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <div class="form-group">
                        <label>Titre</label>
                        <input type="text" name="title" class="form-control" 
                               value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="author" class="form-control" 
                               value="<?php echo htmlspecialchars($post['nom']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="category" class="form-control" required>
                            <option value="FPS" <?php echo $post['category'] === 'FPS' ? 'selected' : ''; ?>>FPS</option>
                            <option value="RPG" <?php echo $post['category'] === 'RPG' ? 'selected' : ''; ?>>RPG</option>
                            <option value="MOBA" <?php echo $post['category'] === 'MOBA' ? 'selected' : ''; ?>>MOBA</option>
                            <option value="Survie" <?php echo $post['category'] === 'Survie' ? 'selected' : ''; ?>>Survie</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>URL de la Photo</label>
                        <input type="url" name="photo_url" class="form-control" 
                               value="<?php echo htmlspecialchars($post['photo_url'] ?? ''); ?>" 
                               placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"><?php echo htmlspecialchars($post['description'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn-neon">Mettre à jour</button>
                </form>
            </div>
            <!-- Comments -->
            <?php foreach ($comments as $comment): ?>
                <?php
                $commentLikeCounts = $likesController->getLikeCounts('comment', $comment['id']);
                $commentUserLike = $likesController->getUserLike($_SESSION['user_id'], 'comment', $comment['id']);
                ?>
                <div class="comment" data-id="<?php echo $comment['id']; ?>">
                    <p class="mb-1">
                        <strong><?php echo htmlspecialchars($comment['nom'] ?? $comment['author'] ?? 'Anonymous'); ?></strong>: 
                        <?php echo htmlspecialchars($comment['content']); ?>
                    </p>
                    <p class="mb-2"><small><?php echo htmlspecialchars($comment['created_at']); ?></small></p>
                    <div class="d-flex gap-2 mb-2">
                        <button class="like-button <?php echo $commentUserLike === 'liked' ? 'active' : ''; ?>" 
                                data-content-id="<?php echo $comment['id']; ?>" data-content-type="comment"
                                onclick="toggleLike('comment', <?php echo $comment['id']; ?>)">
                            <i class="fas fa-heart"></i> 
                            <span id="comment-<?php echo $comment['id']; ?>-likes" class="like-count">
                                <?php echo $commentLikeCounts['likes']; ?>
                            </span>
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <form class="delete-comment-form" method="POST" action="">
                            <input type="hidden" name="action" value="delete-comment">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                            <button type="submit" class="btn-danger btn-sm">
                                <i class="mdi mdi-trash-can"></i> Supprimer
                            </button>
                        </form>
                        <button class="btn-warning btn-sm edit-toggle" 
                                data-form="edit-comment-<?php echo $comment['id']; ?>">
                            <i class="mdi mdi-pencil"></i> Modifier
                        </button>
                    </div>
                    <!-- Edit Comment Form -->
                    <div id="edit-comment-<?php echo $comment['id']; ?>" style="display: none;" class="mt-2">
                        <form class="edit-comment-form" method="POST" action="">
                            <input type="hidden" name="action" value="update-comment">
                            <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                            <div class="form-group">
                                <input type="hidden" name="author" class="form-control" 
                                       value="<?php echo htmlspecialchars($comment['nom'] ?? $comment['author'] ?? 'Anonymous'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Commentaire</label>
                                <textarea name="content" class="form-control" required><?php echo htmlspecialchars($comment['content']); ?></textarea>
                            </div>
                            <button type="submit" class="btn-neon">Mettre à jour</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            <!-- Add Comment -->
            <form class="comment-form mt-3" method="POST" action="">
                <input type="hidden" name="action" value="add-comment">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                <div class="form-group">
                    <input type="hidden" name="author" class="form-control" 
                           value="<?php echo htmlspecialchars($user->getNom() ?? 'Anonymous'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Commentaire</label>
                    <textarea name="content" class="form-control" placeholder="Votre commentaire" required></textarea>
                </div>
                <button type="submit" class="btn-neon">Commenter</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>