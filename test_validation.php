<?php
/**
 * Test script for register_prisoner.php contact_number validation
 */

function test_validation($number) {
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => "http://localhost/MP/backend/register_prisoner.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'full_name' => 'Test User',
            'dob' => '1990-01-01',
            'gender' => 'Male',
            'nationality' => 'Indian',
            'contact_number' => $number,
            'address' => 'Test Address',
            'block_wing' => 'Main Block A',
            'cell_number' => 'C-101',
            'crime' => 'Test Crime',
            'sentence_duration' => '1 Years',
            'admission_date' => date('Y-m-d'),
            'expected_release' => date('Y-m-d', strtotime('+1 year'))
        ]),
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        return "cURL Error: " . $err;
    } else {
        return $response;
    }
}

echo "Testing '9999999999':\n";
echo test_validation('9999999999') . "\n\n";

echo "Testing '9876543210':\n";
echo test_validation('9876543210') . "\n";
?>
