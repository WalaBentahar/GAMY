<!DOCTYPE html>
<html>
<head>
<form action="/gaming_website/support/submit" method="POST">
    <title>GAME ZONE | Support</title>
    <link rel="stylesheet" href="/gaming_website/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav>
        <div class="logo">GAME ZONE</div>
        <div class="nav-links">
            <a href="/gaming_website/view/home/index"><i class="fas fa-home"></i></a>
            <a href="/gaming_website/view/support/form" class="active"><i class="fas fa-headset"></i></a>
        </div>
    </nav>

    <section class="support-form">
        <h1><i class="fas fa-headset"></i> SUBMIT A COMPLAINT</h1>
        <form action="/gaming_website/view/support/submit" method="POST">
            <div class="form-group">
                <label for="userID"><i class="fas fa-user"></i> User ID:</label>
                <input type="text" id="userID" name="userID" required placeholder="Your Game ID">
            </div>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" id="email" name="email" required placeholder="Your email">
            </div>

            <div class="form-group">
                <label for="subject"><i class="fas fa-tag"></i> Subject:</label>
                <input type="text" id="subject" name="subject" required placeholder="Issue summary">
            </div>

            <div class="form-group">
                <label for="message"><i class="fas fa-comment"></i> Message:</label>
                <textarea id="message" name="message" rows="5" required placeholder="Describe the problem..."></textarea>
            </div>

            <div class="form-group">
                <label for="date"><i class="fas fa-calendar"></i> Date:</label>
                <input type="date" id="date" name="date" required>
            </div>

            <button type="submit">Submit</button>
        </form>
    </section>
    
</body>
</html>
