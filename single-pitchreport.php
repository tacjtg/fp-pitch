<?php
/**
 * Single template for 'fp_pitch_reports' CPT
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
get_header(); ?>
<div class="fp-container">
	<aside>
		<div class="fp-logo"><img src="<?php the_field('fp_pitch_report_client_logo'); ?>" /></div>
		<div class="main-contact">
			<?php $posts = get_field( 'media_contact' ); if( $posts ) : foreach( $posts as $post ): setup_postdata( $post ); ?>
	        	<img src="<?php the_field( 'headshot' ); ?>" />
				<p>If you have questions, please contact:</p>
				<h2><?php the_field( 'first_name' ); ?> <?php the_field( 'last_name'); ?></h2>
				<p><a href="mailto:<?php the_field( 'email' ); ?>">Email</a></p>
				<p><a href="tel:<?php $phone = get_field( 'office_phone' ); echo $phone; ?>"><?php echo fp_format_phone( $phone ); ?></a></p>
				<?php if( get_field( 'cell_phone' ) != "" ) { ?>
					<p><a href="tel:<?php $phone = get_field( 'cell_phone' ); echo $phone; ?>"><?php echo fp_format_phone( $phone ); ?></a></p>
				<?php } ?>
			<?php endforeach; wp_reset_postdata(); endif; ?>
		</div>
	</aside>
  
	<div id="fp-content">
		<section class="intro">
			<h1><?php the_field( 'fp_pitch_report_title' ); ?></h1>
			<?php the_field( 'fp_pitch_report_intro' ); ?>			
		</section>
		
		<!-- BEGIN Header Nav -->
		<ul class="fp-header-nav">
			<?php if( have_rows( 'fp_pitch_report_section') ) {
					while ( have_rows( 'fp_pitch_report_section' ) ) : the_row();
						if( get_row_layout() == 'fp_pitch_report_header' ) { ?>
							<li><a href="#<?php the_sub_field( 'fp_pitch_report_header_text' ); ?>"><?php the_sub_field( 'fp_pitch_report_header_text' ); ?></a></li>
			<?php } endwhile; } ?>
		</ul>
		
		<!-- END Header Nav -->					
			
		<!-- BEGIN Pitch Report -->
		<?php if( have_rows( 'fp_pitch_report_section') ) { ?>
			<section class="documents">
				<?php while ( have_rows( 'fp_pitch_report_section' ) ) : the_row();
				
				// BEGIN Header
				if( get_row_layout() == 'fp_pitch_report_header' ) { ?>
					<h2><a name="<?php the_sub_field( 'fp_pitch_report_header_text' ); ?>"><?php the_sub_field( 'fp_pitch_report_header_text' ); ?></a></h2>
				<?php }						
				// END Header
								
				// BEGIN Drumbeat Story					
				if( get_row_layout() == 'fp_pitch_report_drumbeat_story' ) { ?>				
					<div class="pitch-report">
						<h3><strong><span style="color:#777;"><?php the_sub_field( 'fp_pitch_report_drumbeat_story_header_text' ); ?></span> <?php the_sub_field( 'fp_pitch_report_drumbeat_story_title' ); ?></strong></h3>
						<div class="columns">			
							<div><h4>Media Targets</h4><p><?php the_sub_field( 'fp_pitch_report_drumbeat_story_media_targets' ); ?></p></div>
							<div><h4>Additional Ideas/Notes</h4><p><?php the_sub_field( 'fp_pitch_report_drumbeat_story_ideas_notes' ); ?></p></div>
							<?php if ( get_sub_field( 'fp_pitch_report_drumbeat_story_result') != "" ) { ?>
								<div><h4>Results</h4><p><?php the_sub_field( 'fp_pitch_report_drumbeat_story_result' ); ?></p></div>
							<?php } ?>
						</div>
					</div>			          
				<?php } 
				// END Drumbeat Story
								
				endwhile; ?>												
			</section>
		<?php } ?>
		<!-- END Pitch Report -->
		
	</div><!-- END #fp-content-->
</div><!--END .fp-container-->
<a href="#" class="topbutton"></a>
<?php get_footer();