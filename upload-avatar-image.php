<?php

header('Content-Type: application/json');

/** validate image input */
$date = validateImage($_FILES['avatar']);

if (isset($data['error'])) {
    echo json_encode($data);
    exit;
}


/** image name prefixed with loggedin user id */
$imageFileName = $_SESSION['id'] . '_' . date("Ymd") . '_' . rand(10000, 990000) . "." . $date['extension'];

$targetDirectory = __DIR__ . "/public/images/avatars";

$newImagepath = $targetDirectory . "/" . $imageFileName;

/** Copy the file, returns false if failed */
if (!move_uploaded_file($date['imagePath'], $newImagepath)) {
    echo json_encode(["error" => "can't upload file"]);
    exit;
}

/** Delete the temp file */
unlink($filePath);

/** delete old user images */
$prefix = $targetDirectory . '/' . $_SESSION[$APP]['id'] . "_*";
foreach (glob($prefix) as $fp) {
    if ($fp == $newFilepath) {
        continue;
    }
    unlink($fp);
}

/**  return avatar path as response */
echo json_encode(["avatar" => "public/images/avatars/$imageFileName"]);




/**  
 * function to validate image file 
 * 
 * @param array $file image file to be validated
 * @return array data array of validation result 
 * 
 */
function validateImage($file)
{
    $result = [];

    /** check for image in request body */
    if (!isset($file)) {
        $error = "image file is required";
    }

    /** image metadata */
    $filePath = $file['tmp_name'];
    $fileSize = filesize($filePath);
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($fileInfo, $filePath);

    /** validate image */
    if ($fileSize === 0) {
        $error = "image can't be empty";
    }

    if ($fileSize > 1048576) { // 1 MB (1 byte * 1024 * 1024 * 1 (for 1 MB))
        $error = "image can't be more than 1 MB ";
    }

    $allowedTypes = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg'
    ];

    if (!in_array($fileType, array_keys($allowedTypes))) {
        $error = "image type not allowed";
    }

    /** if error return */
    if (isset($error)) {
        $result["error"] = $error;
        return $result;
    }


    /** data needed outside this function */
    $result['imagePath'] = $filePath;
    $result['extension'] = $allowedTypes[$fileType];

    return $result;
}
