<?php
session_start();
if (!isset($_SESSION['doctor'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

$sql = "SELECT * FROM patients";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function printTable() {
            const printContent = document.getElementById('patient-table').outerHTML;
            const originalContent = document.body.innerHTML;
            document.body.innerHTML = `<div style="margin: 20px;">` + printContent + `</div>`;
            window.print();
            document.body.innerHTML = originalContent;
        }
    </script>
    <style>
         body {
            background: linear-gradient(to right, #ffefba, #ffffff);
        }
        </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <div class="container mx-auto p-4">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-700">Patient List</h1>
            <a href="logout.php" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Logout</a>
        </div>

        <!-- Success Message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 mb-6">
            <a href="patientForm.php" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Add New Patient</a>
            <a href="dataexport.php" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600" download>Export to Excel</a>
            <button onclick="printTable()" class="bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600">Print</button>
        </div>

        <!-- Patient Table -->
        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table id="patient-table" class="w-full table-auto border-collapse">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-3 text-left border">Name</th>
                        <th class="p-3 text-left border">Age</th>
                        <th class="p-3 text-left border">Gender</th>
                        <th class="p-3 text-left border">Contact</th>
                        <th class="p-3 text-left border">Adhar</th>
                        <th class="p-3 text-left border">Medical Concern</th>
                        <th class="p-3 text-left border">Treatment/Advice</th>
                        <th class="p-3 text-left border">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-100 transition duration-200">
                            <td class="p-3 border"><?= $row['name'] ?></td>
                            <td class="p-3 border"><?= $row['age'] ?></td>
                            <td class="p-3 border"><?= $row['gender'] ?></td>
                            <td class="p-3 border"><?= $row['contact'] ?></td>
                            <td class="p-3 border"><?= $row['adhar'] ?></td>
                            <td class="p-3 border"><?= $row['concern'] ?></td>
                            <td class="p-3 border"><?= $row['treatment'] ?></td>
                            <td class="p-3 border">
                                <a href="patientView.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:text-blue-700">View</a> |
                                <a href="patientForm.php?id=<?= $row['id'] ?>" class="text-green-500 hover:text-green-700">Edit</a> |
                                <a href="deletePatient.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this patient?');" class="text-red-500 hover:text-red-700">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
