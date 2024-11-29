<style>
    .sidebar {
        width: 230px;
        background: linear-gradient(to bottom, #80ffff, #00bfff); /* Subtle gradient */
        padding-top: 20px;
        text-align: center;
        position: fixed;
        height: 100%;
        overflow-y: auto;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        transform: translateX(0);
        z-index: 1000;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1); /* Adds shadow for depth */
        border-top-right-radius: 10px; /* Rounded corners */
        border-bottom-right-radius: 10px;
    }

    /* Sidebar for mobile view */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .page-content.active {
            transform: translateX(230px);
        }
    }

    .sidebar a {
        font-family: 'Arial', sans-serif;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        text-decoration: none;
        font-size: 16px;
        color: black;
        border-radius: 5px; /* Rounded for links */
        margin: 5px 10px; /* Spacing between links */
        transition: background 0.3s ease, color 0.3s ease; /* Smooth hover effect */
    }

    .sidebar a:hover {
        background-color: #ffffff;
        color: #0077cc;
    }

    .sidebar a.active {
        background-color: #0077cc; /* Active link background */
        color: white; /* Active link text */
        font-weight: bold; /* Highlight active link */
    }

    .profile-photo-container {
        width: 130px;
        height: 130px;
        margin: 0 auto 15px auto;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow to photo */
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
        font-weight: bold; /* Enhance text weight */
        text-transform: uppercase; /* Make name uppercase for emphasis */
    }

    .menu-btn {
        font-size: 30px;
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 1001;
        background: none;
        border: none;
        cursor: pointer;
        display: none;
        color: #0077cc;
        transition: color 0.3s ease; /* Smooth hover */
    }

    .menu-btn:hover {
        color: #004080;
    }

    @media (max-width: 768px) {
        .menu-btn {
            display: block;
        }
    }

    img {
        width: 50%;
    }
</style>

<!-- Menu button (hamburger icon) -->
<button class="menu-btn" onclick="toggleSidebar()">
    &#9776; <!-- Unicode for hamburger menu -->
</button>

<div class="sidebar" id="sidebar">
    <div class="admin-photo">
        <div class="profile-photo-container">
            <img src="../uploads/admin-user-icon-4.jpg" alt="Admin Photo" class="profile-photo">
        </div>
        <div class="profile-name">Admin</div>
    </div>
    <a href="dashboard.php" onclick="setActive(event)">Dashboard <i class="fa fa-tachometer" aria-hidden="true"></i></a>
    <a href="bhouse.php" onclick="setActive(event)">Boarding House List <i class="fa fa-home" aria-hidden="true"></i></a>
    <a href="owner.php" onclick="setActive(event)">Owner List <i class="fa fa-users" aria-hidden="true"></i></a>
    <a href="pending.php" onclick="setActive(event)">Pending List <i class="fa fa-clock" aria-hidden="true"></i></a>
    <a href="report.php" onclick="setActive(event)">Reports <i class="fas fa-file-alt" aria-hidden="true"></i></a>
    <a href="logout.php" onclick="setActive(event)">Logout <i class="fa fa-power-off" aria-hidden="true"></i></a>
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
