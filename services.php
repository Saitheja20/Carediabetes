<?php

$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_carehospital', 'Kakatiya1234$', 'u629694569_carediabetesce');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}else
{
    echo "connection successful";
}


$query = "SELECT * FROM services";
$result = mysqli_query($con, $query);
if (!$result) {
    die("Invalid query: " . mysqli_error($con));
}else{
    echo "<table border='1' class='table table-striped'>
    <tr>
    <th>Service ID</th>
    <th>Service Image</th>
    <th>Service Name</th>
    <th>Service Points</th>
    <th>Description Of Service</th>
    
    </tr>";
    while($row = mysqli_fetch_array($result))
    {
         if (!empty($row['image_for_service'])) {
            $row['image_for_service'] = base64_encode($row['image_for_service']);
        } else {
            $row['image_for_service'] = null;
        }
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
           echo "<td><img src='data:image/jpeg;base64," . $row['image_for_service'] . "' alt='Service Image' width='200px'></td>";
        echo "<td>" . $row['header'] . "</td>";
        echo "<td>" . nl2br(htmlspecialchars(str_replace("\\n", "\n",$row['main_points']))) . "</td>";
              echo "<td>" . nl2br(htmlspecialchars(str_replace("\\n", "\n",$row['description_of_service']))) . "</td>";
        echo "</tr>";
    }

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
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
</head>
<body>
<!-- <h1>Hello World</h1> -->

<div class="container">
    <div class="row">
        <div class="col-lg-12">
             <div class="slide">
                    <div class="ser-card card-1">
                        <div class="row">
                              <?php foreach ($data as $row): ?>
                            <div class="col-lg-6 col-sm-12">
                             <div class="ser-icon">
                            <img src="<?php echo $base_url; ?>assets/images/inner/era-treatment.webp" class="img-fluid icon-img" alt="service-icon">

                        </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                        <div class="ser-text">
                            <h5><?= $row['header'] ?></h5>
                            <p>Sex problems are common in diabetes ,Affecting upto 50% of people over the age of 40 years.</p>
                            <p>Blood vessel Damage in People with Diabetes can to nerve damage and Decreased Harmones Leading to Sex Problams.</p>
                            <p>Sex problems is like all Diseases it is Treatable.</p>
                            <p>There is Treatment for this at Care Diabetes Centre.</p>

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
</div>



     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>