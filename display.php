<!-- //open php
include 'connect.php';
// include 'update.php';
// include 'delete.php';

// Initialize search variable
$search = '';

// Check if form submitted with search term
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Prepare SQL query
if ($search != '') {
    // Use prepared statement with LIKE for search
    $sql = "SELECT * FROM studentstbl WHERE stusname LIKE ?";
    $stmt = $conn->prepare($sql);
    $likeSearch = "%" . $search . "%";
    $stmt->bind_param("s", $likeSearch);
} else {
    // No search, select all
    $sql = "SELECT * FROM studentstbl";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result(); 
//close php
-->



<!-- 
//openphp
include 'connect.php';

// Initialize search variable
$search = '';

// Check if form submitted with search term
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Prepare SQL query
if ($search != '') {
    $sql = "SELECT * FROM studentstbl WHERE stusname LIKE ? ORDER BY id DESC LIMIT 5";
    $stmt = $conn->prepare($sql);
    $likeSearch = "%" . $search . "%";
    $stmt->bind_param("s", $likeSearch);
} else {
    $sql = "SELECT * FROM studentstbl ORDER BY id DESC LIMIT 5";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
?>  closs php-->


<?php
include 'connect.php';

// Initialize search variable
$search = '';

// Pagination setup
$limit = 4;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;


// Check if form submitted with search term
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}



// Count total records for pagination
if ($search != '') {
    $countSql = "SELECT COUNT(*) as total FROM studentstbl WHERE stusname LIKE ?";
    $stmtCount = $conn->prepare($countSql);
    $likeSearch = "%" . $search . "%";
    $stmtCount->bind_param("s", $likeSearch);
    $stmtCount->execute();
    $countResult = $stmtCount->get_result();
    $totalRows = $countResult->fetch_assoc()['total'];
    $stmtCount->close();
} else {
    $countSql = "SELECT COUNT(*) as total FROM studentstbl";
    $countResult = $conn->query($countSql);
    $totalRows = $countResult->fetch_assoc()['total'];
}
$totalPages = ceil($totalRows / $limit);

// Prepare SQL query with JOIN  
if ($search != '') {
    $sql = "SELECT s.*, c.class_title 
            FROM studentstbl s
            LEFT JOIN class c ON s.class_id = c.class_id
            WHERE s.stusname LIKE ?
            ORDER BY s.id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("sii", $likeSearch, $limit, $offset);
} else {
    $sql = "SELECT s.*, c.class_title 
            FROM studentstbl s
            LEFT JOIN class c ON s.class_id = c.class_id
            ORDER BY s.id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container mt-5">
        <h3 class="mb-4">Student Records</h3>

        <!-- Search form -->
        <form class="mb-4" method="GET" action="">
            <div class="input-group w-50">
                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-primary ms-3" type="submit">Search</button>
                <a href="display.php" class="btn btn-secondary ms-3">Reset</a>
            </div>
        </form>


        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>User Records</h4>
            <a href="user.php" class="btn btn-success">Add User</a>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Class</h4>
            <a href="class.php" class="btn btn-info">Add Class</a>
        </div>

        <!-- Data table -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Gender</th>
                    <th>Class</th>
                    <th>Profile Picture</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['stusname']) ?></td>
                            <td><?= htmlspecialchars($row['stuemail']) ?></td>
                            <td><?= htmlspecialchars($row['stuphone']) ?></td>
                            <td>
                                <?php
                                if ($row['stugender'] == 'm') echo 'Male';
                                elseif ($row['stugender'] == 'f') echo 'Female';
                                else echo 'Others';
                                ?>
                            </td>
                            <td><?= $row['class_title'] ?? 'No' ?></td>
                            <td>
                                <img src="uploads/<?php echo !empty($row['stuprofilepic']) ? $row['stuprofilepic'] : 'abc2.png'; ?>" width="60" alt="Profile Picture">
                            </td>
                            <td>
                                <a href="update.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <!-- <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
                    </li>
                <?php endif; ?> -->


                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
                    </li>
                <?php endif; ?> -->
            </ul>
        </nav>

    </div>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>



























<!-- //open php
 
 php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Display Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>User Records</h4>
            <a href="user.php" class="btn btn-success">Add User</a>
        </div>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Password</th>
                    <th>gender</th>
                    <th>Image</th>
                    <th>Action</th>

                </tr>
            </thead>
            <tbody>
                //open php
                $sql = "SELECT * FROM studentstbl";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['stusname']}</td>
                    <td>{$row['stuemail']}</td>
                    <td>{$row['stuphone']}</td>
                    <td>{$row['stupassword']}</td>
                    <td>{$row['stugender']}</td>
                
                    <td><img src='uploads/" . (!empty($row['stuprofilepic']) ? $row['stuprofilepic'] : 'abc2.png
                           ') . "' width='60'></td>

                 
                    <td>
                        <a href='update.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
                        <a href='delete.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure?')\">Delete</a>
                    </td>
                  </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
                }
                //clossphp
            </tbody>
        </table>
    </div>
</body>

</html> -->