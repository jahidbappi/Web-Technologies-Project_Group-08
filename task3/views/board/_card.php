<?php
if (!function_exists('app_url')) {
    require_once dirname(__DIR__, 4) . '/config/app.php';
}
$assignee  = $task['assignee_name'] ?? null;
$priority  = $task['priority'] ?: 'low';
$due       = $task['due_date']     ?: null;
$status    = $task['status'];
$canBack   = !empty(\Task::TRANSITIONS[$status]) && in_array(
    $status === 'in-progress' ? 'todo' : ($status === 'done' ? 'in-progress' : ''),
    \Task::TRANSITIONS[$status], true
);
$canForward = !empty(\Task::TRANSITIONS[$status]) && in_array(
    $status === 'todo' ? 'in-progress' : ($status === 'in-progress' ? 'done' : ''),
    \Task::TRANSITIONS[$status], true
);
?>
<article class="card"
         data-task-id="<?= e($task['id']) ?>"
         data-status="<?= e($status) ?>"
         data-due-date="<?= e($due ?? '') ?>">
    <header class="card__head">
        <h4 class="card__title">
            <a class="card__title-link" href="<?= e(app_url('TASK2/views/task_details.php?task_id=' . (int)$task['id'])) ?>"><?= e($task['title']) ?></a>
        </h4>
        <span class="badge badge--<?= e($priority) ?>"><?= e(ucfirst($priority)) ?></span>
    </header>

    <?php if (!empty($task['description'])): ?>
        <p class="card__desc"><?= e($task['description']) ?></p>
    <?php endif; ?>

    <footer class="card__foot">
        <div class="card__meta">
            <?php if ($assignee): ?>
                <span class="avatar" title="<?= e($assignee) ?>"><?= e(initials($assignee)) ?></span>
            <?php else: ?>
                <span class="avatar avatar--empty" title="Unassigned">·</span>
            <?php endif; ?>
            <?php if ($due): ?>
                <time class="card__due" datetime="<?= e($due) ?>"><?= e($due) ?></time>
            <?php endif; ?>
        </div>

        <div class="card__actions">
            <?php if ($canBack): ?>
                <button type="button" class="icon-btn" data-move="back" title="Move backward">&larr;</button>
            <?php else: ?>
                <span class="icon-btn icon-btn--disabled">&larr;</span>
            <?php endif; ?>
            <?php if ($canForward): ?>
                <button type="button" class="icon-btn" data-move="forward" title="Move forward">&rarr;</button>
            <?php else: ?>
                <span class="icon-btn icon-btn--disabled">&rarr;</span>
            <?php endif; ?>
        </div>
    </footer>
</article>
