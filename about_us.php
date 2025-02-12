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
    $description = $_POST['description'];
    $vision = $_POST['vision'];
    $mission = $_POST['mission'];
    $years_of_exp = $_POST['years_of_exp'];
    $happy_patients = $_POST['happy_patients'];
    $experience_doctors = $_POST['experience_doctors'];
    $COM = $_POST['COM'];
    $online_consulting = $_POST['online_consulting'];
    $lab_home_services = $_POST['lab_home_services'];
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
            $stmt = $con->prepare("UPDATE about_us SET description=?, image=?, vision=?, mission=?, years_of_exp=?, happy_patients=?, experience_doctors=?, COM=?, online_consulting=?, lab_home_services=?,  status=? WHERE id=?");
            $null = NULL;
            $stmt->bind_param("sbssiiisssii", $description, $null, $vision, $mission, $years_of_exp, $happy_patients, $experience_doctors, $COM, $online_consulting, $lab_home_services, $status, $id);
            $stmt->send_long_data(1, $imageData);
        } else {
            // Update without image
            $stmt = $con->prepare("UPDATE about_us SET description=?, vision=?, mission=?, years_of_exp=?, happy_patients=?, experience_doctors=?, COM=?, online_consulting=?, lab_home_services=?,  status=? WHERE id=?");
          $stmt->bind_param("sssiiisssii", $description, $vision, $mission, $years_of_exp, $happy_patients, $experience_doctors, $COM, $online_consulting, $lab_home_services, $status, $id);
        }
    } else {
        // Insert new record with image
        if ($imageData === null) {
            echo "Image is required for new entries";
            exit;
        }

        $stmt = $con->prepare("INSERT INTO about_us (name, image, qualification, specialized_in, contact_info, happy_patients, experience_doctors, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $null = NULL;
        $stmt->bind_param("sbssssssi", $name, $null, $qualification, $specialized_in, $contact_info, $happy_patients, $experience_doctors, $status);
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
    $stmt = $pdo->prepare("DELETE FROM about_us WHERE id = ?");
    $stmt->execute([$id]);

    // Redirect to reload page after deletion
    header('Location: index.php');
    exit;
}

// Fetch single record for editing (if editing)
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM about_us WHERE id = ? and status = 1");
    $stmt->execute([$id]);
    $founder = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $founder = null;
}

$query = "SELECT * FROM about_us WHERE status = 1";
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

function limitWords($string, $wordLimit = 20)
{
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
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
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
            overflow-x: auto;
            /* Horizontal scrolling for small screens */
            display: block;
            /* Make table scrollable */
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
            justify-content: center;
            /* Center horizontally */
            align-items: center;
            /* Center vertically (optional) */
            /* Optional: Make the container take up the full viewport height */
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <h2>Manage About Us Info</h2>
    <!-- <div class="container">
    <div class="row">
        <div class="col-lg-12 col">

         -->
    <!-- Founder Info List Table -->
    <h3>Founder Info List</h3>
    <table id="founderList" class="table table-striped table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <!-- <th>Name</th> -->
                <th>Image</th>
                <th>description</th>
                <!-- <th>Messages</th> -->
                <th>vision</th>
                <th>mission</th>
                <th>years_of_exp</th>
                <th>happy_patients</th>
                <th>experience_doctors</th>
                <th>COM</th>
                <th>online_consulting</th>
                <th>lab_home_services</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $founder): ?>
                <tr>
                    <!-- <td><?= htmlspecialchars($founder['online_consulting']) ?></td> -->
                    <td><img src="data:image/jpeg;base64,<?= $founder['image'] ?>" alt="Faculty Image" class="image-thumbnail"></td>
                    <!-- <td><?= htmlspecialchars($founder['messages']) ?></td> -->
                    <td id="message-<?= $founder['id'] ?>" class="message-container">
                        <?php
                        $wordCount = str_word_count($founder['description']);
                        $shortMessage = limitWords($founder['description']);
                        if ($wordCount > 10) {
                            // If the description exceeds 10 words, show the limited version initially
                            echo '<span class="short-message">' . $shortMessage . '</span>';
                            echo '<span class="full-message" style="display:none;">' . $founder['description'] . '</span>';
                            // echo '<a href="javascript:void(0)" class="btn btn-link read-more" onclick="showFullMessage(' . $founder['id'] . ')">Read More</a>';
                        } else {
                            // If the message does not exceed 10 words, show the full message
                            echo $founder['description'];
                        }
                        ?>
                    </td>
                    <!-- <td><?= htmlspecialchars($founder['messages']) ?></td> -->
                    <!-- <td><?= htmlspecialchars($founder['vision']) ?></td> -->
                    <td id="message-<?= $founder['id'] ?>" class="message-container">
                        <?php
                        $wordCount = str_word_count($founder['vision']);
                        $shortMessage = limitWords($founder['vision']);
                        if ($wordCount > 10) {
                            // If the vision exceeds 10 words, show the limited version initially
                            echo '<span class="short-message">' . $shortMessage . '</span>';
                            echo '<span class="full-message" style="display:none;">' . $founder['vision'] . '</span>';
                            // echo '<a href="javascript:void(0)" class="btn btn-link read-more" onclick="showFullMessage(' . $founder['id'] . ')">Read More</a>';
                        } else {
                            // If the message does not exceed 10 words, show the full message
                            echo $founder['vision'];
                        }
                        ?>
                    </td>
                    <!-- <td><?= htmlspecialchars($founder['mission']) ?></td> -->
                    <td id="message-<?= $founder['id'] ?>" class="message-container">
                        <?php
                        $wordCount = str_word_count($founder['mission']);
                        $shortMessage = limitWords($founder['mission']);
                        if ($wordCount > 10) {
                            // If the mission exceeds 10 words, show the limited version initially
                            echo '<span class="short-message">' . $shortMessage . '</span>';
                            echo '<span class="full-message" style="display:none;">' . $founder['mission'] . '</span>';
                            // echo '<a href="javascript:void(0)" class="btn btn-link read-more" onclick="showFullMessage(' . $founder['id'] . ')">Read More</a>';
                        } else {
                            // If the message does not exceed 10 words, show the full message
                            echo $founder['mission'];
                        }
                        ?>
                    </td>
                    <td><?= htmlspecialchars($founder['years_of_exp']) ?></td>
                    <!-- <td><?= htmlspecialchars($founder['years_of_exp']) ?></td> -->
                    <td><?= htmlspecialchars($founder['experience_doctors']) ?></td>
                    <td><?= htmlspecialchars($founder['happy_patients']) ?></td>
                    <td><?= htmlspecialchars($founder['COM']) ?></td>
                    <td><?= htmlspecialchars($founder['online_consulting']) ?></td>
                    <td><?= htmlspecialchars($founder['lab_home_services']) ?></td>

                    <td><?= htmlspecialchars($founder['status']) ?></td>
                    <td>
                        <button class="edit-btn" data-id="<?= $founder['id'] ?>">Edit</button> |
                        <a href="index.php?action=delete&id=<?= $founder['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- </div>
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

                <!-- <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" name="name" id="name" value="<?= $founder ? htmlspecialchars($founder['online_consulting']) : '' ?>" class="form-control" required>
                </div> -->

                <div class="mb-3">
                    <label for="image" class="form-label">Image:</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">about Message:</label>
                    <textarea name="description" id="description" class="form-control" required><?= $founder ? htmlspecialchars($founder['description']) : '' ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="vision" class="form-label">Vision</label>
                     <textarea name="vision" id="vision" class="form-control" required><?= $founder ? htmlspecialchars($founder['vision']) : '' ?></textarea>
                    <!-- <input type="text" name="vision" id="vision" value="<?= $founder ? htmlspecialchars($founder['vision']) : '' ?>" class="form-control" required> -->
                </div>

                <div class="mb-3">
                    <label for="mission" class="form-label">Mission:</label>
                    <textarea name="mission" id="mission" class="form-control" required><?= $founder ? htmlspecialchars($founder['mission']) : '' ?></textarea>
                    <!-- <input type="text" name="qualification" id="qualification" value="<?= $founder ? htmlspecialchars($founder['qualification']) : '' ?>" class="form-control" required> -->
                </div>

                 <div class="mb-3">
                    <label for="years_of_exp" class="form-label">Years OF Experience</label>
                    <input type="text" name="years_of_exp" id="years_of_exp" value="<?= $founder ? htmlspecialchars($founder['years_of_exp']) : '' ?>" class="form-control" required>
                </div>

                 <div class="mb-3">
                    <label for="happy_patients" class="form-label">Happy Patients:</label>
                    <input type="text" name="happy_patients" id="happy_patients" value="<?= $founder ? htmlspecialchars($founder['happy_patients']) : '' ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="experience_doctors" class="form-label">Experience Doctors:</label>
                    <input type="text" name="experience_doctors" id="experience_doctors" value="<?= $founder ? htmlspecialchars($founder['experience_doctors']) : '' ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="COM" class="form-label"><?= $founder ? htmlspecialchars($founder['COM']) : '' ?></label>
                    <input type="text" name="COM" id="COM" value="<?= $founder ? htmlspecialchars($founder['COM']) : '' ?>" class="form-control" required>
                </div>
                
                 <div class="mb-3">
                    <label for="online_consulting" class="form-label"><?= $founder ? htmlspecialchars($founder['online_consulting']) : '' ?></label>
                    <input type="text" name="online_consulting" id="online_consulting" value="<?= $founder ? htmlspecialchars($founder['online_consulting']) : '' ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="lab_home_services" class="form-label"><?= $founder ? htmlspecialchars($founder['lab_home_services']) : '' ?></label>
                    <input type="text" name="lab_home_services" id="lab_home_services" value="<?= $founder ? htmlspecialchars($founder['lab_home_services']) : '' ?>" class="form-control" required>
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
                           window.location = "about_us.php";
                        });
                    } else {
                        Swal.fire({
                            icon: "success",
                            title: "success!",
                            text: data.message,
                            timer: 3000,
                            timerProgressBar: true
                       }).then(() => {
                           window.location = "about_us.php";
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
                       window.location = "about_us.php";
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
            readMoreLink.textContent = 'Read Less'; // Change link text to 'Read Less'
            readMoreLink.setAttribute('onclick', 'showLessMessage(' + facultyId + ')'); // Change link to toggle back
        }


        function showLessMessage(facultyId) {
            var messageContainer = document.getElementById('message-' + facultyId);
            var shortMessage = messageContainer.querySelector('.short-message');
            var fullMessage = messageContainer.querySelector('.full-message');
            var readMoreLink = messageContainer.querySelector('.read-more');

            // Show short message, hide full message and "Read Less"
            shortMessage.style.display = 'inline';
            fullMessage.style.display = 'none';
            readMoreLink.textContent = 'Read More'; // Change link text back to 'Read More'
            readMoreLink.setAttribute('onclick', 'showFullMessage(' + facultyId + ')'); // Change link to show full message
        }
    </script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

</body>

</html>