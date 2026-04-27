--
-- Test theme records for backend_themes extension
--

INSERT INTO tx_backendthemes_theme (uid, pid, tstamp, crdate, deleted, hidden, sorting, title, primary_color, secondary_color, darkmode_primary_color, darkmode_secondary_color, is_default)
VALUES
    (1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 1, 'Corporate Blue', '#3B82F6', '', '', '', 1),
    (2, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 2, 'Marketing Red', '#EF4444', '', '#F87171', '', 0),
    (3, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 3, 'Nature Green', '#22C55E', '#14532D', '#4ADE80', '#052E16', 0),
    (4, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 4, 'Royal Purple', '#8B5CF6', '', '#A78BFA', '', 0),
    (5, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 5, 'Sunset Orange', '#F97316', '#7C2D12', '', '', 0);
