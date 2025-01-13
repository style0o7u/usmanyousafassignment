<?php
session_start();
// Database connection
$conn = mysqli_connect("localhost", "root", "", "videos1"); // Replace with your database details

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
   

    // Handle file upload
    $thumbnail = $_FILES['thumbnail']['name'];
    $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'];
    $upload_dir = 'uploads/';
    $id = $_SESSION['id'];

    if (move_uploaded_file($thumbnail_tmp, $upload_dir . $thumbnail)) {
        // Insert data into the database
        $sql = "INSERT INTO videos (title, description, filename, uploader_id) VALUES ('$title', '$description', '$thumbnail', $id)";
        if (mysqli_query($conn, $sql)) {
            echo "Video uploaded successfully!";
            sleep(3);
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to upload file.";
    }
}

mysqli_close($conn);
?>
