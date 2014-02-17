network-diffhashgraph
=====================

Tool for mapping diff/nethash/blocks over time. Stored to MySQL DB.

Requirements:
- MySQL
- Apache
- PHP

MySQL Instructions:
1. CREATE DATABASE netdiffhash;
2. CREATE TABLE `diff_records` (
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
     `blocknum` int(11) DEFAULT NULL,
     `diff` float DEFAULT NULL,
     `nethash` float DEFAULT NULL,
     `timestamp` int(11) DEFAULT NULL,
     PRIMARY KEY (`id`)
   ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

Running Instructions:
1. Install DB
2. Update config.php for your server
3. Start the rungraph script as a daemon (can also be done using screens): php /lib/netdiffhash/rungraph.php & 
4. View output using the public_html interface