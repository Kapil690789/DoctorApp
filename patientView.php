<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor'])) {
    header('Location: index.php');
    exit();
}

$patientId = $_GET['id'];
$sql = "SELECT * FROM patients WHERE id = $patientId";
$result = $conn->query($sql);
$patient = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
         body {
            background: linear-gradient(to right, #ffefba, #ffffff);
        }
        </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-2xl font-bold mb-6">Patient Details</h1>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p><strong>Name:</strong> <?php echo $patient['name']; ?></p>
            <p><strong>Age:</strong> <?php echo $patient['age']; ?></p>
            <p><strong>Gender:</strong> <?php echo $patient['gender']; ?></p>
            <p><strong>Contact:</strong> <?php echo $patient['contact']; ?></p>
            <p><strong>Aadhar/KYC:</strong> <?php echo $patient['adhar']; ?></p>
            <p><strong>Medical Concern:</strong> <?php echo $patient['concern']; ?></p>
            <p><strong>Treatment/Advice:</strong> <?php echo $patient['treatment']; ?></p>
        </div>
        <div class="mt-6">
            <a href="patientList.php" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Back to List</a>
        </div>
    </div>
</body>
</html>
