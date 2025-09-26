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
        <form action="insert.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label"><strong>students Name</strong></label>
                <input type="text" name="name" class="form-control" placeholder="Enter name" autocomplete="off" required>
                <small class="text-danger"></small>
            </div>



            <div class="mb-3">
                <label class="form-label"><strong>students emailid</strong></label>
                <input type="email" name="email" class="form-control" placeholder="Enter email" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>students MobileNo</strong></label>
                <input type="text" name="mobile" class="form-control" placeholder="Enter MobileNo" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label for="password">Password</label>
                <input type="text" id="password" name="password" minlength="8" maxlength="8" autocomplete="off" required>

            </div>
            <div class="genderContainer">
                <label><strong>Gender</strong></label>
                <input class="gender1" type="radio" name="gender" value="m" required>Male
                <input class="gender1" type="radio" name="gender" value="f">Female
                <input class="gender1" type="radio" name="gender" value="o">Others
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Upload image</strong></label>
                <input type="file" name="image" class="form-control" autocomplete="off" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

</body>

</html> -->


<?php
include 'connect.php';

// Initialize variables and error messages
$name = $email = $mobile = $password = $gender = $class_id = "";
$errors = [
    'name' => '',
    'email' => '',
    'mobile' => '',
    'password' => '',
    'gender' => '',
    'image' => '',
    'class' => ''
];

// Fetch classes for dropdown
$classes = [];
$classResult = $conn->query("SELECT * FROM class");
if ($classResult) {
    while ($row = $classResult->fetch_assoc()) {
        $classes[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and trim inputs
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $mobile   = trim($_POST['mobile']);
    $password = trim($_POST['password']);
    $gender   = $_POST['gender'] ?? '';
    $class_id = $_POST['class_id'] ?? '';
    $image    = $_FILES['image'];

    // Validate Name
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors['name'] = "Name must contain only letters and spaces.";
    }

    // Validate Email
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    // Validate Mobile
    if (empty($mobile)) {
        $errors['mobile'] = "Mobile number is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $errors['mobile'] = "Mobile number must be exactly 10 digits.";
    }

    // Validate Password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) !== 8) {
        $errors['password'] = "Password must be exactly 8 characters.";
    }

    // Validate Gender
    if (empty($gender)) {
        $errors['gender'] = "Gender is required.";
    } elseif (!in_array($gender, ['m', 'f', 'o'])) {
        $errors['gender'] = "Invalid gender selection.";
    }

    // Validate Class
    if (empty($class_id)) {
        $errors['class'] = "Class selection is required.";
    } else {
        $check = $conn->prepare("SELECT class_id FROM class WHERE class_id = ?");
        $check->bind_param("i", $class_id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows === 0) {
            $errors['class'] = "Invalid class selected.";
        }
        $check->close();
    }

    // Validate Image
    if ($image['error'] !== 0) {
        $errors['image'] = "Image upload error.";
    } else {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($image['type'], $allowedTypes)) {
            $errors['image'] = "Only JPG, JPEG, and PNG images are allowed.";
        } elseif ($image['size'] > 2 * 1024 * 1024) {
            $errors['image'] = "Image must be less than 2MB.";
        }
    }

    // If no errors, proceed to insert
    if (!array_filter($errors)) {
        $newImageName = uniqid("IMG_") . "_" . basename($image['name']);
        $uploadPath = "uploads/" . $newImageName;

        if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
            $stmt = $conn->prepare("INSERT INTO studentstbl 
                (stusname, stuemail, stuphone, stupassword, stugender, stuprofilepic, class_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $name, $email, $mobile, $password, $gender, $newImageName, $class_id);

            if ($stmt->execute()) {
                header("Location: display.php");
                exit;
            } else {
                echo "<div class='alert alert-danger'>Error inserting data: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            $errors['image'] = "Failed to move uploaded image.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5 p-4 bg-warning bg-opacity-25 w-50 rounded">
        <h4 class="mb-4">Student Registration Form</h4>
        <form method="POST" enctype="multipart/form-data">
            <!-- Name Field -->
            <div class="mb-3">
                <label class="form-label"><strong>Student Name</strong></label>
                <input type="text" name="name" class="form-control"
                    value="<?= htmlspecialchars($name) ?>" autocomplete="off" required>
                <div class="text-danger"><?= $errors['name'] ?></div>
            </div>

            <!-- Email Field -->
            <div class="mb-3">
                <label class="form-label"><strong>Student Email</strong></label>
                <input type="email" name="email" class="form-control"
                    value="<?= htmlspecialchars($email) ?>" autocomplete="off" required>
                <div class="text-danger"><?= $errors['email'] ?></div>
            </div>

            <!-- Mobile Field -->
            <div class="mb-3">
                <label class="form-label"><strong>Mobile Number</strong></label>
                <input type="text" name="mobile" class="form-control"
                    value="<?= htmlspecialchars($mobile) ?>" autocomplete="off" required>
                <div class="text-danger"><?= $errors['mobile'] ?></div>
            </div>

            <!-- Password Field -->
            <div class="mb-3">
                <label class="form-label"><strong>Password</strong></label>
                <input type="text" name="password" class="form-control"
                    value="<?= htmlspecialchars($password) ?>" autocomplete="off" required>
                <div class="text-danger"><?= $errors['password'] ?></div>
            </div>

            <!-- Gender Field -->
            <div class="mb-3">
                <label class="form-label"><strong>Gender</strong></label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="m"
                        <?= ($gender === 'm') ? 'checked' : '' ?> autocomplete="off" required>
                    <label class="form-check-label">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="f"
                        <?= ($gender === 'f') ? 'checked' : '' ?>>
                    <label class="form-check-label">Female</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="o"
                        <?= ($gender === 'o') ? 'checked' : '' ?>>
                    <label class="form-check-label">Others</label>
                </div>
                <div class="text-danger"><?= $errors['gender'] ?></div>
            </div>

            <!-- Class Selection -->
            <div class="mb-3">
                <label class="form-label"><strong>Select Class</strong></label>
                <select name="class_id" class="form-select" required>
                    <option value="">Select a Class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['class_id'] ?>"
                            <?= ($class_id == $class['class_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($class['class_title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="text-danger"><?= $errors['class'] ?></div>
            </div>

            <!-- Image Upload -->
            <div class="mb-3">
                <label class="form-label"><strong>Upload Photo</strong></label>
                <input type="file" name="image" class="form-control" required>
                <div class="text-danger"><?= $errors['image'] ?></div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Register Student</button>
                <a href="class.php" class="btn btn-secondary">Manage Classes</a>
            </div>
        </form>
    </div>
</body>

</html>
<?php $conn->close(); ?>