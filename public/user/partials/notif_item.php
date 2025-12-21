<?php
$isUnread = !$n['is_read'];

$icon = stripos($n['title'], 'deadline') !== false ? '⏰' : '🔔';
?>

<div class="notif-row <?= $isUnread ? 'unread' : 'read' ?>"
     data-id="<?= (int)$n['id'] ?>">

  <div class="notif-icon"><?= $icon ?></div>

  <div class="notif-body">
    <div class="notif-title"><?= htmlspecialchars($n['title']) ?></div>
    <div class="notif-desc"><?= htmlspecialchars($n['message']) ?></div>
  </div>

  <div class="notif-meta">
    <span class="notif-date">
      <?= date('d M Y H:i', strtotime($n['created_at'])) ?>
    </span>

    <div class="notif-actions">
      <?php if ($isUnread): ?>
        <button type="button"
                class="btn-read"
                data-id="<?= (int)$n['id'] ?>">
          ✔
        </button>
      <?php endif; ?>

      <button type="button"
              class="btn-delete"
              data-id="<?= (int)$n['id'] ?>">
        🗑️
      </button>
    </div>
  </div>

</div>
