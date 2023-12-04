<div id="navbar">
    <!-- Add your logo here -->
    <img src="../admin/strathmore_logo.png" width="35px" height="35px" alt="Logo">
    <div>
        <!-- Display logged user name -->
        <span>Hello, <?php echo ucwords($lecturer_data['first_name']); ?></span>
        <!-- Logout button -->
        <a href="../logout.php">Logout</a>
    </div>
</div>