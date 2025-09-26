<?php
$host = '127.0.0.1:3307';
$user = 'root';
$password = '';
$db = 'stu'; // Change this to your actual DB name

$conn = mysqli_connect($host, $user, $password, $db);

// if ($conn) {
//     echo "connection sucessfully";
// } else {
//     die("Connection failed: " . mysqli_connect_error());
// }



if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
