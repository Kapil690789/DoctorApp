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
        /* Make sure that background and text color updates are strong */
        body {
            transition: background-color 1.5s ease-in-out, color 1s ease-in-out !important;
            background-color: #ffefba !important; /* Default initial background */
            color: #333 !important; /* Default text color */
        }

        .bg-soft-elegant {
            background-color: #333333 !important;
            color: #d8a7b1 !important;
        }

        .bg-playful {
            background-color: #ff6f61 !important;
            color: #ff4500 !important;
        }

        .bg-minimalist {
            background-color: #001f3f !important;
            color: #ffd700 !important;
        }

        .bg-earthy {
            background-color: #556b2f !important;
            color: #8b4513 !important;
        }

        .bg-serene {
            background-color: #b0e0e6 !important;
            color: #333333 !important;
        }

        .text-dynamic {
            transition: color 1s ease-in-out !important;
        }

        .bg-white-light {
            background-color: #fefefe !important;
        }

        .accent-soft {
            color: #5c9ead !important;
        }

        .accent-strong {
            color: #40e0d0 !important;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800" id="body">

    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-center mb-6 text-dynamic" id="title">Patient Details</h1>
        
        <div class="bg-white-light p-6 rounded-lg shadow-lg overflow-x-auto">
            <!-- Patient Details Table -->
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="border-b-2">
                        <th class="px-4 py-2 text-left text-dynamic">Field</th>
                        <th class="px-4 py-2 text-left text-dynamic">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="px-4 py-2 font-semibold text-dynamic">Name:</td>
                        <td class="px-4 py-2 text-dynamic"><?php echo htmlspecialchars($patient['name']); ?></td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-2 font-semibold text-dynamic">Age:</td>
                        <td class="px-4 py-2 text-dynamic"><?php echo htmlspecialchars($patient['age']); ?></td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-2 font-semibold text-dynamic">Gender:</td>
                        <td class="px-4 py-2 text-dynamic"><?php echo htmlspecialchars($patient['gender']); ?></td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-2 font-semibold text-dynamic">Contact:</td>
                        <td class="px-4 py-2 text-dynamic"><?php echo htmlspecialchars($patient['contact']); ?></td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-2 font-semibold text-dynamic">Aadhar/KYC:</td>
                        <td class="px-4 py-2 text-dynamic"><?php echo htmlspecialchars($patient['adhar']); ?></td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-2 font-semibold text-dynamic">Medical Concerns:</td>
                        <td class="px-4 py-2 text-dynamic">
                            <?php if (!empty($patient['concern'])): ?>
                                <ul class="list-disc pl-5">
                                    <?php foreach (explode("\n", $patient['concern']) as $concern): ?>
                                        <li class="text-dynamic"><?php echo htmlspecialchars(trim($concern)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-gray-500 text-dynamic">No concerns provided.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-2 font-semibold text-dynamic">Treatment/Advice:</td>
                        <td class="px-4 py-2 text-dynamic">
                            <?php if (!empty($patient['treatment'])): ?>
                                <ol class="list-decimal pl-5">
                                    <?php foreach (explode("\n", $patient['treatment']) as $treatment): ?>
                                        <li class="text-dynamic"><?php echo htmlspecialchars(trim($treatment)); ?></li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php else: ?>
                                <p class="text-gray-500 text-dynamic">No treatment provided.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center">
            <a href="patientList.php" 
               class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                Back to List
            </a>
        </div>
    </div>

    <script>
        const colorSchemes = [
            { bgClass: "bg-soft-elegant", textClass: "accent-soft" },  // Elegant
            { bgClass: "bg-playful", textClass: "accent-strong" },     // Playful and Bright
            { bgClass: "bg-minimalist", textClass: "accent-soft" },    // Minimalist and Modern
            { bgClass: "bg-earthy", textClass: "accent-strong" },      // Natural and Earthy
            { bgClass: "bg-serene", textClass: "accent-soft" }         // Serene
        ];

        let currentIndex = 0;

        function changeColorScheme() {
            const body = document.getElementById('body');
            const title = document.getElementById('title');
            const textDynamic = document.querySelectorAll('.text-dynamic');

            // Remove previous background and text color classes
            body.classList.remove(colorSchemes[currentIndex].bgClass);
            textDynamic.forEach(element => {
                element.classList.remove(colorSchemes[currentIndex].textClass);
            });

            currentIndex = (currentIndex + 1) % colorSchemes.length;

            // Add the new background and text color classes
            body.classList.add(colorSchemes[currentIndex].bgClass);
            textDynamic.forEach(element => {
                element.classList.add(colorSchemes[currentIndex].textClass);
            });
        }

        // Change color scheme every 0.5 seconds
        setInterval(changeColorScheme, 500);
    </script>

</body>
</html>
