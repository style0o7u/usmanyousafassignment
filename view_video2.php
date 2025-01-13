<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "videos1"); // Replace with your database details

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['filename'])) {
    $filename = mysqli_real_escape_string($conn, $_GET['filename']);

    // Fetch video details
    $sql = "SELECT * FROM videos WHERE thumbnail = '$filename'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($row['title']); ?></title>
        </head>
        <body>
            <h1><?php echo htmlspecialchars($row['title']); ?></h1>
            <img src="uploads/<?php echo htmlspecialchars($row['thumbnail']); ?>" alt="Thumbnail" style="width: 300px; height: auto;">
            <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
            <p><strong>Producer:</strong> <?php echo htmlspecialchars($row['Producer']); ?></p>
            <p><strong>Genre:</strong> <?php echo htmlspecialchars($row['Genre']); ?></p>
            <p><strong>Age Rating:</strong> <?php echo htmlspecialchars($row['AgeRating']); ?></p>
        </body>
        </html>
        <?php
    } else {
        echo "Video not found.";
    }
}

mysqli_close($conn);
?>
