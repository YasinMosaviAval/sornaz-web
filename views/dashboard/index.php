<h1>Dashboard</h1>

<?php if(auth()->check()): ?>

    <p>
        Welcome
        <?= auth()->user()['email'] ?>
    </p>

<?php endif; ?>

<p>
    User ID:
    <?= user_id() ?>
</p>

<p>
    Email:
    <?= user()['email'] ?>
</p>