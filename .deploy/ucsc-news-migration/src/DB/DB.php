<?php declare(strict_types=1);

namespace TribeUCSC\DB;

class DB {

	public const TABLE_NAME = 'ucsc_xml';

	public static function create_xml_store_table(): void {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			cascade_id varchar(40) ,
			date_added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			file tinytext NOT NULL,
			page_name text NOT NULL,
			content longtext NOT NULL,
			post_html text NOT NULL,
			status tinyint DEFAULT 0 NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $sql );
	}

	public static function get_table_name(): string {
		global $wpdb;

		return $wpdb->prefix . self::TABLE_NAME;
	}

}
