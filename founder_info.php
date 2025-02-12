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
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Success!",
                text: "Successfully data sent to the database",
            }).then(function() {
                setTimeout(function() {
                    window.location = "dep_faculty.php"; // Redirect after success
                }, 5000);
            });
          </script>';
        exit;
    } else {
        echo "Error: " . $stmt->error;
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Founder Info</title>
    <style>
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
    </style>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <h2>Manage Founder Info</h2>

    <!-- Founder Info List Table -->
    <h3>Founder Info List</h3>
    <table border="1" id="founderList">
        <thead>
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
                    <td><img src="data:image/jpeg;base64,<?= $founder['image'] ?>" alt="Faculty Image" width="100px"></td>
                    <td><?= htmlspecialchars($founder['founder_message']) ?></td>
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

    <!-- Modal Popup Form (Initially hidden) -->
    <div id="myModal" class="modal">
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
    </div>

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
    </script>

</body>

</html>