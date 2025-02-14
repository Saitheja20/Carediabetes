<?php
// Start the session to store messages
// session_start();

// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_carehospital', 'Kakatiya1234$', 'u629694569_carediabetesce');
$id = (int) $_GET['id'];

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
} else {
   // echo "connected " . $id;  // Output the connection status and the ID
}

if ($id) {
    $id = (int) $_GET['id'];  // You can skip this as you already cast $id earlier
    $query = "SELECT  id,header,main_points,description_of_service, status FROM services WHERE id = $id";  // Fixed 'quary' to 'query'

    // Execute the query
    $result = mysqli_query($con, $query);

    // Check if the query was successful and if there are rows returned
    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the row as an associative array
        $data = mysqli_fetch_assoc($result);
//echo "data fetched";
        // Return the data as a JSON response
        echo json_encode($data);
        // echo ($data);
    } else {
        // If no results, return null
        //echo json_encode(null);
        echo "No data found";
    }

    // Close the database connection
    mysqli_close($con);

    exit;
}

if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];

    // Make sure the ID is valid before proceeding
    if ($deleteId > 0) {
        $query = "DELETE FROM services WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $deleteId);

        if ($stmt->execute()) {
            // Return JSON response indicating success
            echo json_encode(['success' => true]);
        } else {
            // Return JSON response indicating failure
            echo json_encode(['success' => false, 'message' => "Error deleting the record: " . $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid ID"]);
    }
    exit; // End the script after handling the request
}


?>
