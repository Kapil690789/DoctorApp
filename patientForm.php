<?php
include 'db.php';

// Initialize variables
$name = $age = $gender = $contact = $adhar = $concern = $treatment = "";
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];
    $adhar = $_POST['adhar'];
    $concern = $_POST['concern'];
    $treatment = $_POST['treatment'];

    if ($id) {
        // Update patient data
        $stmt = $conn->prepare("UPDATE patients SET name = ?, age = ?, gender = ?, contact = ?, adhar = ?, concern = ?, treatment = ? WHERE id = ?");
        $stmt->bind_param("sisssssi", $name, $age, $gender, $contact, $adhar, $concern, $treatment, $id);
    } else {
        // Insert new patient data
        $stmt = $conn->prepare("INSERT INTO patients (name, age, gender, contact, adhar, concern, treatment) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssss", $name, $age, $gender, $contact, $adhar, $concern, $treatment);
    }

    $stmt->execute();
    header('Location: patientList.php');
    exit();
}

// If editing, fetch patient data
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        $name = $patient['name'];
        $age = $patient['age'];
        $gender = $patient['gender'];
        $contact = $patient['contact'];
        $adhar = $patient['adhar'];
        $concern = $patient['concern'];
        $treatment = $patient['treatment'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? "Edit Patient" : "Add Patient" ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
         body {
            background: linear-gradient(to right, #ffefba, #ffffff);
        }
        </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="container max-w-4xl mx-auto p-4">
        <h1 class="text-3xl font-bold text-center mb-6"><?= $id ? "Edit Patient Details" : "Add New Patient" ?></h1>
        <form method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <input type="hidden" name="id" value="<?= $id ?>">
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter patient's full name" 
                       class="w-full p-2 mt-1 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                       value="<?= $name ?>" required>
            </div>

            <div class="mb-4">
                <label for="age" class="block text-sm font-medium text-gray-700">Age</label>
                <input type="number" id="age" name="age" placeholder="Enter patient's age" 
                       class="w-full p-2 mt-1 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                       value="<?= $age ?>" required>
            </div>

            <div class="mb-4">
                <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                <select id="gender" name="gender" 
                        class="w-full p-2 mt-1 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                        required>
                    <option value="" disabled>Select gender</option>
                    <option value="Male" <?= $gender == "Male" ? "selected" : "" ?>>Male</option>
                    <option value="Female" <?= $gender == "Female" ? "selected" : "" ?>>Female</option>
                    <option value="Other" <?= $gender == "Other" ? "selected" : "" ?>>Other</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="contact" class="block text-sm font-medium text-gray-700">Contact</label>
                <input type="text" id="contact" name="contact" placeholder="Enter patient's contact number" 
                       class="w-full p-2 mt-1 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                       value="<?= $contact ?>" required>
            </div>

            <div class="mb-4">
                <label for="adhar" class="block text-sm font-medium text-gray-700">Adhar/KYC</label>
                <input type="text" id="adhar" name="adhar" placeholder="Enter patient's Adhar or KYC details" 
                       class="w-full p-2 mt-1 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                       value="<?= $adhar ?>" required>
            </div>

            <div class="mb-4">
                <label for="concern" class="block text-sm font-medium text-gray-700">Medical Concern</label>
                <textarea id="concern" name="concern" placeholder="Describe the medical concern"
                          class="w-full p-2 mt-1 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                          required><?= $concern ?></textarea>
            </div>

            <div class="mb-4">
                <label for="treatment" class="block text-sm font-medium text-gray-700">Treatment/Advice</label>
                <textarea id="treatment" name="treatment" placeholder="Enter treatment or advice"
                          class="w-full p-2 mt-1 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                          required><?= $treatment ?></textarea>
            </div>

            <button type="submit" 
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <?= $id ? "Update" : "Submit" ?>
            </button>
        </form>
    </div>
</body>
</html>
