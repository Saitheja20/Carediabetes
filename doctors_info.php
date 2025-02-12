<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_carehospital', 'Kakatiya1234$', 'u629694569_carediabetesce');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$itemsPerPage = isset($_GET['items_per_page']) ? (int) $_GET['items_per_page'] : 10; // Default to 10 items per page

// Handling file upload
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
//     // Get the form data
//     $name = $_POST['name'];
//     $qualification = $_POST['qualification'];
//     $designation = $_POST['designation'];
//     $department = $_POST['department'];
//     $department_id = $_POST['department_id'];
//     $id = isset($_POST['id']) ? $_POST['id'] : null;
//     $message = $_POST['message'];
//     $phone = $_POST['phone'];
//     $email = $_POST['email'];
//     // Check if the image was uploaded without errors
//     if ($_FILES['image']['error'] == 0) {
//         $imageData = file_get_contents($_FILES['image']['tmp_name']);
//         if ($id) {
//             $stmt = $con->prepare("UPDATE doctors_info SET name=?, image=?, qualification=?, designation=?, department=?, message=?, phone=?, email=?, department_id=? WHERE id=?");
//             $null = NULL;
//             $stmt->bind_param("sbssssssii", $name, $null, $qualification, $designation, $department,$message,$phone,$email, $department_id,$id);
//             $stmt->send_long_data(1, $imageData);
//         } else {
//             $stmt = $con->prepare("INSERT INTO doctors_info (name, image, qualification, designation, department, department_id,message ,phone, email) VALUES (?, ?, ?, ?,?,?,?,?,?)");
//             $null = NULL;
//             $stmt->bind_param("sbsssisss", $name, $null, $qualification, $designation, $department, $department_id,$message,$phone,$email);
//             $stmt->send_long_data(1, $imageData);
//         }

//         if ($stmt->execute()) {
//             echo "Image uploaded and stored in database successfully!";
//             exit;
//         } else {
//             echo "Error: " . $stmt->error;
//         }
//     } else {
//         echo "Error uploading file.";
//     }
// }


// Handling file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    // Get the form data
    $name = $_POST['name'];
    $qualification = $_POST['qualification'];
    $designation = $_POST['designation'];
    $department = $_POST['department'];
    $department_id = $_POST['department_id'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $message = $_POST['message'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    // Check if the image was uploaded without errors

    // Check if an image was uploaded without errors
    $imageData = null;
    if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    } elseif (isset($_FILES['image']['error']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle actual upload errors
        echo "Error uploading file: " . $_FILES['image']['error'];
        exit;
    }

    // Proceed with update or insert
    if ($id) {
        // Update existing record
        if ($imageData !== null) {
            // Update with image
            $stmt = $con->prepare("UPDATE doctors_data2 SET name=?, image=?, qualification=?, specialized_in=?, about_doctor=?, total_experience=?, message=?,phone=?,email=?,address=?,status=? WHERE id=?");
            $null = NULL;
            $stmt->bind_param("sbsssissssii", $name, $null, $qualification, $designation, $department, $department_id, $message, $phone, $email, $address, $status, $id);
            $stmt->send_long_data(1, $imageData);
        } else {
            // Update without image
            $stmt = $con->prepare("UPDATE doctors_data2 SET name=?, qualification=?, specialized_in=?, about_doctor=?, total_experience=?, message=?,phone=?,email=?,address=?,status=? WHERE id=?");
            $stmt->bind_param("ssssissssii", $name, $qualification, $designation, $department, $department_id, $message, $phone, $email, $address, $status, $id);
        }
    } else {
        // Insert new record
        // if ($imageData === null) {
        //     echo "Image is required for new entries";
        //     exit;
        // }
        $stmt = $con->prepare("INSERT INTO doctors_data2 (name, image, qualification, specialized_in, about_doctor, total_experience, message,phone,email,address,status) VALUES (?, ?, ?,?,?,?, ?, ?, ?, ?, ?)");
        $null = NULL;
        $stmt->bind_param("sbsssissssi", $name, $null, $qualification, $designation, $department, $department_id, $message, $phone, $email, $address, $status);
        $stmt->send_long_data(1, $imageData);
    }

    // Execute the statement
    if ($stmt->execute()) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Success!",
                text: "Successfullt Data send to the database",
                // showConfirmButton: true,  // Show "OK" button
                // timer: 5000,  // The alert will disappear after 3 seconds
                // timerProgressBar: true  // Show a progress bar for the timer
            }).then(function() {
                setTimeout(function() {
                    window.location = "dep_faculty.php"; // Optional: Redirect after 3 seconds
                }, 5000); // Optional: Redirect after successful operation
            });
          </script>';
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}




if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $query = "SELECT  id,qualification,name,designation,department,department_id,message,phone,email FROM doctors_data2 WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    mysqli_close($con);
    echo json_encode($data);
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $query = "DELETE FROM doctors_data2 WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        exit;
    } else {
        echo 'Error deleting image: ' . mysqli_error($con);
    }
    exit;
}

$searchQuery = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $searchQuery = "WHERE name LIKE '%$search%' OR qualification LIKE '%$search%' OR designation LIKE '%$search%' OR department LIKE '%$search%' OR department_id LIKE '%$search%'";
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

$query = "SELECT * FROM doctors_data2 $searchQuery LIMIT $itemsPerPage OFFSET $offset";
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

$totalQuery = "SELECT COUNT(*) as total FROM doctors_data2 $searchQuery";
$totalResult = mysqli_query($con, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $itemsPerPage);

mysqli_close($con);


function limitWords($string, $wordLimit = 25)
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
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Vaageswari college of engineering</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="../assets/img/logo3" rel="icon">
    <link href="../assets/img/logo3" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">


    <!-- swal cdn -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Template Main CSS File -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="faculty.css">
    <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            border-radius: 50%;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
        }

        input:checked+.slider {
            background-color: #4CAF50;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .hidden-textarea {
            display: none;
            width: 100%;
            height: 100px;
        }

        .edit-icon {
            cursor: pointer;
            color: blue;
            font-size: 18px;
            margin-left: 5px;
        }

        .edit-icon:hover {
            color: darkblue;
        }

        .accordion-content {
            display: none;
            margin-top: 10px;
        }

        .accordion-content.active {
            display: block;
        }

        form {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* form label {
    font-size: 16px;
    font-weight: bold;
   
    padding: 1rem;
    background-color: #1D3A6C;
    color: white;

} */

        form label {
            font-size: 16px;
            font-weight: bold;
            /* padding: 1rem; */
            background-color: #ffffff;
            color: #000000;
        }

        /* Modal Background */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black with opacity */
            padding-top: 60px;
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            /* 5% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            /* Could be more or less, depending on screen size */
        }

        /* Close Button */
        .close {
            color: rgba(226, 46, 46, 0.83);
            text-align: right;
            font-size: 28px;
            font-weight: bold;
        }

        @media (min-width: 1400px) {

            .container,
            .container-lg,
            .container-md,
            .container-sm,
            .container-xl,
            .container-xxl {
                max-width: 1828px;
            }
        }

        .close:hover,
        .close:focus {
            color: rgb(233, 17, 17);
            text-decoration: none;
            cursor: pointer;
            /* border: 1px solid rgb(226, 46, 46); */
        }

        .modal-content {
            position: relative !important;
            position: relative !important;
            display: flex !important;
            ;
            flex-direction: column !important;
            width: 52% !important;
            color: var(--bs-modal-color) !important;
            pointer-events: auto !important;
            background-color: var(--bs-modal-bg) !important;
            background-clip: padding-box !important;
            /* border: var(--bs-modal-border-width) solid var(--bs-modal-border-color); */
            border-radius: var(--bs-modal-border-radius) !important;
            outline: 0 !important;
        }

        section {
            max-width: 100% !important;
        }
    </style>
</head>

<body>

    <!-- ======= Header ======= -->



    <!-- ======= Sidebar ======= -->



    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Contact HOD</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Contact</li>
                    <li class="breadcrumb-item active">Contact HOD</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <!-- <p>ECharts Examples. You can check the <a href="https://echarts.apache.org/examples/en/index.html" target="_blank">official website</a> for more examples.</p> -->

        <section class="section">
            <div class="container">
                <!-- Search Form -->
                <div class="my-3">
                    <form id="search-form" onsubmit="handleSearchSubmit(event)" method="GET" action="doctors_info.php">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="search" name="search" placeholder="Search by Name, Qualification, Designation or Department">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary w-100" onclick="addNewProfile()"><span>+</span>Add</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Faculty Table -->
                <div id="content">
                    <table class="table table-bordered" border="1">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Qualification</th>
                                <th>Specialized In</th>
                                <th>Personal Message</th>
                                <th>Message</th>
                                <th>phone number</th>
                                <th>email</th>


                                <th>address</th>
                                <th>status</th>
                                <th>Total Experience</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $faculty): ?>
                                <tr>
                                    <td><img src="data:image/jpeg;base64,<?= $faculty['image'] ?>" alt="Faculty Image" width="100px"></td>
                                    <td><?= $faculty['name'] ?></td>
                                    <td><?= $faculty['qualification'] ?></td>
                                    <td><?= $faculty['specialized_in'] ?></td>
                                    <!-- <td><?= $faculty['about_doctor'] ?></td> -->
                                    <!-- about doctor starts -->
                                    <td id="message-<?= $faculty['id'] ?>" class="message-container">
                                        <?php
                                        $wordCount = str_word_count($faculty['about_doctor']);
                                        $shortMessage = limitWords($faculty['about_doctor']);
                                        if ($wordCount > 10) {
                                            // If the about_doctor exceeds 10 words, show the limited version initially
                                            echo '<span class="short-message">' . $shortMessage . '</span>';
                                            echo '<span class="full-message" style="display:none;">' . $faculty['about_doctor'] . '</span>';
                                            echo '<a href="javascript:void(0)" class="btn btn-link read-more" onclick="showFullMessage(' . $faculty['id'] . ')">Read More</a>';
                                        } else {
                                            // If the message does not exceed 10 words, show the full message
                                            echo $faculty['message'];
                                        }
                                        ?>
                                    </td>

                                    <!-- <td><?= $faculty['message'] ?></td> -->
                                    <!-- <td>
                                     <?= limitWords($faculty['message']) ?>
                                    <?php if (str_word_count($faculty['message']) > 10): ?>
                                        <a href="#" class="btn btn-link" onclick="showFullMessage(<?= $faculty['id'] ?>)">Read More</a>
                                    <?php endif; ?>
                                </td> -->
                                    <!-- <td id="message-<?= $faculty['id'] ?>">
                                                <?php
                                                $wordCount = str_word_count($faculty['message']);

                                                if ($wordCount > 10) {
                                                    // If the message exceeds 50 words, show the limited version initially
                                                    echo limitWords($faculty['message']);
                                                    echo '<a href="#" class="btn btn-link" onclick="showFullMessage(' . $faculty['id'] . ')">Read More</a>';
                                                } else {
                                                    // If the message does not exceed 50 words, show the full message
                                                    echo $faculty['message'];
                                                }
                                                ?>
                                            </td> -->
                                    <td id="message-<?= $faculty['id'] ?>" class="message-container">
                                        <?php
                                        $wordCount = str_word_count($faculty['message']);
                                        $shortMessage = limitWords($faculty['message']);
                                        if ($wordCount > 10) {
                                            // If the message exceeds 10 words, show the limited version initially
                                            echo '<span class="short-message">' . $shortMessage . '</span>';
                                            echo '<span class="full-message" style="display:none;">' . $faculty['message'] . '</span>';
                                            echo '<a href="javascript:void(0)" class="btn btn-link read-more" onclick="showFullMessage(' . $faculty['id'] . ')">Read More</a>';
                                        } else {
                                            // If the message does not exceed 10 words, show the full message
                                            echo $faculty['message'];
                                        }
                                        ?>
                                    </td>



                                    <td><?= $faculty['phone'] ?></td>
                                    <td><?= $faculty['email'] ?></td>

                                    <td><?= $faculty['address'] ?></td>
                                    <!-- Toggle button for status -->
                                    <td>
                                        <label class="switch">
                                            <input type="checkbox" class="status-toggle" data-id="<?= $faculty['id'] ?>" <?= $faculty['status'] == 1 ? 'checked' : '' ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td><?= $faculty['total_experience'] ?></td>
                                    <td style="display: flex; gap: 5px;">
                                        <button class="btn btn-primary" onclick="editFaculty(<?= $faculty['id'] ?>)"> <i class="bi bi-pencil-square"></i>EDIT</button>
                                        <a href="update_doctors_info.php?delete=<?= $faculty['id'] ?>"
                                            class="btn btn-danger"
                                            onclick="deleteProfile(event, <?= $faculty['id'] ?>)">
                                            <i class="bi bi-trash3-fill"></i>DELETE
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>


                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&items_per_page=<?= $itemsPerPage ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>

                </div>


                <!-- Modal for Uploading Image -->
                <!-- Modal HTML -->
                <div id="uploadfile" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal()">&times;</span>
                        <form id="facultyForm" method="POST" enctype="multipart/form-data" onsubmit="return handleFormSubmit(event)">
                            <input type="hidden" name="id" id="id">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="qualification">Qualification:</label>
                                <input type="text" class="form-control" id="qualification" name="qualification" required>
                            </div>
                            <div class="form-group">
                                <label for="designation">Specialized In:</label>
                                <input type="text" class="form-control" id="designation" name="designation" required>
                            </div>

                            <div class="form-group">
                                <label for="department">About doctor:</label>
                                <input type="text" class="form-control" id="department" name="department" required>
                            </div>

                            <div class="form-group">
                                <label for="department_id">total Experience:</label>
                                <input type="text" class="form-control" id="department_id" name="department_id" required>
                            </div>
                            <!-- <label for="department">Department (B.Tech Branches):</label>
                    <select id="department" name="department" required>
                        <option value="">Select Department</option>
                        <option value="CSE">Computer Science</option>
                        <option value="MEC">Mechanical Engineering</option>
                        <option value="EEE">Electrical Engineering</option>
                        <option value="CIV">Civil Engineering</option>
                        <option value="ECE">Electronics and Communication</option>
                        <option value="AI_ML">Artificial Intelligence & Machine Learning</option>
                        <option value="DSE">Data Science Engineering</option>
                             <option value="ENGLISH">ENGLISH - HUMANITIES</option>
                               <option value="PHYSICS">PHYSICS</option>
                               <option value="CHEMISTRY">CHEMISTRY</option>
                               <option value="MATHS">MATHS</option>
                    </select><br><br>

                    <label for="department_id">Department ID:</label>
                    <select id="department_id" name="department_id" required>
                        <option value="">Select Department ID</option>
                        <option value="1">CSE</option>
                        <option value="2">EEE</option>
                        <option value="3">ECE</option>
                        <option value="4">MEC</option>
                        <option value="5">CIV</option>
                        <option value="6">AI & ML</option>
                        <option value="7">DS</option>
                         <option value="8">ENGLISH</option>
                        <option value="9">PHYSICS</option>
                         <option value="10">CHEMISTRY</option>
                          <option value="11">MATHS</option>
                    </select><br><br> -->

                            <div class="form-group">
                                <label for="message">Message:</label>
                                <textarea class="form-control" id="message" name="message" required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number:</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="address">address:</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>
                            <div class="form-group">
                                <label for="status">status:</label>
                                <input type="text" class="form-control" id="status" name="status" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Image:</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                            <div class="form-group" style="display: flex; justify-content: center;margin-top: 10px;">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>




            </div>
        </section>

    </main>
    <!-- End #main -->

    <!-- ======= Footer ======= -->



    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <script>
        // Check if the user is logged in


        // function showFullMessage(id) {
        //     // Get the message element
        //     const messageElement = document.getElementById('message-' + id);

        //     // Get the full message text (from the PHP variable)
        //     const fullMessage = "<?= addslashes($faculty['message']) ?>"; // Get the full message dynamically

        //     // Update the content to show full message and a "View Less" link
        //     messageElement.innerHTML = fullMessage + ' <a href="#" class="btn btn-link" onclick="showLessMessage(' + id + ')">View Less</a>';
        // }

        // function showLessMessage(id) {
        //     // Get the message element
        //     const messageElement = document.getElementById('message-' + id);

        //     // Get the message text (PHP function limitWords is used here to shorten it back)
        //     const limitedMessage = "<?= limitWords($faculty['message']) ?>"; // Limit the message again

        //     // Update the content to show limited message and a "Read More" link
        //     messageElement.innerHTML = limitedMessage + ' <a href="#" class="btn btn-link" onclick="showFullMessage(' + id + ')">Read More</a>';
        // }
    </script>
    <script>
        function deleteProfile(event, facultyId) {
            event.preventDefault(); // Prevent the default link behavior

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with the deletion
                    window.location.href = `update_doctors_info.php?delete=${facultyId}`;
                } else {
                    // If the user cancels, do nothing
                    Swal.fire('Cancelled', 'The profile was not deleted.', 'error');
                }
            });
        }

        function addNewProfile() {
            // Clear the form fields
            document.getElementById('id').value = "";
            document.getElementById('name').value = "";
            document.getElementById('qualification').value = "";
            document.getElementById('message').value = "";
            document.getElementById('designation').value = "";
            document.getElementById('department').value = "";
            document.getElementById('department_id').value = "";
            document.getElementById('email').value = "";
            document.getElementById('phone').value = "";
            document.getElementById('image').value = "";
            document.getElementById('address').value = "";
            document.getElementById('status').value = ""; // Reset image input

            openModal()
            // uploadimagefunction(1); // Open the modal
        }


        // Function to open the modal
        function openModal() {
            document.getElementById('uploadfile').style.display = 'block';
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('uploadfile').style.display = 'none';
        }

        // Function to toggle modal visibility (open/close)
        function uploadimagefunction(action) {
            if (action === 1) {
                closeModal(); // Close the modal if action is 1
            } else {
                openModal(); // Open the modal if action is 0
            }
        }

        // Close the modal when the user clicks outside of the modal content
        window.onclick = function(event) {
            var modal = document.getElementById("uploadfile");
            if (event.target == modal) {
                closeModal();
            }
        }

        function editFaculty(id) {
            fetch(`update_doctors_info.php?id=${id}`)
                .then(response => {
                    console.log(response); // Log the full response object
                    return response.json(); // Parse the JSON from the response
                })
                .then(data => {
                    console.log(data);
                    console.log(data.qualification); // Log the actual data returned from JSON parsing
                    uploadimagefunction(0);
                    document.getElementById('id').value = data.id;
                    document.getElementById('name').value = data.name;
                    document.getElementById('qualification').value = data.qualification;
                    document.getElementById('designation').value = data.specialized_in;
                    document.getElementById('department').value = data.about_doctor;
                    document.getElementById('department_id').value = data.total_experience;
                    document.getElementById('message').value = data.message;
                    document.getElementById('phone').value = data.phone;
                    document.getElementById('email').value = data.email;
                    document.getElementById('address').value = data.address;
                    document.getElementById('status').value = data.status;
                    uploadimagefunction(0);
                })
                .catch(error => console.error('Error fetching data:', error));

        }

        function handleFormSubmit(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('facultyForm'));
            fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // alert("data saved aithinira pumma");
                    // closemodal()
                    closeModal();
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: "Successfullt Data send to the database",
                        // showConfirmButton: true,  // Show "OK" button
                        timer: 3000, // The alert will disappear after 3 seconds
                        timerProgressBar: true // Show a progress bar for the timer
                    }).then(function() {
                        setTimeout(function() {
                            window.location = "doctors_info.php?"; // Optional: Redirect after 3 seconds
                        }, 100); // 3000 milliseconds = 3 seconds

                    });
                    // location.reload();
                })
                .catch(error => console.error('Error:', error));
        }

        function handleSearchSubmit(event) {
            event.preventDefault();
            const searchQuery = document.getElementById('search').value;
            window.location.href = `doctors_info.php?search=${searchQuery}&page=1`;
        }

        function uploadimagefunction(show) {
            const uploadFileDiv = document.getElementById('uploadfile');
            uploadFileDiv.style.display = show ? 'block' : 'block';
        }

        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const id = this.getAttribute('data-id');
                const status = this.checked ? 1 : 0; // Set status to 1 or 0

                // Send AJAX request to update the status
                fetch('update_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: id,
                            status: status
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {

                            console.log('Status updated successfully');
                            location.reload();
                        } else {
                            console.error('Failed to update status');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });


        // function uploadimagefunction(id) {
        //     if (modal.style.display === "block") {
        //         modal.style.display = "none"; // If it's already open, close it
        //     } else {
        //         modal.style.display = "block"; // If it's closed, open it
        //     }
        //     if (id !== 2) {
        //         document.getElementById('id').value = '';
        //         document.getElementById('name').value = '';
        //         document.getElementById('qualification').value = '';
        //         document.getElementById('designation').value = '';
        //         document.getElementById('department').value = '';
        //         document.getElementById('department_id').value = '';
        //     }
        // }
        // Close the modal when clicking the "X" button
        // closeBtn.onclick = function () {
        //     modal.style.display = "none";
        // }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
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

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/apexcharts/apexcharts.min.js">
        < /scrip> <
        script src = "../assets/vendor/bootstrap/js/bootstrap.bundle.min.js" >
    </script>
    <script src="../assets/vendor/chart.js/chart.umd.js"></script>
    <script src="../assets/vendor/echarts/echarts.min.js"></script>
    <script src="../assets/vendor/quill/quill.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="../assets/js/main.js"></script>

</body>

</html>