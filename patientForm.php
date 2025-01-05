<?php
include 'db.php';

// Initialize variables
$name = $age = $gender = $contact = $adhar = $existingConcern = $newConcern = $existingTreatment = $newTreatment = "";
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];
    $adhar = $_POST['adhar'];
    $existingConcern = trim($_POST['existing_concern'] ?? "");
    $newConcern = trim($_POST['new_concern'] ?? "");
    $existingTreatment = trim($_POST['existing_treatment'] ?? "");
    $newTreatment = trim($_POST['new_treatment'] ?? "");

    // Combine existing and new concerns
    $concern = $existingConcern;
    if (!empty($newConcern)) {
        $concern .= "\n" . $newConcern;
    }

    // Combine existing and new treatments
    $treatment = $existingTreatment;
    if (!empty($newTreatment)) {
        $treatment .= "\n" . $newTreatment;
    }

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
        $existingConcern = $patient['concern'];
        $existingTreatment = $patient['treatment'];
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
            background: linear-gradient(to right, #ffebef, #ffffff);
        }
        .form-section {
            transition: box-shadow 0.3s ease;
        }
        .form-section:hover {
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="container max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-8"><?= $id ? "Edit Patient Details" : "Add New Patient" ?></h1>
        <form method="POST" class="bg-white p-6 rounded-lg shadow form-section">
            <input type="hidden" name="id" value="<?= $id ?>">
            
            <!-- Name Section -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter patient's full name"
                       class="w-full p-3 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
                       value="<?= $name ?>" required>
            </div>

            <!-- Age Section -->
            <div class="mb-4">
                <label for="age" class="block text-sm font-medium text-gray-700">Age</label>
                <input type="number" id="age" name="age" placeholder="Enter patient's age"
                       class="w-full p-3 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
                       value="<?= $age ?>" required>
            </div>

            <!-- Gender Section -->
            <div class="mb-4">
                <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                <select id="gender" name="gender"
                        class="w-full p-3 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                        required>
                    <option value="" disabled>Select gender</option>
                    <option value="Male" <?= $gender == "Male" ? "selected" : "" ?>>Male</option>
                    <option value="Female" <?= $gender == "Female" ? "selected" : "" ?>>Female</option>
                    <option value="Other" <?= $gender == "Other" ? "selected" : "" ?>>Other</option>
                </select>
            </div>

            <!-- Contact Section -->
            <div class="mb-4">
                <label for="contact" class="block text-sm font-medium text-gray-700">Contact</label>
                <input type="text" id="contact" name="contact" placeholder="Enter patient's contact number"
                       class="w-full p-3 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
                       value="<?= $contact ?>" required>
            </div>

            <!-- Adhar Section -->
            <div class="mb-4">
                <label for="adhar" class="block text-sm font-medium text-gray-700">Adhar/KYC</label>
                <input type="text" id="adhar" name="adhar" placeholder="Enter patient's Adhar or KYC details"
                       class="w-full p-3 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
                       value="<?= $adhar ?>" required>
            </div>

            <!-- Concerns Section -->
            <div class="mb-4">
                <label for="existing-concern" class="block text-sm font-medium text-gray-700">Medical Concerns</label>
                <textarea id="existing-concern" name="existing_concern" placeholder=" Add medical concerns"
                          class="w-full p-3 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"><?= htmlspecialchars($existingConcern) ?></textarea>
            </div>
            <?php if ($id): ?>
            <div class="mb-4">
                <label for="new-concern" class="block text-sm font-medium text-gray-700">Add New Medical Concern</label>
                <textarea id="new-concern" name="new_concern" placeholder="Add new medical concerns"
                          class="w-full p-3 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"></textarea>
            </div>
            <?php endif; ?>

            <!-- Treatment Section -->
            <div class="mb-4">
                <label for="existing-treatment" class="block text-sm font-medium text-gray-700">Treatment/Advice</label>
                <textarea id="existing-treatment" name="existing_treatment" placeholder="Add  treatment or advice"
                          class="w-full p-3 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"><?= htmlspecialchars($existingTreatment) ?></textarea>
            </div>
            <?php if ($id): ?>
            <div class="mb-4">
                <label for="new-treatment" class="block text-sm font-medium text-gray-700">Add New Treatment/Advice</label>
                <textarea id="new-treatment" name="new_treatment" placeholder="Add new treatment or advice"
                          class="w-full p-3 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"></textarea>
            </div>
            <?php endif; ?>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-blue-500 text-white py-3 px-4 rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <?= $id ? "Update" : "Submit" ?>
            </button>
        </form>
    </div>
</body>
</html>
