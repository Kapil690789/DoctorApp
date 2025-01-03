<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor'])) {
    header('Location: index.php');
    exit();
}

if (isset($_GET['format'])) {
    $format = $_GET['format'];
    $sql = "SELECT * FROM patients";
    $result = $conn->query($sql);

    if ($format === 'excel') {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="patients_data.xls"');
        
        echo "Name\tAge\tGender\tContact Number\tAdhar/KYC\tMedical Concern\tTreatment/Advice\n";
        while ($row = $result->fetch_assoc()) {
            echo $row['name'] . "\t" . $row['age'] . "\t" . $row['gender'] . "\t" . $row['contact'] . "\t" . $row['adhar'] . "\t" . $row['concern'] . "\t" . $row['treatment'] . "\n";
        }
    } elseif ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="patients_data.csv"');
        
        echo "Name,Age,Gender,Contact Number,Adhar/KYC,Medical Concern,Treatment/Advice\n";
        while ($row = $result->fetch_assoc()) {
            echo $row['name'] . "," . $row['age'] . "," . $row['gender'] . "," . $row['contact'] . "," . $row['adhar'] . "," . $row['concern'] . "," . $row['treatment'] . "\n";
        }
    } elseif ($format === 'xml') {
        header('Content-Type: text/xml');
        header('Content-Disposition: attachment;filename="patients_data.xml"');
        
        $xml = new SimpleXMLElement('<Patients/>');
        while ($row = $result->fetch_assoc()) {
            $patient = $xml->addChild('Patient');
            foreach ($row as $key => $value) {
                $patient->addChild($key, htmlspecialchars($value));
            }
        }
        echo $xml->asXML();
    } elseif ($format === 'pdf') {
        require_once('tcpdf/tcpdf.php');
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('Helvetica', '', 12);

        $content = "Name\tAge\tGender\tContact Number\tAdhar/KYC\tMedical Concern\tTreatment/Advice\n\n";
        while ($row = $result->fetch_assoc()) {
            $content .= "{$row['name']}\t{$row['age']}\t{$row['gender']}\t{$row['contact']}\t{$row['adhar']}\t{$row['concern']}\t{$row['treatment']}\n";
        }
        $pdf->Write(0, $content);
        $pdf->Output('patients_data.pdf', 'D');
    }

    exit();
}
?>
