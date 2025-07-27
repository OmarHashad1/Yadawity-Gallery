<?php
require_once "db.php"; // Ensure this file contains your database dbection logic
if ($_POST) {
    // Database dbection

    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $user_type = $_POST['user_type'];

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $sql = "INSERT INTO users (email, password, first_name, last_name, user_type) 
            VALUES ('$email', '$hashed_password', '$first_name', '$last_name', '$user_type')";

    if ($db->query($sql) === TRUE) {
        echo "<h2>User registered successfully!</h2>";
    } 

    $db->close();
} else {
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Sign Up</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <input type="text" name="first_name" placeholder="First Name" required><br><br>
        <input type="text" name="last_name" placeholder="Last Name" required><br><br>
        <select name="user_type" required>
            <option value="">Select Type</option>
            <option value="artist">Artist</option>
            <option value="buyer">Buyer</option>
        </select><br><br>
        <input type="submit" value="Sign Up">
    </form>
</body>
</html>
<?php
}
?>