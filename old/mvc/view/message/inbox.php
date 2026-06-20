<?php
/**
 * mvc/view/message/inbox.php
 * متغیرها: $conversations, $unread_total
 */
?>
<section class="section">
  <div class="container container--narrow">

    <div class="page-header">
      <h1 class="page-title">
        پیام‌ها
        <?php if ($unread_total): ?>
          <span class="badge badge--primary"><?= $unread_total ?></span>
        <?php endif; ?>
      </h1>
      <button class="btn btn-primary" onclick="document.getElementById('modal-new-group').hidden=false">
        <i class="fa-solid fa-users" aria-hidden="true"></i> گروه جدید
      </button>
    </div>

    <?php if (empty($conversations)): ?>
      <div class="empty-state">
        <i class="fa-solid fa-inbox" aria-hidden="true"></i>
        <p>هیچ مکالمه‌ای ندارید</p>
      </div>
    <?php else: ?>
      <ul class="conversation-list" role="list">
        <?php foreach ($conversations as $conv): ?>
          <li class="conversation-item <?= $conv['unread_count'] ? 'conversation-item--unread' : '' ?>">
            <a href="/message/show/<?= $conv['id'] ?>" class="conversation-item__link">

              <!-- آواتار -->
              <div class="conversation-item__avatar">
                <?php if ($conv['type'] === 'direct' && $conv['other_avatar']): ?>
                  <img src="<?= htmlspecialchars($conv['other_avatar']) ?>"
                       alt="" class="avatar avatar--md">
                <?php elseif ($conv['type'] === 'group'): ?>
                  <div class="avatar avatar--md avatar--group">
                    <i class="fa-solid fa-users" aria-hidden="true"></i>
                  </div>
                <?php else: ?>
                  <div class="avatar avatar--md avatar--placeholder">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                  </div>
                <?php endif; ?>
              </div>

              <!-- محتوا -->
              <div class="conversation-item__body">
                <div class="conversation-item__header">
                  <span class="conversation-item__name">
                    <?php if ($conv['type'] === 'direct'): ?>
                      <?= htmlspecialchars($conv['other_user_name'] ?? $conv['other_username'] ?? 'کاربر') ?>
                    <?php else: ?>
                      <?= htmlspecialchars($conv['title'] ?? 'گروه') ?>
                    <?php endif; ?>
                  </span>
                  <?php if ($conv['last_message_at']): ?>
                    <span class="conversation-item__time">
                      <?= jdate($conv['last_message_at'], 'H:i') ?>
                    </span>
                  <?php endif; ?>
                </div>

                <div class="conversation-item__preview">
                  <?php if ($conv['last_message_type'] === 'file'): ?>
                    <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
                    <span>فایل</span>
                  <?php else: ?>
                    <span><?= htmlspecialchars(mb_substr($conv['last_message'] ?? '', 0, 60)) ?></span>
                  <?php endif; ?>

                  <?php if ($conv['unread_count']): ?>
                    <span class="badge badge--primary badge--sm"><?= $conv['unread_count'] ?></span>
                  <?php endif; ?>
                </div>
              </div>

            </a>

            <!-- منوی مکالمه -->
            <div class="conversation-item__menu">
              <button class="btn-icon" onclick="toggleConvMenu(<?= $conv['id'] ?>)"
                      aria-label="گزینه‌ها">
                <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>
              </button>
              <div id="conv-menu-<?= $conv['id'] ?>" class="dropdown-menu hidden">
                <a href="/message/mute/<?= $conv['id'] ?>" class="dropdown-item">
                  <i class="fa-solid fa-bell-slash" aria-hidden="true"></i>
                  <?= $conv['is_muted'] ?? false ? 'رفع سکوت' : 'سکوت' ?>
                </a>
                <a href="/message/delete-conv/<?= $conv['id'] ?>"
                   class="dropdown-item dropdown-item--danger"
                   onclick="return confirm('مکالمه حذف شود؟')">
                  <i class="fa-solid fa-trash" aria-hidden="true"></i>
                  حذف مکالمه
                </a>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

  </div>
</section>

<!-- Modal: گروه جدید -->
<div id="modal-new-group" class="modal" hidden>
  <div class="modal__content">
    <div class="modal__header">
      <h2 class="modal__title">گروه جدید</h2>
      <button class="btn-icon" onclick="document.getElementById('modal-new-group').hidden=true">
        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
      </button>
    </div>
    <form action="/message/group/create" method="POST" class="form">
      <div class="form__group">
        <label class="form__label" for="group-title">نام گروه *</label>
        <input class="form__input" type="text" id="group-title" name="title" required
               placeholder="مثلاً: گروه کلاس گیتار">
      </div>
      <div class="form__group">
        <label class="form__label">اعضا *</label>
        <input class="form__input" type="text" id="member-search"
               placeholder="جستجوی کاربر..." autocomplete="off">
        <div id="member-results" class="member-search-results"></div>
        <div id="selected-members" class="selected-members"></div>
        <input type="hidden" name="members[]" id="member-ids">
      </div>
      <div class="form__actions">
        <button type="submit" class="btn btn-primary">ساخت گروه</button>
        <button type="button" class="btn btn-ghost"
                onclick="document.getElementById('modal-new-group').hidden=true">انصراف</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleConvMenu(id) {
  const menu = document.getElementById('conv-menu-' + id);
  document.querySelectorAll('.dropdown-menu').forEach(m => { if (m !== menu) m.classList.add('hidden'); });
  menu.classList.toggle('hidden');
}

// جستجوی کاربر برای گروه
let selectedIds = [];
document.getElementById('member-search')?.addEventListener('input', function() {
  const q = this.value.trim();
  if (q.length < 2) return;
  fetch('/api/user/search?q=' + encodeURIComponent(q))
    .then(r => r.json())
    .then(res => {
      const box = document.getElementById('member-results');
      box.innerHTML = '';
      (res.data || []).forEach(u => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'member-result-item';
        btn.textContent = u.fullname || u.username;
        btn.onclick = () => addMember(u);
        box.appendChild(btn);
      });
    });
});

function addMember(user) {
  if (selectedIds.includes(user.id)) return;
  selectedIds.push(user.id);
  const box   = document.getElementById('selected-members');
  const chip  = document.createElement('span');
  chip.className = 'member-chip';
  chip.innerHTML = `${user.fullname || user.username} <button type="button" onclick="removeMember(${user.id}, this)">×</button>`;
  chip.dataset.id = user.id;
  box.appendChild(chip);
  syncMemberInput();
}

function removeMember(id, btn) {
  selectedIds = selectedIds.filter(i => i !== id);
  btn.closest('.member-chip').remove();
  syncMemberInput();
}

function syncMemberInput() {
  document.getElementById('member-ids').value = selectedIds.join(',');
  // باید به name="members[]" تبدیل بشه — با JS اضافه کن
  const form = document.querySelector('#modal-new-group form');
  form.querySelectorAll('input[name="members[]"]').forEach(i => i.remove());
  selectedIds.forEach(id => {
    const inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'members[]'; inp.value = id;
    form.appendChild(inp);
  });
}
</script>
