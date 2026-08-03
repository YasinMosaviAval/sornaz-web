<?php
$currentPage ??= 1;
$totalPages ??= 1;
if($totalPages<=1){
    return;
}
?>

<nav class="pagination">
    <?php if($currentPage>1): ?>
        <a href="?page=<?=$currentPage-1?>">قبلی</a>
    <?php endif; ?>
    <?php for($i=1;$i<=$totalPages;$i++): ?>
        <a href="?page=<?=$i?>" class="<?=$i==$currentPage ? 'active' : ''?>"><?=$i?></a>
    <?php endfor; ?>
    <?php if($currentPage<$totalPages): ?>
        <a href="?page=<?=$currentPage+1?>">بعدی</a>
    <?php endif; ?>
</nav>