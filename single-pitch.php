<?php
/**
 * Single template for 'fp_pitches' CPT
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();if ( ! post_password_required( $post ) ) {?>

<div class="fp-container">
	<aside>
		<div class="fp-logo"><img src="<?php the_field('client_logo'); ?>" /></div>
		<div class="main-contact">
			<?php $posts = get_field( 'media_contact' ); if( $posts ) : foreach( $posts as $post ): setup_postdata( $post ); ?>
	        	<img src="<?php the_field( 'headshot' ); ?>" />
				<p>If you have questions, please contact:</p>
				<h2><?php the_field( 'first_name' ); ?> <?php the_field( 'last_name'); ?></h2>
				<p><a href="mailto:<?php the_field('email'); ?>">Email</a></p>
				<p><a href="tel:<?php $phone=get_field('office_phone'); echo $phone; ?>"><?php echo fp_format_phone($phone); ?></a></p>
				<?php if( get_field( 'cell_phone' ) != "" ) { ?>
					<p><a href="tel:<?php $phone=get_field('cell_phone'); echo $phone; ?>"><?php echo fp_format_phone($phone); ?></a></p>
				<?php }?>
			<?php endforeach;  wp_reset_postdata(); endif; ?>
		</div>
	</aside>
  
	<div id="fp-content">
		<section class="intro">
			<h1><?php the_field( 'pitch_title' ); ?></h1>
			<?php the_field( 'pitch_intro' ); ?>			
			<?php if( get_field( 'include_release' ) ) { ?>
				<a href="<?php the_field( 'release_download' ); ?>" class="btn">Download Press Release</a>
			<?php } ?>
		</section>
		
		<?php if( have_rows( 'documents') ){ ?>
			<section class="documents">
				<h2>Documents</h2>
				<div class="columns">
				<?php while ( have_rows( 'documents' ) ) : the_row(); if( get_row_layout() == 'document_section' ){ ?>
					<div>
						<p><strong><?php the_sub_field( 'title' ); ?></strong></p>
						<?php if( get_sub_field( 'document_links' ) ) { // BEGIN Repeater ?>
							<ul>
								<?php while( has_sub_field( 'document_links' ) ): ?>
									<li>
										<?php if( get_sub_field( 'is_release' ) ) { ?>
											<span><?php the_sub_field( 'release_date' ); ?></span>
										<?php } ?>
										<a href="<?php the_sub_field( 'document_file' ); ?>"><?php the_sub_field( 'document_title' ); ?></a>
									</li>
								<?php endwhile; ?>
							</ul>
						<?php } // END Repeater ?> 
					</div>            
				<?php } // END Flex Content Row ?>
				<?php endwhile; ?>
				</div>
			</section>
		<?php } ?>

		<?php if( get_field( 'images' )) { ?>
			<section class="media-gallery">
				<h2>Images</h2>
				<div class="links popup-gallery">
				<?php while( the_repeater_field( 'images' ) ): $image = get_sub_field( 'image' ); ?>
					<div>
						<span class="image"><img src="<?php echo $image['sizes']['lightbox_thumb']; ?>" /></span>
						<span class="title"><?php the_sub_field('image_title'); ?></span>
						<a href="<?php echo $image['sizes']['lightbox_lg']; ?>" class="view popup simptip-movable simptip-position-top" data-tooltip="View">View</a>
						<?php $highRes = get_sub_field('high_res_file');
							if($highRes) {
								echo '<a href="' .$highRes .'" target="_blank" class="download simptip-movable simptip-position-top" data-tooltip="Download">Download</a>';
							} else {
								echo '<a href="' .$image['url'] .'" target="_blank" class="download simptip-movable simptip-position-top" data-tooltip="Download">Download</a>';
						} ?>
					</div>					
				<?php endwhile;?>
		    	</div>
    		</section>
		<?php } ?>
	</div><!-- END #fp-content-->
</div><!--END .fp-container-->
<a href="#" class="topbutton"></a>
<?php get_footer();
	
} else {
	// we will show password form here
	?>
	<div class="fp-container">
		
	<?php echo get_the_password_form(); ?>
	
	</div>
<?php }