<?php
session_start();

// Display success message if available
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success" role="alert">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}

// Database connection
$hostname = "usmanassignmentforscal.mysql.database.azure.com";
$username = "style0o7u";
$password = "AMD.yousaf123";
$dbname = "videos1";

$conn = mysqli_connect($hostname, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch genres from the genres table
$genreQuery = "SELECT DISTINCT genre_name FROM genres";
$genreResult = mysqli_query($conn, $genreQuery);
$genres = [];
if ($genreResult && mysqli_num_rows($genreResult) > 0) {
    while ($row = mysqli_fetch_assoc($genreResult)) {
        $genres[] = $row['genre_name'];
    }
}

// Fetch age ratings from the agerating table
$ageRatingQuery = "SELECT DISTINCT rating_name FROM agerating";
$ageRatingResult = mysqli_query($conn, $ageRatingQuery);
$ageRatings = [];
if ($ageRatingResult && mysqli_num_rows($ageRatingResult) > 0) {
    while ($row = mysqli_fetch_assoc($ageRatingResult)) {
        $ageRatings[] = $row['rating_name'];
    }
}

// Check if the user clicked the search button
if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $genre = isset($_POST['genre']) ? $_POST['genre'] : '';
    $ageRating = isset($_POST['age_rating']) ? $_POST['age_rating'] : '';

    // Construct the query based on search terms
    $query = "SELECT * FROM videos WHERE (title LIKE '%$search%' OR description LIKE '%$search%' OR Producer LIKE '%$search%' OR Genre LIKE '%$search%' OR AgeRating LIKE '%$search%')";
    if ($genre != '') {
        $query .= " AND Genre = '$genre'";
    }
    if ($ageRating != '') {
        $query .= " AND AgeRating = '$ageRating'";
    }

    $result = mysqli_query($conn, $query);
} else {
    // If not, fetch all videos
    $query = "SELECT * FROM videos";
    $result = mysqli_query($conn, $query);
}

// // Process sign-up form submission
// if (isset($_POST['signup'])) {
//     $username = $_POST['username'];
//     $password = $_POST['password'];
//     $fname = $_POST['fname'];
//     $lname = $_POST['lname'];
//     $email = $_POST['email'];
//     $contact = $_POST['contact'];

//     // Insert data into users table
//     $signup_query = "INSERT INTO users (username, password, FName, LName, Email, ContactNumber) VALUES ('$username', '$password', '$fname', '$lname', '$email', '$contact')";
//     if (mysqli_query($conn, $signup_query)) {
//         // Redirect to sign-in page after successful sign-up
//         header("Location: index.php");
//         exit();
//     } else {
//         echo "Error: " . $signup_query . "<br>" . mysqli_error($conn);
//     }
// }

?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Sharing Platform</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="stylesv.css"> <!-- Link to external CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #ffffff; /* White background */
            color: #333; /* Dark text for contrast */
            margin: 0;
            padding: 0;
        }

        h2 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            color:rgb(0, 0, 0); /* Dark gray for header */
        }

        .logout,
        .signup {
            float: right;
            margin-top: 20px;
            margin-right: 20px;
            background-color: #fe2c55; /* TikTok's signature red-pink color */
            color: #fff; /* White text */
            border: none;
            border-radius: 25px; /* Rounded corners */
            padding: 10px 20px; /* Button padding */
            text-decoration: none;
            font-size: 16px;
            font-weight: 600; /* Slightly bolder text */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth hover effect */
        
        }

        .logout:hover,
        .signup:hover {
            color:rgb(0, 0, 0); /* TikTok pink on hover */
        }

        .search-form {
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-control {
            background-color: #f2f2f2; /* Light gray background for input */
            color: #333;
            border: 1px solid #ccc;
            border-radius: 25px;
            padding: 12px 20px;
            margin-right: 10px;
            width: 300px;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: none;
            background-color: #fff; /* White background on focus */
            color: #333;
            border-color: #fe2c55; /* TikTok pink border on focus */
        }

        .btn-search {
            background-color: #fe2c55; /* TikTok pink */
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-search:hover {
            background-color: #ff3e75; /* Lighter pink on hover */
        }

        .table {
            background-color: #ffffff; /* White background */
            color: #333; /* Dark text */
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 20px;
        }

        .table th {
            font-size: 18px;
            font-weight: 700;
            background-color: #f8f8f8; /* Light gray header */
            color: #010101; /* Dark text */
        }

        .table tbody tr:nth-child(even) {
            background-color: #fafafa; /* Very light gray for alternating rows */
        }

        .table tbody tr:hover {
            background-color: #f2f2f2; /* Slightly darker gray on hover */
        }

        .video-link {
            color: #010101; /* Dark link text */
            text-decoration: none;
            transition: all 0.3s;
        }

        .video-link:hover {
            color: #fe2c55; /* TikTok pink hover color */
        }

        .age-rating {
            color: #333; /* Dark text */
        }

        .age-rating.pg-13 {
            color: #00FF00; /* Green for PG-13 */
        }

        .age-rating.r {
            color: #FF0000; /* Red for R-rated */
        }

        /* Modal */
        .modal-container {
            display: none; /* Initially hidden */
    position: fixed; /* Fix it relative to the viewport */
    top: 50%; /* Center vertically */
    left: 50%; /* Center horizontally */
    transform: translate(-50%, -50%); /* Adjust for element's dimensions */
    width: 300px; /* Adjust width as needed */
    height: auto; /* Allow flexible height */
    background-color: rgba(255, 255, 255, 0.95); /* Slightly opaque background */
   
    justify-content: center; /* Center content horizontally */
    align-items: center; /* Center content vertically */
    padding: 20px; /* Add padding inside the menu */
    border-radius: 10px; /* Rounded corners */
    z-index: 1000; /* Ensure it appears on top */
        }

        .modal-content {
            background-color:rgb(255, 255, 255); /* White modal background */
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }

        .close-btn {
            color: #333;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .username {
            float: right;
            margin-top: 20px;
            margin-right: 20px;
            color: #010101; /* Dark text */
        }

        .thumbnail {
            width: 120px;
            height: auto;
            border-radius: 10px;
        }

        .upload-link {
            float: right;
            margin-top: 20px;
            margin-right: 20px;
            color: #fe2c55; /* TikTok pink for upload link */
            text-decoration: none;
        }

        /* Dashboard */
        .dashboard {
            background-color: #ffffff; /* White background */
            padding: 20px;
            margin-top: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .dashboard h3 {
            color: #010101;
            margin-bottom: 20px;
        }

        .dashboard .video-list {
            list-style: none;
            padding: 0;
        }

        .dashboard .video-list li {
            margin-bottom: 15px;
        }

        .dashboard .video-list li a {
            color: #010101;
            text-decoration: none;
            transition: all 0.3s;
        }

        .dashboard .video-list li a:hover {
            color: #fe2c55; /* TikTok pink hover color */
        }
    </style>

    <style>
       

        .video-container1 {
            width: 100%;
            margin-left: 25%;
            max-width: 500px; /* Set max width for videos */
            height: 90vh; /* Full viewport height */
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            margin-bottom: 10px;
        }

        video {
            width: 100%;
            height: auto;
            max-height: 100%; /* Ensure video fits the container */
            outline: none;
        }

        .controls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: rgba(255, 255, 255, 0.8);
            color: black;
        }
    </style>


<style>
       
        .sidebar1 {
            width: 250px; /* Set width of the sidebar */
            height: 100vh; /* Full viewport height */
            position: fixed; /* Keep sidebar fixed */
            top: 10%;
            left: 0;
            background-color:rgb(255, 255, 255); /* Dark background for sidebar */
            color: white;
            padding: 20px;
            overflow-y: auto; /* Enable vertical scrolling if needed */
            font-family: Arial, sans-serif;
        }

        .sidebar1 h2 {
            color:rgb(0, 0, 0);
            font-size: 24px;
            margin-bottom: 20px;
        }

        .hashtag {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .hashtag a {
            color: #ff4b4b; /* Red color for hashtags */
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
        }

        .hashtag a:hover {
            color: #ff8080; /* Lighter red color on hover */
        }

        .trend {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 10px;
        }

        .trend a {
            color: #ff4b4b;
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
        }

        .trend a:hover {
            color: #ff8080; /* Light hover effect for trends */
        }

        .content {
            margin-left: 250px; /* Make space for the sidebar */
            padding: 20px;
            flex-grow: 1;
        }
    </style>
    
    <style>
    /* Grid Container */
    .video-grid {
        display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Reduced min-width for smaller grid items */
    gap: 20px; /* Spacing between videos */
    padding: 20px;
    }

    /* Each video item */
    .video-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        background-color: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    /* Thumbnail image */
    .thumbnail {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Hover effect on video item */
    .video-item:hover {
        transform: scale(1.05); /* Slight zoom on hover */
    }

    /* Title and description styling */
    .video-title {
        font-size: 16px;
        font-weight: bold;
        margin: 10px;
    }

    .video-description {
        font-size: 14px;
        color: #555;
        margin: 0 10px 10px 10px;
    }
</style>
<style>
    /* Overall container */
    .upload-container {
        width: 100%;
        max-width: 500px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        display: none; /* Initially hidden */
    }

    /* Header */
    .upload-container h1 {
        text-align: center;
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
    }

    /* Form */
    .upload-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Form Group */
    .form-group {
        display: flex;
        flex-direction: column;
    }

    /* Input fields */
    input[type="text"], textarea {
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-top: 8px;
        outline: none;
        transition: border-color 0.3s;
    }

    input[type="text"]:focus, textarea:focus {
        border-color: #3498db;
    }

    /* File Input Styling */
    input[type="file"] {
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-top: 8px;
        cursor: pointer;
        background-color: #f9f9f9;
        transition: background-color 0.3s ease;
    }

    input[type="file"]:hover {
        background-color: #f1f1f1;
    }

    /* Submit Button */
    .upload-button {
        padding: 14px;
        background-color: #3498db;
        color: #fff;
        font-size: 18px;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .upload-button:hover {
        background-color: #2980b9;
    }

    /* Show Upload Form Button */
    #showUploadButton {
        padding: 14px;
        background-color: #2ecc71;
        color: white;
        font-size: 18px;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: block;
        margin: 20px auto;
        transition: background-color 0.3s ease;
    }

    #showUploadButton:hover {
        background-color: #27ae60;
    }

    /* Mobile Responsiveness */
    @media (max-width: 600px) {
        .upload-container {
            padding: 15px;
        }

        .upload-container h1 {
            font-size: 20px;
        }

        .upload-button {
            font-size: 16px;
        }
    }
</style>
<style>
    /* Overall container */
    .upload-container {
        width: 100%;
        max-width: 500px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        display: none; /* Initially hidden */
    }

    /* Header */
    .upload-container h1 {
        text-align: center;
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
    }

    /* Form */
    .upload-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Form Group */
    .form-group {
        display: flex;
        flex-direction: column;
    }

    /* Input fields */
    input[type="text"], textarea {
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-top: 8px;
        outline: none;
        transition: border-color 0.3s;
    }

    input[type="text"]:focus, textarea:focus {
        border-color: #3498db;
    }

    /* File Input Styling */
    input[type="file"] {
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-top: 8px;
        cursor: pointer;
        background-color: #f9f9f9;
        transition: background-color 0.3s ease;
    }

    input[type="file"]:hover {
        background-color: #f1f1f1;
    }

    /* Submit Button */
    .upload-button {
        padding: 14px;
        background-color: #3498db;
        color: #fff;
        font-size: 18px;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .upload-button:hover {
        background-color: #2980b9;
    }

    /* Show Upload Form Button */
    #showUploadButton {
        padding: 14px;
        background-color: #2ecc71;
        color: white;
        font-size: 18px;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: block;
        margin: 20px auto;
        transition: background-color 0.3s ease;
    }

    #showUploadButton:hover {
        background-color: #27ae60;
    }

    /* Mobile Responsiveness */
    @media (max-width: 600px) {
        .upload-container {
            padding: 15px;
        }

        .upload-container h1 {
            font-size: 20px;
        }

        .upload-button {
            font-size: 16px;
        }
    }
</style>
</head>



<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Video Sharing Platform</h2>
            </div>
            <div class="col-md-6">
                <?php if (isset($_SESSION['username'])) : ?>
                    <span class="username">Welcome, <?php echo $_SESSION['username']; ?></span>
                    <a class="logout" href="logout.php">Logout</a>
                    <?php if ($_SESSION['username'] == 'Admin') : ?>
                        <a class="upload-link" href="secure3.php">Upload Video</a>
                    <?php endif; ?>
                <?php else : ?>
                    <a class="signup" href="#" id="signup-link">Sign Up</a>
                    <a class="logout" href="#" id="signin-link">Sign In</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sign-up form -->
        <div id="signup-form" class="modal-container">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h3>Sign Up</h3>
                <form action="signup-process.php" method="POST">
                    <div class="form-group">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact Number" required>
                    </div>
                    <button type="submit" name="signup" class="btn btn-primary">Sign Up</button>
                </form>
            </div>
        </div>

        <!-- Sign-in form -->
        <div id="signin-form" class="modal-container">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h3>Sign In</h3>
                <form action="signin_process.php" method="POST">
                    <div class="form-group">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" name="signin" class="btn btn-primary">Sign In</button>
                </form>
            </div>
        </div>

       
            

        <?php if (isset($_SESSION['username'])): ?>

           
            <button id="showUploadButton">Upload Video</button>

<!-- Upload Form (Initially Hidden) -->
<div class="upload-container" id="uploadForm">
    <h1>Upload Video</h1>
    <form action="upload_video.php" method="POST" enctype="multipart/form-data" class="upload-form">
        <!-- Thumbnail -->
        <div class="form-group">
            <label for="thumbnail">Thumbnail:</label>
            <input type="file" name="thumbnail" id="thumbnail" required>
        </div>

        <!-- Title -->
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" placeholder="Enter video title" required>
        </div>

        <!-- Description -->
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4" placeholder="Enter video description" required></textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="upload-button">Upload Video</button>
    </form>
</div>

<script>
    // JavaScript to toggle visibility of the upload form
    document.getElementById('showUploadButton').addEventListener('click', function() {
        const uploadForm = document.getElementById('uploadForm');
        uploadForm.style.display = (uploadForm.style.display === 'block') ? 'none' : 'block'; // Toggle visibility
    });
</script>

    <?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "videos1"); // Replace with your database details

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to fetch videos
$sql = "SELECT * FROM videos";
$result = mysqli_query($conn, $sql);
?>

    <!-- Display search results -->
    <table class="table">
    <thead>
        <!-- Table Header can go here if needed -->
    </thead>
    <h2>Recommandtion page</h2>
    <tbody>
        <div class="video-grid">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                // Display each search result as a grid item
                while ($row = mysqli_fetch_assoc($result)) {
                    $ageRatingClass = strtolower($row['AgeRating']);
                    echo '<div class="video-item">';
                    
                    // Video Thumbnail with link to view the video
                    echo '<a href="view_video1.php?filename=' . $row['filename'] . '">';
                    echo '<img src="uploads/' . $row['thumbnail'] . '" alt="Thumbnail" class="thumbnail">';
                    echo '</a>';
                    
                    // Video Title and Description (Optional)
                    echo '<div class="video-title">' . $row['title'] . '</div>';
                    echo '<div class="video-description">' . $row['description'] . '</div>';
                    
                    echo '</div>';
                }
            } else {
                echo '<tr><td colspan="6">No videos found</td></tr>';
            }
            ?>
        </div>
    </tbody>
</table>
<?php else: ?>
    <body>
    <div class="video-container1">
        <video class="video-player" src="videos/video2.mp4" muted autoplay loop></video>
        <div class="controls">
        <button class="btn play-btn">Like</button>
        <button class="btn pause-btn">Comment</button>
        </div>
    </div>
    <div class="video-container1">
        <video class="video-player" src="videos/video3.mp4" muted autoplay loop></video>
        <div class="controls">
            <button class="btn play-btn">Like</button>
            <button class="btn pause-btn">Comment</button>
        </div>
    </div>
    <div class="video-container1">
        <video class="video-player" src="videos/video4.mp4" muted autoplay loop></video>
        <div class="controls">
        <button class="btn play-btn">Like</button>
        <button class="btn pause-btn">Comment</button>
        </div>
    </div>

    <script>
        const videoContainers = document.querySelectorAll('.video-container1');

        videoContainers.forEach(container => {
            const video = container.querySelector('.video-player');
            const playBtn = container.querySelector('.play-btn');
            const pauseBtn = container.querySelector('.pause-btn');

            // Play button event
            playBtn.addEventListener('click', () => {
                video.play();
            });

            // Pause button event
            pauseBtn.addEventListener('click', () => {
                video.pause();
            });

            // Auto-pause videos when out of view
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        video.play();
                    } else {
                        video.pause();
                    }
                });
            }, { threshold: 0.5 }); // Trigger when 50% of the video is visible

            observer.observe(video);
        });
    </script>
</body>

<body>

    <!-- Sidebar for hashtags and trends -->
    <div class="sidebar1">
        <h2>Hashtags</h2>
        <div class="hashtag">
            <a href="#">#ViralChallenge</a>
            <a href="#">#DanceMoves</a>
            <a href="#">#FunnyVideos</a>
            <a href="#">#FoodLovers</a>
            <a href="#">#TikTokTrends</a>
        </div>

        <h2>Trending Topics</h2>
        <div class="trend">
            <a href="#">#ViralVideo</a>
            <a href="#">#FashionInspo</a>
            <a href="#">#TechTalk</a>
            <a href="#">#TravelGoals</a>
            <a href="#">#MusicLovers</a>
        </div>
    </div>

    <!-- Main content area -->
   

</body>


<?php endif; ?>


        <?php if (isset($_SESSION['username'])) : ?>
           
        <?php endif; ?>

    </div>

    <script>
        // Show sign-in form when "Sign In" link is clicked
        document.getElementById("signin-link").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("signin-form").style.display = "block";
            document.getElementById("signup-form").style.display = "none";
        });

        // Show sign-up form when "Sign Up" link is clicked
        document.getElementById("signup-link").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("signup-form").style.display = "block";
            document.getElementById("signin-form").style.display = "none";
        });

        // Close sign-in and sign-up forms when close button is clicked
        document.querySelectorAll(".close-btn").forEach(function(closeBtn) {
            closeBtn.addEventListener("click", function() {
                document.getElementById("signin-form").style.display = "none";
                document.getElementById("signup-form").style.display = "none";
            });
        });
    </script>

</body>

</html>

<?php
mysqli_close($conn);
?>
