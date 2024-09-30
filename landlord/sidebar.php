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
        background-color:#80ffff; /* Adjust background color as needed */
        padding-top: 20px; /* Adjust padding top as needed */
        text-align: center; /* Center align the contents */
    }

    .sidebar a {
        font-family: serif;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 13px;
        text-decoration: none;
        font-size: 18px;
        color: black; /* Adjust text color as needed */
    }

    .sidebar a i {
        margin-left: 10px; /* Adjust the margin as needed */
    }

    .sidebar a:hover {
        background-color: white; /* Add hover effect if needed */
        color: #000; /* Adjust hover text color if needed */
    }

    .sidebar a:active, .sidebar a:focus {
        background-color: white; /* Change background color when clicked or focused */
        color: #fff; /* Change text color when clicked or focused */
        outline: none; /* Remove default outline for better appearance */
    }

    .sidebar a.active {
        background-color: white; /* Set different background color for the active link */
        color: black; /* Set different text color for the active link */
    }

    .profile-photo-container {
        width: 130px; /* Adjust width as needed */
        height: 130px; /* Adjust height as needed */
        margin: 0 auto; /* Center the photo container */
        border-radius: 50%; /* Rounded shape for the photo container */
        overflow: hidden; /* Ensure the image fits within the rounded container */
    }

    .profile-photo {
        width: 100%; /* Make sure the image covers the container */
        height: 100%; /* Make sure the image covers the container */
        object-fit: cover; /* Maintain aspect ratio and cover the container */
        cursor: pointer; /* Show pointer cursor to indicate it's clickable */
    }

    .profile-name {
        margin-top: 10px; /* Adjust margin top for the name */
        font-size: 18px; /* Adjust font size as needed */
        color: black; /* Adjust text color as needed */
    }

    .sidebar-content {
        margin-top: 20px; /* Adjust margin top for the content */
    }
</style>
<div class="sidebar">
    <!-- Profile Photo -->
    <a href="edit_owner.php?owner_id=<?php echo htmlspecialchars($profile_data['id']); ?>" class="profile-photo-container">
        <img src="../uploads/<?php echo htmlspecialchars($profile_data['profile_photo']); ?>" alt="Profile Photo" class="profile-photo">
    </a>
    <!-- Profile Name -->
    <div class="profile-name"><?php echo htmlspecialchars($profile_data['firstname']); ?></div>

    <!-- Content Links -->
    <div class="sidebar-content">
        <a href="dashboard.php" onclick="setActive(event)">
            Dashboard <i class="fa fa-tachometer" aria-hidden="true"></i>
        </a>
        <a href="create.php" onclick="setActive(event)"> <i class="fa fa-plus-circle" aria-hidden="true"></i></a>
        <a href="bhouse.php" onclick="setActive(event)">BHouse List <i class="fa fa-home" aria-hidden="true"></i></a>
        <a href="booker.php" onclick="setActive(event)">Boarder List <i class="fa fa-list-ul" aria-hidden="true"></i></a>
        <a href="report.php" onclick="setActive(event)">Reports <i class="fa fa-file-text" aria-hidden="true"></i></a>
        <a href="logout.php" onclick="setActive(event)">Logout <i class="fa fa-power-off" aria-hidden="true"></i></a>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var activeLink = localStorage.getItem("activeLink") || "dashboard.php";
        var link = document.querySelector('.sidebar a[href="' + activeLink + '"]');
        if (link) {
            link.classList.add("active");
        } else {
            // Navigate to the dashboard if no active link is stored
            window.location.href = "dashboard.php";
        }
    });

    function setActive(event) {
        // Prevent default link behavior
        event.preventDefault();

        // Get the clicked link element
        var element = event.currentTarget;

        // Remove 'active' class from all sidebar links
        var links = document.querySelectorAll('.sidebar a');
        links.forEach(function(link) {
            link.classList.remove('active');
        });

        // Add 'active' class to the clicked link
        element.classList.add('active');

        // Store the active link in localStorage
        localStorage.setItem("activeLink", element.getAttribute("href"));

        // Navigate to the clicked link
        window.location.href = element.getAttribute("href");
    }
</script>
