<?php
include 'connect.php';

header('Content-Type: application/json');

if (isset($_GET['class_id'])) {
    $classId = (int)$_GET['class_id'];

    $stmt = $conn->prepare("
        SELECT c.class_title, COUNT(s.id) AS student_count 
        FROM class c
        LEFT JOIN studentstbl s ON c.class_id = s.class_id
        WHERE c.class_id = ?
        GROUP BY c.class_id
    ");

    $stmt->bind_param("i", $classId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Class not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
