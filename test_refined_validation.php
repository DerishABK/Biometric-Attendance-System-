<?php
/**
 * Refined test script for register_prisoner.php contact_number validation
 */

function test_validation($number) {
    echo "Testing '$number': ";
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
        echo "cURL Error: " . $err . "\n";
    } else {
        echo $response . "\n";
    }
}

test_validation('9999999999'); // Invalid (Identical)
test_validation('+91 9999999999'); // Invalid (Identical)
test_validation('987654321'); // Invalid (Short)
test_validation('+91 98765432101'); // Invalid (Long)
test_validation('+91 9876543210'); // Valid
?>
