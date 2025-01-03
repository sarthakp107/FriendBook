<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/topnav.css">
    <link rel="stylesheet" href="style/errors.css">
    <link rel="stylesheet" href="style/form.css">
    <link rel="stylesheet" href="style/info.css">
    <title>My Friends System</title>
</head>

<body>
    <nav>
        <a id="logo" class="nav-link" href="index.php">My Friends System</a>
        <ul>
            <li><a class="nav-link" href="signup.php">Sign Up</a></li>
            <li><a class="nav-link" href="login.php">Log In</a></li>
            <li><a class="nav-link" href="about.php">About</a></li>
        </ul>
    </nav>
    <?php
    header("Location: About.php");
    ?>
    
    <h1> Home Page</h1>




    <?php


    require_once("settings.php");

    //create table "friends" only if it doesnot exists
    $sql1 = "CREATE TABLE IF NOT EXISTS $table1(
             friend_id INT NOT NULL AUTO_INCREMENT,
             friend_email VARCHAR(50) NOT NULL,
             password VARCHAR(20) NOT NULL,
             profile_name VARCHAR(30) NOT NULL,
             date_started DATE NOT NULL,
             num_of_friends INT UNSIGNED,
             PRIMARY KEY (friend_id)
             )";
    echo "<div class='echo-message'>";
    //executing the first query ($sql1) and check for errors
    $result1 = mysqli_query($conn, $sql1);
    if ($result1) {
        echo "<p>Table $table1 created successfully</p>";
    } else {
        echo "<p>Error creating table 1</p>";
    }

    //creating myfriends table
    $sql2 = "CREATE TABLE IF NOT EXISTS $table2(
            friend_id1 INT NOT NULL,
            friend_id2 INT NOT NULL
            )";

    $result2 = mysqli_query($conn, $sql2);
    if ($result2) {
        echo "<p>Table $table2 created successfully</p>";
    } else {
        echo "<p>Error creating table 2</p>";
    }

    //checking if the table 1 (friends) is empty
    $sql3 = "SELECT * FROM $table1";
    //execute the query
    $result = mysqli_query($conn, $sql3);
    if (mysqli_num_rows($result) > 0) {
        echo "Table 1 is not empty";
    } else {
        //sample data for friends
        $sql4 = "INSERT INTO $table1 (friend_email, password, profile_name, date_started, num_of_friends)
                VALUES
                ('sarthak@example.com', 'password1', 'Sarthak Pradhan', '2023-01-01', 4),
                ('samip@example.com', 'password2', 'Samip Pudasaini', '2023-02-10', 4),
                ('ashim@example.com', 'password3', 'Ashim Adhikari', '2023-03-16', 4),
                ('prabesh@example.com', 'password4', 'Prabesh Bhat', '2023-04-21', 4),
                ('ram@example.com', 'password5', 'Ram Lama', '2023-05-09', 4),
                ('sam@example.com', 'password6', 'Sam Lama', '2023-06-01', 4),
                ('hari@example.com', 'password7', 'Hari Lama', '2023-07-07', 4),
                ('dipty@example.com', 'password8', 'Dipty Pudasaini', '2023-08-12', 4),
                ('bisakha@example.com', 'password9', 'Bisakha Shrestha', '2023-09-25', 4),
                ('sita@example.com', 'password10', 'Sita Lama', '2023-10-31', 4),
                ('alex@example.com', 'password11', 'Alex Tamang', '2023-10-31', 0),
                ('zion@example.com', 'password12', 'Zion Pradhan', '2023-10-31', 0),
                ('lion@example.com', 'password13', 'Lion Lama', '2023-10-31', 0),
                ('abdul@example.com', 'password14', 'Abdul Sharma', '2023-10-31', 0),
                ('sahil@example.com', 'password15', 'Sahil Khadka', '2023-10-31', 0),
                ('prateek@example.com', 'password16', 'Prateek Bhandari', '2023-10-31', 0)";

        //execute the query and check for errors
        $result4 = mysqli_query($conn, $sql4);
    }

    //checking if the table 2 (myfriends) is empty
    $sql5 = "SELECT * FROM $table2";
    //execute the query
    $result = mysqli_query($conn, $sql5);
    if (mysqli_num_rows($result) > 0) {
        echo "<p>Table 2 is not empty</p>";
    } else {
        //sample data for myfriends
        $sql6 = "INSERT INTO $table2(friend_id1 , friend_id2)
                VALUES
                (1,2),
                (2,3),
                (3,4),
                (4,5),
                (5,6),
                (6,7),
                (7,8),
                (8,9),
                (9,10),
                (10,1),
                (1,3),
                (2,4),
                (3,5),
                (4,6),
                (5,7),
                (6,8),
                (7,9),
                (8,10),
                (9,1),
                (10,2)
                ";

        if (mysqli_query($conn, $sql6)) {
            echo "<p>Sample data populated in table $table2</p>";
        } else {
            echo "Error populating the data in table 2";
        }
    }
    echo "</div>";

    mysqli_close($conn);
    ?>
</body>

</html>