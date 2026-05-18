<?php require __DIR__ . '/../layout/header.php'; ?>

<section class="section section--projects">
    <header class="section__hero">
        <h1 class="section__title">Projects</h1>
        <p class="section__subtitle">Open a board — tasks load per column from <code>tasks</code> where <code>project_id</code> + <code>status</code> match.</p>
    </header>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert--error"><?= e($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?php if (empty($projects)): ?>
        <div class="empty-state glass">
            <p>No active projects in this workspace.</p>
            <p class="muted small">Seed data lives in <code>projects</code> after importing <code>project_management.sql</code>.</p>
        </div>
    <?php else: ?>
        <div class="project-grid">
            <?php foreach ($projects as $p): ?>
                <?php
                    $accent = $p['color_label'] ?: '#6366f1';
                    $href   = route('board', ['project_id' => (int)$p['id']]);
                ?>
                <a class="project-card glass" style="--accent: <?= e($accent) ?>" href="<?= e($href) ?>">
                    <span class="project-card__glow" aria-hidden="true"></span>
                    <span class="project-card__bar"></span>
                    <h3 class="project-card__title"><?= e($p['name']) ?></h3>
                    <p class="project-card__desc"><?= e($p['description']) ?></p>
                    <?php if (!empty($p['deadline'])): ?>
                        <time class="project-card__deadline" datetime="<?= e($p['deadline']) ?>">
                            <?= e($p['deadline']) ?>
                        </time>
                    <?php endif; ?>
                    <span class="project-card__cta">Open board →</span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
