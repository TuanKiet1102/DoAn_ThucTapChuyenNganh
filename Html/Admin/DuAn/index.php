<?php
session_start();

$dataFile = __DIR__ . '/data.json';

// Initialize data file if not exists
if (!file_exists($dataFile)) {
    $initial = [
        "days" => [],
        "archive_done" => []
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

// Current date from query or today
$currentDate = $_GET['date'] ?? date('Y-m-d');

// Ensure a day exists (auto-create)
if (!isset($data['days'][$currentDate])) {
    $data['days'][$currentDate] = [
        "columns" => [
            ["id" => "todo", "name" => "To Do", "cards" => []],
            ["id" => "doing", "name" => "Doing", "cards" => []],
            ["id" => "done", "name" => "Done", "cards" => []]
        ]
    ];
    saveData($dataFile, $data);
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // For safety, accept explicit dates from POST (from_date / to_date / date)
    $postDate = $_POST['date'] ?? $currentDate;
    $fromDate = $_POST['from_date'] ?? $postDate;
    $toDate   = $_POST['to_date'] ?? $postDate;

    // Ensure days exist
    foreach ([$postDate, $fromDate, $toDate] as $d) {
        if (!isset($data['days'][$d])) {
            $data['days'][$d] = [
                "columns" => [
                    ["id" => "todo", "name" => "To Do", "cards" => []],
                    ["id" => "doing", "name" => "Doing", "cards" => []],
                    ["id" => "done", "name" => "Done", "cards" => []]
                ]
            ];
        }
    }

    if ($action === 'add_card') {
        $columnId = $_POST['column_id'] ?? '';
        $title    = trim($_POST['card_title'] ?? '');
        $desc     = trim($_POST['card_desc'] ?? '');

        if ($title !== '' && $columnId !== '') {
            foreach ($data['days'][$postDate]['columns'] as &$col) {
                if ($col['id'] === $columnId) {
                    $cardId = 'c-' . substr(uniqid('', true), -6);
                    $card = ["id" => $cardId, "title" => $title, "desc" => $desc, "date" => $postDate, "col" => $columnId];
                    $col['cards'][] = $card;

                    if ($columnId === 'done') {
                        $data['archive_done'][] = $card;
                    }
                    break;
                }
            }
            unset($col);
            saveData($dataFile, $data);
        }

        header('Location: index.php?date=' . urlencode($postDate));
        exit;
    }

    if ($action === 'move_card') {
        $cardId   = $_POST['card_id'] ?? '';
        $fromCol  = $_POST['from_col'] ?? '';
        $toCol    = $_POST['to_col'] ?? '';
        $position = intval($_POST['position'] ?? 0);

        if ($cardId && $fromCol && $toCol) {
            $card = null;
            // Remove from source day/column
            foreach ($data['days'][$fromDate]['columns'] as &$col) {
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

            if ($card) {
                // Update metadata
                $card['date'] = $toDate;
                $card['col']  = $toCol;

                // Insert into target day/column
                foreach ($data['days'][$toDate]['columns'] as &$col) {
                    if ($col['id'] === $toCol) {
                        $position = max(0, min($position, count($col['cards'])));
                        array_splice($col['cards'], $position, 0, [$card]);

                        // If moved into Done, append to archive
                        if ($toCol === 'done') {
                            $data['archive_done'][] = $card;
                        }
                        break;
                    }
                }
                unset($col);
                saveData($dataFile, $data);
            }
        }

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(["ok" => true, "from" => $fromDate, "to" => $toDate]);
        exit;
    }

    if ($action === 'delete_card') {
        $cardId   = $_POST['card_id'] ?? '';
        $columnId = $_POST['column_id'] ?? '';
        $date     = $_POST['date'] ?? $postDate;

        if ($cardId && $columnId) {
            foreach ($data['days'][$date]['columns'] as &$col) {
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

        header('Location: index.php?date=' . urlencode($date));
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
<link rel="stylesheet" href="DuAn.css"/>
</head>
<body>
<header class="app-header">
  <h1>Trello Mini</h1>
  <form method="get" class="inline-form">
    <input type="date" name="date" value="<?= htmlspecialchars($currentDate) ?>" required/>
    <button type="submit">Chọn ngày</button>
  </form>
</header>

<main class="board">
<?php foreach ($data['days'][$currentDate]['columns'] as $col): ?>
  <section class="column" data-col-id="<?= htmlspecialchars($col['id']) ?>">
    <div class="column-header">
      <h2><?= htmlspecialchars($col['name']) ?> — <?= htmlspecialchars($currentDate) ?></h2>
    </div>

    <div class="cards" data-col-id="<?= htmlspecialchars($col['id']) ?>" data-date="<?= htmlspecialchars($currentDate) ?>">
      <?php foreach ($col['cards'] as $card): ?>
        <article class="card" draggable="true"
                 data-card-id="<?= htmlspecialchars($card['id']) ?>"
                 data-col-id="<?= htmlspecialchars($col['id']) ?>"
                 data-date="<?= htmlspecialchars($card['date'] ?? $currentDate) ?>">
          <div class="card-title"><?= htmlspecialchars($card['title']) ?></div>
          <?php if (!empty($card['desc'])): ?>
            <div class="card-desc"><?= nl2br(htmlspecialchars($card['desc'])) ?></div>
          <?php endif; ?>
          <form method="post" class="delete-form">
            <input type="hidden" name="action" value="delete_card"/>
            <input type="hidden" name="date" value="<?= htmlspecialchars($currentDate) ?>"/>
            <input type="hidden" name="card_id" value="<?= htmlspecialchars($card['id']) ?>"/>
            <input type="hidden" name="column_id" value="<?= htmlspecialchars($col['id']) ?>"/>
            <button type="submit" title="Xóa thẻ">×</button>
          </form>
        </article>
      <?php endforeach; ?>
    </div>

    <form method="post" class="add-card-form">
      <input type="hidden" name="action" value="add_card"/>
      <input type="hidden" name="date" value="<?= htmlspecialchars($currentDate) ?>"/>
      <input type="hidden" name="column_id" value="<?= htmlspecialchars($col['id']) ?>"/>
      <input type="text" name="card_title" placeholder="Tiêu đề thẻ" required/>
      <textarea name="card_desc" placeholder="Mô tả (tuỳ chọn)"></textarea>
      <button type="submit">Thêm thẻ</button>
    </form>
  </section>
<?php endforeach; ?>
</main>

<section class="archive">
  <div class="archive-header">
    <h2>Toàn bộ công việc Done</h2>
    <span class="archive-count"><?= count($data['archive_done']) ?> thẻ</span>
  </div>
  <div class="archive-cards">
    <?php if (empty($data['archive_done'])): ?>
      <div class="archive-empty">Chưa có thẻ hoàn thành.</div>
    <?php else: ?>
      <?php foreach (array_reverse($data['archive_done']) as $card): ?>
        <div class="card card-archive">
          <div class="card-title"><?= htmlspecialchars($card['title']) ?></div>
          <?php if (!empty($card['desc'])): ?>
            <div class="card-desc"><?= nl2br(htmlspecialchars($card['desc'])) ?></div>
          <?php endif; ?>
          <div class="card-meta">
            <span class="chip chip-date"><?= htmlspecialchars($card['date'] ?? '') ?></span>
            <span class="chip chip-col"><?= htmlspecialchars(strtoupper($card['col'] ?? '')) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<script src="script.js"></script>
</body>
</html>
