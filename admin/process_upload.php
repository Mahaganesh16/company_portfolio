<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $target_dir = "../assets/images/uploads/";
    $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo json_encode(['success' => true, 'path' => 'assets/images/uploads/' . $new_filename]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Upload failed.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
