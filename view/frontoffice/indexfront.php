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

ini_set('display_errors', 1);
error_reporting(E_ALL);

$dsn = "mysql:host=localhost;dbname=gamy_bd;charset=utf8mb4";
$username = "root";
$password = "";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT id, nom, description, prix, categorie, disponibilite, image FROM produits";
    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion ou de requête : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAMY | Gaming Gear</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        
        .search-bar {
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }

        .search-bar input {
            width: 60%;
            padding: 10px 15px;
            border: none;
            border-radius: 25px 0 0 25px;
            background-color: rgba(30, 30, 30, 0.8);
            color: white;
            font-size: 1rem;
            border: 1px solid var(--primary-red);
        }

        .search-bar button {
            padding: 10px 15px;
            background-color: var(--primary-red);
            color: white;
            border: none;
            border-radius: 0 25px 25px 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-bar button:hover {
            background-color: var(--dark-red);
        }

        .category-filter {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            flex-wrap: wrap;
            gap: 10px;
        }

        .category-btn {
            background-color: rgba(30, 30, 30, 0.8);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 600;
        }

        .category-btn:hover {
            background-color: var(--primary-red);
            transform: scale(1.05);
        }

        .category-btn.active {
            background-color: var(--primary-red);
        }

        .products-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }

        .product-card {
            background-color: rgba(30, 30, 30, 0.8);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--primary-red);
            box-shadow: var(--neon-glow);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 20px rgba(255, 42, 42, 0.5);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-info {
            padding: 15px;
            flex-grow: 1;
        }

        .product-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 10px;
        }

        .product-description {
            font-size: 1rem;
            color: #ddd;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 1.3rem;
            color: var(--primary-red);
            margin-bottom: 10px;
        }

        .product-category {
            font-size: 0.9rem;
            background-color: rgba(20, 20, 20, 0.8);
            padding: 5px 10px;
            border-radius: 15px;
            display: inline-block;
            margin-bottom: 10px;
            color: white;
        }

        .quantity-selector {
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quantity-selector label {
            color: #ddd;
            font-weight: 500;
        }

        .quantity-selector input {
            width: 60px;
            padding: 5px;
            border: 1px solid var(--primary-red);
            border-radius: 4px;
            background-color: rgba(30, 30, 30, 0.8);
            color: white;
            text-align: center;
        }

        .add-to-cart {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary-red);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .add-to-cart:hover {
            background-color: var(--dark-red);
            transform: scale(1.05);
        }

        .logui {
            position: fixed;
            left: 15px;
            bottom: 15px;
            z-index: 10;
        }

        .logui img {
            height: 40px;
        }

        .download-btn {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: var(--primary-red);
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .download-btn:hover {
            background-color: var(--dark-red);
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .search-bar input {
                width: 80%;
            }

            .products-container {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }

            .product-image {
                height: 150px;
            }
        }
    </style>
</head>
<body>
 <?php require 'navbar.php' ?>

    <div class="container py-5">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Rechercher un produit...">
            <button><i class="fas fa-search"></i></button>
        </div>

        <div class="category-filter">
            <button class="category-btn active">All</button>
            <button class="category-btn">Keyboards</button>
            <button class="category-btn">Mice</button>
            <button class="category-btn">Headsets</button>
            <button class="category-btn">Chairs</button>
            <button class="category-btn">Accessories</button>
        </div>

        <div id="results"></div>

        <div class="products-container" id="all-products">
            <?php
            if ($result && count($result) > 0) {
                foreach ($result as $row) {
                    $disponibilite = $row['disponibilite'];
            ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['nom']); ?>" class="product-image">
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($row['nom']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($row['description']); ?></p>
                            <div class="product-price"><?php echo htmlspecialchars($row['prix']); ?> €</div>
                            <div class="product-category"><?php echo htmlspecialchars($row['categorie']); ?></div>
                            <div class="quantity-selector">
                                <label for="qty-<?php echo $row['id']; ?>">Disponibilité :</label>
                                <?php if ($disponibilite): ?>
                                    <i class="fas fa-check-circle" style="color: #00ff00;" title="Disponible"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle" style="color: var(--primary-red);" title="Indisponible"></i>
                                <?php endif; ?>
                            </div>
                            <div class="quantity-selector">
                                <label for="qty-<?php echo $row['id']; ?>">Quantité :</label>
                                <input type="number" id="qty-<?php echo $row['id']; ?>" name="qty-<?php echo $row['id']; ?>" min="1" max="10" value="1">
                                <a class="add-to-cart" href="commande_front.php?id=<?php echo $row['id']; ?>&image=<?php echo urlencode($row['image']); ?>&nom=<?php echo urlencode($row['nom']); ?>&prix=<?php echo urlencode($row['prix']); ?>">Add to Cart</a>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p class='text-center' style='color: #ddd;'>Aucun produit trouvé.</p>";
            }
            ?>
        </div>

        <a href="../../controller/generate_pdf.php" target="_blank" class="download-btn">
            <i class="fas fa-file-csv me-1"></i> Télécharger CSV
        </a>
    </div>

    <nav class="logui">
        <img src="logo.png" alt="GAMY">
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#searchInput').on('input', function() {
                let query = $(this).val();
                if (query.length > 0) {
                    $.ajax({
                        url: 'gamy/controller/researchproduit.php',
                        type: 'POST',
                        data: { search: query },
                        success: function(response) {
                            $('#results').html(response);
                            $('#all-products').hide();
                        }
                    });
                } else {
                    $('#results').html('');
                    $('#all-products').show();
                }
            });

            // Category filter
            $('.category-btn').click(function() {
                $('.category-btn').removeClass('active');
                $(this).addClass('active');
                // Add category filtering logic here if needed
            });
        });

        // Cursor glow effect
        document.addEventListener('mousemove', (e) => {
            const cursorGlow = document.getElementById('cursor-glow');
            cursorGlow.style.left = `${e.clientX}px`;
            cursorGlow.style.top = `${e.clientY}px`;
        });
    </script>
</body>
</html>