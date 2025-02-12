<?php
// Database connection (as before)
$host = 'srv1328.hstgr.io'; // Your database host
$dbname = 'u629694569_carediabetesce'; // Your database name
$username = 'u629694569_carehospital'; // Your database username
$password = 'Kakatiya1234$'; // Your database password

try {
    // Establish PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $con = mysqli_connect('srv1328.hstgr.io', 'u629694569_carehospital', 'Kakatiya1234$', 'u629694569_carediabetesce');
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


// Handle Add/Update action

// Assuming you have a database connection ($con) established

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    // Get the form data
    $name = $_POST['name'];
    $founder_message = $_POST['founder_message'];
    $specialized_in = $_POST['specialized_in'];
    $qualification = $_POST['qualification'];
    $contact_info = $_POST['contact_info'];
    $location = $_POST['location'];
    $status = $_POST['status'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    // Check if an image was uploaded without errors
    $imageData = null;
    if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Get the image content
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    } elseif (isset($_FILES['image']['error']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle actual upload errors
        echo "Error uploading file: " . $_FILES['image']['error'];
        exit;
    }

    // Proceed with update or insert into the database
    if ($id) {
        // Update existing record
        if ($imageData !== null) {
            // Update with image 
           $stmt = $con->prepare("UPDATE founder_info SET name=?, image=?, qualification=?, specialized_in=?, contact_info=?, founder_message=?, location=?, status=? WHERE id=?");
           $null = NULL; 
           $stmt->bind_param("sbsssssii", $name, $null, $qualification, $specialized_in, $contact_info, $founder_message, $location, $status, $id);
            $stmt->send_long_data(1, $imageData);
        } else {
            // Update without image
         $stmt = $con->prepare("UPDATE founder_info SET name=?, qualification=?, specialized_in=?, contact_info=?, founder_message=?, location=?, status=? WHERE id=?");
            $stmt->bind_param("ssssssii", $name, $qualification, $specialized_in, $contact_info, $founder_message, $location, $status, $id);
        }
    } else {
        // Insert new record with image
        if ($imageData === null) {
            echo "Image is required for new entries";
            exit;
        }

         $stmt = $con->prepare("INSERT INTO founder_info (name, image, qualification, specialized_in, contact_info, founder_message, location, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
           $null = NULL; 
         $stmt->bind_param("sbssssssi", $name, $null, $qualification, $specialized_in, $contact_info, $founder_message, $location, $status);
         $stmt->send_long_data(1, $imageData);
    }

    // Execute the statement and provide feedback
    if ($stmt->execute()) {
        // echo '<script>
        //     Swal.fire({
        //         icon: "success",
        //         title: "Success!",
        //         text: "Successfully data sent to the database",
        //     }).then(function() {
        //         setTimeout(function() {
        //             window.location = "dep_faculty.php"; // Redirect after success
        //         }, 5000);
        //     });
        //   </script>';
         echo json_encode(["success" => true, "message" => "Successfully data sent to the database"]);
    exit;
    } else {
          echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    exit;
    }
}



// Handle Delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM founder_info WHERE id = ?");
    $stmt->execute([$id]);

    // Redirect to reload page after deletion
    header('Location: index.php');
    exit;
}

// Fetch single record for editing (if editing)
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM founder_info WHERE id = ?");
    $stmt->execute([$id]);
    $founder = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $founder = null;
}

$query = "SELECT * FROM founder_info";
$result = mysqli_query($con, $query);
$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['image'])) {
            $row['image'] = base64_encode($row['image']);
        } else {
            $row['image'] = null;
        }
        $data[] = $row;
    }
}

function limitWords($string, $wordLimit = 20) {
    $words = explode(' ', $string);
    if (count($words) > $wordLimit) {
        return implode(' ', array_slice($words, 0, $wordLimit)) . '...';
    }
    return $string;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Founder Info</title>
    <!-- <style>
        /* Modal Style */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        /* Disabled form fields */
        .disabled-form input,
        .disabled-form textarea,
        .disabled-form select {
            background-color: #f0f0f0;
            pointer-events: none;
            cursor: not-allowed;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style> -->
    <style>
    /* Modal Style */
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Close Button */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

       /* Table Styling */
    #founderList {
        width: 100%;
        border-collapse: separate;
        margin: 20px 0;
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        overflow-x: auto; /* Horizontal scrolling for small screens */
        display: block;  /* Make table scrollable */
    }

    #founderList th,
    #founderList td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    #founderList th {
        /* background-color: #4CAF50; */
        color: white;
        font-size: 1.1rem;
    }

    #founderList td {
        background-color: #fff;
        font-size: 1rem;
    }

    #founderList tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #founderList tr:hover {
        background-color: #e8f5e9;
    }

    /* Styling for Images */
    .image-thumbnail {
        width: 186px;
        height: 212px;
        object-fit: cover;
        border-radius: 11%;
    }

    /* Button Styling */
    .edit-btn {
        background-color: #4CAF50;
        color: white;
        padding: 6px 12px;
        font-size: 0.9rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .edit-btn:hover {
        background-color: #45a049;
    }

    /* Link Styling (Delete) */
    a {
        color: #f44336;
        text-decoration: none;
        font-size: 0.9rem;
    }

    a:hover {
        text-decoration: underline;
    }

    /* Responsive Styling */
    @media (max-width: 768px) {
        /* Make the table horizontally scrollable on smaller screens */
        #founderList {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Make table headers sticky on scroll (optional) */
        #founderList thead {
            position: sticky;
            top: 0;
            background-color: #4CAF50;
            z-index: 1;
        }

        /* Smaller font size and padding for small devices */
        #founderList th,
        #founderList td {
            padding: 8px 10px;
            font-size: 0.9rem;
        }

        /* Adjust image size for small devices */
        /* .image-thumbnail {
            width: 60px;
            height: 60px;
        } */
@media (max-width: 768px) {
    .image-thumbnail {
        width: 230px;
        height: 168px;
    }
}
        /* Adjust button size for small devices */
        .edit-btn {
            font-size: 0.8rem;
            padding: 4px 8px;
        }
    }
        .button-container {
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically (optional) */
            /* Optional: Make the container take up the full viewport height */
        }

</style>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <h2>Manage Founder Info</h2>
<div class="container">
    <div class="row">
        <div class="col-lg-12 col">

        
    <!-- Founder Info List Table -->
    <h3>Founder Info List</h3>
   <table id="founderList" class="table table-striped table-bordered mt-3">
    <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Image</th>
            <th>Message</th>
            <th>Specialized In</th>
            <th>Qualification</th>
            <th>Contact</th>
            <th>Location</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $founder): ?>
            <tr>
                <td><?= htmlspecialchars($founder['name']) ?></td>
                <td><img src="data:image/jpeg;base64,<?= $founder['image'] ?>" alt="Faculty Image" class="image-thumbnail"></td>
                <!-- <td><?= htmlspecialchars($founder['founder_message']) ?></td> -->
                  <td id="message-<?= $founder['id'] ?>" class="message-container">
                                                <?php 
                                                $wordCount = str_word_count($founder['founder_message']);
                                                $shortMessage = limitWords($founder['founder_message']);
                                                if ($wordCount > 10) {
                                                    // If the founder_message exceeds 10 words, show the limited version initially
                                                    echo '<span class="short-message">' . $shortMessage . '</span>';
                                                    echo '<span class="full-message" style="display:none;">' . $founder['founder_message'] . '</span>';
                                                    echo '<a href="javascript:void(0)" class="btn btn-link read-more" onclick="showFullMessage(' . $founder['id'] . ')">Read More</a>';
                                                } else {
                                                    // If the message does not exceed 10 words, show the full message
                                                    echo $founder['message'];
                                                }
                                                ?>
                                            </td>
                <td><?= htmlspecialchars($founder['specialized_in']) ?></td>
                <td><?= htmlspecialchars($founder['qualification']) ?></td>
                <td><?= htmlspecialchars($founder['contact_info']) ?></td>
                <td><?= htmlspecialchars($founder['location']) ?></td>
                <td><?= htmlspecialchars($founder['status']) ?></td>
                <td>
                    <button class="edit-btn" data-id="<?= $founder['id'] ?>">Edit</button> |
                    <a href="index.php?action=delete&id=<?= $founder['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>
    </div>
</div>
    <!-- Modal Popup Form (Initially hidden) -->
    <!-- <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="form-title"><?= $founder ? 'Edit Founder Info' : 'Add Founder Info' ?></h3>
            <form id="founderForm" method="POST" enctype="multipart/form-data" class="disabled-form">
                <input type="hidden" id="id" name="id" value="<?= $founder ? $founder['id'] : '' ?>">

                <label for="name">Name:</label>
                <input type="text" name="name" id="name" value="<?= $founder ? htmlspecialchars($founder['name']) : '' ?>" required><br>

                <label for="image">Image:</label>
                <input type="file" name="image" id="image" accept="image/*"><br>

                <label for="founder_message">Founder Message:</label>
                <textarea name="founder_message" id="founder_message" required><?= $founder ? htmlspecialchars($founder['founder_message']) : '' ?></textarea><br>

                <label for="specialized_in">Specialized In:</label>
                <input type="text" name="specialized_in" id="specialized_in" value="<?= $founder ? htmlspecialchars($founder['specialized_in']) : '' ?>" required><br>

                <label for="qualification">Qualification:</label>
                <input type="text" name="qualification" id="qualification" value="<?= $founder ? htmlspecialchars($founder['qualification']) : '' ?>" required><br>

                <label for="contact_info">Contact Info:</label>
                <input type="text" name="contact_info" id="contact_info" value="<?= $founder ? htmlspecialchars($founder['contact_info']) : '' ?>" required><br>

                <label for="location">Location:</label>
                <input type="text" name="location" id="location" value="<?= $founder ? htmlspecialchars($founder['location']) : '' ?>" required><br>

                <label for="status">Status:</label>
                <select name="status" id="status">
                    <option value="1" <?= $founder && $founder['status'] == '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= $founder && $founder['status'] == '0' ? 'selected' : '' ?>>Inactive</option>
                </select><br>

                <button type="submit" id="submit-btn"><?= $founder ? 'Update Founder Info' : 'Add Founder Info' ?></button>
            </form>
        </div>
    </div> -->
<!-- <div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="form-title"><?= $founder ? 'Edit Founder Info' : 'Add Founder Info' ?></h3>
        <form id="founderForm" method="POST" enctype="multipart/form-data" class="form-container">
            <input type="hidden" id="id" name="id" value="<?= $founder ? $founder['id'] : '' ?>">

            <div class="form-group">
                <label for="name">Name:</label><br>
                <input type="text" name="name" id="name" value="<?= $founder ? htmlspecialchars($founder['name']) : '' ?>" required><br>
            </div>

            <div class="form-group">
                <label for="image">Image:</label><br>
                <input type="file" name="image" id="image" accept="image/*"><br>
            </div>

            <div class="form-group">
                <label for="founder_message">Founder Message:</label><br>
                <textarea name="founder_message" id="founder_message" required><?= $founder ? htmlspecialchars($founder['founder_message']) : '' ?></textarea><br>
            </div>

            <div class="form-group">
                <label for="specialized_in">Specialized In:</label><br>
                <input type="text" name="specialized_in" id="specialized_in" value="<?= $founder ? htmlspecialchars($founder['specialized_in']) : '' ?>" required><br>
            </div>

            <div class="form-group">
                <label for="qualification">Qualification:</label><br>
                <input type="text" name="qualification" id="qualification" value="<?= $founder ? htmlspecialchars($founder['qualification']) : '' ?>" required><br>
            </div>

            <div class="form-group">
                <label for="contact_info">Contact Info:</label><br>
                <input type="text" name="contact_info" id="contact_info" value="<?= $founder ? htmlspecialchars($founder['contact_info']) : '' ?>" required><br>
            </div>

            <div class="form-group">
                <label for="location">Location:</label><br>
                <input type="text" name="location" id="location" value="<?= $founder ? htmlspecialchars($founder['location']) : '' ?>" required><br>
            </div>

            <div class="form-group">
                <label for="status">Status:</label><br>
                <select name="status" id="status">
                    <option value="1" <?= $founder && $founder['status'] == '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= $founder && $founder['status'] == '0' ? 'selected' : '' ?>>Inactive</option>
                </select><br>
            </div>

            <button type="submit" id="submit-btn"><?= $founder ? 'Update Founder Info' : 'Add Founder Info' ?></button>
        </form>
    </div>
</div> -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" style="
    text-align: end;
    color: red;">&times;</span>
        <h3 id="form-title"><?= $founder ? 'Edit Founder Info' : 'Add Founder Info' ?></h3>
        <form id="founderForm" method="POST" enctype="multipart/form-data" onsubmit="return handleFormSubmit(event)">
            <input type="hidden" id="id" name="id" value="<?= $founder ? $founder['id'] : '' ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" name="name" id="name" value="<?= $founder ? htmlspecialchars($founder['name']) : '' ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image:</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label for="founder_message" class="form-label">Founder Message:</label>
                <textarea name="founder_message" id="founder_message" class="form-control" required><?= $founder ? htmlspecialchars($founder['founder_message']) : '' ?></textarea>
            </div>

            <div class="mb-3">
                <label for="specialized_in" class="form-label">Specialized In:</label>
                <input type="text" name="specialized_in" id="specialized_in" value="<?= $founder ? htmlspecialchars($founder['specialized_in']) : '' ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="qualification" class="form-label">Qualification:</label>
                <input type="text" name="qualification" id="qualification" value="<?= $founder ? htmlspecialchars($founder['qualification']) : '' ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="contact_info" class="form-label">Contact Info:</label>
                <input type="text" name="contact_info" id="contact_info" value="<?= $founder ? htmlspecialchars($founder['contact_info']) : '' ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Location:</label>
                <input type="text" name="location" id="location" value="<?= $founder ? htmlspecialchars($founder['location']) : '' ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select name="status" id="status" class="form-select">
                    <option value="1" <?= $founder && $founder['status'] == '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= $founder && $founder['status'] == '0' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="form-group" style="display: flex; justify-content: center;margin-top: 10px;">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
        </form>
    </div>
</div>
<script>
    
function handleFormSubmit(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('founderForm'));

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Expecting JSON response
    .then(data => {
        if (data.success) {
            closeModal();
            Swal.fire({
                icon: "success",
                title: "Success!",
                text: data.message,
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                window.location = "founder_info2.php";
            });
        } else {
            Swal.fire({
                icon: "success",
                title: "success!",
                text: data.message,
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                window.location = "founder_info2.php";
            });
            
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: "success",
            title: "success!",
            text: "Successfully data sent to the database.",
            timer: 3000,
                timerProgressBar: true
            }).then(() => {
                window.location = "founder_info2.php";
            });
    });
}

</script>

    <script>
        // Get the modal and close button
        var modal = document.getElementById("myModal");
        var span = document.getElementsByClassName("close")[0];

        // Open the modal when the edit button is clicked
        document.querySelectorAll('.edit-btn').forEach(function(button) {
            button.addEventListener('click', function(e) {
                var id = e.target.getAttribute('data-id');
                modal.style.display = "block"; // Show the modal

                // Optionally: Fetch and fill form data via AJAX using the 'id'
                // For now, we'll just enable the form as an example
                var form = document.getElementById('founderForm');
                form.classList.remove('disabled-form'); // Remove disabled class
                form.querySelectorAll('input, textarea, select').forEach(function(input) {
                    input.removeAttribute('disabled'); // Enable form fields
                });

                // Change modal title and button text
                document.getElementById('form-title').textContent = 'Edit Founder Info';
                document.getElementById('submit-btn').textContent = 'Update Founder Info';
            });
        });

        // Close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close modal if user clicks outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function showFullMessage(facultyId) {
    var messageContainer = document.getElementById('message-' + facultyId);
    var shortMessage = messageContainer.querySelector('.short-message');
    var fullMessage = messageContainer.querySelector('.full-message');
    var readMoreLink = messageContainer.querySelector('.read-more');

    // Show full message, hide short message and "Read More"
    shortMessage.style.display = 'none';
    fullMessage.style.display = 'inline';
    readMoreLink.textContent = 'Read Less';  // Change link text to 'Read Less'
    readMoreLink.setAttribute('onclick', 'showLessMessage(' + facultyId + ')');  // Change link to toggle back
}


function showLessMessage(facultyId) {
    var messageContainer = document.getElementById('message-' + facultyId);
    var shortMessage = messageContainer.querySelector('.short-message');
    var fullMessage = messageContainer.querySelector('.full-message');
    var readMoreLink = messageContainer.querySelector('.read-more');

    // Show short message, hide full message and "Read Less"
    shortMessage.style.display = 'inline';
    fullMessage.style.display = 'none';
    readMoreLink.textContent = 'Read More';  // Change link text back to 'Read More'
    readMoreLink.setAttribute('onclick', 'showFullMessage(' + facultyId + ')');  // Change link to show full message
}
    </script>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

</body>

</html>