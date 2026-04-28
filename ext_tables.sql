CREATE TABLE tx_typo3backendthemes_theme (
    title varchar(255) NOT NULL DEFAULT '',
    primary_color varchar(7) NOT NULL DEFAULT '',
    header_color varchar(7) NOT NULL DEFAULT '',
    sidebar_color varchar(7) NOT NULL DEFAULT '',
    darkmode_primary_color varchar(7) NOT NULL DEFAULT '',
    darkmode_header_color varchar(7) NOT NULL DEFAULT '',
    darkmode_sidebar_color varchar(7) NOT NULL DEFAULT '',
    is_default tinyint(1) unsigned NOT NULL DEFAULT 0
);
