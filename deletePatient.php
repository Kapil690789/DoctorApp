<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor'])) {
    header('Location: index.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the deletion query
    $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Patient deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete patient.";
    }
    $stmt->close();
}

header('Location: patientList.php');
exit();
?>
