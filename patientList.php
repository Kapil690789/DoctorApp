<?php
session_start();
if (!isset($_SESSION['doctor'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

// Fetch patients based on search input, if provided
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM patients";

if (!empty($searchQuery)) {
    $searchQueryEscaped = $conn->real_escape_string($searchQuery);
    $sql .= " WHERE name LIKE '%$searchQueryEscaped%' OR contact LIKE '%$searchQueryEscaped%' OR adhar LIKE '%$searchQueryEscaped%'";
}

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

        function searchPatients(query) {
            if (query.length === 0) {
                document.getElementById('suggestions').innerHTML = '';
                return;
            }

            fetch('ajaxSearch.php?query=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    let suggestions = '';
                    data.forEach(patient => {
                        suggestions += `<li class="p-2 hover:bg-gray-200 cursor-pointer" onclick="selectSuggestion('${patient.name}')">${patient.name} (${patient.contact})</li>`;
                    });
                    document.getElementById('suggestions').innerHTML = `<ul class="bg-white border border-gray-300 rounded-lg">${suggestions}</ul>`;
                });
        }

        function selectSuggestion(name) {
            document.getElementById('search-input').value = name;
            document.getElementById('suggestions').innerHTML = '';
        }

        function resetSearch() {
            document.getElementById('search-input').value = '';
            document.getElementById('suggestions').innerHTML = '';
            window.location.href = 'patientList.php';
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

        <!-- Search Bar -->
        <form method="GET" action="patientList.php" class="relative mb-6 flex flex-wrap gap-2">
            <input 
                type="text" 
                name="search"
                id="search-input"
                onkeyup="searchPatients(this.value)" 
                placeholder="Search by Name, Contact, or Aadhaar" 
                class="flex-grow py-2 px-4 border border-gray-300 rounded-lg"
                value="<?= htmlspecialchars($searchQuery) ?>"
            >
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Search</button>
            <button type="button" onclick="resetSearch()" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">Reset</button>
            <div id="suggestions" class="absolute top-full left-0 w-full z-10"></div>
        </form>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 mb-6">
            <a href="patientForm.php" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Add New Patient</a>
            <!-- Export to Excel Button -->
            <a href="?export=true" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">Export to Excel</a>
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
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-100 transition duration-200">
                                <td class="p-3 border"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-3 border"><?= htmlspecialchars($row['age']) ?></td>
                                <td class="p-3 border"><?= htmlspecialchars($row['gender']) ?></td>
                                <td class="p-3 border"><?= htmlspecialchars($row['contact']) ?></td>
                                <td class="p-3 border"><?= htmlspecialchars($row['adhar']) ?></td>
                                <td class="p-3 border"><?= htmlspecialchars($row['concern']) ?></td>
                                <td class="p-3 border"><?= htmlspecialchars($row['treatment']) ?></td>
                                <td class="p-3 border">
                                    <a href="patientView.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:text-blue-700">View</a> |
                                    <a href="patientForm.php?id=<?= $row['id'] ?>" class="text-green-500 hover:text-green-700">Edit</a> |
                                    <a href="deletePatient.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this patient?');" class="text-red-500 hover:text-red-700">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">No patients found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
