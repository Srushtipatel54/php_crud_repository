
<?php
include 'connect.php';


$name = $email = $mobile = $password = $gender = "";
$nameErr = $emailErr = $mobileErr = $passwordErr = $genderErr = $imageErr = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form inputs
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $mobile   = trim($_POST['mobile']);
    $password = trim($_POST['password']);
    $gender   = isset($_POST['gender']) ? $_POST['gender'] : '';
    $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';

    $image    = $_FILES['image'];

    // Error array to collect validation issues
    $errors = [];

    // Validate Name
    // if (empty($name)) {
    //     $errors[] = "Name is required.";
    // }

    if (empty($name)) {
        $errors[] = "Name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors[] = "Name must contain only letters and spaces.";
    }




    // Validate Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate Mobile (10 digit number)
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $errors[] = "Mobile number must be exactly 10 digits.";
    }

    // Validate Password (8 characters)
    if (strlen($password) !== 8) {
        $errors[] = "Password must be exactly 8 characters.";
    }

    // Validate Gender
    if (!in_array($gender, ['m', 'f', 'o'])) {
        $errors[] = "Invalid gender selection.";
    }

    // Validate Image
    if ($image['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($image['type'], $allowedTypes)) {
            $errors[] = "Only JPG, JPEG, and PNG images are allowed.";
        }

        if ($image['size'] > 2 * 1024 * 1024) {
            $errors[] = "Image must be less than 2MB.";
        }

        $newImageName = uniqid("IMG_") . "_" . basename($image['name']);
        $uploadPath = "uploads/" . $newImageName;
    } else {
        $errors[] = "Image upload error.";
    }

    // If no errors, insert into database
    if (empty($errors)) {
        if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
            // Use prepared statements to prevent SQL injection
            $email    = trim($_POST['email']);
            // $stmt = $conn->prepare("INSERT INTO studentstbl (stusname, stuemail, stuphone, stupassword, stugender, stuprofilepic) VALUES ( $name,$email ,$mobile,$password,$gender,$image)");
            // $stmt->bind_param("ssssss", $name, $email, $mobile, $password, $gender, $newImageName);
            $stmt = $conn->prepare("INSERT INTO studentstbl (stusname, stuemail, stuphone, stupassword, stugender,class_id, stuprofilepic) VALUES (?, ?, ?, ?, ?, ?,?)");
            $stmt->bind_param("ssssss", $name, $email, $mobile, $password, $gender, $class_id, $newImageName);



            if ($stmt->execute()) {
                header("Location: display.php");
                exit;
            } else {
                echo " Error inserting data: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Failed to move uploaded image.";
        }
    } else {
        // Show all validation errors
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }

    $conn->close();
}
?>