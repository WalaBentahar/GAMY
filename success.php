<!DOCTYPE html>
<html>
<head>
    <title>Complaint Received</title>
    <link rel="stylesheet" href="/gaming_website/public/css/style.css">
    <style>
        /* Force full page content */
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .confirmation {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">GAME ZONE</div>
    </nav>

    <section class="confirmation">
        <h1><i class="fas fa-check-circle"></i> COMPLAINT RECEIVED!</h1>
        <p>We've registered your complaint and will contact you soon.</p>
        <a href="/gaming_website/home" class="btn">
            <i class="fas fa-arrow-left"></i> Return to Home
        </a>
    </section>
</body>
</html>