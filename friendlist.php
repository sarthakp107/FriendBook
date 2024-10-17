<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/topnav.css">
    <link rel="stylesheet" href="style/form.css">

    <title>Friend List Page</title>
</head>


<body>
    <nav>
        <a id="logo" class="nav-link" href="index.php">My Friends System</a>
        <ul>
            <li><a class="nav-link" href="login.php">Log Out</a></li>
            <li><a class="nav-link" href="about.php">About</a></li>
        </ul>
    </nav>

    <?php
    session_start();

    if (!isset($_SESSION['email']) || !isset($_SESSION['loggedIn'])) {
        // Redirect to the login page
        header("Location: login.php");
        exit();
    }

    require_once("settings.php");

    //query the result for profile name and number of friends
    $sql = "SELECT friend_id, profile_name, num_of_friends FROM $table1 WHERE friend_email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION["email"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Get the profile name and number of friends
    $userId = $row["friend_id"];
    $profileName = $row["profile_name"];
    $numOfFriends = $row["num_of_friends"];
//friend id (table1) should match friend id 1 or 2 of myfriends table
    $sql = "SELECT f.friend_id, f.profile_name
          FROM $table1 f JOIN $table2 mf 
          ON f.friend_id = mf.friend_id1 OR f.friend_id = mf.friend_id2 
          WHERE (mf.friend_id1 = ? OR mf.friend_id2 = ?) 
          AND f.friend_id != ? 
          ORDER BY f.profile_name ASC"; // Sorting by profile name in ascending order
    $stmt = mysqli_prepare($conn, $sql);


    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $userId, $userId, $userId); //binding for the three placeholder
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    function removeFriend($friendId)
    {
        global $conn, $numOfFriends, $userId, $table1, $table2;

        //query to delete friends from the myFriends table

        $sql = "DELETE FROM $table2 WHERE (friend_id1 = ? AND friend_id2 = ?) OR (friend_id1 = ? AND friend_id2 = ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiii", $userId, $friendId, $friendId, $userId);
        mysqli_stmt_execute($stmt);

        //decrease the number of friends count after unfriend
        $numOfFriends--;

        //update the table after decrementing
        $sql = "UPDATE $table1 SET num_of_friends = ? WHERE friend_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $numOfFriends, $userId);
        mysqli_stmt_execute($stmt);

        //get number of friends of the friend
        $sql = "SELECT num_of_friends FROM $table1 WHERE friend_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $friendId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        $numOfFriendsofFriend = $row["num_of_friends"];

        //update the number of friends of friend
        $numOfFriendsofFriend--;
        $sql = "UPDATE $table1 SET num_of_friends = ? WHERE friend_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $numOfFriends2, $friendId);
        mysqli_stmt_execute($stmt);
    }

    //execute the unfriend function
    if (isset($_POST["unfriend"])) {
        removeFriend($_POST["friendId"]);

        //redirect to friendlist page

        header("Location: friendlist.php");
        exit();
    }
    ?>

    <h2><?php echo $profileName ?>'s Friend List Page</h2>
    <h2>Total number of friends is <?php echo $numOfFriends ?></h2>

    <?php
    if (mysqli_num_rows($result) > 0) {
        echo "<table class='centered-form'>";
        echo "<thead><tr><th>Profile Name</th><th>Action</th></tr></thead>";
        while ($row = mysqli_fetch_assoc($result)) {
            $friendId = $row["friend_id"];
            $friendProfileName = $row["profile_name"];
            echo "<tbody>";
            echo "<tr>";
            echo "<td>{$friendProfileName}</td>";
            echo "<td>
                  <form method='post' action='friendlist.php' >
                    <input type='hidden' name='friendId' value='{$friendId}'>
                    <input type='submit' name='unfriend' value='Unfriend'>
                    </form>
                    
                </td>";
            echo "</tr>";
            echo "</tbody>";
        }
        echo "</table>";
    } else {
        echo "<p>No friend found.</p>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    ?>
    <div class="button-container">
    <a href='friendadd.php' class="button">Add friends</a>
    <a href='logout.php' class="button">Log Out</a>
</div>


</body>

</html>