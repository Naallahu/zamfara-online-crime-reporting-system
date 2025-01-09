<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Generate unique reference number
    $reference = 'ZCRS-' . date('Ymd') . '-' . rand(1000, 9999);
    
    // Collect form data
    $crime_type = $_POST['crime_type'];
    $description = $_POST['description'];
    $lga = $_POST['lga'];
    $location = $_POST['location'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $crime_date = $_POST['crime_date'];
    $crime_time = $_POST['crime_time'];
    
    // Handle file uploads
    $evidenceFiles = [];
    if (!empty($_FILES['evidence']['name'][0])) {
        $uploadDir = '../../uploads/evidence/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        foreach ($_FILES['evidence']['tmp_name'] as $key => $tmp_name) {
            $fileName = $reference . '-' . $_FILES['evidence']['name'][$key];
            move_uploaded_file($tmp_name, $uploadDir . $fileName);
            $evidenceFiles[] = $fileName;
        }
    }
    
    // Handle voice recording
    $voiceFile = '';
    if (isset($_POST['audio_data'])) {
        $uploadDir = '../../uploads/voice/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $audioData = $_POST['audio_data'];
        $audioData = str_replace('data:audio/wav;base64,', '', $audioData);
        $audioData = base64_decode($audioData);
        
        $voiceFile = $reference . '-voice.wav';
        file_put_contents($uploadDir . $voiceFile, $audioData);
    }
    
    // Save to database
    $sql = "INSERT INTO reports (
        reference_number,
        crime_type,
        description,
        lga,
        location,
        latitude,
        longitude,
        crime_date,
        crime_time,
        evidence_files,
        voice_recording,
        status,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'sssssssssss',
        $reference,
        $crime_type,
        $description,
        $lga,
        $location,
        $latitude,
        $longitude,
        $crime_date,
        $crime_time,
        json_encode($evidenceFiles),
        $voiceFile
    );
    
    if ($stmt->execute()) {
        header("Location: report_success.php?ref=" . $reference);
        exit();
    } else {
        header("Location: report.php?error=1");
        exit();
    }
}
?>
