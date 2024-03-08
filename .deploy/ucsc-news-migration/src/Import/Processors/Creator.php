<?php declare(strict_types=1);

namespace TribeUCSC\Import\Processors;

use TribeUCSC\Import\Traits\With_Log_Entry;

class Creator {

	use With_Log_Entry;

	private int $existing_id = 0;
	private string $cascade_id = '';

	protected function assign_co_author( int $post_id, string $name, string $email = '' ) {
		global $wpdb;
		$co_authors_plus = new \CoAuthors_Plus();
		$query 			 = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = '%s' AND post_status = 'publish' AND post_name = '%s'", 'guest-author', sprintf( 'cap-%s', sanitize_title( $name ) ) );
		$existing_author = $wpdb->get_var( $query );

		// Create new author
		if ( ! $existing_author ) {
			$existing_author = [
				'post_name'   => sprintf( 'cap-%s', sanitize_title( $name ) ),
				'post_title'  => $name,
				'post_type'   => 'guest-author',
				'post_status' => 'publish',
			];

			$author_id = wp_insert_post( $existing_author );

			if ( is_wp_error( $author_id ) ) {
				return;
			}

			$meta_fields = [
				'cap-display_name' => $name,
				'cap-first_name'   => '',
				'cap-last_name'    => '',
				'cap-user_login'   => sanitize_title( $name ),
				'cap-user_email'   => $email ?: '',
				'cap-website'      => '',
				'cap-description'  => '',
			];

			foreach ( $meta_fields as $key => $meta_field) {
				update_post_meta( $author_id, $key, $meta_field );
			}

			$guest 		   = new \CoAuthors_Guest_Authors();
			$author_object = $guest->get_guest_author_by( 'ID', $author_id );
			$author_term   = $co_authors_plus->update_author_term( $author_object );

			// Add the author as a post term
			wp_set_post_terms( $post_id, [ $author_term->slug ], $co_authors_plus->coauthor_taxonomy, false );
		}

		$co_authors_plus->add_coauthors( $post_id, [ $name ], true );
	}

	protected function maybe_create_post( array $post_data, int $existing_id = 0 ): int {
		$args = $post_data;

		if ( $existing_id > 0 ) {
			$args = array_merge( [ 'ID' => $existing_id ], $post_data );
		}

		$id = wp_insert_post( $args );

		if ( is_wp_error( $id ) || $id === 0 ) {
			/**
			 * @var \WP_Error $id
			 */
			\WP_CLI::warning( sprintf( 'Could not create/update the post. Data: %s', json_encode( [
				'post_name'  => $args['post_name'],
				'ID' 		 => $args['ID'] ?? 0,
				'cascade_id' => $this->cascade_id
			] ) ) );

			$this->write_log( 'Could not create/update the post', [
				'post_name'  => $args['post_name'],
				'ID' 		 => $args['ID'] ?? 0,
				'cascade_id' => $this->cascade_id,
				'args' 		 => $args,
				'error'		 => $id !== 0 ? $id->get_error_messages() : 'Wrong query',
			] );

			return 0;
		}

		\WP_CLI::success( sprintf( 'Post created/updated successfully. The id is %d', $id ) ) ;

		$this->write_log( 'Post created/updated successfully', [
			'id' => $id,
		] );

		return (int) $id;
	}

}
