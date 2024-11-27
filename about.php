<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Madridejos Boarding House Finder</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .about-header {
            text-align: center;
            padding: 50px 20px;
            background-color: #343a40;
            color: #fff;
        }

        .about-header h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .about-header p {
            font-size: 1.2rem;
            line-height: 1.8;
        }

        .about-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .gallery-item {
            flex: 1 1 calc(25% - 30px); /* Adjust image width to 4 images per row */
            min-width: 250px;
            margin: 0;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.4s ease, opacity 0.4s ease;
            opacity: 0;
            animation: photoFadeIn 1.2s ease-in-out forwards;
        }

        .gallery-item img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.4s ease-in-out;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        @keyframes photoFadeIn {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include('header.php'); ?>

    <!-- About Header -->
    <div class="about-header"><br><br>
        <h1>About Madridejos Boarding House Finder</h1>
        <p>
            Welcome to Madridejos Boarding House Finder, the premier platform for finding comfortable and affordable accommodations 
            in the heart of Madridejos. Our mission is to connect tenants and landlords, simplifying the rental process for everyone.
        </p>
    </div>

    <!-- About Gallery -->
    <div class="about-gallery">
        <!-- Image 1: Locality -->
        <div class="gallery-item" style="animation-delay: 0.2s;">
            <img src="a.jpg" alt="Explore Madridejos">
        </div>
        <!-- Image 2: User Platform -->
        <div class="gallery-item" style="animation-delay: 0.4s;">
            <img src="a.jpg" alt="User-Friendly Platform">
        </div>
        <!-- Image 3: Tenant-Landlord Interaction -->
        <div class="gallery-item" style="animation-delay: 0.6s;">
            <img src="a.jpg" alt="Connecting Tenants and Landlords">
        </div>
        <!-- Image 4: Listings -->
        <div class="gallery-item" style="animation-delay: 0.8s;">
            <img src="a.jpg" alt="Detailed Property Listings">
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
    <?php include('footer.php'); ?>