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
    
    // Server-side validation for contact number (Prevent 10 same digits and enforce 10 digits)
    $clean_number = preg_replace('/\D/', '', $contact_number);
    // If it starts with 91 and has 12 digits, it's likely +91XXXXXXXXXX
    $digits_to_check = (strlen($clean_number) === 12 && substr($clean_number, 0, 2) === '91') ? substr($clean_number, 2) : $clean_number;
    
    if (strlen($digits_to_check) !== 10) {
        echo json_encode(["status" => "error", "message" => "Mobile number must be exactly 10 digits."]);
        exit();
    }
    
    $unique_digits = count(array_unique(str_split($digits_to_check)));
    
    if ($unique_digits === 1) {
        echo json_encode(["status" => "error", "message" => "Mobile number cannot consist of 10 identical digits."]);
        exit();
    }
    
    // Generate a unique Prisoner ID if not provided (though form has a readonly one)
    $prisoner_id = "P-" . date("Y") . "-" . rand(1000, 9999);

    // Handle photo storage
    $upload_dir = "../uploads/prisoners/" . $prisoner_id . "/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $photo_path = ""; // Primary photo path (front)
    $angles = ['front', 'left', 'right'];
    $saved_photos = [];

    foreach ($angles as $angle) {
        $field_name = "photo_" . $angle;
        if (!empty($_POST[$field_name])) {
            $captured_data = $_POST[$field_name];
            $image_parts = explode(";base64,", $captured_data);
            if (isset($image_parts[1])) {
                $image_base64 = base64_decode($image_parts[1]);
                $filename = $angle . ".jpg";
                $full_path = $upload_dir . $filename;
                file_put_contents($full_path, $image_base64);
                $saved_photos[$angle] = $full_path;
                
                if ($angle === 'front') {
                    $photo_path = $full_path;
                }
            }
        }
    }

    $fingerprint_data = "SAMPLE_FINGERPRINT_DATA"; // Placeholder

    $sql = "INSERT INTO prisoners (prisoner_id, full_name, dob, gender, nationality, contact_number, address, block_wing, cell_number, crime, sentence_duration, admission_date, expected_release, photo_path, fingerprint_data) 
            VALUES ('$prisoner_id', '$full_name', '$dob', '$gender', '$nationality', '$contact_number', '$address', '$block_wing', '$cell_number', '$crime', '$sentence_duration', '$admission_date', '$expected_release', '$photo_path', '$fingerprint_data')";

    if ($conn->query($sql) === TRUE) {
        // Trigger Python script to reload face data
        $ch = curl_init("http://127.0.0.1:5000/reload_data");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_exec($ch);
        curl_close($ch);

        echo json_encode(["status" => "success", "message" => "Prisoner registered successfully with 3-angle face biometric!", "prisoner_id" => $prisoner_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
