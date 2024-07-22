<style>
  .sidebar {
    width: 230px;
    background-color: #333; /* Adjust background color as needed */
  }

  .sidebar a {
    font-family: 'Roboto', serif;
    display: flex;
    align-items: center;
    padding: 13px;
    text-decoration: none;
    font-size: 18px;
    color: white; /* Adjust text color as needed */
  }

  .sidebar a i {
    margin-left: auto; /* Align the icon to the right */
  }

  .sidebar a:hover {
    background-color: red; /* Add hover effect if needed */
    color: #000; /* Adjust hover text color if needed */
  }

  .sidebar a:active, .sidebar a:focus {
    background-color: red; /* Change background color when clicked or focused */
    color: #fff; /* Change text color when clicked or focused */
    outline: none; /* Remove default outline for better appearance */
  }

  .sidebar a.active {
    background-color: red; /* Set different background color for the active link */
    color: #fff; /* Set different text color for the active link */
  }

  .sidebar .admin-photo {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px 0;
    margin-bottom: 20px;
  }

  .sidebar .admin-photo img {
    width: 130px; /* Adjust size as needed */
    height: 120px; /* Adjust size as needed */
    border-radius: 50%;
    margin-bottom: 10px; /* Adjust margin as needed */
  }

  .sidebar .admin-photo .admin-name {
    color: white; /* Adjust text color as needed */
    font-size: 18px; /* Adjust font size as needed */
  }
</style>

<div class="sidebar">
  <div class="admin-photo">
    <img src="../uploads/admin-user-icon-4.jpg" alt="Admin Photo">
    <div class="admin-name">Admin</div>
  </div>
  <a href="dashboard.php" onclick="setActive(event)">Dashboard <i class="fa fa-tachometer" aria-hidden="true"></i></a>
  <a href="bhouse.php" onclick="setActive(event)">Boarding House List <i class="fa fa-home" aria-hidden="true"></i></a>
  <a href="owner.php" onclick="setActive(event)">Owner List <i class="fa fa-list-ul" aria-hidden="true"></i></a>
  <a href="pending.php" onclick="setActive(event)">Pending List <i class="fa fa-list-ul" aria-hidden="true"></i></a>
 <a href="report.php" onclick="setActive(event)">Reports <i class="fas fa-file-alt" aria-hidden="true"></i></a>
  <a href="logout.php" onclick="setActive(event)">Logout <i class="fa fa-power-off" aria-hidden="true"></i></a>
</div>

<script>
  // Set active class based on the current page
  document.addEventListener("DOMContentLoaded", function() {
    var activeLink = localStorage.getItem("activeLink");
    if (activeLink) {
      var link = document.querySelector('.sidebar a[href="' + activeLink + '"]');
      if (link) {
        link.classList.add("active");
      }
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
