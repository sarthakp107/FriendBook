<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/topnav.css">
    <link rel="stylesheet" href="style/form.css">
    <title>Sign Up Form</title>
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

    <form method="post" action="signup.php" class="centered-form">
        <label for="email">Email</label>
        <input type="text" name="email" id="email">

        <label for="profileName">Profile Name</label>
        <input type="text" name="profileName" id="profileName">

        <label for="password">Password</label>
        <input type="password" name="password" id="password">

        <label for="confirmPassword">Confirm Password</label>
        <input type="password" name="confirmPassword" id="confirmPassword">

        <input type="submit" value="Register" name="register">
        <input type="reset" value="Clear" name="clear">


        <a href="index.php">Home</a>
    </form>

    <?php


    require_once("settings.php");


    if (isset($_POST["register"])) {
        $signUpEmail = $_POST["email"];
        $signUpProfileName = $_POST["profileName"];
        $signUpPassword = $_POST["password"];
        $signUpConfirmPassword = $_POST["confirmPassword"];

        //array to hold error messages
        $errors = [];


        //function to check if the email is valid
        function validateEmail($email)
        {
            $emailRegex = "/^[a-zA-Z0-9.]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/";
            return preg_match($emailRegex, $email) === 1; //return true if the regex matches with the input

        }

        //function to check if the profile name is not empty and only containes letters
        function validateProfileName($profileName)
        {
            $profileNameRegex = "/^[a-zA-Z\s]+$/";
            return !empty($profileName) && (preg_match($profileNameRegex, $profileName) === 1);
        }

        //function to check if the password only contains  letters and numbers
        function validatePassword($password)
        {
            $passwordRegex = "/^[a-zA-Z0-9]+$/";
            return preg_match($passwordRegex, $password) === 1;
        }

        //function to check if passwords match
        function passwordMatch($password, $confirmPassword)
        {
            return $password === $confirmPassword; //returns true if passwords match
        }

        function checkUniqueEmail($email)
        {
            // Check if email exists in the ‘friends’ table
            global $conn, $table1;
            $sql = "SELECT friend_email FROM $table1 WHERE friend_email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $numRows = mysqli_stmt_num_rows($stmt);
            mysqli_stmt_close($stmt);

            if ($numRows > 0) {
                echo "<span>Email already exists</span>";
                return false;
            }
            return true;
        }



        if (!validateEmail($signUpEmail)) {
            $errors[] = "Invalid Email format";
        } else {
            $isMailUnique = checkUniqueEmail($signUpEmail);
            if ($isMailUnique !== true) {
                $errors[] = $isMailUnique; // Add error message if email is not unique
            }
        }

        if (!validateProfileName($signUpProfileName)) {
            $errors[] = "Invalid Profile Name: Profile name must contain only letters and cannot be blank.";
        }

        if (!validatePassword($signUpPassword)) {
            $errors[] = "Invalid Password: Password must contain only letters and numbers";
        }

        if (!passwordMatch($signUpPassword, $signUpConfirmPassword)) {
            $error[] = "Passwords do not match";
        }



        //display the errors
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        } else {
            $sql = "INSERT INTO $table1 (friend_email, password, profile_name, date_started, num_of_friends) VALUES (?, ?, ?, CURDATE(), 0)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $signUpEmail, $signUpPassword, $signUpProfileName);
            if (mysqli_stmt_execute($stmt)) {
                session_start();
                $_SESSION['email'] = $signUpEmail;
                $_SESSION['loggedIn'] = true;
                header("Location: friendadd.php");
                exit();
            }
            else{
                echo "<p>Error creating account</p>";
            }
            mysqli_stmt_close($stmt);
        }
    }

    ?>

</body>

</html>