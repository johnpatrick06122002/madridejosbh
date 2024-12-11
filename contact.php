<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Madridejos Boarding House Finder</title>
      <link rel="shortcut icon" type="x-icon" href="b.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .contact-header {
            text-align: center;
            padding: 50px 20px;
            background-color: #343a40;
            color: #fff;
        }

        .contact-header h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .contact-header p {
            font-size: 1.2rem;
            line-height: 1.8;
        }

        .contact-section {
            padding: 30px 20px;
            background-color: #f9f9f9;
        }

        .contact-info {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .contact-card {
            flex: 1 1 calc(30% - 20px);
            min-width: 280px;
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .contact-card i {
            font-size: 2.5rem;
            color: #343a40;
            margin-bottom: 15px;
        }

        .contact-card h5 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .contact-card p {
            font-size: 1rem;
            margin-bottom: 0;
        }

        .contact-card a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-card a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include('header.php'); ?>

    <!-- Contact Header -->
    <div class="contact-header"><br><br>
        <h1>Contact Us</h1>
        <p>
            Have questions or need assistance? Reach out to us and let us help you find your perfect boarding house.
        </p>
    </div>

    <!-- Contact Section -->
    <div class="contact-section">
    <div class="container">
        <div class="contact-info">
            <!-- Contact Card 1: Email -->
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h5>Email Us</h5>
                <p><a href="mailto:madridejosbh2@gmail.com">madridejosbh2@gmail.com</a></p>
            </div>
            <!-- Contact Card 2: Phone -->
            <div class="contact-card">
                <i class="fas fa-phone-alt"></i>
                <h5>Call Us</h5>
                <p><a href="tel:+639932685248">+63 993 268 5248</a></p>
            </div>
            <!-- Contact Card 3: Location -->
            <div class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <h5>Visit Us</h5>
                <p>123 Barangay St., Madridejos, Cebu, Philippines</p>
            </div>
        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php include('footer.php'); ?>
