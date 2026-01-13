<?php 
require 'db.php';

$errors = [];
$id = 0;
$name = '';
$email = '';
$course = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $id = isset($_GET['id'])?(int)$_GET['id']:0;
} else {
  $id = isset($_POST['id'])?(int)$_POST['id']:0;
}

$name = '';
$email = '';
$course = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if ($id <= 0) {
    die("Invalid student id.");
  }

  $sql = "SELECT name, email, course FROM students WHERE id = ?";
  $stmt = mysqli_prepare($conn, $sql);

  if (!$stmt) {
    die("Database error.");
  }

  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $student = mysqli_fetch_assoc($result);
  mysqli_stmt_close($stmt);

  if (!$student) {
    die("Student not found.");
  }

  $name = $student['name'];
  $email = $student['email'];
  $course = $student['course'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (count($errors) === 0){
    $sql = "DELETE FROM students WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt){
      mysqli_stmt_bind_param($stmt, "i", $id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
      
      header("Location: index.php");
      exit;
    } else {
      $errors[] = "Database error. Please try again.";
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Student Info</title>
</head>

<body>
  <h1>Delete Student</h1>
  <p><a href="index.php">Back to Dashboard</a></p>

  <?php if (!empty($errors)): ?>
    <ul style="color: red;">
      <?php foreach($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif;?>

  <p><strong>Are you sure you want to delete this student?</strong></p>

  <form action="delete.php" method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

    <label>Full Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>" readonly><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" readonly><br><br>

    <label>Course:</label><br>
    <input type="text" name="course" value="<?= htmlspecialchars($course ?? '') ?>" readonly><br><br>

    <button type="submit">Confirm Delete</button>
  </form>

</body>
</html>