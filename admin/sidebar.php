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
            width: 5%;
            height: 50%;
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
  <div class="admin-photo">
    <img src="../uploads/admin-user-icon-4.jpg" alt="Admin Photo">
    <div class="admin-name">Admin</div>
  </div>
  <a href="dashboard.php" onclick="setActive(event)">Dashboard <i class="fa fa-tachometer" aria-hidden="true"></i></a>
  <a href="bhouse.php" onclick="setActive(event)">Boarding House List <i class="fa fa-home" aria-hidden="true"></i></a>
<a href="owner.php" onclick="setActive(event)">Owner List <i class="fa fa-users" aria-hidden="true"></i></a>
  <!-- <a href="pending.php" onclick="setActive(event)">Pending List <i class="fa fa-list-ul" aria-hidden="true"></i></a>
  <a href="report.php" onclick="setActive(event)">Reports <i class="fas fa-file-alt" aria-hidden="true"></i></a>!-->
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
