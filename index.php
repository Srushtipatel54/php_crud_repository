<?php
// Include database connection FIRST
include 'connect.php';
$message = '';

// CSV Upload Logic

$deleteBadRows = "DELETE FROM studentstbl WHERE stusname = 'stusname' AND stuemail = 'stuemail'";
mysqli_query($conn, $deleteBadRows);
if (isset($_POST["upload"])) {
    if ($_FILES['Product_file']['name']) {
        $filename = explode(".", $_FILES['Product_file']['name']);
        $file_ext = strtolower(end($filename));

        // Validate CSV format
        if ($file_ext == "csv") {
            $handle = fopen($_FILES['Product_file']['tmp_name'], "r");

            // Skip CSV header row
            fgetcsv($handle);

            // Process CSV data
            while ($data = fgetcsv($handle)) {
                // Validate row data (ensure 7 columns)
                if (empty($data) || count($data) < 7) continue;

                // Trim and sanitize data
                $id = trim(mysqli_real_escape_string($conn, $data[0]));
                $stusname = mysqli_real_escape_string($conn, $data[1]);
                $stuemail = mysqli_real_escape_string($conn, $data[2]);
                $stuphone = mysqli_real_escape_string($conn, $data[3]);
                $stupassword = mysqli_real_escape_string($conn, $data[4]);
                $stugender = mysqli_real_escape_string($conn, $data[5]);
                $stuprofilepic = mysqli_real_escape_string($conn, $data[6]);

                // Check if ID exists (update or insert)
                $check = mysqli_query($conn, "SELECT id FROM studentstbl WHERE id = '$id'");
                if (mysqli_num_rows($check) > 0) {
                    $sql = "UPDATE studentstbl SET 
                            stusname='$stusname', 
                            stuemail='$stuemail', 
                            stuphone='$stuphone', 
                            stupassword='$stupassword', 
                            stugender='$stugender', 
                            stuprofilepic='$stuprofilepic' 
                            WHERE id='$id'";
                } else {
                    $sql = "INSERT INTO studentstbl (id, stusname, stuemail, stuphone, stupassword, stugender, stuprofilepic) 
                            VALUES ('$id', '$stusname', '$stuemail', '$stuphone', '$stupassword', '$stugender', '$stuprofilepic')";
                }
                mysqli_query($conn, $sql) or die("Error: " . mysqli_error($conn));
            }
            fclose($handle);
            header("Location: index.php?updation=1");
            exit();
        } else {
            $message = '<label class="text-danger">Only CSV files are allowed.</label>';
        }
    } else {
        $message = '<label class="text-danger">Please select a CSV file.</label>';
    }
}

// Fetch data for display
$sql = "SELECT * FROM studentstbl";
$result = mysqli_query($conn, $sql);
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5 p-4 bg-warning bg-opacity-25 w-60 rounded">
        <h4 class="mb-4">Upload CSV File to Database</h4>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Select CSV File</label>
                <input type="file" name="Product_file" class="form-control" accept=".csv">
            </div>
            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
        </form>
        <?php echo $message; ?>

        <h2 class="text-center mt-4">Student Records</h2>
        <div class="table-responsive"> <!-- Fixed typo -->
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Password</th>
                        <th>Gender</th>
                        <th>Profile Pic</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['stusname']}</td>
                            <td>{$row['stuemail']}</td>
                            <td>{$row['stuphone']}</td>
                            <td>{$row['stupassword']}</td>
                            <td>{$row['stugender']}</td>
                           
                           <td><img src='uploads/" . (!empty($row['stuprofilepic']) ? $row['stuprofilepic'] : 'abc2.png
                           ') . "' width='60'></td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>