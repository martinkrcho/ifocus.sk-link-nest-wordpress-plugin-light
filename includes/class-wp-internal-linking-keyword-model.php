<?php

/**
 * Define the data structure related to a keyword entry.
 *
 * @link       https://www.linkedin.com/in/martinkrcho/
 * @since      1.0.0
 *
 * @package    Wp_Internal_Linking
 * @subpackage Wp_Internal_Linking/includes
 */

/**
 * Keyword model class.
 *
 * Defines the data structure related to a keyword entry from the database. Also provides static methods for accessing
 * the data in the database.
 *
 * @package    Wp_Internal_Linking
 * @subpackage Wp_Internal_Linking/includes
 * @author     Martin Krcho <martin.krcho@devstudio.sk>
 */
class Wp_Internal_Linking_Keyword_Model {

	public const TABLE_NAME = 'intlink_keywords';

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var string
	 */
	public $keyword;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $rel;

	/**
	 * @var string
	 */
	public $href;

	/**
	 * @return Wp_Internal_Linking_Keyword_Model[]
	 */
	public static function get_all() {
		global $wpdb;

		$table_name = Wp_Internal_Linking_Database::get_table_name( self::TABLE_NAME );
		$data       = $wpdb->get_results( "SELECT * FROM $table_name;" );

		$result = [];
		foreach ( $data as $entry ) {
			$result[] = self::build_from_db_entry( $entry );
		}

		return $result;
	}

	/**
	 * @param object $entry Raw database entry.
	 *
	 * @return Wp_Internal_Linking_Keyword_Model
	 */
	private static function build_from_db_entry( $entry ) {
		$result          = new Wp_Internal_Linking_Keyword_Model();
		$result->id      = $entry->keyword_id;
		$result->keyword = $entry->keyword;
		$result->title   = $entry->title;
		$result->rel     = $entry->rel;
		$result->href    = $entry->href;

		return $result;
	}

	/**
	 * @param array $data Generic array.
	 *
	 * @return Wp_Internal_Linking_Keyword_Model
	 */
	public static function build_from_generic_array( $data ) {
		$result          = new Wp_Internal_Linking_Keyword_Model();
		$result->keyword = $data[0];
		$result->title   = $data[1];
		$result->rel     = $data[2];
		$result->href    = $data[3];

		return $result;
	}

	/**
	 * @param Wp_Internal_Linking_Keyword_Model $model
	 *
	 * @return int|false The ID of the inserted row, or false on error.
	 */
	public static function insert( $model ) {
		global $wpdb;
		$table_name = Wp_Internal_Linking_Database::get_table_name( self::TABLE_NAME );

		$wpdb->insert(
			$table_name,
			[
				'keyword' => $model->keyword,
				'title'   => $model->title,
				'rel'     => $model->rel,
				'href'    => $model->href,
			]
		);

		return $wpdb->insert_id;
	}

	/**
	 * @return bool True if deleted. Boolean false on error.
	 */
	public static function delete_all() {
		global $wpdb;

		$table_name = Wp_Internal_Linking_Database::get_table_name( self::TABLE_NAME );

		return $wpdb->query( "TRUNCATE $table_name;" );
	}

	public function save() {
		if ( $this->id > 0 ) {
			global $wpdb;
			$table_name = Wp_Internal_Linking_Database::get_table_name( self::TABLE_NAME );

			$wpdb->update(
				$table_name,
				[
					'keyword' => $this->keyword,
					'title'   => $this->title,
					'rel'     => $this->rel,
					'href'    => $this->href,
				],
				[
					'keyword_id' => $this->id,
				]
			);

			return $this->id;
		} else {
			return self::insert( $this );
		}
	}

	public function delete() {
		if ( $this->id > 0 ) {
			global $wpdb;
			$table_name = Wp_Internal_Linking_Database::get_table_name( self::TABLE_NAME );

			return $wpdb->delete(
				$table_name,
				[
					'keyword_id' => $this->id,
				]
			);
		}
	}
}
