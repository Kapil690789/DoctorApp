<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor'])) {
    header('Location: index.php');
    exit();
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="patients_data.xls"');

$sql = "SELECT * FROM patients";
$result = $conn->query($sql);

echo "Name\tAge\tGender\tContact Number\tAdhar/KYC\tMedical Concern\tTreatment/Advice\n";

while ($row = $result->fetch_assoc()) {
    echo $row['name'] . "\t" . $row['age'] . "\t" . $row['gender'] . "\t" . $row['contact'] . "\t" . $row['adhar'] . "\t" . $row['concern'] . "\t" . $row['treatment'] . "\n";
}
?>
