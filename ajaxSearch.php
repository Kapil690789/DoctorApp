<?php
include 'db.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';
$sql = "SELECT name, contact, adhar FROM patients WHERE name LIKE ? OR contact LIKE ? OR adhar LIKE ?";
$stmt = $conn->prepare($sql);
$searchParam = "%" . $query . "%";
$stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

$patients = [];
while ($row = $result->fetch_assoc()) {
    $patients[] = [
        'name' => $row['name'],
        'contact' => $row['contact'],
        'adhar' => $row['adhar']
    ];
}

header('Content-Type: application/json');
echo json_encode($patients);
