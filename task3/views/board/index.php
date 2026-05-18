<?php require __DIR__ . '/../layout/header.php'; ?>

<section class="board-page">
    <header class="section__head section__head--row">
        <div>
            <a href="<?= e(route('projects')) ?>" class="back-link">&larr; Projects</a>
            <h1>
                <span class="project-dot" style="background: <?= e($project['color_label'] ?: '#6366f1') ?>"></span>
                <?= e($project['name']) ?>
            </h1>
            <?php if (!empty($project['description'])): ?>
                <p class="muted"><?= e($project['description']) ?></p>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn--primary" id="open-new-task">
            + New Task
        </button>
    </header>

    <?php if (!empty($flash_ok)): ?>
        <div class="alert alert--ok"><?= e($flash_ok) ?></div>
    <?php endif; ?>

    <div class="board" data-project-id="<?= e($project['id']) ?>"
         data-api-url="<?= e(route('api_task_status')) ?>">
        <?php foreach (\Task::STATUSES as $status): ?>
            <?php
                $cards   = $columns[$status] ?? [];
                $colCls  = 'board-col board-col--' . $status;
            ?>
            <section class="<?= e($colCls) ?>" data-status="<?= e($status) ?>">
                <header class="board-col__head">
                    <h2>
                        <span class="status-dot status-dot--<?= e($status) ?>"></span>
                        <?= e(status_label($status)) ?>
                    </h2>
                    <span class="count" data-count><?= count($cards) ?></span>
                </header>

                <div class="board-col__body" data-column-body>
                    <?php foreach ($cards as $task): ?>
                        <?php require __DIR__ . '/_card.php'; ?>
                    <?php endforeach; ?>

                    <?php if (empty($cards)): ?>
                        <p class="board-col__empty" data-empty>No tasks here yet.</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
</section>

<div class="modal" id="new-task-modal" <?= !empty($old['open_modal']) ? 'data-open="1"' : '' ?>
     aria-hidden="true" role="dialog" aria-labelledby="new-task-title">
    <div class="modal__backdrop" data-close-modal></div>
    <div class="modal__panel">
        <header class="modal__head">
            <h2 id="new-task-title">Create a new task</h2>
            <button type="button" class="modal__close" data-close-modal aria-label="Close">&times;</button>
        </header>
        <form method="post" action="<?= e(route('task_create')) ?>" class="form form--modal" novalidate>
            <input type="hidden" name="project_id" value="<?= e($project['id']) ?>">

            <div class="field">
                <label for="t-title">Title <span class="req">*</span></label>
                <input id="t-title" type="text" name="title" maxlength="100" required
                       value="<?= e($old['title'] ?? '') ?>">
                <?php if (!empty($errors['title'])): ?>
                    <small class="field__error"><?= e($errors['title']) ?></small>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="t-desc">Description</label>
                <textarea id="t-desc" name="description" rows="3"><?= e($old['description'] ?? '') ?></textarea>
            </div>

            <div class="field">
                <label for="t-due">Due date <span class="req">*</span></label>
                <input id="t-due" type="date" name="due_date" required
                       value="<?= e($old['due_date'] ?? '') ?>">
                <?php if (!empty($errors['due_date'])): ?>
                    <small class="field__error"><?= e($errors['due_date']) ?></small>
                <?php endif; ?>
            </div>

            <fieldset class="field">
                <legend>Priority <span class="req">*</span></legend>
                <div class="radio-row">
                    <?php foreach (\Task::PRIORITIES as $p): ?>
                        <label class="radio radio--<?= e($p) ?>">
                            <input type="radio" name="priority" value="<?= e($p) ?>" required
                                <?= (($old['priority'] ?? 'medium') === $p) ? 'checked' : '' ?>>
                            <span><?= e(ucfirst($p)) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($errors['priority'])): ?>
                    <small class="field__error"><?= e($errors['priority']) ?></small>
                <?php endif; ?>
            </fieldset>

            <footer class="modal__foot">
                <button type="button" class="btn btn--ghost" data-close-modal>Cancel</button>
                <button type="submit" class="btn btn--primary">Create task</button>
            </footer>
        </form>
    </div>
</div>

<script src="<?= e(asset('public/js/board.js')) ?>"></script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
