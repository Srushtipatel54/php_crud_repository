<?php
include 'connect.php';

// Get existing classes for dropdown
$classes = [];
$classResult = $conn->query("SELECT * FROM class");
if ($classResult) {
    while ($row = $classResult->fetch_assoc()) {
        $classes[] = $row;
    }
}

$id = $_GET['id'];
$sql = "SELECT * FROM studentstbl WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $class_id = $_POST['class_id']; // Added class_id

    // Image handling
    $image = $row['stuprofilepic'];
    if ($_FILES['image']['name']) {
        // Delete old image
        if (file_exists("uploads/" . $row['stuprofilepic'])) {
            unlink("uploads/" . $row['stuprofilepic']);
        }

        // Upload new image
        $image = uniqid("IMG_") . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$image");
    }

    // Update query with class_id
    $stmt = $conn->prepare("UPDATE studentstbl SET 
        stusname=?, 
        stuemail=?, 
        stuphone=?, 
        stupassword=?, 
        stugender=?, 
        stuprofilepic=?, 
        class_id=?
        WHERE id=?");

    $stmt->bind_param(
        "ssssssii",
        $name,
        $email,
        $mobile,
        $password,
        $gender,
        $image,
        $class_id,
        $id
    );

    if ($stmt->execute()) {
        header('Location: display.php');
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5 p-4 bg-warning bg-opacity-25 w-50 rounded">
        <h4 class="mb-4">Edit Student Record</h4>
        <form method="post" enctype="multipart/form-data">
            <!-- Existing fields -->
            <div class="mb-3">
                <label class="form-label"><strong>Student Name</strong></label>
                <input type="text" name="name" class="form-control" value="<?= $row['stusname'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label"><strong>Student Email</strong></label>
                <input type="email" name="email" class="form-control"
                    value="<?= $row['stuemail'] ?>" required>

            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Mobile Number</strong></label>
                <input type="text" name="mobile" class="form-control"
                    value="<?= $row['stuphone'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label"><strong>Password</strong></label>
                <input type="text" name="password" class="form-control"
                    value="<?= $row['stupassword'] ?>" required>

            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Gender</strong></label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="m"
                        <?= ($row['stugender'] ?? '') === 'm' ? 'checked' : '' ?> required>
                    <label class="form-check-label">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="f"
                        <?= ($row['stugender'] ?? '') === 'f' ? 'checked' : '' ?>>
                    <label class="form-check-label">Female</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="o"
                        <?= ($row['stugender'] ?? '') === 'o' ? 'checked' : '' ?>>
                    <label class="form-check-label">Others</label>
                </div>
                <div class="text-danger"><?= $errors['gender'] ?? '' ?></div>
            </div>

            <!-- Add class selection dropdown -->
            <div class="mb-3">
                <label class="form-label"><strong>Class</strong></label>
                <select name="class_id" class="form-select" required>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['class_id'] ?>"
                            <?= ($row['class_id'] == $class['class_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($class['class_title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>







            <div class="mb-3">
                <label class="form-label"><strong>Upload Photo</strong></label>
                <input type="file" name="image" class="form-control" value="<?= $row['stuprofilepic'] ?>" required>

            </div>

            <!-- Rest of the form remains the same -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</body>

</html>






















<!-- php
include 'connect.php';

$id = $_GET['id'];
$sql = "SELECT * FROM studentstbl WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];

    $password = $_POST['password'];
    $gender = isset($_POST['gender']) ? $_POST['gender'] : 'gender';
      //$class_id = $_POST['class_id']; 

    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $temp_name = $_FILES['image']['tmp_name'];
        $folder = "uploads/" . $image;
        move_uploaded_file($temp_name, $folder);
    } else {
        $image = $row['stuprofilepic'];
    }

    // $sql = "UPDATE studentstbl SET stusname='$name',stuemail='$email',stuphone='$mobile', stugender=$gender WHERE Id=$id";
    $sql = "UPDATE studentstbl SET stusname='$name', stuemail='$email', stuphone='$mobile',stupassword='$password', stugender='$gender', stuprofilepic='$image' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header('Location:display.php');
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?> -->
<!-- 
//my update -->

<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>userphp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5 p-4 bg-warning bg-opacity-25 w-50 rounded">
        <h4 class="mb-4">students Registration Form</h4>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label"><strong>students Name</strong></label>
                <input type="text" name="name" class="form-control" placeholder="Enter name" autocomplete="off" value="<?= $row['stusname'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>students emailid</strong></label>
                <input type="text" name="email" class="form-control" placeholder="Enter email" autocomplete="off" value="<?= $row['stuemail'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="8" maxlength="8" value="<?= $row['stupassword'] ?>" required>

            </div>
            <div class="mb-3">
                <label class="form-label"><strong>students MobileNo</strong></label>
                <input type="text" name="mobile" class="form-control" placeholder="Enter MobileNo" autocomplete="off" value="<?= $row['stuphone'] ?>" required>
            </div>


            <!-- <div class="genderContainer">
                <label><strong>Gender</strong></label>
                <input class="gender1" type="radio" name="gender" value="<?= $row['stugender'] ?>" required>Male
                <input class="gender1" type="radio" name="gender" value="<?= $row['stugender'] ?>">Female
                <input class="gender1" type="radio" name="gender" value="<?= $row['stugender'] ?>"> Others
            </div> -->
<!-- <div class="genderContainer">
    <input class="gender1" type="radio" name="gender" value="m" <?= ($row['stugender'] == 'm') ? 'checked' : '' ?>> Male
    <input class="gender1" type="radio" name="gender" value="f" <?= ($row['stugender'] == 'f') ? 'checked' : '' ?>> Female
    <input class="gender1" type="radio" name="gender" value="o" <?= ($row['stugender'] == 'o') ? 'checked' : '' ?>> Others
</div>

<div class="mb-3">
    <label class="form-label"><strong>Upload image</strong></label>
    <input type="file" name="image" class="form-control" autocomplete="off" required>
    <img src="uploads/<?= $row['stuprofilepic'] ?>" width="100" class="mt-2">
</div>
<div class="text-center">
    <button type="submit" class="btn btn-primary">Submit</button>
</div>
</form>
</div>

</body>

</html>  -->












<!-- 

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5 p-4 bg-warning bg-opacity-25 rounded">
        <h4 class="mb-4">Edit User</h4>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label"><strong>Name</strong></label>
                <input type="text" name="name" class="form-control" value="<?= $row['Name'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Email</strong></label>
                <input type="email" name="email" class="form-control" value="<?= $row['Email'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Mobile</strong></label>
                <input type="text" name="mobile" class="form-control" value="<?= $row['Mobile'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Password</strong></label>
                <input type="password" name="password" class="form-control" value="<?= $row['Passwords'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Date</strong></label>
                <input type="date" name="date" class="form-control" value="<?= $row['date'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>State</strong></label>
                <select name="state" class="form-select" required>
                    <option value="Gujarat" <?= $row['state'] == 'Gujarat' ? 'selected' : '' ?>>Gujarat</option>
                    <option value="Maharashtra" <?= $row['state'] == 'Maharashtra' ? 'selected' : '' ?>>Maharashtra</option>
                    <option value="Rajasthan" <?= $row['state'] == 'Rajasthan' ? 'selected' : '' ?>>Rajasthan</option>
                    <option value="Panjab" <?= $row['state'] == 'panjab' ? 'selected' : '' ?>>Panjab</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Image</strong></label><br>
                <input type="file" name="image" class="form-control">
                <img src="uploads/<?= $row['Image'] ?>" width="100" class="mt-2">
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-4">Update</button>
            </div>
        </form>
    </div>
</body>

</html> -->

























<!-- //open php
include 'connect.php';

if (!isset($_GET['updatesids'])) {
    die("❌ Error: No user ID provided.");
}

$id = $_GET['updatesids'];

$sql = "SELECT * FROM crud WHERE id = $id";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

$name = $row['names'];
$email = $row['email'];
$mobile = $row['mobile'];
$password = $row['password'];

if (isset($_POST['submit'])) {
    $name = $_POST['namey'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['psw'];

    $sql = "UPDATE crud SET names = '$name', email = '$email', mobile = '$mobile', password = '$password' WHERE id = $id";
    $result = mysqli_query($con, $sql);

    if ($result) {
        header('location:display.php');
        exit();
    } else {
        die("❌ Error updating data: " . mysqli_error($con));
    }
}
//close php

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5 w-50 bg-warning-subtle">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label"><b>Name</b></label>
                <input type="text" class="form-control" name="namey" value="<?php echo $name; ?>" autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label"><b>Email</b></label>
                <input type="email" class="form-control" name="email" value="<?php echo $email; ?>" autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label"><b>Mobile number</b></label>
                <input type="text" class="form-control" name="mobile" value="<?php echo $mobile; ?>" autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label"><b>Password</b></label>
                <input type="text" class="form-control" name="psw" value="<?php echo $password; ?>" autocomplete="off">
            </div>

            <button type="submit" class="btn btn-primary" name="submit">Update</button>
        </form>
    </div>
</body>

</html> -->
<!-- //open
include 'connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM prass WHERE Id=$id";
    $result = mysqli_query($con, $sql);
    $user = mysqli_fetch_assoc($result);
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['namey'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['psw'];
    $date = $_POST['date'];
    $state = $_POST['states'];

    if ($_FILES['image']['name'] != "") {
        // Delete old image
        if (file_exists("uploads/".$user['Image'])) {
            unlink("uploads/".$user['Image']);
        }
        
        // Upload new image
        $image_name = $_FILES['image']['name'];
        $temp_name = $_FILES['image']['tmp_name'];
        $file_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $unique_filename = uniqid() . '.' . $file_extension;
        move_uploaded_file($temp_name, "uploads/".$unique_filename);
        
        $sql = "UPDATE prass SET Name='$name', Email='$email', Mobile='$mobile', 
                Passwords='$password', date='$date', state='$state', Image='$unique_filename' 
                WHERE Id=$id";
    } else {
        $sql = "UPDATE prass SET Name='$name', Email='$email', Mobile='$mobile', 
                Passwords='$password', date='$date', state='$state' WHERE Id=$id";
    }

    if (mysqli_query($con, $sql)) {
        header('Location: display.php');
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
}
//closs

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5 w-50 bg-warning-subtle">
        <h2 class="text-center">Edit User</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $user['Id'] ?>">
            
            <!-- All form fields same as user.php but with values populated -->
<!-- <div class="mb-3">
    <label class="form-label"><b>Name</b></label>
    <input type="text" class="form-control" name="namey" value="<?= $user['Name'] ?>" required>
</div>

<!-- Include all other fields similarly -->

<!-- <div class="mb-3">
    <label class="form-label"><b>Current Image</b></label><br>
    <img src="uploads/<?= $user['Image'] ?>" width="100">
</div>

<div class="mb-3">
    <label for="image"><b>New Image</b></label>
    <input type="file" name="image" id="image">
</div>

<button type="submit" class="btn btn-primary" name="update">Update</button>
<a href="display.php" class="btn btn-secondary">Cancel</a>
</form>
</div>
</body>

</html>  -->