<!-- //open php
include('connect.php');
if (isset($_POST['insert_cal'])) {
    $cal_title = $_POST['class_title'];
    //select data from database
    $select_query = "Select * from class where class_title='$cal_title'";
    // $result = mysqli_query($con, $insert_query);
    // $number = mysqli_num_rows($result_select);
    $result_select = mysqli_query($con, $select_query);
    $number = mysqli_num_rows($result_select);

    if ($number > 0) {
        echo "<script>alert('This category already in database')</script>";
    } else {
        $insert_query = "INSERT INTO class (class_title	) VALUES ('class_title	')";

        $result = mysqli_query($con,  $insert_query);
        if ($result) {
            echo "<script>alert('Category has been inserted successfully')</script>";
        }
    }
}


?> //close -->

<!-- 
<h2 class="text-center">Insert class</h2>

<form action="" method="POST" class="mb-2">
    <div class="input-group mb-3  bg-opacity-25 w-50 rounded">
        <span class="input-group-text bg-info" id="basic-addon1"><i class="fa-solid fa-receipt"></i></span>
        <input type="text" class="form-control" name="class_title" placeholder="insert category" aria-label="Username" aria-describedby="basic-addon1">
    </div>
    <div class="input-group w-10 mb-2 m-auto">

        <input type="submit" class="bg-info border-0 p-2 my-3" name="insert_cal" value="Insert Categories" placeholder="insert class">

    </div>
</form> -->

//hello srushti

<!-- php
include 'connect.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_title = trim($_POST['class_title']);

    // Validate class title
    if (empty($class_title)) {
        $error = "Class title is required";
    } else {
        // Check for duplicate
        $check_stmt = $conn->prepare("SELECT class_id FROM class WHERE class_title = ?");
        $check_stmt->bind_param("s", $class_title);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "Class already exists";
        } else {
            // Insert new class
            $insert_stmt = $conn->prepare("INSERT INTO class (class_title) VALUES (?)");
            $insert_stmt->bind_param("s", $class_title);

            if ($insert_stmt->execute()) {
                $success = "Class added successfully!";
            } else {
                $error = "Error adding class: " . $conn->error;
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}

// Get existing classes
$classes = [];
$result = $conn->query("SELECT * FROM class ORDER BY class_id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}
$conn->close();
?> -->


<?php
include 'connect.php';

$class_id = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
$students = [];
$class_title = '';

if ($class_id > 0) {
    // Get class title
    $class_query = "SELECT class_title FROM class WHERE class_id = $class_id";
    $class_result = mysqli_query($conn, $class_query);
    if ($class_result && mysqli_num_rows($class_result) > 0) {
        $class_row = mysqli_fetch_assoc($class_result);
        $class_title = $class_row['class_title'];
    }

    // Get students
    $student_query = "SELECT * FROM studentstbl WHERE class_id = $class_id";
    $student_result = mysqli_query($conn, $student_query);
    if ($student_result && mysqli_num_rows($student_result) > 0) {
        while ($row = mysqli_fetch_assoc($student_result)) {
            $students[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Students by Class ID</title>
    <!-- Bootstrap CSS Only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1000px;
        }

        .student-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="my-4">Student data</h1>

        <div class="form-container mb-4">
            <form method="post">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="class_id" class="form-label">Enter Class ID</label>
                        <input type="number" class="form-control" name="class_id" id="class_id"
                            min="1" required value="<?= $class_id > 0 ? $class_id : '' ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-info w-100">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($class_id > 0): ?>
            <div class="card">
                <div class="card-header bg-light text-black">
                    <h4 class="mb-0">
                        <?= !empty($class_title) ? "Class: $class_title" : "Class ID: $class_id" ?>
                    </h4>
                </div>

                <?php if (!empty($students)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Profile</th>
                                    <th>Class Id</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['id']) ?></td>
                                        <td><?= htmlspecialchars($student['stusname']) ?></td>
                                        <td><?= htmlspecialchars($student['stuemail']) ?></td>
                                        <td><?= htmlspecialchars($student['stuphone']) ?></td>
                                        <td><?= htmlspecialchars($student['stugender']) ?></td>
                                        <td>
                                            <?php if (!empty($student['stuprofilepic'])): ?>
                                                <img src="uploads/<?= htmlspecialchars($student['stuprofilepic']) ?>"
                                                    class="student-img" alt="Profile">
                                            <?php else: ?>
                                                <span class="text-muted">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($student['class_id']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-muted">
                        <?= count($students) ?> students record
                    </div>
                <?php else: ?>
                    <div class="card-body">
                        <div class="alert alert-light text-black mb-0">No student in class</div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>







<!-- <!DOCTYPE html>
<html lang=" en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>class</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

        </head>

        <body>
            <form>
                <div class="container mt-5 p-4 bg-warning bg-opacity-25 w-50 rounded">
                    <h4 class="mb-4">Students Registration Form</h4>

                    <div class="mb-3">
                        <label class="form-label"><strong>class_title</strong></label>
                        <input type="text" name="title" class="form-control" placeholder="Enter class title" autocomplete="off" required>

                    </div>
                </div>

            </form>
        </body>

        </html> -->