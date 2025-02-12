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
    header('Location: founder_info');
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
 <style>
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

    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Manage Founder Info</h2>

    <h3 class="mt-4">Founder Info List</h3>

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
                    <td><img src="data:image/jpeg;base64,<?= $founder['image'] ?>" alt="Faculty Image" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;"></td>
                    <td><?= htmlspecialchars($founder['founder_message']) ?></td>
                    <td><?= htmlspecialchars($founder['specialized_in']) ?></td>
                    <td><?= htmlspecialchars($founder['qualification']) ?></td>
                    <td><?= htmlspecialchars($founder['contact_info']) ?></td>
                    <td><?= htmlspecialchars($founder['location']) ?></td>
                    <td><?= htmlspecialchars($founder['status']) ?></td>
                    <td>
                        <button class="btn btn-success btn-sm edit-btn" data-id="<?= $founder['id'] ?>">Edit</button>
                        <a href="founder_info.php?action=delete&id=<?= $founder['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Popup Form (Initially hidden) -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="form-title"><?= $founder ? 'Edit Founder Info' : 'Add Founder Info' ?></h3>
        <form id="founderForm" method="POST" enctype="multipart/form-data">
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

            <button type="submit" class="btn btn-primary"><?= $founder ? 'Update Info' : 'Add Info' ?></button>
        </form>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

<!-- <script>
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            window.location.href = 'founder_info?action=edit&id=' + id;
        });
    });
</script> -->

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
