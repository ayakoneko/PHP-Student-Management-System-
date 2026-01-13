<?php 
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $name   = isset($_POST['name']) ? trim($_POST['name']) : '';
  $email  = isset($_POST['email']) ? trim($_POST['email']) : '';
  $course = isset($_POST['course']) ? trim($_POST['course']) : '';

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
    $sql = "INSERT INTO students (name, email, course) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt){
      mysqli_stmt_bind_param($stmt, "sss", $name, $email, $course);
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
  <title>New Student Form</title>
</head>

<body>
  <h1>Student Application</h1>

  <?php if (!empty($errors)): ?>
    <ul style="color: red;">
      <?php foreach($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>

    </ul>
  <?php endif;?>

  <form action="add.php" method="post">
    <label>Full Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required><br><br>

    <label>Course:</label><br>
    <input type="text" name="course" value="<?= htmlspecialchars($course ?? '') ?>" required><br><br>

    <button type="submit">Add Student</button>
  </form>

</body>
</html>