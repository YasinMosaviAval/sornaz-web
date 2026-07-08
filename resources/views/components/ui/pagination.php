<?php
if (empty($pagination)) {
    return;
}
?>

<nav class="sn-pagination">
    <?php if ($pagination['current_page'] > 1): ?>
        <a class="sn-page" href="?page=<?= $pagination['current_page'] - 1 ?>">قبلی</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
        <a class="sn-page <?= $i == $pagination['current_page'] ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
        <a class="sn-page" href="?page=<?= $pagination['current_page'] + 1 ?>">بعدی</a>
    <?php endif; ?>
</nav>