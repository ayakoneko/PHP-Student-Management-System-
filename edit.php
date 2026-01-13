<?php 
require 'db.php';

$errors = [];
$id = 0;

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
  $name   = isset($_POST['name']) ? trim($_POST['name']) : '';
  $email  = isset($_POST['email']) ? trim($_POST['email']) : '';
  $course = isset($_POST['course']) ? trim($_POST['course']) : '';

  // Validate id
  if ($id <=0) {
    $errors[]="Invalid student id";
  }

  // Required checks
  if (empty($name)) { 
    $errors[] = "Name is required";
  }

  if (empty($email)) {
    $errors[] = "Email is required";
  }

  if (empty($course)) {
    $errors[] = "Course is required";
  }

  // Length checks
  if (strlen($name) > 100 || strlen($email) > 100 || strlen($course) > 100) {
    $errors[] = "Input must be less than 100 characters";
  }

  // Email format check
  if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
  }

  if (count($errors) === 0){
    $sql = "UPDATE students SET name=?, email=?, course=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt){
      mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $course, $id);

      if (!mysqli_stmt_execute($stmt)) {
        $errors[] = "Update failed: " . mysqli_stmt_error($stmt);
      }
      
      mysqli_stmt_close($stmt);

      if (count($errors) === 0) {
        header("Location: index.php");
        exit;
      }
      
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
  <h1>Edit Student</h1>
  <p><a href="index.php">Back to Dashboard</a></p>

  <?php if (!empty($errors)): ?>
    <ul style="color: red;">
      <?php foreach($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif;?>

  <form action="edit.php" method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

    <label>Full Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required><br><br>

    <label>Course:</label><br>
    <input type="text" name="course" value="<?= htmlspecialchars($course ?? '') ?>" required><br><br>

    <button type="submit">Update Student</button>
  </form>

</body>
</html>