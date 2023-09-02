-- Create table for location types
CREATE TABLE IF NOT EXISTS `wp_gmapradius_type` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(220) DEFAULT NULL,
    `color` varchar(220) DEFAULT NULL,
    PRIMARY KEY (`id`)
);

-- Create table for locations and radius
CREATE TABLE IF NOT EXISTS `wp_gmapradius_locations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(220) DEFAULT NULL,
    `radius` int(11) DEFAULT 0,
    `type_id` int(11),
    `lat` DECIMAL(10, 6),
    `lng` DECIMAL(10, 6),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`type_id`) REFERENCES `wp_gmapradius_type` (`id`) ON DELETE CASCADE
);

-- Create table for settings
CREATE TABLE IF NOT EXISTS `wp_gmapradius_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_name` varchar(220) NOT NULL,
    `setting_value` text,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_setting_name` (`setting_name`)
);

-- Insert sample data for location types
INSERT INTO `wp_gmapradius_type` (`name`, `color`) VALUES
    ('Type A', '#FF5733'),
    ('Type B', '#33FF57'),
    ('Type C', '#5733FF');

-- Insert sample data for locations in Indonesia
INSERT INTO `wp_gmapradius_locations` (`name`, `radius`, `type_id`, `lat`, `lng`) VALUES
    ('Jakarta', 5000, 1, -6.2088, 106.8456),
    ('Bali', 8000, 2, -8.3405, 115.0920),
    ('Yogyakarta', 6000, 1, -7.7956, 110.3695),
    ('Surabaya', 7000, 3, -7.2575, 112.7521);

-- Insert API Key setting
INSERT INTO `wp_gmapradius_settings` (`setting_name`, `setting_value`) VALUES ('GMAP_API_KEY', NULL);
