<?php

get_header();

?>

<main id="content">
    <div class="container-fluid blog-ajax__scroll">
        <div class="row sizers">
            <div class="custom-tax-heading">
                <div class="col">
                    <h1><?php echo esc_html('Всі проекти','wpwp'); ?></h1>
                </div>            
            </div>
        </div>

        <div class="row sizers blog-ajax">

	        <?php
	        $wpwp_category_ids = get_terms(
		        array(
			        'taxonomy' => 'project_category',
			        'fields'   => 'ids',
			        'hide_empty' => true,
			        'number' => 3,
		        )
	        );
	        foreach( $wpwp_category_ids as $wpwp_category_id  ){
		        wp_reset_query();
		        $wpwp_args = [
			        'posts_per_page' => 3,
			        'order'          => 'ASC',
			        'post_type' => 'project',
			        'tax_query' => array(
				        array(
					        'taxonomy' => 'project_category',
					        'field' => 'term_id',
					        'terms' => $wpwp_category_id,
				        )
			        )
		        ];
		        $wpwp_query = new WP_Query( $wpwp_args );
		        $wpwp_category_description = category_description( $wpwp_category_id );
		        if( $wpwp_query->have_posts() ) {
			        ?>
                    <div class="catalog blog-ajax__item">
                        <div class="row catalog__posts-list-description">
                            <div class="col-sm-12 col-md-6">
                                <div class="catalog__title_holder">
                                    <h2 class="catalog__title"><a href="<?php echo esc_url( get_category_link($wpwp_category_id) ); ?>"> <?php echo get_term($wpwp_category_id) -> name; ?></a></h2>
							        <?php $wpwp_current_category = 'category_'.$wpwp_category_id; ?>

                                </div><!-- end of .catalog__title_holder -->
                            </div><!-- end of .col-sm-12 col-md-6 -->
                            <div class="col-sm-12 col-md-6 catalog__posts-list-description-right">
	                            <?php echo $wpwp_category_description; ?>
                            </div>
                        </div>

                        <div class="catalog__posts-list">

					        <?php while( $wpwp_query->have_posts() ) : $wpwp_query->the_post(); ?>
                                <div class="catalog__post">

	                                <?php if ( has_post_thumbnail() ) { ?>
                                        <div class="post-content__featured_box">
                                            <a href="<?php esc_url( the_permalink() ); ?>" rel="bookmark" class="post-content__featured_box-img">
                                                <?php the_post_thumbnail('full'); ?>
                                            </a>
                                        </div>
		                            <?php } else { ?>
                                        <div class="post-content__featured_box post-content__featured_box_default">
                                            <a href="<?php esc_url( the_permalink() ); ?>" rel="bookmark" class="post-content__featured_box-img">
                                                <img src="<?php echo get_template_directory_uri();?>/src/img/dniprotv-default.svg" alt="<?php the_title(); ?>">
                                            </a>
                                        </div>
			                        <?php } ?>

                                    <div class="post-info">
                                        <div class="meta">
									        <?php $wpwp_tag = get_the_tags();
									        if ($wpwp_tag) { ?>
                                                <div class="fr_tags tagcloud">
											        <?php the_tags('<ul><li>','</li><li>, ','</li></ul>'); ?>
                                                </div>
									        <?php } ?>
                                            <div class="date-time">
                                                <span><?php echo get_post_time('g:i'); ?></span>
	                                            <?php echo esc_html('|'); ?>
                                                <span><?php echo get_the_date('d.m'); ?></span>
                                            </div>
                                        </div>
                                        <h2 class="post-content__title">
                                            <a href="<?php esc_url( the_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a>
                                        </h2>
                                    </div>
                                </div>
					        <?php endwhile; ?>

                        </div><!-- end of .catalog__posts-list -->
                        <a class="button-arrow" href="<?php echo esc_url( get_category_link($wpwp_category_id) ); ?>"><?php echo esc_html('Перейти до проекту','dniprotv'); ?></a>

                    </div>
			        <?php
		        }
	        }

	        wp_reset_postdata(); ?>

        </div>
    </div>
</main>

<?php get_footer(); ?>
