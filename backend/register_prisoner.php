<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $block_wing = mysqli_real_escape_string($conn, $_POST['block_wing']);
    $cell_number = mysqli_real_escape_string($conn, $_POST['cell_number']);
    $crime = mysqli_real_escape_string($conn, $_POST['crime']);
    $sentence_duration = mysqli_real_escape_string($conn, $_POST['sentence_duration']);
    $admission_date = mysqli_real_escape_string($conn, $_POST['admission_date']);
    $expected_release = mysqli_real_escape_string($conn, $_POST['expected_release']);
    
    // Generate a unique Prisoner ID if not provided (though form has a readonly one)
    $prisoner_id = "P-" . date("Y") . "-" . rand(1000, 9999);

    // Handle photo storage
    $photo_path = ""; 
    $upload_dir = "../uploads/prisoners/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_POST['captured_photo'])) {
        // Handle Captured Photo (Base64)
        $captured_data = $_POST['captured_photo'];
        $image_parts = explode(";base64,", $captured_data);
        $image_base64 = base64_decode($image_parts[1]);
        $filename = "captured_" . $prisoner_id . "_" . time() . ".jpg";
        $photo_path = $upload_dir . $filename;
        file_put_contents($photo_path, $image_base64);
    } elseif (isset($_FILES['prisoner_photo']) && $_FILES['prisoner_photo']['error'] === UPLOAD_ERR_OK) {
        // Handle Uploaded Photo
        $file_ext = pathinfo($_FILES['prisoner_photo']['name'], PATHINFO_EXTENSION);
        $filename = "uploaded_" . $prisoner_id . "_" . time() . "." . $file_ext;
        $photo_path = $upload_dir . $filename;
        move_uploaded_file($_FILES['prisoner_photo']['tmp_name'], $photo_path);
    }

    $fingerprint_data = "SAMPLE_FINGERPRINT_DATA"; // Placeholder

    $sql = "INSERT INTO prisoners (prisoner_id, full_name, dob, gender, nationality, contact_number, address, block_wing, cell_number, crime, sentence_duration, admission_date, expected_release, photo_path, fingerprint_data) 
            VALUES ('$prisoner_id', '$full_name', '$dob', '$gender', '$nationality', '$contact_number', '$address', '$block_wing', '$cell_number', '$crime', '$sentence_duration', '$admission_date', '$expected_release', '$photo_path', '$fingerprint_data')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Prisoner registered successfully!", "prisoner_id" => $prisoner_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
