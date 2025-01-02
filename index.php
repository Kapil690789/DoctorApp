<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepared statement to prevent SQL Injection
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Direct password comparison (plain text password)
        if ($password === $row['password']) {
            $_SESSION['doctor'] = $username;
            header('Location: patientList.php');
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to right, #ffefba, #ffffff);
        }
        .btn-gold {
            background-color: #fbbf24;
            color: white;
            box-shadow: 0 4px 6px rgba(251, 191, 36, 0.4);
            transition: all 0.3s ease-in-out;
        }
        .btn-gold:hover {
            background-color: #d97706;
            box-shadow: 0 6px 8px rgba(200, 119, 6, 0.5);
        }
        .card-shadow {
            box-shadow: 0 10px 15px rgba(251, 191, 36, 0.2), 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-input {
            border: 2px solid #fbbf24;
            padding: 10px;
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white rounded-lg card-shadow p-8">
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">Doctor Login</h1>

        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center mb-4"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" 
                       class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500" 
                       required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" 
                       class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500" 
                       required>
            </div>
            <button type="submit" class="btn-gold w-full py-2 px-4 rounded-md text-lg font-medium">Login</button>
        </form>
    </div>
</body>
</html>
