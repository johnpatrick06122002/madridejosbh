    <?php
    // Function to fetch profile photo and name based on user ID
    function fetchProfileData($dbconnection, $login_session) {
        $query = "SELECT id, profile_photo, firstname FROM register2 WHERE register1_id = ?";
        $stmt = $dbconnection->prepare($query);
        $stmt->bind_param("i", $login_session);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            // Default profile photo and name or handle error as needed
            return ['id' => 0, 'profile_photo' => 'default_profile_photo.jpg', 'firstname' => 'Default Name'];
        }
    }

    // Fetch profile data for the logged-in user
    $profile_data = fetchProfileData($dbconnection, $login_session);

    // Check if the user has just logged in
    if (!isset($_SESSION['has_logged_in'])) {
        $_SESSION['has_logged_in'] = true;
        echo "<script>localStorage.setItem('activeLink', 'dashboard.php');</script>";
    }
    ?>
    <style>
        .sidebar {
            width: 230px;
            background-color: #80ffff;
            padding-top: 20px;
            text-align: center;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            transition: transform 0.3s ease;
            transform: translateX(0); /* Default position for desktop */
            z-index: 1000; /* Ensure it's on top */
        }

        /* Sidebar for mobile view */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%); /* Hide sidebar by default */
                position: fixed;
            }

            .sidebar.active {
                transform: translateX(0); /* Show sidebar when active */
            }

            /* Ensure the page content shifts when sidebar is active */
            .page-content.active {
                transform: translateX(230px); /* Adjust the content */
            }
        }

        .sidebar a {
            font-family: serif;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 13px;
            text-decoration: none;
            font-size: 16px;
            color: black;
            
        }

        .sidebar a:hover {
            background-color: white;
            color: #000;
        }

        .profile-photo-container {
            width: 130px;
            height: 130px;
            margin: 0 auto;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
        }

        .profile-name {
            margin-top: 10px;
            font-size: 18px;
            color: black;
        }

        /* Hamburger icon styling */
        .menu-btn {
            font-size: 30px;
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1001; /* Ensure it's above the sidebar */
            background: none;
            border: none;
            cursor: pointer;
            display: none; /* Hidden by default for desktop */
        }

        @media (max-width: 768px) {
            .menu-btn {
                display: block; /* Show menu button in mobile view */
            }
        }
        
    </style>

    <!-- Menu button (hamburger icon) -->
    <button class="menu-btn" onclick="toggleSidebar()">
        &#9776; <!-- Unicode for hamburger menu -->
    </button>
    
    <div class="sidebar" id="sidebar">
        <!-- Profile Photo -->
        <br><br><a href="edit_owner.php?owner_id=<?php echo htmlspecialchars($profile_data['id']); ?>" class="profile-photo-container">
            <img src="../uploads/<?php echo htmlspecialchars($profile_data['profile_photo']); ?>" alt="Profile Photo" class="profile-photo">
        </a>
        <!-- Profile Name -->
        <div class="profile-name"><?php echo htmlspecialchars($profile_data['firstname']); ?></div>

        <!-- Sidebar Links -->
        <div class="sidebar-content">
            <a href="dashboard.php" onclick="setActive(event)">Dashboard <i class="fa fa-tachometer" aria-hidden="true"></i></a>
            <a href="bhouse.php" onclick="setActive(event)">BHouse List <i class="fa fa-home" aria-hidden="true"></i></a>
            <a href="booker.php" onclick="setActive(event)">Boarder List <i class="fa fa-list-ul" aria-hidden="true"></i></a>
            <a href="pending.php" onclick="setActive(event)">Pending  <i class="fa fa-home" aria-hidden="true"></i></a>
            <a href="report.php" onclick="setActive(event)">Reports <i class="fa fa-file-text" aria-hidden="true"></i></a>
            <a href="logout.php" onclick="setActive(event)">Logout <i class="fa fa-power-off" aria-hidden="true"></i></a>
        </div>
    </div>

    <!-- Main page content -->  
    <div class="page-content" id="pageContent">
        <!-- Your page content goes here -->
    </div>

    <script>
        // Function to show or hide the sidebar for mobile view
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var pageContent = document.getElementById("pageContent");

            sidebar.classList.toggle("active");
            pageContent.classList.toggle("active");

            console.log('Sidebar toggled'); // Debugging to ensure the function is triggered
        }

        // Close the sidebar when clicking outside of it
        window.onclick = function(event) {
            var sidebar = document.getElementById("sidebar");
            var menuBtn = document.querySelector(".menu-btn");

            if (event.target !== sidebar && !sidebar.contains(event.target) && event.target !== menuBtn) {
                sidebar.classList.remove("active");
                document.getElementById("pageContent").classList.remove("active");
            }
        };

        // Highlight the active link
        document.addEventListener("DOMContentLoaded", function() {
            var activeLink = localStorage.getItem("activeLink") || "dashboard.php";
            var link = document.querySelector('.sidebar a[href="' + activeLink + '"]');
            if (link) {
                link.classList.add("active");
            } else {
                window.location.href = "dashboard.php";
            }
        });

        function setActive(event) {
            event.preventDefault();
            var element = event.currentTarget;
            var links = document.querySelectorAll('.sidebar a');
            links.forEach(function(link) {
                link.classList.remove('active');
            });
            element.classList.add('active');
            localStorage.setItem("activeLink", element.getAttribute("href"));
            window.location.href = element.getAttribute("href");
        }
    </script>
