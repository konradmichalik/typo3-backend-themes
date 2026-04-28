--
-- Demo theme records for backend_themes extension
--

INSERT INTO tx_backendthemes_theme (pid, tstamp, crdate, deleted, hidden, sorting, title, primary_color, header_color, sidebar_color, darkmode_primary_color, darkmode_header_color, darkmode_sidebar_color, is_default)
VALUES
    (0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 1, 'Corporate Blue', '#3B82F6', '', '', '', '', '', 0),
    (0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 2, 'Nature Green', '#22C55E', '', '', '', '', '', 0),
    (0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 3, 'Warm Sunset', '#F97316', '#7C2D12', '#92400E', '#FB923C', '#431407', '#4A1A0A', 0);
