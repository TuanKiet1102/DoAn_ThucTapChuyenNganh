<?php
session_start();

$dataFile = __DIR__ . '/data.json';

// Initialize data file if not exists
if (!file_exists($dataFile)) {
    $initial = [
        "columns" => [
            ["id" => "todo", "name" => "To Do", "cards" => []],
            ["id" => "doing", "name" => "Doing", "cards" => []],
            ["id" => "done", "name" => "Done", "cards" => []]
        ]
    ];
    file_put_contents($dataFile, json_encode($initial, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function loadData($file) {
    $json = file_get_contents($file);
    return json_decode($json, true);
}

function saveData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$data = loadData($dataFile);

// Handle actions: add column, add card, move card, delete card
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_column') {
        $name = trim($_POST['column_name'] ?? '');
        if ($name !== '') {
            $id = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name)) . '-' . substr(uniqid(), -4);
            $data['columns'][] = ["id" => $id, "name" => $name, "cards" => []];
            saveData($dataFile, $data);
        }
        header('Location: index.php');
        exit;
    }

    if ($action === 'add_card') {
        $columnId = $_POST['column_id'] ?? '';
        $title = trim($_POST['card_title'] ?? '');
        $desc  = trim($_POST['card_desc'] ?? '');
        if ($title !== '' && $columnId !== '') {
            foreach ($data['columns'] as &$col) {
                if ($col['id'] === $columnId) {
                    $cardId = 'c-' . substr(uniqid(), -6);
                    $col['cards'][] = ["id" => $cardId, "title" => $title, "desc" => $desc];
                    break;
                }
            }
            unset($col);
            saveData($dataFile, $data);
        }
        header('Location: index.php');
        exit;
    }

    if ($action === 'move_card') {
        $cardId   = $_POST['card_id'] ?? '';
        $fromCol  = $_POST['from_col'] ?? '';
        $toCol    = $_POST['to_col'] ?? '';
        $position = intval($_POST['position'] ?? 0);

        if ($cardId && $fromCol && $toCol) {
            $card = null;

            // Remove from source
            foreach ($data['columns'] as &$col) {
                if ($col['id'] === $fromCol) {
                    foreach ($col['cards'] as $i => $c) {
                        if ($c['id'] === $cardId) {
                            $card = $c;
                            array_splice($col['cards'], $i, 1);
                            break 2;
                        }
                    }
                }
            }
            unset($col);

            // Insert into target
            if ($card) {
                foreach ($data['columns'] as &$col) {
                    if ($col['id'] === $toCol) {
                        $position = max(0, min($position, count($col['cards'])));
                        array_splice($col['cards'], $position, 0, [$card]);
                        break;
                    }
                }
                unset($col);
                saveData($dataFile, $data);
            }
        }
        echo json_encode(["ok" => true]);
        exit;
    }

    if ($action === 'delete_card') {
        $cardId  = $_POST['card_id'] ?? '';
        $columnId = $_POST['column_id'] ?? '';
        if ($cardId && $columnId) {
            foreach ($data['columns'] as &$col) {
                if ($col['id'] === $columnId) {
                    foreach ($col['cards'] as $i => $c) {
                        if ($c['id'] === $cardId) {
                            array_splice($col['cards'], $i, 1);
                            break 2;
                        }
                    }
                }
            }
            unset($col);
            saveData($dataFile, $data);
        }
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Trello Mini — PHP</title>
<link rel="stylesheet" href="style.css"/>
</head>
<body>
<header class="app-header">
  <h1>Trello Mini</h1>
  <form method="post" class="inline-form">
    <input type="hidden" name="action" value="add_column"/>
    <input type="text" name="column_name" placeholder="Tên cột mới" required/>
    <button type="submit">Thêm cột</button>
  </form>
</header>

<main class="board">
<?php foreach ($data['columns'] as $col): ?>
  <section class="column" data-col-id="<?= htmlspecialchars($col['id']) ?>">
    <div class="column-header">
      <h2><?= htmlspecialchars($col['name']) ?></h2>
    </div>

    <div class="cards" data-col-id="<?= htmlspecialchars($col['id']) ?>">
      <?php foreach ($col['cards'] as $card): ?>
        <article class="card" draggable="true"
                 data-card-id="<?= htmlspecialchars($card['id']) ?>"
                 data-col-id="<?= htmlspecialchars($col['id']) ?>">
          <div class="card-title"><?= htmlspecialchars($card['title']) ?></div>
          <?php if (!empty($card['desc'])): ?>
            <div class="card-desc"><?= nl2br(htmlspecialchars($card['desc'])) ?></div>
          <?php endif; ?>
          <form method="post" class="delete-form">
            <input type="hidden" name="action" value="delete_card"/>
            <input type="hidden" name="card_id" value="<?= htmlspecialchars($card['id']) ?>"/>
            <input type="hidden" name="column_id" value="<?= htmlspecialchars($col['id']) ?>"/>
            <button type="submit" title="Xóa thẻ">×</button>
          </form>
        </article>
      <?php endforeach; ?>
    </div>

    <form method="post" class="add-card-form">
      <input type="hidden" name="action" value="add_card"/>
      <input type="hidden" name="column_id" value="<?= htmlspecialchars($col['id']) ?>"/>
      <input type="text" name="card_title" placeholder="Tiêu đề thẻ" required/>
      <textarea name="card_desc" placeholder="Mô tả (tuỳ chọn)"></textarea>
      <button type="submit">Thêm thẻ</button>
    </form>
  </section>
<?php endforeach; ?>
</main>

<script src="script.js"></script>
</body>
</html>
