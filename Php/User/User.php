<?php
session_start();

$dataFile = __DIR__ . '/../../Html/Admin/DuAn/data.json'; // dùng chung data.json với Admin

function loadData($file) {
    return json_decode(file_get_contents($file), true);
}
function saveData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$data = loadData($dataFile);
$userId = $_SESSION['user_id'] ?? 'user_demo';
$action = $_POST['action'] ?? '';

// ================== TIẾP NHẬN TASK ==================
if ($action === 'accept_task') {
    $cardId = $_POST['card_id'] ?? '';

    foreach ($data['columns'] as &$col) {
        if ($col['id'] === 'todo') {
            foreach ($col['cards'] as $i => $card) {
                if ($card['id'] === $cardId && empty($card['assignee'])) {
                    // Gán người nhận và đổi trạng thái
                    $card['assignee'] = $userId;
                    $card['status'] = 'doing';

                    // Di chuyển sang cột Doing
                    foreach ($data['columns'] as &$col2) {
                        if ($col2['id'] === 'doing') {
                            array_splice($col['cards'], $i, 1);
                            $col2['cards'][] = $card;
                            break 2;
                        }
                    }
                }
            }
        }
    }
    unset($col, $col2);
    saveData($dataFile, $data);
    header('Location: ../../Html/User/DuAnUser.php');
    exit;
}

// ================== HOÀN THÀNH TASK ==================
if ($action === 'submit_task') {
    $cardId = $_POST['card_id'] ?? '';
    $uploadedFiles = [];
    $uploadDir = __DIR__ . '/../Uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Upload file
    foreach ($_FILES['task_files']['tmp_name'] as $i => $tmp) {
        $name = basename($_FILES['task_files']['name'][$i]);
        $path = $uploadDir . $name;
        if (move_uploaded_file($tmp, $path)) {
            $uploadedFiles[] = 'Uploads/' . $name;
        }
    }

    // Cập nhật trạng thái thẻ
    foreach ($data['columns'] as &$col) {
        if ($col['id'] === 'doing') {
            foreach ($col['cards'] as $i => &$card) {
                if ($card['id'] === $cardId && $card['assignee'] === $userId) {
                    $card['files'] = $uploadedFiles;
                    $card['status'] = 'done';

                    // Di chuyển sang cột Done
                    foreach ($data['columns'] as &$col2) {
                        if ($col2['id'] === 'done') {
                            array_splice($col['cards'], $i, 1);
                            $col2['cards'][] = $card;
                            break 2;
                        }
                    }
                }
            }
        }
    }
    unset($col, $col2, $card);
    saveData($dataFile, $data);
    header('Location: ../../Html/User/DuAnUser.php');
    exit;
}
