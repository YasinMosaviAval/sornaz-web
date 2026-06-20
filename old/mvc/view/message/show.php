<?php
/**
 * mvc/view/message/show.php
 * متغیرها: $conv, $messages, $members
 */
$isGroup    = $conv['type'] === 'group';
$isAdmin    = $conv['role'] === 'admin';
$otherMember= null;
if (!$isGroup) {
  foreach ($members as $m) {
    if ($m['user_id'] !== getUserId()) { $otherMember = $m; break; }
  }
}
?>

<section class="chat-layout">

  <!-- Sidebar: اعضا (برای گروه) -->
  <?php if ($isGroup): ?>
    <aside class="chat-sidebar" aria-label="اعضای گروه">
      <div class="chat-sidebar__header">
        <h2 class="chat-sidebar__title"><?= htmlspecialchars($conv['title'] ?? 'گروه') ?></h2>
        <?php if ($isAdmin): ?>
          <button class="btn-icon" title="ویرایش گروه" onclick="document.getElementById('modal-edit-group').hidden=false">
            <i class="fa-solid fa-pen" aria-hidden="true"></i>
          </button>
        <?php endif; ?>
      </div>
      <ul class="member-list" role="list">
        <?php foreach ($members as $m): ?>
          <li class="member-list__item">
            <?php if ($m['avatar']): ?>
              <img src="<?= htmlspecialchars($m['avatar']) ?>" alt="" class="avatar avatar--sm">
            <?php else: ?>
              <div class="avatar avatar--sm avatar--placeholder"><i class="fa-solid fa-user"></i></div>
            <?php endif; ?>
            <span class="member-list__name">
              <?= htmlspecialchars($m['fullname'] ?? $m['username'] ?? '') ?>
              <?php if ($m['role'] === 'admin'): ?>
                <span class="badge badge--sm">ادمین</span>
              <?php endif; ?>
              <?php if ($m['user_id'] === getUserId()): ?>
                <span class="badge badge--sm badge--muted">شما</span>
              <?php endif; ?>
            </span>
            <?php if ($isAdmin && $m['user_id'] !== getUserId()): ?>
              <button class="btn-icon btn-icon--danger"
                      onclick="removeMember(<?= $m['user_id'] ?>, <?= $conv['id'] ?>)"
                      title="حذف از گروه">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
              </button>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="chat-sidebar__actions">
        <a href="/message/leave/<?= $conv['id'] ?>" class="btn btn-outline btn--sm"
           onclick="return confirm('از گروه خارج شوید؟')">
          <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
          ترک گروه
        </a>
      </div>
    </aside>
  <?php endif; ?>

  <!-- Main chat -->
  <div class="chat-main">

    <!-- Header -->
    <header class="chat-header">
      <a href="/message" class="chat-header__back">
        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
      </a>
      <?php if ($isGroup): ?>
        <div class="avatar avatar--md avatar--group"><i class="fa-solid fa-users"></i></div>
        <div class="chat-header__info">
          <span class="chat-header__name"><?= htmlspecialchars($conv['title'] ?? 'گروه') ?></span>
          <span class="chat-header__sub"><?= count($members) ?> عضو</span>
        </div>
      <?php else: ?>
        <?php if ($otherMember && $otherMember['avatar']): ?>
          <img src="<?= htmlspecialchars($otherMember['avatar']) ?>" alt="" class="avatar avatar--md">
        <?php else: ?>
          <div class="avatar avatar--md avatar--placeholder"><i class="fa-solid fa-user"></i></div>
        <?php endif; ?>
        <div class="chat-header__info">
          <span class="chat-header__name">
            <?= htmlspecialchars($otherMember['fullname'] ?? $otherMember['username'] ?? 'کاربر') ?>
          </span>
          <a href="/profile/show/<?= $otherMember['user_id'] ?? '' ?>" class="chat-header__sub">
            مشاهده پروفایل
          </a>
        </div>
      <?php endif; ?>

      <!-- گزینه‌ها -->
      <div class="chat-header__actions">
        <button class="btn-icon" onclick="toggleMenu('chat-options')" aria-label="گزینه‌ها">
          <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>
        </button>
        <div id="chat-options" class="dropdown-menu dropdown-menu--left hidden">
          <form action="/message/mute/<?= $conv['id'] ?>" method="POST">
            <button type="submit" class="dropdown-item">
              <i class="fa-solid fa-bell-slash" aria-hidden="true"></i>
              <?= $conv['is_muted'] ? 'رفع سکوت' : 'سکوت' ?>
            </button>
          </form>
          <a href="/message/delete-conv/<?= $conv['id'] ?>" class="dropdown-item dropdown-item--danger"
             onclick="return confirm('مکالمه حذف شود؟')">
            <i class="fa-solid fa-trash" aria-hidden="true"></i>
            حذف مکالمه
          </a>
        </div>
      </div>
    </header>

    <!-- پیام‌ها -->
    <div class="chat-messages" id="chat-messages" role="log" aria-live="polite">

      <?php if (count($messages) >= 30): ?>
        <div class="chat-load-more">
          <button class="btn btn-ghost btn--sm" id="load-more"
                  data-before="<?= $messages[0]['id'] ?? 0 ?>"
                  data-conv="<?= $conv['id'] ?>">
            بارگذاری پیام‌های قدیمی‌تر
          </button>
        </div>
      <?php endif; ?>

      <?php foreach ($messages as $msg): ?>
        <?php $isMine = $msg['sender_id'] === getUserId(); ?>
        <div class="message <?= $isMine ? 'message--mine' : 'message--theirs' ?>"
             id="msg-<?= $msg['id'] ?>"
             data-id="<?= $msg['id'] ?>">

          <?php if (!$isMine): ?>
            <div class="message__avatar">
              <?php if ($msg['sender_avatar']): ?>
                <img src="<?= htmlspecialchars($msg['sender_avatar']) ?>" alt="" class="avatar avatar--sm">
              <?php else: ?>
                <div class="avatar avatar--sm avatar--placeholder"><i class="fa-solid fa-user"></i></div>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <div class="message__bubble">

            <?php if (!$isMine && $isGroup): ?>
              <span class="message__sender">
                <?= htmlspecialchars($msg['sender_name'] ?? $msg['sender_username'] ?? '') ?>
              </span>
            <?php endif; ?>

            <!-- reply -->
            <?php if ($msg['reply_content']): ?>
              <div class="message__reply">
                <span class="message__reply-sender"><?= htmlspecialchars($msg['reply_sender_name'] ?? '') ?></span>
                <p><?= htmlspecialchars(mb_substr($msg['reply_content'], 0, 80)) ?></p>
              </div>
            <?php endif; ?>

            <!-- محتوا -->
            <?php if ($msg['type'] === 'file'): ?>
              <div class="message__file">
                <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
                <a href="<?= htmlspecialchars($msg['file_path'] ?? '') ?>"
                   download="<?= htmlspecialchars($msg['file_name'] ?? '') ?>"
                   target="_blank">
                  <?= htmlspecialchars($msg['file_name'] ?? 'فایل') ?>
                </a>
                <?php if ($msg['file_size']): ?>
                  <span class="message__file-size">
                    (<?= round($msg['file_size'] / 1024) ?> KB)
                  </span>
                <?php endif; ?>
              </div>
            <?php elseif ($msg['type'] === 'system'): ?>
              <p class="message__system"><?= htmlspecialchars($msg['content'] ?? '') ?></p>
            <?php else: ?>
              <p class="message__text"><?= nl2br(htmlspecialchars($msg['content'] ?? '')) ?></p>
            <?php endif; ?>

            <div class="message__meta">
              <span class="message__time"><?= date('H:i', strtotime($msg['created_at'])) ?></span>
              <?php if ($msg['edited_at']): ?>
                <span class="message__edited">(ویرایش‌شده)</span>
              <?php endif; ?>
              <?php if ($isMine): ?>
                <span class="message__status">
                  <?= $msg['is_read'] ? '✓✓' : '✓' ?>
                </span>
              <?php endif; ?>
            </div>

          </div>

          <!-- منوی پیام -->
          <?php if ($msg['type'] !== 'system'): ?>
            <div class="message__actions">
              <button class="btn-icon btn-icon--sm"
                      onclick="setReply(<?= $msg['id'] ?>, '<?= htmlspecialchars(addslashes($msg['content'] ?? ''), ENT_QUOTES) ?>')"
                      title="پاسخ">
                <i class="fa-solid fa-reply" aria-hidden="true"></i>
              </button>
              <?php if ($isMine && !$msg['deleted_at']): ?>
                <button class="btn-icon btn-icon--sm"
                        onclick="setEdit(<?= $msg['id'] ?>, '<?= htmlspecialchars(addslashes($msg['content'] ?? ''), ENT_QUOTES) ?>')"
                        title="ویرایش">
                  <i class="fa-solid fa-pen" aria-hidden="true"></i>
                </button>
                <form action="/message/delete/<?= $msg['id'] ?>" method="POST" style="display:inline">
                  <input type="hidden" name="conversation_id" value="<?= $conv['id'] ?>">
                  <button type="submit" class="btn-icon btn-icon--sm btn-icon--danger"
                          onclick="return confirm('حذف شود؟')" title="حذف">
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                  </button>
                </form>
              <?php endif; ?>
            </div>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>
    </div>

    <!-- نوار ارسال -->
    <div class="chat-composer">

      <!-- نمایش reply -->
      <div id="reply-preview" class="chat-reply-preview hidden">
        <div class="chat-reply-preview__content">
          <span class="chat-reply-preview__label">در پاسخ به:</span>
          <span id="reply-text"></span>
        </div>
        <button type="button" class="btn-icon" onclick="clearReply()">
          <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
      </div>

      <!-- فرم ارسال متن -->
      <form id="send-form" action="/message/send/<?= $conv['id'] ?>" method="POST" class="chat-composer__form">
        <input type="hidden" name="reply_to_id" id="reply-to-id" value="">

        <label class="btn-icon" for="file-upload" title="ارسال فایل">
          <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
        </label>
        <input type="file" id="file-upload" hidden accept="image/*,.pdf,audio/*"
               onchange="uploadFile(this)">

        <textarea class="chat-composer__input" name="content" id="message-input"
                  rows="1" placeholder="پیام بنویسید..."
                  onkeydown="handleKey(event)" oninput="autoResize(this)"></textarea>

        <button type="submit" class="btn-icon btn-icon--primary" title="ارسال">
          <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
        </button>
      </form>

      <!-- فرم ویرایش -->
      <form id="edit-form" action="" method="POST" class="chat-composer__form hidden">
        <span class="chat-composer__edit-label">ویرایش پیام</span>
        <textarea class="chat-composer__input" name="content" id="edit-input" rows="1"></textarea>
        <input type="hidden" name="conversation_id" value="<?= $conv['id'] ?>">
        <button type="submit" class="btn-icon btn-icon--primary" title="ذخیره">
          <i class="fa-solid fa-check" aria-hidden="true"></i>
        </button>
        <button type="button" class="btn-icon" onclick="clearEdit()" title="انصراف">
          <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
      </form>

    </div>
  </div>

</section>

<script>
// اسکرول به پایین
const msgList = document.getElementById('chat-messages');
msgList.scrollTop = msgList.scrollHeight;

// Reply
function setReply(id, text) {
  document.getElementById('reply-to-id').value = id;
  document.getElementById('reply-text').textContent = text.substring(0, 80);
  document.getElementById('reply-preview').classList.remove('hidden');
  document.getElementById('message-input').focus();
}
function clearReply() {
  document.getElementById('reply-to-id').value = '';
  document.getElementById('reply-preview').classList.add('hidden');
}

// Edit
function setEdit(id, text) {
  const form = document.getElementById('edit-form');
  form.action = '/message/edit/' + id;
  document.getElementById('edit-input').value = text;
  document.getElementById('send-form').classList.add('hidden');
  form.classList.remove('hidden');
  document.getElementById('edit-input').focus();
}
function clearEdit() {
  document.getElementById('edit-form').classList.add('hidden');
  document.getElementById('send-form').classList.remove('hidden');
}

// Ctrl+Enter = ارسال
function handleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    document.getElementById('send-form').submit();
  }
}

// Auto resize textarea
function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 150) + 'px';
}

// آپلود فایل
function uploadFile(input) {
  if (!input.files[0]) return;
  const form = new FormData();
  form.append('file', input.files[0]);
  fetch('/message/upload/<?= $conv['id'] ?>', { method: 'POST', body: form })
    .then(() => location.reload());
}

// Load more
document.getElementById('load-more')?.addEventListener('click', function() {
  const btn    = this;
  const before = btn.dataset.before;
  const conv   = btn.dataset.conv;
  fetch(`/api/message/messages/${conv}?before_id=${before}&limit=30`)
    .then(r => r.json())
    .then(res => {
      const msgs = res.data?.messages || [];
      if (!msgs.length) { btn.remove(); return; }
      // ... render messages و prepend به chat
      if (msgs.length < 30) btn.remove();
      else btn.dataset.before = msgs[0].id;
    });
});

// Remove member از گروه
function removeMember(userId, convId) {
  if (!confirm('عضو حذف شود؟')) return;
  fetch('/api/message/' + convId + '/remove-member', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ user_id: userId })
  }).then(() => location.reload());
}

function toggleMenu(id) {
  document.getElementById(id)?.classList.toggle('hidden');
}
</script>
