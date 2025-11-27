<?php
session_start();
$dataFile = __DIR__ . '/../../Html/Admin/DuAn/data.json'; // đường dẫn tới data.json dùng chung
$data = json_decode(file_get_contents($dataFile), true);
$userId = $_SESSION['user_id'] ?? 'user_demo';

// Gán mặc định để tránh lỗi nếu thẻ thiếu trường
foreach ($data['columns'] as &$col) {
  foreach ($col['cards'] as &$card) {
    $card['status']   = $card['status']   ?? 'todo';
    $card['assignee'] = $card['assignee'] ?? null;
    $card['files']    = $card['files']    ?? [];
    $card['review']   = $card['review']   ?? ["approved" => null, "feedback" => ""];
  }
}
unset($col, $card);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Trello Mini — Nhân viên</title>
  <link rel="stylesheet" href="../../Html/Admin/DuAn/style.css"/>
</head>
<body>
<header class="app-header">
  <h1>Bảng công việc của bạn</h1>
</header>

<main class="board">
<?php foreach ($data['columns'] as $col): ?>
  <section class="column" data-col-id="<?= htmlspecialchars($col['id']) ?>">
    <div class="column-header">
      <h2><?= htmlspecialchars($col['name']) ?></h2>
    </div>

    <div class="cards" data-col-id="<?= htmlspecialchars($col['id']) ?>">
      <?php foreach ($col['cards'] as $card): ?>
        <article class="card" data-card-id="<?= htmlspecialchars($card['id']) ?>">
          <div class="card-title"><?= htmlspecialchars($card['title']) ?></div>
          <?php if (!empty($card['desc'])): ?>
            <div class="card-desc"><?= nl2br(htmlspecialchars($card['desc'])) ?></div>
          <?php endif; ?>

          <!-- Chỉ hiển thị nút cho User -->
          <?php if ($card['status'] === 'todo' && empty($card['assignee'])): ?>
            <form method="post" action="../../Php/User/User.php">
              <input type="hidden" name="action" value="accept_task">
              <input type="hidden" name="card_id" value="<?= htmlspecialchars($card['id']) ?>">
              <button type="submit">Tiếp nhận</button>
            </form>

          <?php elseif ($card['status'] === 'doing' && $card['assignee'] === $userId): ?>
            <button disabled>Đang tiến hành</button>
            <form method="post" action="../../Php/User/User.php" enctype="multipart/form-data">
              <input type="hidden" name="action" value="submit_task">
              <input type="hidden" name="card_id" value="<?= htmlspecialchars($card['id']) ?>">
              <input type="file" name="task_files[]" multiple required>
              <button type="submit">Hoàn thành & Gửi</button>
            </form>

          <?php elseif ($card['status'] === 'done' && $card['assignee'] === $userId): ?>
            <div class="card-status">Đã gửi, chờ duyệt</div>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
<?php endforeach; ?>
</main>
</body>
</html>
