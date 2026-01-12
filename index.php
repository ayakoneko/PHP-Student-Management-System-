<?php
require 'db.php';

$sql = "SELECT id, name, email, course FROM students ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

if (!$result) {
  die("Query failed: " . mysqli_error($conn));
}

$students = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Management System</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 12px; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background: #f5f5f5; }
    a.button { display: inline-block; padding: 8px 12px; text-decoration: none; border: 1px solid #333; border-radius: 6px; }
    .actions a { margin-right: 10px; }
  </style>
</head>

<body>
  <h1>Student Dashboard</h1>
  <a class="button" href="add.php">+ Add Student</a>

  <table>
    <tr>
      <th>Student ID</th>
      <th>Full Name</th>
      <th>Email</th>
      <th>Course</th>
      <th>Action</th>
    </tr>
    
    <?php if (count($students) === 0): ?>
      <tr>
        <td colspan="5">No students found.</td>
      </tr>
    <?php else: ?>
      <?php foreach ($students as $student): ?>
      <tr>
        <td><?= htmlspecialchars($student['id']) ?></td>
        <td><?= htmlspecialchars($student['name']) ?></td>
        <td><?= htmlspecialchars($student['email']) ?></td>
        <td><?= htmlspecialchars($student['course']) ?></td>
        <td>
          <a href="edit.php?id=<?= urlencode($student['id']) ?>">Edit</a>
          <a href="delete.php?id=<?= urlencode($student['id']) ?>" onclick="return confirm('Are you sure you want to delete this student?');"> Delete </a>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>
</body>
</html>
