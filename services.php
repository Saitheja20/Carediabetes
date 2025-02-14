<?php
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_carehospital', 'Kakatiya1234$', 'u629694569_carediabetesce');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    // echo "connection successful";
}

$query = "SELECT * FROM services";
$result = mysqli_query($con, $query);
if (!$result) {
    die("Invalid query: " . mysqli_error($con));
} else {

    //betsy code for table view of services 
    //  echo "<table border='1' class='table table-striped'>
    // <tr>
    // <th>Service ID</th>
    // <th>Service Image</th>
    // <th>Service Name</th>
    // <th>Service Points</th>
    // <th>Description Of Service</th>

    // </tr>";
    // while($row = mysqli_fetch_array($result))
    // {
    //      if (!empty($row['image_for_service'])) {
    //         $row['image_for_service'] = base64_encode($row['image_for_service']);
    //     } else {
    //         $row['image_for_service'] = null;
    //     }
    //     echo "<tr>";
    //     echo "<td>" . $row['id'] . "</td>";
    //        echo "<td><img src='data:image/jpeg;base64," . $row['image_for_service'] . "' alt='Service Image' width='200px'></td>";
    //     echo "<td>" . $row['header'] . "</td>";
    //     echo "<td>" . nl2br(htmlspecialchars(str_replace("\\n", "\n",$row['main_points']))) . "</td>";
    //           echo "<td>" . nl2br(htmlspecialchars(str_replace("\\n", "\n",$row['description_of_service']))) . "</td>";
    //     echo "</tr>";
    // }
    // echo "</table>";
    echo "";

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['image_for_service'])) {
            $row['image_for_service'] = base64_encode($row['image_for_service']);
        } else {
            $row['image_for_service'] = null;
        }
        $data[] = $row;
    }
}

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
//     $address = $_POST['address'];
//     $status = $_POST['status'];

//     // Check if the image was uploaded without errors

//     // Check if an image was uploaded without errors
//     $imageData = null;
//     if (isset($_FILES['image']['error']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
//         $imageData = file_get_contents($_FILES['image']['tmp_name']);
//     } elseif (isset($_FILES['image']['error']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
//         // Handle actual upload errors
//         echo "Error uploading file: " . $_FILES['image']['error'];
//         exit;
//     }

//     // Proceed with update or insert
//     if ($id) {
//         // Update existing record
//         if ($imageData !== null) {
//             // Update with image
//             $stmt = $con->prepare("UPDATE doctors_data2 SET name=?, image=?, qualification=?, specialized_in=?, about_doctor=?, total_experience=?, message=?,phone=?,email=?,address=?,status=? WHERE id=?");
//             $null = NULL;
//             $stmt->bind_param("sbsssissssii", $name, $null, $qualification, $designation, $department, $department_id, $message, $phone, $email, $address, $status, $id);
//             $stmt->send_long_data(1, $imageData);
//         } else {
//             // Update without image
//             $stmt = $con->prepare("UPDATE doctors_data2 SET name=?, qualification=?, specialized_in=?, about_doctor=?, total_experience=?, message=?,phone=?,email=?,address=?,status=? WHERE id=?");
//             $stmt->bind_param("ssssissssii", $name, $qualification, $designation, $department, $department_id, $message, $phone, $email, $address, $status, $id);
//         }
//     } else {
//         // Insert new record
//         // if ($imageData === null) {
//         //     echo "Image is required for new entries";
//         //     exit;
//         // }
//         $stmt = $con->prepare("INSERT INTO doctors_data2 (name, image, qualification, specialized_in, about_doctor, total_experience, message,phone,email,address,status) VALUES (?, ?, ?,?,?,?, ?, ?, ?, ?, ?)");
//         $null = NULL;
//         $stmt->bind_param("sbsssissssi", $name, $null, $qualification, $designation, $department, $department_id, $message, $phone, $email, $address, $status);
//         $stmt->send_long_data(1, $imageData);
//     }

//     // Execute the statement
//     if ($stmt->execute()) {
//         echo '<script>
//             Swal.fire({
//                 icon: "success",
//                 title: "Success!",
//                 text: "Successfullt Data send to the database",
//                 // showConfirmButton: true,  // Show "OK" button
//                 // timer: 5000,  // The alert will disappear after 3 seconds
//                 // timerProgressBar: true  // Show a progress bar for the timer
//             }).then(function() {
//                 setTimeout(function() {
//                     window.location = "dep_faculty.php"; // Optional: Redirect after 3 seconds
//                 }, 5000); // Optional: Redirect after successful operation
//             });
//           </script>';
//         exit;
//     } else {
//         echo "Error: " . $stmt->error;
//     }
// }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image_for_service']) && isset($_FILES['image_for_service_details']) && isset($_FILES['banner_image_for_service'])) {
    // Get the form data
    $header = $_POST['header'];
    $main_points = $_POST['main_points'];
    $description_of_service = $_POST['description_of_service'];
    $status = $_POST['status'];
    // $date_of_creation = $_POST['date_of_creation'];
    // $modification_date = $_POST['modification_date'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    // Image uploads (service images and banner)
    $imageForServiceData = null;
    if (isset($_FILES['image_for_service']['error']) && $_FILES['image_for_service']['error'] === UPLOAD_ERR_OK) {
        $imageForServiceData = file_get_contents($_FILES['image_for_service']['tmp_name']);
    }

    $imageForServiceDetailsData = null;
    if (isset($_FILES['image_for_service_details']['error']) && $_FILES['image_for_service_details']['error'] === UPLOAD_ERR_OK) {
        $imageForServiceDetailsData = file_get_contents($_FILES['image_for_service_details']['tmp_name']);
    }

    $bannerImageForServiceData = null;
    if (isset($_FILES['banner_image_for_service']['error']) && $_FILES['banner_image_for_service']['error'] === UPLOAD_ERR_OK) {
        $bannerImageForServiceData = file_get_contents($_FILES['banner_image_for_service']['tmp_name']);
    }

    // Proceed with update or insert
    if ($id) {
        echo "id is " . $id;
        // Update existing record
        if ($imageForServiceData !== null || $imageForServiceDetailsData !== null || $bannerImageForServiceData !== null) {
            // Update with images
            $stmt = $con->prepare("UPDATE services SET header=?, image_for_service=?, image_for_service_details=?, main_points=?, description_of_service=?, banner_image_for_service=?, status=? WHERE id=?");
            $null = NULL;
            $stmt->bind_param("sbbssbii", $header, $null, $null, $main_points, $description_of_service, $null,  $status, $id);

            if ($imageForServiceData !== null) {
                $stmt->send_long_data(1, $imageForServiceData); // Send image for service
            }
            if ($imageForServiceDetailsData !== null) {
                $stmt->send_long_data(2, $imageForServiceDetailsData); // Send image for service details
            }
            if ($bannerImageForServiceData !== null) {
                $stmt->send_long_data(3, $bannerImageForServiceData); // Send banner image for service
            }
        } else {
            // Update without images
            echo "It's came to correct Destination";
              echo "id is " . $id;
            echo "the header for updating is " . $header;
            echo "the main points for updating is " . $main_points;
            echo "the description of service for updating is " . $description_of_service;
            $stmt = $con->prepare("UPDATE services SET header=?, main_points=?, description_of_service=?, status=? WHERE id=?");
            $stmt->bind_param("sssii", $header, $main_points, $description_of_service, $status, $id);
        }
    } else {
        // Insert new record
        $stmt = $con->prepare("INSERT INTO services (header, image_for_service, image_for_service_details, main_points, description_of_service, banner_image_for_service, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $null = NULL;
        $stmt->bind_param("sbssssi", $header, $null, $null, $main_points, $description_of_service, $null, $status);

        if ($imageForServiceData !== null) {
            $stmt->send_long_data(1, $imageForServiceData); // Send image for service
        }
        if ($imageForServiceDetailsData !== null) {
            $stmt->send_long_data(2, $imageForServiceDetailsData); // Send image for service details
        }
        if ($bannerImageForServiceData !== null) {
            $stmt->send_long_data(3, $bannerImageForServiceData); // Send banner image for service
        }
    }

    // Execute the statement
    // if ($stmt->execute()) {
    //     echo '<script>
    //         Swal.fire({
    //             icon: "success",
    //             title: "Success!",
    //             text: "Successfully data sent to the database",
    //         }).then(function() {
    //             setTimeout(function() {
    //                 window.location = "services_page.php"; // Optional: Redirect after 5 seconds
    //             }, 5000); // Optional: Redirect after successful operation
    //         });
    //       </script>';
    //     exit;
    // } else {
    //     echo "Error: " . $stmt->error;
    // }
      if ($stmt->execute()) {
        // Send success response as JSON
        $response = [
            'status' => 'success',
            'message' => 'Successfully data sent to the database',
            'redirect' => 'services.php',  // Optional: Include a redirect URL
        ];
        echo json_encode($response);  // Send the response as JSON
    } else {
        // Send error response as JSON
        $response = [
            'status' => 'error',
            'message' => 'Error: ' . $stmt->error,
        ];
        echo json_encode($response);  // Send the error message as JSON
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        a {
            text-decoration: none;
            color: black;
        }

        a:hover,
        card {
            transform: scale(1.1);
        }

        /* Add styles for modal and other elements */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            display: none;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 60%;
        }

        @media screen and (max-width: 600px) {
            .modal-content {
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
        }
            
        }

        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 30px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }
        .card-container {
    position: relative;
}

.card {
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.card-body {
    transition: opacity 0.3s ease;
}

.card-buttons {
    transition: opacity 0.3s ease, transform 0.3s ease;
    opacity: 0;
    transform: scale(0.8);  /* Initially smaller size */
    position: absolute; /* To position buttons inside the card */
    bottom: 10px; /* Positioning them near the bottom */
    left: 50%;
    transform: translateX(-50%) scale(0.8);
}

.card:hover {
    transform: scale(1.05); /* Scaling up the card on hover */
}

.card:hover .card-body {
    opacity: 1; /* Dimming the card body */
}

.card:hover .card-buttons {
    opacity: 1; /* Show the buttons */
    transform: translateX(-50%) scale(1); /* Center and scale up the buttons */
}


/* .card-buttons {
    position: absolute !important; 
    top: 10px !important;
    right: 10px !important;
    display: none !important;
}

.card:hover .card-buttons {
    display: block !important;
}

.card-buttons button {
    margin-left: 10px !important;
} */
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- SweetAlert2 JS -->
</head>

<body>

    <!-- <div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="slide">
                <div class="ser-card card-1">
                    <div class="row">
                        <?php foreach ($data as $row): ?>
                        <div class="col-lg-6 col-sm-12">
                            <div class="ser-icon">
                                <img src="data:image/jpeg;base64,<?= $row['image_for_service'] ?>" alt="Faculty Image" width="100px">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="ser-text">
                                <h5><?= htmlspecialchars($row['header']); ?></h5>
                                <p><?= nl2br(htmlspecialchars($row['main_points'])); ?></p>
                                <p><?= nl2br(htmlspecialchars($row['description_of_service'])); ?></p>

                                <div class="read">
                                    <a href="<?php echo $base_url; ?>erectile_dysfunction.php" class="btns btn-dark" tabindex="0">View More</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!-- <div class="col-lg-4 col-sm-12 mb-4"> Adjust column size as needed -->
                    <!-- Card for Adding New Entry -->
                    <!-- <div class="card" style="height: 100%; cursor: pointer;" onclick="openAddModal()">
                        
                        <div class="card-header text-center">
                            <h5 class="card-title">Add New Service</h5>
                        </div>
                    </div> -->
                   <div style="display: flex; justify-content: flex-end;margin: 10px;">
                        <button class="btn btn-primary" onclick="addNewCardService()">
                           <span style="font-size: large;font-weight: 900;">+</span> Add New Service</button>
                    </div>
                <!-- </div> -->
            </div>
        </div>



        <div class="row">
            <div class="col-lg-12">
                <div class="slide">
                    <div class="row">
                        <?php foreach ($data as $row): ?>
                            <div class="col-lg-4 col-sm-12 mb-4"> 
                             <!-- <button><span><i class="fa-solid fa-trash"></i></span></button>    -->
                            <!-- Adjust column size as needed -->
                                <!-- Pass two parameters (id and header) to getCardId -->
                                <span href="#" onclick="getCardId(<?= $row['id'] ?>, '<?= addslashes($row['header']) ?>', 'card-buttons-<?= $row['id'] ?>')">
                                    <div class="card" style="height: 100%;">
                                        <!-- Card Header with Image -->
                                        <img src="data:image/jpeg;base64,<?= $row['image_for_service'] ?>" class="card-img-top" alt="Service Image">

                                        <!-- Card Body with Main Points -->
                                        <div class="card-body">
                                            <h5 class="card-title"><?= nl2br(htmlspecialchars(str_replace("\\n", "\n", $row['header']))); ?></h5>
                                            <p class="card-text"><?= nl2br(htmlspecialchars(str_replace("\\n", "\n", $row['main_points']))); ?></p>
                                        </div>
                                         <div class="card-buttons" id="card-buttons-<?= $row['id'] ?>" style="display: none;margin: 10px auto;">
                                        <button class="btn btn-primary" onclick="editservices(<?= $row['id'] ?>)">Edit</button>
                                        <button class="btn btn-danger" onclick="deleteService(<?= $row['id'] ?>)">Delete</button>
                                    </div>
                                    </div>
                                   
                                </span>
                            </div>
                        <?php endforeach; ?>
                        <!-- temporary add new Row Card -->

                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- model Form For Update / Add New cards -->

    <div id="uploadfile" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <form id="serviceForm" method="POST" enctype="multipart/form-data" onsubmit="return handleFormSubmit(event)">
                <input type="hidden" name="id" id="id">

                <!-- Image for service -->
                <div class="form-group">
                    <label for="image_for_service">Image for Service:</label>
                    <input type="file" class="form-control" id="image_for_service" name="image_for_service" accept="image/*">
                </div>

                <!-- Image for service details -->
                <div class="form-group">
                    <label for="image_for_service_details">Image for Service Details:</label>
                    <input type="file" class="form-control" id="image_for_service_details" name="image_for_service_details" accept="image/*">
                </div>

                <!-- Header -->
                <div class="form-group">
                    <label for="header">Header:</label>
                    <input type="text" class="form-control" id="header" name="header" required>
                </div>

                <!-- Main points -->
                <div class="form-group">
                    <label for="main_points">Main Points:</label>
                    <textarea class="form-control" id="main_points" name="main_points" required></textarea>
                </div>

                <!-- Description of service -->
                <div class="form-group">
                    <label for="description_of_service">Description of Service:</label>
                    <textarea class="form-control" id="description_of_service" name="description_of_service" required></textarea>
                </div>

                <!-- Banner image for service -->
                <div class="form-group">
                    <label for="banner_image_for_service">Banner Image for Service:</label>
                    <input type="file" class="form-control" id="banner_image_for_service" name="banner_image_for_service" accept="image/*">
                </div>

                <!-- Date of creation -->
                <!-- <div class="form-group">
                <label for="date_of_creation">Date of Creation:</label>
                <input type="datetime-local" class="form-control" id="date_of_creation" name="date_of_creation" required>
            </div> -->

                <!-- Modification date -->
                <!-- <div class="form-group">
                <label for="modification_date">Modification Date:</label>
                <input type="datetime-local" class="form-control" id="modification_date" name="modification_date" required>
            </div> -->

                <!-- Status -->
                <div class="form-group">
                    <label for="status">Status:</label>
                    <input type="text" class="form-control" id="status" name="status" required>
                </div>

                <!-- Submit Button -->
                <div class="form-group" style="display: flex; justify-content: center; margin-top: 10px;">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function getCardId(id, header, buttonsId) {
            // Example: you can use alert or log the values to check if they are passed correctly
            console.log('Card ID: ' + id + ', Header: ' + header);
            // You can perform further actions like navigating to a new page or updating a section of the page.

            // document.getElementById('openFormButton').addEventListener('click', function() {
            // document.getElementById('uploadfile').style.display = 'block';

            //});
            // document.getElementById('card-buttons').style.display = 'block';
              var allButtons = document.querySelectorAll('.card-buttons');
                allButtons.forEach(function(buttons) {
                    buttons.style.display = 'none';
                });

                // Show the buttons for the clicked card
                document.getElementById(buttonsId).style.display = 'block';
        }

        function editservices(id){
            fetch(`services_data.php?id=${id}`)
                .then(response => {
                    console.log(response); // Log the full response object
                    return response.json(); // Parse the JSON from the response
                })
                .then(data => {
                    console.log(data);
                    // console.log(data.qualification); // Log the actual data returned from JSON parsing
                    // uploadimagefunction(0);
                    document.getElementById('id').value = data.id;
                    document.getElementById('header').value = data.header;
                    document.getElementById('main_points').value = data.main_points;
                    document.getElementById('description_of_service').value = data.description_of_service;
                    document.getElementById('status').value = data.status;
                    document.getElementById('uploadfile').style.display = 'block';
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // delete function
// function deleteService(id) {
//     // Ask for confirmation before deleting
//     if (confirm("Are you sure you want to delete this service? Saiteja")) {
//         fetch(`services_data.php?delete=${id}`, {
//             method: 'GET', // Use GET or POST, but GET is more common in this case for simplicity
//         })
//         .then(response => response.json()) // Assuming your PHP script returns a JSON response
//         .then(data => {
//             // Check if the deletion was successful
//             if (data.success) {
//                 Swal.fire({
//                     icon: 'success',
//                     title: 'Deleted!',
//                     text: 'The service has been deleted.',
//                     timer: 2000,
//                     timerProgressBar: true
//                 }).then(() => {
//                     // Redirect after deletion
//                     window.location.href = "services_page.php"; // Redirect to your desired page
//                 });
//             } else {
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Error!',
//                     text: 'There was an error deleting the service.',
//                 });
//             }
//         })
//         .catch(error => {
//             console.error('Error:', error);
//             Swal.fire({
//                 icon: 'error',
//                 title: 'Error!',
//                 text: 'There was a problem with the deletion request.',
//             });
//         });
//     }
// }


function deleteService(id) {
    // Use SweetAlert2 to confirm deletion
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
            // Proceed with deletion if confirmed
            fetch(`services_data.php?delete=${id}`, {
                method: 'GET', // Use GET or POST, but GET is more common in this case for simplicity
            })
            .then(response => response.json()) // Assuming your PHP script returns a JSON response
            .then(data => {
                // Check if the deletion was successful
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'The service has been deleted.🗑️',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        // Redirect after deletion
                        window.location.href = "services.php"; // Redirect to your desired page
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'The service has been deleted.🗑️',
                         timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        // Redirect after deletion
                        window.location.href = "services.php"; // Redirect to your desired page
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'The service has been deleted.🗑️',
                     timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        // Redirect after deletion
                        window.location.href = "services.php"; // Redirect to your desired page
                    });
            });
        } else {
            // If canceled, show a message
            Swal.fire('Cancelled', 'The service was not deleted ❌.', 'info');
        }
    });
}

        function closeModal() {
            document.getElementById('uploadfile').style.display = 'none';
        }

        // function openAddNewModal() {
        function addNewCardService(){
            document.getElementById('id').value = '';
            document.getElementById('header').value = '';
            document.getElementById('main_points').value = '';
            document.getElementById('description_of_service').value = '';
            document.getElementById('status').value = '';
            document.getElementById('uploadfile').style.display = 'block';
        }

        // function handleFormSubmit(event) {
        //     event.preventDefault();
        //     const formData = new FormData(document.getElementById('serviceForm'));
        //     fetch('', {
        //             method: 'POST',
        //             body: formData
        //         })
        //         .then(response => response.text())
        //         .then(data => {
        //             // alert("data saved aithinira pumma");
        //             // closemodal()
        //             closeModal();
        //             Swal.fire({
        //                 icon: "success",
        //                 title: "Success!",
        //                 text: "Successfullt Data send to the database",
        //                 // showConfirmButton: true,  // Show "OK" button
        //                 timer: 3000, // The alert will disappear after 3 seconds
        //                 timerProgressBar: true // Show a progress bar for the timer
        //             }).then(function() {
        //                 setTimeout(function() {
        //                     window.location = "doctors_info.php?"; // Optional: Redirect after 3 seconds
        //                 }, 100); // 3000 milliseconds = 3 seconds

        //             });
        //             // location.reload();
        //         })
        //         .catch(error => console.error('Error:', error));
        // }

        function handleFormSubmit(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('serviceForm'));

    fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Expect a JSON response
        .then(data => {
            if (data.status === 'success') {
                // Success - Show a success message
                closeModal();
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: data.message+"🚀",
                    timer: 3000, // The alert will disappear after 3 seconds
                    timerProgressBar: true // Show a progress bar for the timer
                }).then(function() {
                    // Redirect after success (if provided in the response)
                    window.location = data.redirect || 'doctors_info.php'; // Default redirect
                });
            } else {
                // Error - Show an error message
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: "Data Updated Sucessfully🚀",
                      timer: 5000,
                        timerProgressBar: true
                    }).then(() => {
                        // Redirect after deletion
                        window.location.href = "services.php"; // Redirect to your desired page
                    });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Optionally, show a general error message to the user
            Swal.fire({
                icon: "success",
                title: "success!",
                text: "Data Updated Sucessfully🚀",
                 timer: 5000,
                        timerProgressBar: true
                    }).then(() => {
                        // Redirect after deletion
                        window.location.href = "services.php"; // Redirect to your desired page
                    });
        });
}

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>