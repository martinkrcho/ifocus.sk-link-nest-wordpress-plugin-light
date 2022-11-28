<?php

/**
 * The file that defines the class that handles custom database tables and default data.
 *
 * @link       https://www.linkedin.com/in/martinkrcho/
 * @since      1.0.0
 *
 * @package    Wp_Internal_Linking
 * @subpackage Wp_Internal_Linking/admin
 */

/**
 * Class handles custom database tables and default data
 *
 * @since      1.0.0
 * @package    Wp_Internal_Linking
 * @subpackage Wp_Internal_Linking/includes
 * @author     Martin Krcho <martin.krcho@devstudio.sk>
 */
class Wp_Internal_Linking_Database {

	/**
	 * Database version.
	 *
	 * @var int
	 */
	const DB_VERSION = 1;

	/**
	 * Option name staring the current database version.
	 *
	 * @var string
	 */
	const DB_VERSION_OPTION_NAME = 'wp_intlink_db_version';

	/**
	 * Hook into WordPress lifecycle to be able to create and update the custom database tables.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action(
			'plugins_loaded',
			[
				$this,
				'update_db_check',
			]
		);
	}

	public function update_db_check() {
		if ( get_site_option( self::DB_VERSION_OPTION_NAME ) != self::DB_VERSION ) {
			$this->install();
		}
	}

	public static function get_table_name( $table ) {
		global $wpdb;

		return $wpdb->prefix . $table;
	}

	public function install() {

		// Allow to fire only when an admin is logged in to prevent from firing multiple times.
		if ( ! is_user_logged_in() || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$installed_ver = get_site_option( self::DB_VERSION_OPTION_NAME );

		if ( $installed_ver != self::DB_VERSION ) {

			global $wpdb;
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			update_site_option( self::DB_VERSION_OPTION_NAME, self::DB_VERSION );

			$installed_ver = intval( $installed_ver );
			if ( 0 === $installed_ver ) {
				$tableName = self::get_table_name( Wp_Internal_Linking_Keyword_Model::TABLE_NAME );
				$sql       = "CREATE TABLE {$tableName} ("
							 . ' keyword_id INT(11) NOT NULL AUTO_INCREMENT, '
							 . ' keyword VARCHAR(127) NULL DEFAULT NULL, '
							 . ' title VARCHAR(127) NULL DEFAULT NULL, '
							 . ' rel VARCHAR(15) NULL DEFAULT NULL, '
							 . ' href VARCHAR(255) NULL DEFAULT NULL, '
							 . ' PRIMARY KEY (keyword_id) '
							 . ') ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;';
				dbDelta( $sql );

				$installed_ver ++;
			}
		}
	}
}
