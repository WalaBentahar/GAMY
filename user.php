<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAME ZONE | Front Office</title>
    <link rel="stylesheet" href="front-style.css">
    <!-- Font Awesome Icons (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="front-header">
        <div class="header-container">
            <div class="logo-container">
                <img src="assets/images/logo.webp" alt="Game Zone Logo" class="main-logo">
                <span class="logo-text">GAME ZONE</span>
            </div>
            
            <nav class="front-nav">
                <ul class="nav-menu">
                    <li><a href="index.html" class="active"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="products.html"><i class="fas fa-gamepad"></i> Produits</a></li>
                    <li><a href="categories.html"><i class="fas fa-list"></i> Catégories</a></li>
                    <li><a href="cart.html"><i class="fas fa-shopping-cart"></i> Panier</a></li>
                    <li><a href="account.html"><i class="fas fa-user"></i> Compte</a></li>
                    <li><a href="support.html"><i class="fas fa-headset"></i> Support</a></li>
                </ul>
            </nav>
            
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <main class="front-main">
        <section class="hero-banner">
            <div class="hero-content">
                <h1>DÉCOUVREZ LES JEUX LES PLUS TENDANCES</h1>
                <p class="hero-subtitle">Des offres exclusives sur les derniers hits du gaming</p>
                <a href="products.html" class="cta-button">VOIR LA COLLECTION</a>
            </div>
        </section>

        <section class="featured-products">
            <h2 class="section-title">NOS PRODUITS PHARES</h2>
            <div class="products-grid">
                <!-- Produit 1 -->
                <div class="product-card">
                    <div class="product-badge">Nouveau</div>
                    <img src="assets/images/game1.jpg" alt="Jeu à la une" class="product-image">
                    <div class="product-info">
                        <h3>Cyberpunk 2077</h3>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="product-price">49,99€ <span class="old-price">69,99€</span></p>
                        <button class="add-to-cart">Ajouter au panier</button>
                    </div>
                </div>
                
                <!-- Produit 2 -->
                <div class="product-card">
                    <div class="product-badge">Promo</div>
                    <img src="assets/images/game2.jpg" alt="Jeu à la une" class="product-image">
                    <div class="product-info">
                        <h3>Elden Ring</h3>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="product-price">39,99€ <span class="old-price">59,99€</span></p>
                        <button class="add-to-cart">Ajouter au panier</button>
                    </div>
                </div>
                
                <!-- Produit 3 -->
                <div class="product-card">
                    <img src="assets/images/game3.jpg" alt="Jeu à la une" class="product-image">
                    <div class="product-info">
                        <h3>God of War: Ragnarök</h3>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <p class="product-price">54,99€</p>
                        <button class="add-to-cart">Ajouter au panier</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="newsletter-section">
            <div class="newsletter-container">
                <h2>ABONNEZ-VOUS À NOTRE NEWSLETTER</h2>
                <p>Recevez les dernières nouveautés et offres exclusives</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Votre email" required>
                    <button type="submit">S'abonner</button>
                </form>
            </div>
        </section>
    </main>

    <footer class="front-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>A PROPOS</h3>
                <ul>
                    <li><a href="#">Qui sommes-nous</a></li>
                    <li><a href="#">Nos magasins</a></li>
                    <li><a href="#">Carrières</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>AIDE</h3>
                <ul>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Livraison</a></li>
                    <li><a href="#">Retours</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>CONTACT</h3>
                <ul>
                    <li><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                    <li><i class="fas fa-envelope"></i> contact@gamezone.com</li>
                    <li><i class="fas fa-map-marker-alt"></i> 123 Rue du Jeu, Paris</li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>SUIVEZ-NOUS</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-discord"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2023 GAME ZONE. Tous droits réservés.</p>
            <div class="payment-methods">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-paypal"></i>
                <i class="fab fa-cc-apple-pay"></i>
            </div>
        </div>
    </footer>

    <div class="cursor-glow"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Effet curseur glow
            const glow = document.querySelector('.cursor-glow');
            
            document.addEventListener('mousemove', (e) => {
                glow.style.left = `${e.clientX}px`;
                glow.style.top = `${e.clientY}px`;
            });
            
            // Menu mobile
            const menuToggle = document.querySelector('.mobile-menu-toggle');
            const navMenu = document.querySelector('.nav-menu');
            
            menuToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });
            
            // Animation des cartes produits
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-10px)';
                    card.style.boxShadow = '0 15px 30px rgba(255, 0, 0, 0.3)';
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0)';
                    card.style.boxShadow = '0 5px 15px rgba(255, 0, 0, 0.2)';
                });
            });
        });
    </script>
</body>
</html>