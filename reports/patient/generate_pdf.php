<?php
require "../../dbh.inc.php";
require('fpdf.php'); 

session_start();

if (!isset($_SESSION['unique_id']) || empty($_SESSION['unique_id'])) {
    echo "<p>Unauthorized access. Please log in.</p>";
    exit;
}

if (!isset($_GET['appointment_id']) || empty($_GET['appointment_id'])) {
    echo "<p>No appointment ID provided.</p>";
    exit;
}

$appointmentId = $_GET['appointment_id'];
$userUniqueId = $_SESSION['unique_id'];

try {
    $query = "SELECT * FROM appointments WHERE id = :appointment_id AND pat_id = :unique_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':appointment_id', $appointmentId, PDO::PARAM_INT);
    $stmt->bindParam(':unique_id', $userUniqueId, PDO::PARAM_INT);
    $stmt->execute();
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($appointment) {
        $query1 = "SELECT * FROM users WHERE unique_id = :userUniqueId";
        $stmt1 = $pdo->prepare($query1);
        $stmt1->bindParam(":userUniqueId", $userUniqueId, PDO::PARAM_INT);
        $stmt1->execute();
        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $name = $result1['fname'] . " " . $result1['lname'];

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, 'Appointment ID: ' . $appointment['id'], 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Patient Name: ' . $name, 0, 1);  // Fixed the patient name
        $pdf->Cell(0, 10, 'Patient ID: ' . $appointment['pat_id'], 0, 1);
        $pdf->Cell(0, 10, 'Date: ' . $appointment['date'], 0, 1);
        $pdf->Cell(0, 10, 'Time: ' . $appointment['time'], 0, 1);
        $pdf->Cell(0, 10, 'Message: ' . $appointment['message'], 0, 1);
        $pdf->Cell(0, 10, 'Preferred Doctor: ' . $appointment['preferred_doctors'], 0, 1);
        $pdf->Cell(0, 10, 'Time of Registration: ' . $appointment['time_of_reg'], 0, 1);
        $pdf->Cell(0, 10, 'Dosage: ' . $appointment['dosage'], 0, 1);
        $pdf->Cell(0, 10, 'Frequency: ' . $appointment['frequency'], 0, 1);
        $pdf->Cell(0, 10, 'Duration: ' . $appointment['duration'], 0, 1);
        $pdf->Cell(0, 10, 'Route: ' . $appointment['route'], 0, 1);
        $pdf->Cell(0, 10, 'Instructions: ' . $appointment['instructions'], 0, 1);
        $pdf->Cell(0, 10, 'Refills: ' . $appointment['refills'], 0, 1);
        $pdf->Cell(0, 10, 'Signature: ' . $appointment['signature'], 0, 1);
        $pdf->Cell(0, 10, 'Drug Interaction Warnings: ' . $appointment['drug_interaction_warnings'], 0, 1);
        $pdf->Cell(0, 10, 'Additional Notes: ' . $appointment['additional_notes'], 0, 1);
        
        $pdf->Output('D', 'appointment_' . $appointment['id'] . '.pdf');
    } else {
        echo "<p>Appointment not found or you do not have permission to access it.</p>";
    }
} catch (PDOException $e) {
    echo "<p>Error retrieving appointment: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
