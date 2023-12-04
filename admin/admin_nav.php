<div id="navbar">
    <!-- Add your logo here -->
    <img src="strathmore_logo.png" width="35px" height="35px" alt="Logo">
    <div>
        <!-- Display logged user name -->
        <span>Hello, <?php echo ucwords($admin_data['user_name']); ?></span>
        <!-- Logout button -->
        <a href="../logout.php">Logout</a>
    </div>
</div>