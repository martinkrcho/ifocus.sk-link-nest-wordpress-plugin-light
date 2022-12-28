<?php

/**
 * Handle CSV file upload in the plugin settings
 *
 * @link       https://www.linkedin.com/in/martinkrcho/
 * @since      1.0.0
 *
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/includes
 */

/**
 * Handles import of CSV file.
 *
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/includes
 * @author     Martin Krcho <martin.krcho@devstudio.sk>
 */
class iFocus_Link_Nest_Csv_Import {

	public static function import( $file_path ) {
		$file_contents = file_get_contents( $file_path );
		if ( false === $file_contents ) {
			return;
		}

		// Split to get individual lines.
		$raw_data = explode( "\n", $file_contents );
		if ( ! is_array( $raw_data ) ) {
			return;
		}

		// Skip header.
		array_shift( $raw_data );

		$csv_parsed = array();
		foreach ( $raw_data as $row ) {
			$row_data     = str_getcsv( $row );
			$csv_parsed[] = $row_data;
		}

		iFocus_Link_Nest_Keyword_Model::delete_all();
		foreach ( $csv_parsed as $csv_item ) {
			$model = iFocus_Link_Nest_Keyword_Model::build_from_generic_array( $csv_item );
			iFocus_Link_Nest_Keyword_Model::insert( $model );
		}
	}
}
