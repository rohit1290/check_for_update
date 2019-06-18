CREATE TABLE IF NOT EXISTS `prefix_check_for_update` (
    `id` INT AUTO_INCREMENT,
    `plugin_id` TEXT NOT NULL,
    `github_url` TEXT NOT NULL,
    `github_tag_name` TEXT NOT NULL,
    `github_adv_commit` TEXT NOT NULL,
    `github_manifest` TEXT NOT NULL,
    `elgg_release` TEXT NOT NULL,
    `current_version` TEXT NOT NULL,
    `check_update` TEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=INNODB;


