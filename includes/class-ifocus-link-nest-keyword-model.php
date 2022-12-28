<?php

/**
 * Define the data structure related to a keyword entry.
 *
 * @link       https://www.linkedin.com/in/martinkrcho/
 * @since      1.0.0
 *
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/includes
 */

/**
 * Keyword model class.
 *
 * Defines the data structure related to a keyword entry from the database. Also provides static methods for accessing
 * the data in the database.
 *
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/includes
 * @author     Martin Krcho <martin.krcho@devstudio.sk>
 */
class iFocus_Link_Nest_Keyword_Model {

	public const TABLE_NAME = 'ifocus_keywords';

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
	 * @return iFocus_Link_Nest_Keyword_Model[]
	 */
	public static function get_all() {
		global $wpdb;

		$table_name = iFocus_Link_Nest_Database::get_table_name( self::TABLE_NAME );
		$data       = $wpdb->get_results( "SELECT * FROM $table_name;" );

		$result = array();
		foreach ( $data as $entry ) {
			$result[] = self::build_from_db_entry( $entry );
		}

		return $result;
	}

	/**
	 * @param object $entry Raw database entry.
	 *
	 * @return iFocus_Link_Nest_Keyword_Model
	 */
	private static function build_from_db_entry( $entry ) {
		$result          = new iFocus_Link_Nest_Keyword_Model();
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
	 * @return iFocus_Link_Nest_Keyword_Model
	 */
	public static function build_from_generic_array( $data ) {
		$result          = new iFocus_Link_Nest_Keyword_Model();
		$result->keyword = $data[0];
		$result->title   = $data[1];
		$result->rel     = $data[2];
		$result->href    = $data[3];

		return $result;
	}

	/**
	 * @param iFocus_Link_Nest_Keyword_Model $model
	 *
	 * @return int|false The ID of the inserted row, or false on error.
	 */
	public static function insert( $model ) {
		global $wpdb;
		$table_name = iFocus_Link_Nest_Database::get_table_name( self::TABLE_NAME );

		$wpdb->insert(
			$table_name,
			array(
				'keyword' => $model->keyword,
				'title'   => $model->title,
				'rel'     => $model->rel,
				'href'    => $model->href,
			)
		);

		return $wpdb->insert_id;
	}

	/**
	 * @return bool True if deleted. Boolean false on error.
	 */
	public static function delete_all() {
		global $wpdb;

		$table_name = iFocus_Link_Nest_Database::get_table_name( self::TABLE_NAME );

		return $wpdb->query( "TRUNCATE $table_name;" );
	}

	public function save() {
		if ( $this->id > 0 ) {
			global $wpdb;
			$table_name = iFocus_Link_Nest_Database::get_table_name( self::TABLE_NAME );

			$wpdb->update(
				$table_name,
				array(
					'keyword' => $this->keyword,
					'title'   => $this->title,
					'rel'     => $this->rel,
					'href'    => $this->href,
				),
				array(
					'keyword_id' => $this->id,
				)
			);

			return $this->id;
		} else {
			return self::insert( $this );
		}
	}

	public function delete() {
		if ( $this->id > 0 ) {
			global $wpdb;
			$table_name = iFocus_Link_Nest_Database::get_table_name( self::TABLE_NAME );

			return $wpdb->delete(
				$table_name,
				array(
					'keyword_id' => $this->id,
				)
			);
		}
	}
}
