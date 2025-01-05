<?php
session_start();
if (!isset($_SESSION['doctor'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];
    $adhar = $_POST['adhar'];

    // Get existing and new concerns
    $existingConcern = trim($_POST['existing_concern']); // Editable existing concerns
    $newConcern = trim($_POST['new_concern']);           // New concerns to append

    // Combine both concerns
    $updatedConcern = $existingConcern;
    if (!empty($newConcern)) {
        $updatedConcern .= "\n" . $newConcern; // Append new concerns with a newline separator
    }

    $treatment = $_POST['treatment'];

    if ($id) {
        // Update patient data
        $stmt = $conn->prepare("UPDATE patients SET name = ?, age = ?, gender = ?, contact = ?, adhar = ?, concern = ?, treatment = ? WHERE id = ?");
        $stmt->bind_param("sisssssi", $name, $age, $gender, $contact, $adhar, $updatedConcern, $treatment, $id);
    } else {
        // Insert new patient data
        $stmt = $conn->prepare("INSERT INTO patients (name, age, gender, contact, adhar, concern, treatment) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssss", $name, $age, $gender, $contact, $adhar, $updatedConcern, $treatment);
    }

    $stmt->execute();
    header('Location: patientList.php');
    exit();
}

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

        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            dropdown.classList.toggle('hidden');
        }
    </script>
   <style>
    body {
        background: linear-gradient(to right, #ffefba 50%, #fffeba 50%);
        background-size: 200% 100%;
        animation: gradientShift 5s ease-in-out infinite;
    }

    @keyframes gradientShift {
        0% {
            background-position: 100% 0;
        }
        50% {
            background-position: 0 0;
        }
        100% {
            background-position: 100% 0;
        }
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
            <button type="submit" 
            class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
        Search
    </button>
    <button type="button" 
            onclick="resetSearch()" 
            class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600  focus:outline-none focus:ring-2 focus:ring-gray-400">
        Reset
    </button>
            <div id="suggestions" class="absolute top-full left-0 w-full z-10"></div>
        </form>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 mb-6">
            <a href="patientForm.php" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Add New Patient</a>
            <!-- Export to Excel Button -->
            <div class="relative">
                <button class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600" onclick="toggleDropdown()">Export To</button>
                <ul id="dropdown" class="hidden absolute bg-white border border-gray-300 rounded-lg mt-2 shadow-lg w-48">
                    <li><a href="dataExport.php?format=excel" class="block px-4 py-2 hover:bg-gray-100">Export to Excel</a></li>
                    <li><a href="dataExport.php?format=csv" class="block px-4 py-2 hover:bg-gray-100">Export to CSV</a></li>
                    <li><a href="dataExport.php?format=xml" class="block px-4 py-2 hover:bg-gray-100">Export to XML</a></li>
                </ul>
            </div>
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
                        <td class="p-3 border">
                            <?php if (!empty($row['concern'])): ?>
                                <ul class="list-disc list-inside">
                                    <?php foreach (explode("\n", $row['concern']) as $concern): ?>
                                        <li><?= htmlspecialchars(trim($concern)) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <span class="text-gray-500">No concerns provided.</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 border">
                            <?php if (!empty($row['treatment'])): ?>
                                <ol class="list-decimal list-inside">
                                    <?php foreach (explode("\n", $row['treatment']) as $treatment): ?>
                                        <li><?= htmlspecialchars(trim($treatment)) ?></li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php else: ?>
                                <span class="text-gray-500">No treatments provided.</span>
                            <?php endif; ?>
                        </td>
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
