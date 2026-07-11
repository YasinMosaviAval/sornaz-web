<div class="sn-card academy-card">
    <div class="academy-avatar">
        👤
    </div>
    <div class="academy-content">
        <h2>
            <?= e($academy['username']) ?>
        </h2>
        <div class="academy-meta">
            <span>
                📧
                <?= e($academy['email'] ?: '-') ?>
            </span>
            <span>
                📱
                <?= e($academy['phone'] ?: '-') ?>
            </span>
            <span>
                🌐
                <?= e($academy['locale'] ?: '-') ?>
            </span>
        </div>
    </div>
    <div class="academy-status">
        <?php if($academy['status']=='approved'): ?>
            <span class="sn-badge success">
                فعال
            </span>
        <?php else: ?>
            <span class="sn-badge warning">
                غیرفعال
            </span>
        <?php endif; ?>
    </div>
</div>