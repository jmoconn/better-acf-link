<?php

defined( 'ABSPATH' ) or die();

if ( !class_exists( 'TmLink' ) ) {

	class TmLink extends acf_field
	{
		/**
		 * initialize
		 */
		public function initialize()
		{

			$this->name = 'TmLink';
			$this->label = __( 'Custom Link', 'tm-link' );
			$this->category = 'relational';
			$this->defaults = array(
				'post_type' => array()
			);

			$this->add_action( 'wp_ajax_tm/fields/tm_link/post_query', array( $this, 'ajax_query' ) );
			$this->add_action( 'wp_ajax_nopriv_tm/fields/tm_link/post_query', array( $this, 'ajax_query' ) );

		}

		/**
		 * render_field_settings
		 *
		 * @param $field
		 */
		public function render_field_settings( $field )
		{

			// filter post types
			acf_render_field_setting( $field, array(
				'label' => __( 'Filter by Post Type', 'acf' ),
				'instructions' => '',
				'type' => 'select',
				'name' => 'post_type',
				'choices' => acf_get_pretty_post_types(),
				'multiple' => 1,
				'ui' => 1,
				'allow_null' => 1,
				'placeholder' => __( 'All post types', 'acf' )
			) );

			acf_render_field_setting( $field, array(
				'label' => __( 'Restrict Type', 'acf' ),
				'instructions' => '',
				'type' => 'radio',
				'name' => 'restrict_type',
				'choices' => ['post' => 'Internal', 'url' => 'External'],
				'allow_null' => 1
			) );

			acf_render_field_setting( $field, array(
				'label' => __( 'Show Post Type Filter', 'acf' ),
				'instructions' => '',
				'type' => 'true_false',
				'name' => 'show_post_type_filter',
				'ui' => 1,
				'default_value' => 1
			) );

			acf_render_field_setting( $field, array(
				'label' => __( 'Include Link Text', 'acf' ),
				'instructions' => '',
				'type' => 'true_false',
				'name' => 'include_link_text',
				'ui' => 1,
				'default_value' => 1
			) );

			acf_render_field_setting( $field, array(
				'label' => __( 'Include Link Title', 'acf' ),
				'instructions' => '',
				'type' => 'true_false',
				'name' => 'include_link_title',
				'ui' => 1,
				'default_value' => 1
			) );

			acf_render_field_setting( $field, array(
				'label' => __( 'Include Link Relationship', 'acf' ),
				'instructions' => '',
				'type' => 'true_false',
				'name' => 'include_link_rel',
				'ui' => 1,
				'default_value' => 1
			) );

			acf_render_field_setting( $field, array(
				'label' => __( 'Include Link Target', 'acf' ),
				'instructions' => '',
				'type' => 'true_false',
				'name' => 'include_link_target',
				'ui' => 1,
				'default_value' => 1
			) );

		}

		/**
		 * render_field
		 *
		 * @param $field
		 */
		public function render_field( $field )
		{

			// vars
			$div = array(
				'id' => $field['id'],
				'class' => $field['class'] . ' acf-link'
			);

			// get value render
			$value = $field['value'];
			$value = $this->load_value( $value, false, $field );
			$value = $this->format_value( $value, false, $field );

			// get sub fields
			$sub_fields = $this->get_sub_fields( $field );

			// classes
			if ( $value['url'] || $value['title'] ) {
				$div['class'] .= ' -value';
			}

			if ( $value['target'] ) {
				$div['class'] .= ' -external';
			}

			$modal_args = [
				'field' => $field,
				'div' => $div,
				'value' => $value,
				'sub_fields' => $sub_fields
			];
			include TM_LINK_PATH . 'modal.php';

		}

		/**
		 * get_sub_fields
		 *
		 * @param $field
		 *
		 * @return mixed|null
		 */
		public function get_sub_fields( $field )
		{
			// get value
			$value = $field['value'];

			$post_types = isset( $field['post_type'] ) && !empty( $field['post_type'] ) ? $field['post_type'] : apply_filters( 'tm_link_post_types', $this->get_post_types() );

			// storage
			$sub_fields = array();

			// type
			$sub_fields[] = array(
				'name' => 'type',
				'key' => 'type',
				'label' => __( 'Type', 'acf' ),
				'type' => 'radio',
				'required' => false,
				'class' => 'input-type',
				'choices' => array(
					'post' => __( 'Internal', 'acf' ),
					'url' => __( 'External', 'acf' )
				),
				"wrapper" => [
					"width" => "20",
					"class" => !empty( $field['restrict_type'] ) ? 'hidden' : '',
					"id" => ""
				]
			);
			// value
			$sub_fields[] = array(
				'name' => 'value',
				'key' => 'value',
				'label' => '',
				'type' => 'acfe_hidden',
				'required' => false
			);

			// url
			$sub_fields[] = array(
				'name' => 'url',
				'key' => 'url',
				'label' => __( 'URL', 'acf' ),
				'type' => 'url',
				'required' => false,
				'class' => 'input-url',
				'value' => isset( $value['type'] ) && $value['type'] === 'url' ? $value['value'] : '', // inject value based on type
				'conditional_logic' => array(
					array(
						array(
							'field' => 'type',
							'operator' => '==',
							'value' => 'url'
						)
					)
				)
			);

			if ( $field['show_post_type_filter'] ) {
				$sub_fields[] = [
					"key" => "post_types",
					"label" => "Filter by Post Type",
					"name" => "post_types",
					"aria-label" => "",
					"type" => "select",
					"instructions" => "Select the post type(s) in which to search.",
					"required" => 0,
					"conditional_logic" => [
						[
							[
								'field' => 'type',
								'operator' => '==',
								'value' => 'post'
							]
						]
					],
					"wrapper" => [
						"width" => "80",
						"class" => "",
						"id" => ""
					],
					"choices" => $post_types,
					"default_value" => [],
					"return_format" => "value",
					"multiple" => 1,
					"allow_custom" => 0,
					"placeholder" => "",
					"allow_null" => 1,
					"ui" => 1,
					"ajax" => 0,
					"search_placeholder" => ""
				];
			}

			// post
			$sub_fields[] = array(
				'name' => 'post',
				'key' => 'post',
				'label' => __( 'Post', 'acf' ),
				'type' => 'select',
				'required' => false,
				'class' => 'input-post',
				'allow_null' => 1,
				'ui' => 1,
				'ajax' => 1,
				'ajax_action' => 'tm/fields/tm_link/post_query',
				'choices' => $this->get_post_choices( $field ),
				'value' => isset( $value['type'] ) && $value['type'] === 'post' ? $value['value'] : '', // inject value based on type
				'conditional_logic' => array(
					array(
						array(
							'field' => 'type',
							'operator' => '==',
							'value' => 'post'
						)
					)
				)
			);

			if ( $field['include_link_text'] ) {
				$sub_fields[] = array(
					'name' => 'label',
					'key' => 'label',
					'label' => __( 'Link text', 'acf' ),
					'type' => 'text',
					'required' => false,
					'class' => 'input-title',
					"wrapper" => [
						"width" => "100",
						"class" => "",
						"id" => ""
					]
				);
			}

			if ( $field['include_link_title'] ) {

				$sub_fields[] = array(
					'name' => 'title',
					'key' => 'title',
					'label' => __( 'Title Attribute', 'acf' ),
					"instructions" => "Not visible, but accessible to screen readers and crawlers. Use this to provide more context to the link.",
					'type' => 'text',
					'required' => false,
					'class' => 'input-title',
					"wrapper" => [
						"width" => "100",
						"class" => "",
						"id" => ""
					]
				);
			}

			if ( $field['include_link_target'] ) {

				// target
				$sub_fields[] = array(
					'name' => 'target',
					'key' => 'target',
					'label' => __( 'Target', 'acf' ),
					'type' => 'true_false',
					'message' => __( 'Open in a new window', 'acf' ),
					'required' => false,
					'class' => 'input-target',
					"wrapper" => [
						"width" => "50",
						"class" => "",
						"id" => ""
					]
				);
			}

			if ( $field['include_link_rel'] ) {

				$sub_fields[] = array(
					'name' => 'relationship',
					'key' => 'relationship',
					'label' => __( 'Relationship', 'acf' ),
					'type' => 'select',
					'required' => false,
					'choices' => [
						'' => 'None',
						'nofollow' => 'Nofollow',
						'ugc' => 'User-generated content',
						'sponsored' => 'Sponsored'
					],
					"wrapper" => [
						"width" => "50",
						"class" => "",
						"id" => ""
					]
				);
			}
			// Sub Fields Filters
			$sub_fields = apply_filters( 'tm/fields/tm_link/sub_fields', $sub_fields, $field, $value );
			$sub_fields = apply_filters( 'tm/fields/tm_link/sub_fields/name=' . $field['_name'], $sub_fields, $field, $value );
			$sub_fields = apply_filters( 'tm/fields/tm_link/sub_fields/key=' . $field['key'], $sub_fields, $field, $value );

			foreach ( $sub_fields as &$sub_field ) {

				// add value
				if ( isset( $value[$sub_field['key']] ) ) {

					// this is a normal value
					$sub_field['value'] = $value[$sub_field['key']];

				} elseif ( isset( $sub_field['default_value'] ) ) {

					// no value, but this subfield has a default value
					$sub_field['value'] = $sub_field['default_value'];

				}

				// update prefix to allow for nested values
				$sub_field['prefix'] = $field['name'];

				// validate sub field
				$sub_field = acf_validate_field( $sub_field );

			}

			return $sub_fields;

		}

		public function get_post_types()
		{
			$post_types = get_post_types( array(
				'public' => true
			), 'objects' );

			$pts = [];

			foreach ( $post_types as $post_type ) {
				$pts[$post_type->name] = $post_type->label;
			}

			// remove attachment
			unset( $pts['attachment'] );

			// return
			return $pts;
		}

		/**
		 * load_value
		 *
		 * @param $value
		 * @param $post_id
		 * @param $field
		 *
		 * @return array
		 */
		public function load_value( $value, $post_id, $field )
		{
			if ( empty( $value ) ) {
				// For some reason when an external link is being used
				// in a repeater within a flexible content field, the value is not being
				// fetched properly for the flex preview. Internal links work fine.
				// This fixes the issue for now, but need to dig into this more.
				$store = acf_get_store( 'values' );

				global $post;
				$post_id = $post->ID ?? null;

				if ( empty( $post_id ) ) {
					return $value;
				}

				if ( $store->has( "$post_id:{$field['name']}" ) ) {
					return $store->get( "$post_id:{$field['name']}" );
				}
			}
			// if value is string then set as value
			if ( is_string( $value ) ) {
				$value = array( 'value' => $value );
			}

			$link_type = !empty( $field['restrict_type'] ) ? $field['restrict_type'] : apply_filters( 'tm_default_link_type', 'post' );

			// defaults
			$value = wp_parse_args( $value, array(
				'type' => $link_type,
				'value' => '',
				'title' => '',
				'target' => false,
				'post_types' => array()
			) );

			// handle old args
			foreach ( array( 'post' ) as $arg ) {

				if ( isset( $value[$arg] ) ) {

					$value['value'] = $value[$arg];
					unset( $value[$arg] );

				}

			}

			// sanitize value
			if ( !empty( $value['value'] ) ) {

				switch ( $value['type'] ) {

					case 'post':{
							$value['value'] = is_numeric( $value['value'] ) ? absint( $value['value'] ) : $value['value'];
							break;
						}

				}

			}

			// sanitize target
			$value['target'] = (bool) $value['target'];

			// return value
			return $value;

		}

		/**
		 * format_value
		 *
		 * @param $value
		 * @param $post_id
		 * @param $field
		 *
		 * @return array
		 */
		public function format_value( $value, $post_id, $field )
		{
			// if value is string then set as value
			if ( is_string( $value ) ) {
				$value = array( 'value' => $value );
			}

			// defaults
			$value = wp_parse_args( $value, array(
				'type' => 'post',
				'value' => '',
				'url' => '',
				'name' => '',
				'title' => '',
				'target' => '',
				'post_types' => array()
			) );

			if ( !empty( $value['value'] ) ) {

				switch ( $value['type'] ) {

					case 'url':{

							$value['url'] = $value['value'];
							$value['name'] = $value['value'];
							break;
						}

					case 'post':{

							$value['url'] = is_numeric( $value['value'] ) ? get_permalink( $value['value'] ) : null;
							$value['name'] = is_numeric( $value['value'] ) ? get_the_title( $value['value'] ) : '';
							break;
						}

				}

			}

			// format target
			$value['target'] = $value['target'] ? '_blank' : '';

			// return
			return $value;

		}

		/**
		 * validate_value
		 *
		 * @param $valid
		 * @param $value
		 * @param $field
		 * @param $input
		 *
		 * @return false
		 */
		public function validate_value( $valid, $value, $field, $input )
		{
			// bail early if not required
			if ( !$field['required'] ) {
				return $valid;
			}

			// loop over fields
			foreach ( array( 'url', 'post' ) as $type ) {

				if ( $value['type'] === $type && empty( $value[$type] ) ) {
					return false;
				}

			}

			// return
			return $valid;

		}

		/**
		 * update_value
		 *
		 * @param $value
		 * @param $post_id
		 * @param $field
		 *
		 * @return array
		 */
		public function update_value( $value, $post_id, $field )
		{
			// bail early
			if ( empty( $value ) ) {
				return $value;
			}

			// compatibility with string
			if ( is_string( $value ) ) {
				$value = array( 'value' => $value );
			}

			// defaults
			$value = wp_parse_args( $value, array(
				'type' => 'url',
				'value' => '',
				'url' => '',
				'title' => '',
				'target' => '',
				'post_types' => array()
			) );

			// loop over fields
			foreach ( array( 'url', 'post' ) as $type ) {

				if ( $value['type'] === $type && isset( $value[$type] ) ) {
					$value['value'] = $value[$type];
					break;
				}

			}

			// remove unecessary arguments
			unset( $value['url'], $value['post'] );

			// sanitize target
			$value['target'] = (bool) $value['target'];

			// empty value
			// allow to save empty value to not pollute db
			if ( empty( $value['value'] ) && empty( $value['title'] ) ) {
				$value = false;
			}

			return $value;

		}

		/**
		 * get_post_choices
		 *
		 * @param $field
		 *
		 * @return array
		 */
		public function get_post_choices( $field )
		{
			// vars
			$value = $field['value'];
			$choices = array();

			if ( empty( $value ) ) {
				return $choices;
			}

			$post_object = acf_get_field_type( 'post_object' );

			// load posts
			$posts = $post_object->get_posts( $value['value'], $field );

			if ( $posts ) {

				foreach ( array_keys( $posts ) as $i ) {

					// append choice
					$tm_post = acf_extract_var( $posts, $i );
					$choices[$tm_post->ID] = $post_object->get_post_title( $tm_post, $field );

				}

			}

			return $choices;

		}

		/**
		 * ajax_query
		 */
		public function ajax_query()
		{

			// validate
			if ( !acf_verify_ajax() ) {
				die();
			}

			// get choices
			$response = $this->get_ajax_query( $_POST );

			// return
			acf_send_ajax_results( $response );

		}

		/**
		 * get_ajax_query
		 *
		 * Based on the post_object get_ajax_query() function
		 *
		 * @param $options
		 *
		 * @return array|false
		 */
		public function get_ajax_query( $options = array() )
		{
			// defaults
			$options = acf_parse_args( $options, array(
				'post_id' => 0,
				's' => '',
				'field_key' => '',
				'paged' => 1,
				'post_types' => array_keys( apply_filters( 'tm_link_post_types', $this->get_post_types() ) ),
				'post_status' => apply_filters( 'tm_link_post_status', ['publish', 'future'] )
			) );

			// post object
			$post_object = acf_get_field_type( 'post_object' );

			// load field
			$field = acf_get_field( $options['field_key'] );
			if ( !$field ) {
				return false;
			}

			// vars
			$results = array();
			$args = array();
			$is_search = false;

			// paged
			$args['posts_per_page'] = 20;
			$args['paged'] = (int) $options['paged'] ?? 1;

			// status
			$args['post_status'] = acf_get_array( $options['post_status'] );

			// search
			if ( $options['s'] !== '' ) {

				// strip slashes (search may be integer)
				$s = wp_unslash( strval( $options['s'] ) );

				// update vars
				$args['s'] = $s;
				$is_search = true;

			}

			if ( isset( $options['post_types'] ) && !empty( $options['post_types'] ) ) {
				$args['post_type'] = $options['post_types'];
			} else if ( isset( $field['post_type'] ) && !empty( $field['post_type'] ) ) {
				$args['post_type'] = acf_get_array( $field['post_type'] );
			}

			// filters
			$args = apply_filters( 'acf/fields/post_object/query', $args, $field, $options['post_id'] );
			$args = apply_filters( 'acf/fields/post_object/query/name=' . $field['name'], $args, $field, $options['post_id'] );
			$args = apply_filters( 'acf/fields/post_object/query/key=' . $field['key'], $args, $field, $options['post_id'] );

			// get posts grouped by post type
			$groups = acf_get_grouped_posts( $args );

			// loop
			foreach ( array_keys( $groups ) as $group_title ) {

				// vars
				$posts = acf_extract_var( $groups, $group_title );

				// data
				$data = array(
					'text' => $group_title,
					'children' => array()
				);

				// convert post objects to post titles
				foreach ( array_keys( $posts ) as $post_id ) {
					$posts[$post_id] = $post_object->get_post_title( $posts[$post_id], $field, $options['post_id'], $is_search );
				}

				// order posts by search
				if ( $is_search && empty( $args['orderby'] ) && isset( $args['s'] ) ) {
					$posts = acf_order_by_search( $posts, $args['s'] );
				}

				// append to $data
				foreach ( array_keys( $posts ) as $post_id ) {
					$data['children'][] = $post_object->get_post_result( $post_id, $posts[$post_id] );
				}

				// append to $results
				$results[] = $data;

			}

			// optgroup or single
			// $post_type = acf_get_array( $args['post_type'] );
			// if ( count( $post_type ) === 1 ) {
			// 	$results = $results[0]['children'];
			// }

			// vars
			$response = array(
				'results' => $results,
				'limit' => $args['posts_per_page']
			);

			// return
			return $response;

		}

	}

	// initialize
	acf_register_field_type( 'TmLink' );

}