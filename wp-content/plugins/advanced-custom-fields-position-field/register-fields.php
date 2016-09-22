<?php


class acf_field_country extends acf_field {
	// vars
	var $settings,
		$defaults,
		$dir,
		$path,
		$version,
		$slug;


	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since   3.6
	*  @date    23/01/13
	*/

	function __construct() {
		// vars
		$this->slug = 'acf-position';
		$this->name     = 'position';
		$this->label    = __( 'Страна/Регион/Город' );
		$this->category = __( "Basic", 'acf' ); // Basic, Content, Choice, etc

		$this->defaults = array(
			"country_id" => 0,
			"city_id"    => 0,
			"region_id"  => 0,
		);

		$this->dir = plugin_dir_url( __FILE__ );
		$this->path = plugin_dir_path(__FILE__);
		$this->version = '2.0.15';

		// do not delete!
		parent::__construct();

	}


	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like below) to save extra data to the $field
	*
	*  @type    action
	*  @since   3.6
	*  @date    23/01/13
	*
	*  @param   $field  - an array holding all the field's data
	*/

	function create_options( $field ) {
		$key = $field['name'];

	}

	/**
	 * Get Countries
	 *
	 * Get all countries from the database
	 *
	 */
	function _acf_get_countries() {
		global $wpdb;
		$countries_db = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "countries ORDER BY ( 'Россия' != country_name ), country_name ASC" );

		$countries = array();

		foreach ( $countries_db AS $country ) {
			if ( trim( $country->country_name ) == '' ) {
				continue;
			}
			$countries[ $country->country_id ] = $country->country_name;
		}

		return $countries;
	}

	/**
	 * Get Country
	 *
	 * Get a particular country from the database
	 *
	 */
	function _acf_get_country( $country_id ) {
		global $wpdb;
		$country = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "countries WHERE country_id = '" . $country_id . "'" );

		if ( $country ) {
			return $country->country_name;
		} else {
			return false;
		}
	}

	/**
	 * Get Cities
	 *
	 * Get all cities for a particular country
	 *
	 */
	function _acf_get_cities( $region_id ) {
		global $wpdb;
		$cities_db = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "cities WHERE region_id='" . $region_id . "' ORDER BY city_name ASC" );

		$cities = array();

		foreach ( $cities_db AS $city ) {
			if ( trim( $city->city_name ) == '' ) {
				continue;
			}
			$cities[ $city->city_id ] = ( ! empty ( $city->area_name ) ) ? sprintf( '%s (%s)', $city->city_name, $city->area_name ) : $city->city_name;;
		}

		return $cities;
	}

	/**
	 * Get City
	 *
	 * Get a particular city based on its ID
	 *
	 */
	function _acf_get_city( $city_id ) {
		global $wpdb;
		$city = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "cities WHERE city_id = '" . $city_id . "'" );

		if ( $city ) {
			return $city->city_name;
		} else {
			return false;
		}
	}

	function _acf_get_regions( $country_id ) {
		global $wpdb;
		$regions_db = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "regions WHERE country_id='" . $country_id . "' ORDER BY region_name ASC" );

		$regions = array();

		foreach ( $regions_db AS $region ) {
			if ( trim( $region->region_name ) == '' ) {
				continue;
			}
			$regions[ $region->region_id ] = $region->region_name;
		}

		return $regions;
	}

	/**
	 * Get State
	 *
	 * Get a particular state based on its ID
	 *
	 */
	function _acf_get_region( $region_id ) {
		global $wpdb;
		$state = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "regions WHERE region_id = '" . $region_id . "'" );

		if ( $state ) {
			return $state->region_name;
		} else {
			return false;
		}
	}


	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param   $field - an array holding all the field's data
	*
	*  @type    action
	*  @since   3.6
	*  @date    23/01/13
	*/

	function render_field( $field ) {
		$field['value'] = isset( $field['value'] ) ? $field['value'] : '';

		$country_id = ( isset( $field['value']['country_id'] ) ) ? $field['value']['country_id'] : 0;
		$region_id  = ( isset( $field['value']['region_id'] ) ) ? $field['value']['region_id'] : 0;
		$city_id    = ( isset( $field['value']['city_id'] ) ) ? $field['value']['city_id'] : 0;

		$key = $field['name'];

		//global $wpdb;

		$countries = $this->_acf_get_countries();
		$regions   = $this->_acf_get_regions( $country_id );
		$cities    = $this->_acf_get_cities( $region_id );

		?>

		<ul class="country-selector-list">
			<li id="field-<?php echo $key; ?>[country_id]">
				<div class="field-inner">
					<strong><?php _e("Страна", 'acf'); ?></strong><br />

					<?php

					$country_field = $field['name'] . '[country_id]';
					acf_render_field_wrap( array(
						'type'        =>  'select',
						'name'        =>  $country_field,
						'value'       =>  $country_id,
						'choices'     =>  $countries,
						//'placeholder' => 'Choose a country...',
						'allow_null'  => 1,
						'class' => 'select2'
					));

					?>

				</div>
			</li>
			<li id="field-<?php echo $key; ?>[region_id]">
				<div class="css3-loader" style="display:none;"><div class="css3-spinner"></div></div>
				<div class="field-inner">
					<strong><?php _e("Регион", 'acf'); ?></strong><br />

					<?php

					$region_field = $field['name'] . '[region_id]';
					acf_render_field_wrap( array(
						'type'        =>  'select',
						'name'        =>  $region_field,
						'value'       =>  $region_id,
						'choices'     =>  $regions,
						//'placeholder' => 'Choose a region...',
						'allow_null'  => 1,
						'class' => 'select2'
					));

					?>

				</div>
			</li>
			<li id="field-<?php echo $key; ?>[city_id]">
				<div class="css3-loader" style="display:none;"><div class="css3-spinner"></div></div>
				<div class="field-inner">
					<strong><?php _e("Город", 'acf'); ?></strong><br />

					<?php

					$city_field = $field['name'] . '[city_id]';
					acf_render_field_wrap( array(
						'type'    =>  'select',
						'name'    =>  $city_field,
						'value'   =>  $city_id,
						'choices' =>  $cities,
						'allow_null'  => 1,
						'class' => 'select2'
					));

					?>
				</div>
			</li>
		</ul>

		<?php
	}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info    http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type    action
	*  @since   3.6
	*  @date    23/01/13
	*/

	function input_admin_enqueue_scripts() {

		wp_register_script( $this->slug, $this->dir . 'js/input.js', array('acf-input'), $this->version );
		wp_register_style( $this->slug, $this->dir . 'css/input.css', array('acf-input'), $this->version );

		wp_localize_script( $this->slug, "acfCountry", array(
			"ajaxurl" => admin_url( "admin-ajax.php" ),
		));

		if ( is_admin() ) {

			// scripts
			wp_enqueue_script( $this->slug );

			// styles
			wp_enqueue_style( $this->slug );
		}
	}


	/*
	*  load_value()
	*
	*  This filter is appied to the $value after it is loaded from the db
	*
	*  @type    filter
	*  @since   3.6
	*  @date    23/01/13
	*
	*  @param   $value - the value found in the database
	*  @param   $post_id - the $post_id from which the value was loaded from
	*  @param   $field - the field array holding all the field options
	*
	*  @return  $value - the value to be saved in te database
	*/

	function load_value( $value, $post_id, $field ) {
		// Note: This function can be removed if not used
		return $value;
	}


	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type    filter
	*  @since   3.6
	*  @date    23/01/13
	*
	*  @param   $value - the value which will be saved in the database
	*  @param   $post_id - the $post_id of which the value will be saved
	*  @param   $field - the field array holding all the field options
	*
	*  @return  $value - the modified value
	*/

	function update_value( $value, $post_id, $field ) {
		//$value['country_name'] = $this->_acf_get_country($value['country_id']);
		//$value['region_name']    = $this->_acf_get_region($value['region_id']);
		//$value['city_name']    = $this->_acf_get_city($value['city_id']);


		return $value;
	}


	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*  @type    filter
	*  @since   3.6
	*  @date    23/01/13
	*
	*  @param   $value  - the value which was loaded from the database
	*  @param   $post_id - the $post_id from which the value was loaded
	*  @param   $field  - the field array holding all the field options
	*
	*  @return  $value  - the modified value
	*/

	function format_value( $value, $post_id, $field ) {
		$field = array_merge( $this->defaults, $field );

		// perhaps use $field['preview_size'] to alter the $value?


		// Note: This function can be removed if not used
		return $value;
	}


	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type    filter
	*  @since   3.6
	*  @date    23/01/13
	*
	*  @param   $value  - the value which was loaded from the database
	*  @param   $post_id - the $post_id from which the value was loaded
	*  @param   $field  - the field array holding all the field options
	*
	*  @return  $value  - the modified value
	*/

	function format_value_for_api( $value, $post_id, $field ) {
		/* $old_values = $value;
		 $value      = array();

		 $value['country_name'] = $this->_acf_get_country($old_values['country_id']);
		 $value['region_name']    = $this->_acf_get_region($old_values['region_id']);
		 $value['city_name']    = $this->_acf_get_city($old_values['city_id']);


		 $value['country_id'] = $old_values['country_id'];
		 $value['region_id'] = $old_values['region_id'];
		 $value['city_id'] = $old_values['city_id'];*/

		return $value;
	}


	/*
	*  load_field()
	*
	*  This filter is appied to the $field after it is loaded from the database
	*
	*  @type    filter
	*  @since   3.6
	*  @date    23/01/13
	*
	*  @param   $field - the field array holding all the field options
	*
	*  @return  $field - the field array holding all the field options
	*/

	function load_field( $field ) {

		// Note: This function can be removed if not used
		return $field;
	}


	/*
	*  update_field()
	*
	*  This filter is appied to the $field before it is saved to the database
	*
	*  @type    filter
	*  @since   3.6
	*  @date    23/01/13
	*
	*  @param   $field - the field array holding all the field options
	*  @param   $post_id - the field group ID (post_type = acf)
	*
	*  @return  $field - the modified field
	*/

	function update_field( $field, $post_id ) {
		return $field;
	}


}

add_action( "wp_ajax_get_cities", "get_cities" );
function get_cities() {
	global $wpdb;

	$region_id = (int) trim( $_POST['regionId'] );

	$cities_db = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "cities WHERE  region_id='" . $region_id . "' ORDER BY city_name ASC" );
	$cities    = array();

	if ( $cities_db ) {
		foreach ( $cities_db AS $city ) {
			$cities[ $city->city_id ] = ( ! empty ( $city->area_name ) ) ? sprintf( '%s (%s)', $city->city_name, $city->area_name ) : $city->city_name;
		}
	}

	echo json_encode( $cities );

	die();
}

add_action( "wp_ajax_get_regions", "get_regions" );
function get_regions() {
	global $wpdb;

	$country_id = (int) trim( $_POST['countryId'] );

	$regions_db = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "regions WHERE  country_id='" . $country_id . "' ORDER BY region_name ASC" );
	$regions    = array();

	if ( $regions_db ) {
		foreach ( $regions_db AS $region ) {
			$regions[ $region->region_id ] = $region->region_name;
		}
	}

	echo json_encode( $regions );

	die();
}


// create field
$GLOBALS['acf_position'] = new acf_field_country();

/**
 * Можно вывести поле на фронте при помощи <?php echo do_shortcode('[acf_position]'); ?>
 *
 * @param $atts
 * @param null $content
 *
 * @return string
 */
function acf_position_shortcode( $atts, $content = null ) {

	global $acf_position;

	$atts = shortcode_atts([
		'country_id' => 1,
		'region_id' => 1053480,
		'city_id' => 1,
		'style' => 'true',
		'script' => 'true',
	], $atts );

	if ( 'true' == $atts['style'] ) {
		wp_enqueue_style( $acf_position->slug );
	}

	if ( 'true' == $atts['script'] ) {
		wp_enqueue_script( $acf_position->slug );
	}

	ob_start();

	$acf_position->render_field([
		'name' => 'location',
		'value' => [
			'country_id' => $atts['country_id'],
			'region_id' => $atts['region_id'],
			'city_id' => $atts['city_id']
		]
	]);

	$result = ob_get_contents();
	ob_end_clean();

	return $result;
}
add_shortcode( 'acf_position', 'acf_position_shortcode' );
?>