<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/topnav.css">
  <link rel="stylesheet" href="style/form.css">
  <link rel="stylesheet" href="style/errors.css">
  <link rel="stylesheet" href="style/pagination.css">
  <title>My Friends System</title>
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

  if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
  }

  require_once("settings.php"); //connects to the database returns error if connection failed

  // to display the list of registered users .. profile name
  $query1 = "SELECT friend_id, profile_name, num_of_friends FROM $table1 WHERE friend_email = ?";
  $stmt = mysqli_prepare($conn, $query1);
  mysqli_stmt_bind_param($stmt, "s", $_SESSION["email"]);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($result);

  //get the name, number of friends and friendID
  $userId = $row["friend_id"];
  $profileName = $row["profile_name"];
  $numOfFriends = $row["num_of_friends"];

  //function to add friend
  function addFriend($friendId)
  {
    global $conn, $numOfFriends, $userId, $table1, $table2;

    $sql = "INSERT INTO $table2 (friend_id1, friend_id2) VALUES (? , ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $userId, $friendId);
    mysqli_stmt_execute($stmt);

    //increase the number of friends of the logged in user each time by one
    $numOfFriends++;

    //update the numOfFriends
    $query = "UPDATE $table1 SET num_of_friends = ? WHERE friend_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $numOfFriends, $userId);
    mysqli_stmt_execute($stmt);

    //query for getting the numOfFriends of the friend
    $query = "SELECT num_of_friends FROM $table1 WHERE friend_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $friendId); //providing the value(friendId) to the query
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $numOfFriendsOfFriend = $row["num_of_friends"];

    //increase the number of friends of friend by 1 each time
    $numOfFriendsOfFriend++;
    //update the number of friends
    $query = "UPDATE $table1 SET num_of_friends = ? WHERE friend_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $numOfFriendsOfFriend, $friendId);
    mysqli_stmt_execute($stmt);
  }

  //executing the function addFriend

  if (isset($_POST["submit"])) {
    $friendIdForm = $_POST["friendId"];

    addFriend($friendIdForm);

    //to keep the current page after adding
    header("Location: friendadd.php?page={$_POST['page']}");
    exit();
  }
  //Number of names per page
  $namesPerPage = 10;

  // Query to get a list of all users that are not yet friends
  $sql = "SELECT COUNT(f.friend_id) AS total_names
FROM $table1 f
WHERE f.friend_id != ?
  AND f.friend_id NOT IN (
    SELECT mf.friend_id1
    FROM $table2 mf
    WHERE mf.friend_id2 = ?)
  AND f.friend_id NOT IN (
    SELECT mf.friend_id2
    FROM $table2 mf
    WHERE mf.friend_id1 = ?)";

  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "iii", $userId, $userId, $userId);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($result);

  //settings for Pagination
  $totalNames = $row["total_names"];
  $totalPages = ceil($totalNames / $namesPerPage);
  $currentPage = isset($_GET['page']) ? max(1, min($_GET['page'], $totalPages)) : 1;
  $offset = ($currentPage - 1) * $namesPerPage;

  // Retrieve friends for the current page
  $sql = "SELECT f.friend_id, f.profile_name
        FROM $table1 f
        LEFT JOIN $table2 mf1 ON f.friend_id = mf1.friend_id1 AND mf1.friend_id2 = ?
        LEFT JOIN $table2 mf2 ON f.friend_id = mf2.friend_id2 AND mf2.friend_id1 = ?
        WHERE f.friend_id != ? 
        AND mf1.friend_id1 IS NULL 
        AND mf2.friend_id2 IS NULL
        LIMIT ?, ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "iiiii", $userId, $userId, $userId, $offset, $namesPerPage);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);


  ?>
  <h2>Hello! <?php echo $profileName ?></h2>
<h2>Add Friend Page</h2>
  
  <h2> Total number of friends is <?php echo $numOfFriends ?></h2>
  <a href='friendlist.php' class="button">Friend List</a>
    <a href='logout.php' class="button">Log Out</a>

  <?php
  
  if (mysqli_num_rows($result) > 0) {
    echo "<table class='centered-form'>";
    echo "<thead><tr><th>Profile Name</th><th>Mutual Friends</th><th>Action</th></tr></thead>";
    while ($row = mysqli_fetch_assoc($result)) {
      $friendId = $row["friend_id"];
      $friendProfileName = $row["profile_name"];

      $queryForMutual = "SELECT friend_id, COUNT(*) AS mutual_friend_count
                  FROM $table1 AS f JOIN $table2 AS mf
                  ON (f.friend_id = mf.friend_id1 AND mf.friend_id2 = {$row['friend_id']})
                  OR (f.friend_id = mf.friend_id2 AND mf.friend_id1 = {$row['friend_id']})
                  WHERE f.friend_id != ?
                  AND f.friend_id IN (
                    SELECT friend_id1 FROM $table2 WHERE friend_id2 = $userId
                    UNION SELECT friend_id2 FROM $table2 WHERE friend_id1 = $userId
                  )";
      $stmt = mysqli_prepare($conn, $queryForMutual);
      mysqli_stmt_bind_param($stmt, "i", $userId);
      mysqli_stmt_execute($stmt);
      $queryForMutualResult = mysqli_stmt_get_result($stmt);
      $row = mysqli_fetch_assoc($queryForMutualResult);
      // Display the data
      $mutualFriendCount = $row["mutual_friend_count"];

      echo "<tr>";
      echo "<td>{$friendProfileName}</td>";
      echo "<td>{$mutualFriendCount} mutual friends</td>";
      echo "<td>
                  <form method='post' action='friendadd.php'>
                    <input type='hidden' name='friendId' value='{$friendId}'>
                   <input type='hidden' name='page' value='{$currentPage}'>
                    <input type='submit' name='submit' value='Add as friend'>
                  </form>
                </td>";
      echo "</tr>";
    }
    echo "</table>";

    //pagination
     echo "<div class='pagination'>";
    if ($currentPage > 1) {
      $previousPage = $currentPage - 1;
      echo "<a href='friendadd.php?page={$previousPage}'>Previous Page</a>";
    }
    // page numbers for pagination
    for ($i = 1; $i <= $totalPages; $i++) {
      $activeClass = ($i == $currentPage) ? 'active' : '';
      echo "<a href='friendadd.php?page={$i}' class='{$activeClass} pagenumber'>$i</a>&nbsp;";
    }
    if ($currentPage < $totalPages) {
      $nextPage = $currentPage + 1;
      echo "<a class='pagenumber' href='friendadd.php?page={$nextPage}'>Next</a>";
    }
    echo "</div>";
  }

//if the result is empty
else {
  echo "No Friends to Add";
}

// Close the result and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
  ?>



</body>

</html>