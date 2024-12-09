<?php
function encrypt($data, $key) {
    $cipher = "AES-128-CTR";
    // Ensure the IV length matches the cipher (16 bytes for AES-128-CTR)
    $ivLength = openssl_cipher_iv_length($cipher);
    $iv = random_bytes($ivLength); // Generate a random IV of correct length
    $encryptedData = openssl_encrypt($data, $cipher, $key, 0, $iv);
    // Return IV + encrypted data, encoded in base64
    return base64_encode($iv . $encryptedData);
}

function decrypt($encryptedData, $key) {
    $cipher = "AES-128-CTR";
    $decodedData = base64_decode($encryptedData);
    $ivLength = openssl_cipher_iv_length($cipher);
    
    // Check if decoded data is long enough to contain both IV and ciphertext
    if (strlen($decodedData) < $ivLength) {
        // Invalid encrypted data format
        return false;
    }

    // Extract the IV and encrypted data
    $iv = substr($decodedData, 0, $ivLength);
    $ciphertext = substr($decodedData, $ivLength);

    // Decrypt the data using the extracted IV and ciphertext
    return openssl_decrypt($ciphertext, $cipher, $key, 0, $iv);
}
?>
