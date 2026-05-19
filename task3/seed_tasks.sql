USE `project_management`;

INSERT INTO `tasks` (`project_id`, `title`, `description`, `assigned_to`, `priority`, `due_date`, `status`, `created_at`) VALUES
(13, 'Design recycling-bin UI',  'Initial mockups for the dashboard',         3, 'medium', '2026-05-22', 'todo',        NOW()),
(13, 'Set up routes & DB models', 'Skeleton MVC scaffolding',                  3, 'high',   '2026-05-15', 'in-progress', NOW()),
(13, 'Project kickoff doc',       'Draft the project brief',                   3, 'low',    '2026-05-12', 'done',        NOW());

INSERT INTO `tasks` (`project_id`, `title`, `description`, `assigned_to`, `priority`, `due_date`, `status`, `created_at`) VALUES
(14, 'Map provider integration',  'Compare Mapbox vs Google Maps APIs',        2, 'high',   '2026-06-01', 'todo',        NOW()),
(14, 'Safety-score algorithm',    'Weighting of crime + lighting + traffic',   3, 'high',   '2026-05-28', 'in-progress', NOW()),
(14, 'Logo & color palette',      'Final brand assets',                        2, 'low',    '2026-05-20', 'todo',        NOW());

INSERT INTO `tasks` (`project_id`, `title`, `description`, `assigned_to`, `priority`, `due_date`, `status`, `created_at`) VALUES
(15, 'Reminder push notifications', 'Web push + email fallback',               2, 'medium', '2026-06-10', 'todo',        NOW()),
(15, 'Medicine catalog importer',   'Seed common medications',                 2, 'medium', '2026-05-25', 'in-progress', NOW());
