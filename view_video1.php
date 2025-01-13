<?php

session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: signin_form.php");
    exit();
}

// Redirect if filename is not provided
if (!isset($_GET['filename']) || empty($_GET['filename'])) {
    header("Location: secure.php");
    exit();
}

$filename = $_GET['filename'];

// Database connection
$conn = mysqli_connect("localhost", "root", "", "videos1");

// Fetch video details based on filename
$query = "SELECT * FROM videos WHERE filename = '$filename'";

$result = mysqli_query($conn, $query);

// Check if video exists
if (mysqli_num_rows($result) == 1) {
    $video = mysqli_fetch_assoc($result);
} else {
    echo "Video not found.";
    echo "Query: $query<br>";
echo "Filename: " . htmlspecialchars($filename) . "<br>";
    exit();
}

$videoId = $video['id'];
$userId = $_SESSION['id'];

// Fetch existing comments for the video
$fetchCommentsQuery = "SELECT comments.*, users.username, DATE_FORMAT(upload_datetime, '%W, %M %e, %Y, %l:%i %p') AS formatted_datetime
                       FROM comments 
                       INNER JOIN users ON comments.commenter_id = users.id
                       WHERE comments.video_id = $videoId
                       ORDER BY comments.upload_datetime DESC";
$commentsResult = mysqli_query($conn, $fetchCommentsQuery);

// Check if user has liked or disliked the video
$checkLikeQuery = "SELECT * FROM likes WHERE video_id = $videoId AND user_id = $userId";
$checkDislikeQuery = "SELECT * FROM dislikes WHERE video_id = $videoId AND user_id = $userId";

$hasLiked = mysqli_num_rows(mysqli_query($conn, $checkLikeQuery)) > 0;
$hasDisliked = mysqli_num_rows(mysqli_query($conn, $checkDislikeQuery)) > 0;

// Count total likes and dislikes
$countLikesQuery = "SELECT COUNT(*) AS total_likes FROM likes WHERE video_id = $videoId";
$countDislikesQuery = "SELECT COUNT(*) AS total_dislikes FROM dislikes WHERE video_id = $videoId";

$totalLikesResult = mysqli_query($conn, $countLikesQuery);
$totalLikes = mysqli_fetch_assoc($totalLikesResult)['total_likes'];

$totalDislikesResult = mysqli_query($conn, $countDislikesQuery);
$totalDislikes = mysqli_fetch_assoc($totalDislikesResult)['total_dislikes'];

// Process form submission to add new comment
if (isset($_POST['submit_comment'])) {
    $comment = $_POST['comment'];
    $commenter_id = $_SESSION['id'];

    $insertCommentQuery = "INSERT INTO comments (video_id, commenter_id, comment, upload_datetime) 
                       VALUES ($videoId, $commenter_id, '$comment', NOW())";

    if (mysqli_query($conn, $insertCommentQuery)) {
        // Redirect to prevent form resubmission
        header("Location: view_video1.php?filename=$filename");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle like button click
if (isset($_POST['like'])) {
    if (!$hasLiked) {
        // Remove dislike if user has already disliked
        if ($hasDisliked) {
            $deleteDislikeQuery = "DELETE FROM dislikes WHERE video_id = $videoId AND user_id = $userId";
            mysqli_query($conn, $deleteDislikeQuery);
        }

        // Insert like into database
        $likeQuery = "INSERT INTO likes (video_id, user_id) VALUES ($videoId, $userId)";
        mysqli_query($conn, $likeQuery);

        // Reload the page to update the button state
        header("Location: view_video1.php?filename=$filename");
        exit();
    } else {
        // Remove like if already liked
        $deleteLikeQuery = "DELETE FROM likes WHERE video_id = $videoId AND user_id = $userId";
        mysqli_query($conn, $deleteLikeQuery);

        // Reload the page to update the button state
        header("Location: view_video1.php?filename=$filename");
        exit();
    }
}

// Handle dislike button click
if (isset($_POST['dislike'])) {
    if (!$hasDisliked) {
        // Remove like if user has already liked
        if ($hasLiked) {
            $deleteLikeQuery = "DELETE FROM likes WHERE video_id = $videoId AND user_id = $userId";
            mysqli_query($conn, $deleteLikeQuery);
        }

        // Insert dislike into database
        $dislikeQuery = "INSERT INTO dislikes (video_id, user_id) VALUES ($videoId, $userId)";
        mysqli_query($conn, $dislikeQuery);

        // Reload the page to update the button state
        header("Location: view_video1.php?filename=$filename");
        exit();
    } else {
        // Remove dislike if already disliked
        $deleteDislikeQuery = "DELETE FROM dislikes WHERE video_id = $videoId AND user_id = $userId";
        mysqli_query($conn, $deleteDislikeQuery);

        // Reload the page to update the button state
        header("Location: view_video1.php?filename=$filename");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Video</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        video {
            width: 100%;
            max-width: 640px;
            height: auto;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .video-info {
            text-align: center;
            margin-top: 20px;
        }

        .video-info h2 {
            font-size: 24px;
            margin: 0;
        }

        .video-info p {
            font-size: 16px;
            color: #aaa;
            margin: 5px 0;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .action-buttons button {
            background-color: #fff;
            color: #000;
            border: none;
            padding: 10px 20px;
            margin: 0 10px;
            font-size: 16px;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .action-buttons button.clicked {
            background-color: #4CAF50;
            color: white;
        }

        .comment-section {
            width: 100%;
            max-width: 640px;
            margin-top: 30px;
        }

        .comment-section h3 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .comment {
            background-color: #333;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
        }

        .comment .username {
            font-weight: bold;
        }

        .comment .datetime {
            font-size: 12px;
            color: #bbb;
        }

        .add-comment {
            margin-top: 20px;
        }

        .add-comment textarea {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            resize: none;
            margin-bottom: 10px;
            background-color: #333;
            color: #fff;
            border: 1px solid #444;
        }

        .add-comment button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
        }

        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <a href="index.php" class="back-button">Back</a>
    <div class="container">
    <h2><?php echo $video['title']; ?></h2>
        <p class="video-info"><strong>Description:</strong> <?php echo $video['description']; ?></p>

        <video controls>
            <source src="uploads/<?php echo $filename; ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Like and Dislike buttons -->
        <div class="action-buttons">
            <form action="" method="POST">
                <button type="submit" name="like" class="like <?php echo $hasLiked ? 'clicked' : ''; ?>">Like</button>
                <span><?php echo $totalLikes; ?> Likes</span>
                <button type="submit" name="dislike" class="dislike <?php echo $hasDisliked ? 'clicked' : ''; ?>">Dislike</button>
                <span><?php echo $totalDislikes; ?> Dislikes</span>
            </form>
        </div>

        <!-- Comments Section -->
        <div class="comment-section">
            <h3>Comments</h3>
            <?php
            if (mysqli_num_rows($commentsResult) > 0) {
                while ($comment = mysqli_fetch_assoc($commentsResult)) {
                    echo '<div class="comment"><p class="username">' . $comment['username'] . '</p>';
                    echo '<p class="datetime">' . $comment['formatted_datetime'] . '</p>';
                    echo '<p>' . $comment['comment'] . '</p></div>';
                }
            } else {
                echo '<p>No comments yet.</p>';
            }
            ?>

            <!-- Add Comment Form -->
            <div class="add-comment">
                <form action="" method="POST">
                    <textarea name="comment" rows="4" placeholder="Enter your comment" required></textarea><br>
                    <button type="submit" name="submit_comment">Submit Comment</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
