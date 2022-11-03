<?php

function wpwp_project_permalink( $permalink, $post ){

    if( strpos( $permalink, '%project_category%' ) === FALSE ) {
        return $permalink;
    }

    $terms = get_the_terms( $post, 'project_category' );

    if( ! is_wp_error( $terms ) && ! empty( $terms ) && is_object( $terms[0] ) ) {

        $taxonomy_slug = $terms[0];
        $term = $terms[0];
        $taxonomy_slug = $term->slug;

    }  else {
        $taxonomy_slug = 'no-projects';
    }

    return str_replace( '%project_category%', $taxonomy_slug, $permalink );
}
add_filter( 'post_type_link', 'wpwp_project_permalink', 1, 2 );


function wpwp_edit_term_fields( $term, $taxonomy ) {

    $allowed_social_links = ['youtube', 'facebook', 'instagram', 'telegram'];
    $output = '';
    foreach ( $allowed_social_links as $key) {
        $value = get_term_meta( $term->term_id, 'wpwp-project-'.$key.'', true );
        $output .= '<tr class="form-field">';
        $output .= '<th>';
            $output .= '<label for="wpwp-project-'.$key.'">'.ucwords(strtolower($key)).' посилання</label>';
        $output .= '</th>';
        $output .= '<td>';
            $output .= '<input name="wpwp-project-'.$key.'" id="wpwp-project-'.$key.'" type="text" value="' . esc_attr( $value ) .'" />';
        $output .= '</td>';
        $output .= '</tr>';
    }
    $output .= '<p class="description">Field description may go here.</p>';
    echo $output;

}
add_action( 'project_category_edit_form_fields', 'wpwp_edit_term_fields', 10, 2 );


//set excerpt length
function wpwp_excerpt_max_charlength( $charlength, $id = 0 ){

	$excerpt = get_the_excerpt($id === 0 ? null : $id);

    $charlength++;

    if ( mb_strlen( $excerpt ) > $charlength ) {
        $subex = mb_substr( $excerpt, 0, $charlength - 5 );
        $exwords = explode( ' ', $subex );
        $excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
        if ( $excut < 0 ) {
            echo mb_substr( $subex, 0, $excut );
        } else {
            echo $subex;
        }
        echo ' ...';
    } else {
        echo $excerpt;
    }
}


// ajax search
add_action( 'wp_ajax_nopriv_wpwp_ajax_search', 'wpwp_ajax_search' );
add_action( 'wp_ajax_wpwp_ajax_search', 'wpwp_ajax_search' );
function wpwp_ajax_search() {
  
	$wpwp_args               = array(
		'post_type'      => ['post','product'],
		'post_status'    => 'publish',
		'order'          => 'DESC',
		'orderby'        => 'date',
		's'              => $_POST['term'],
		'posts_per_page' => 5
	);

	$wpwp_query = new WP_Query( $wpwp_args );
	if ( $wpwp_query->have_posts() ) {
		while ( $wpwp_query->have_posts() ) {

			$wpwp_query->the_post();

			if( get_post_type() === 'product' ) {
				$wpwp_products_terms = get_the_terms( $post->ID, 'product_cat' );
				foreach ( $wpwp_products_terms as $wpwp_products_term ) {
					$wpwp_product_cat = $wpwp_products_term->name;
				} ?>
				<li class="header-top__search-result-item">
					<div class="search-item">
						<div class="search-item__left">
							<?php if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'thumbnail' );
							} ?>
						</div>
						<div class="search-item__right">
							<ul>
								<li class="post-category">
									<a href="<?php echo get_term_link( $wpwp_products_term ); ?>"></a>
									<?php echo $wpwp_product_cat; ?>

								</li>
							</ul>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</div>
					</div>
				</li>

			<?php } else { ?>

				<li class="header-top__search-result-item">
					<div class="search-item">
						<div class="search-item__left">
							<?php if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'thumbnail' );
							} ?>
						</div>
						<div class="search-item__right">
							<ul>
								<li class="post-category">
									<?php the_category(', '); ?>
								</li>
							</ul>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</div>
					</div>
				</li>

			<?php } ?>

		<?php } //end while

	} else { ?>

        <li class="header-top__search-result--key-words">
            <p><?php esc_html_e( 'Often searched', 'theme-domain' ); ?></p>
			<?php
			$wpwp_key_words = get_field( 'wpwp_key_words', 'option' );
			if ( $wpwp_key_words ) { ?>
                <ul class="key-words">
					<?php while ( have_rows( 'wpwp_key_words', 'option' ) ) : the_row();
						$wpwp_key_single_word = get_sub_field( 'wpwp_key_single_word' );
						$wpwp_key_plural_word = get_sub_field( 'wpwp_key_plural_word' );
						?>
                        <li class="key-words__item"><a
                                    href="<?php echo get_search_link( $wpwp_key_single_word ); ?>"><?php echo $wpwp_key_plural_word; ?></a>
                        </li>
					<?php endwhile; ?>
                </ul>
			<?php } ?>
        </li>
	<?php }
	exit;
}

?>
