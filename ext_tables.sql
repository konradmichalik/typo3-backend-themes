CREATE TABLE tx_backendthemes_theme (
    title varchar(255) NOT NULL DEFAULT '',
    primary_color varchar(7) NOT NULL DEFAULT '',
    secondary_color varchar(7) NOT NULL DEFAULT '',
    auto_secondary tinyint(1) unsigned NOT NULL DEFAULT 1,
    darkmode_primary_color varchar(7) NOT NULL DEFAULT '',
    darkmode_secondary_color varchar(7) NOT NULL DEFAULT '',
    is_default tinyint(1) unsigned NOT NULL DEFAULT 0
);
