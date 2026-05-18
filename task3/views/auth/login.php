<?php require __DIR__ . '/../layout/header.php'; ?>

<section class="section section--login">
    <header class="section__hero">
        <h1 class="section__title">Sign in</h1>
        <p class="section__subtitle">Choose a demo user to access the board.</p>
    </header>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert--error"><?= e($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <form method="post" action="<?= e(route('login')) ?>" class="form-stack">
        <label for="user_id">User</label>
        <select id="user_id" name="user_id" required>
            <option value="">Select a user</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= e($user['id']) ?>"><?= e($user['name']) ?> (<?= e($user['email']) ?>)</option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn--primary" type="submit">Sign in</button>
    </form>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
