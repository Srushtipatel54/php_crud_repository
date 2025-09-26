<?php
include 'connect.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM studentstbl WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header('Location:display.php');
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
