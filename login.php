<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/topnav.css">
    <link rel="stylesheet" href="style/form.css">
    <link rel="stylesheet" href="style/errors.css">
    <title>Login Page</title>
</head>

<body>
    <nav>
        <a id="logo" class="nav-link">My Friends System</a>
        <ul>
            <li><a class="nav-link" href="signup.php">Sign Up</a></li>
            <li><a class="nav-link" href="login.php">Log In</a></li>
            <li><a class="nav-link" href="about.php">About</a></li>
        </ul>
    </nav>
    <h1>Login</h1>

    <?php
    

    require_once("settings.php");

    //initializing variables
    $email = "";
    $password = "";

    //array of error messages
    $errorMessages = [];

    //function to check the email format
    function validateEmail($email)
    {
        $emailRegex = "/^[a-zA-Z0-9.]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/";
        return preg_match($emailRegex, $email) === 1; //return true if the regex matches with the input

    }
    //check if the form is set

    if (isset($_POST["login"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        //validate
        if (empty($email) && empty($password)) {
            $errorMessages[] = "Both fields are required";
        } elseif (empty($email)) {
            $errorMessages[] = "Email is required";
        } elseif(!validateEmail($email)){
            $errorMessages[] = "Email is invalid";
        }
        elseif (empty($password)) {
            $errorMessages[] = "Password is required.";
        }

        //if no errors proceed with database validation

        if(empty($errorMessages)){
            $sql = "SELECT friend_email, password FROM $table1 WHERE friend_email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            //if the email exists check the password
            if(mysqli_stmt_num_rows($stmt) > 0){
                
                mysqli_stmt_bind_result($stmt, $dbEmail, $dbPassword);
                mysqli_stmt_fetch($stmt);

                if($dbPassword === $password){
                    // Set up session variable(s) and redirect to friendlist.php
                    session_start();
                    $_SESSION['email'] = $email;
                    $_SESSION['loggedIn'] = true;

                    header("Location: friendlist.php");
                    exit();
                }
                else {
                    // Invalid password
                    $errorMessages[] = "Invalid password.";
                }
            } else{
                $errorMessages[] = "Email not found.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    ?>
    <form method="post" action="login.php" class="centered-form">
        <label for="email">Email</label>
        <input type="text" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>">

        <label for="password">Password</label>
        <input type="password" name="password" id="password">

        <input type="submit" value="Log in" name="login">
        <input type="reset" value="Clear" name="clear">

        <a href="index.php">Home</a>
    </form>

    <?php
    // Display error messages
if (!empty($errorMessages)) {
    echo '<div class="error-message">';
    foreach ($errorMessages as $error) {
        echo '<p>' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
}
    ?>

</body>

</html>