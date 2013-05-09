<?php
/*
 * Plugin Name: Review Ratings
 * Version: 1.5.3
 * Plugin URI: http://sportsmansratings.com
 * Description: A plugin that turns commenting into a rating/review system.
 */
// Add custom meta (ratings) fields to the default comment form
// Default comment form includes name, email and URL
// Default comment form elements are hidden when user is logged in
// Original code for this (prior to my modifications) came from 
// http://wp.smashingmagazine.com/2012/05/08/adding-custom-fields-in-wordpress-comment-form/

add_filter('comment_form_default_fields','custom_fields');
function custom_fields($fields) {

    $commenter = wp_get_current_commenter();
		$req = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true'" : '' );

		$fields[ 'author' ] = '<p class="comment-form-author">'.
			'<label for="author">' . __( 'Name' ) . '</label>'.
			( $req ? '<span class="required">*</span>' : '' ).
			'<input id="author" name="author" type="text" value="'. esc_attr( $commenter['comment_author'] ) . 
			'" size="30" tabindex="1"' . $aria_req . ' /></p>';

		$fields[ 'email' ] = '<p class="comment-form-email">'.
			'<label for="email">' . __( 'Email' ) . '</label>'.
			( $req ? '<span class="required">*</span>' : '' ).
			'<input id="email" name="email" type="text" value="'. esc_attr( $commenter['comment_author_email'] ) . 
			'" size="30"  tabindex="2"' . $aria_req . ' /></p>';

	return $fields;
}

// Add fields after default fields above the comment box, always visible

add_action( 'comment_form_logged_in_after', 'additional_fields' );
add_action( 'comment_form_after_fields', 'additional_fields' );

function additional_fields () {
	echo '<p><label for="title">' . __( 'Review Title ' ) . '</label>'.
	'&nbsp;<input class="reviewForm" id="title" name="title" type="text" size="30"  tabindex="5" /></p>';

	echo '<br/><strong>Rating Scale</strong><br/>1 - Lowest Rating (Most Expensive) / 10 - Highest Rating (Least Expensive)<br/><br/><strong>Your Rating</strong><br/>';

	echo '<table><tr><td>'.
	'<label for="rating">'. __('Performance: ') . '<span class="required">*</span></label>
	<span class="commentratingbox"></td>';

	for( $i=1; $i <= 10; $i++ )
	echo '<td><span class="commentrating"><input type="radio" name="performance" id="performance" value="'. $i .'"/>'. $i .'</span></td>';

	echo'</span></tr>';

	echo '<tr><td>'.
	'<label for="rating">'. __('Price: ') . '<span class="required">*</span></label>
	<span class="commentratingbox"></td>';

	for( $i=1; $i <= 10; $i++ )
	echo '<td><span class="commentrating"><input type="radio" name="price" id="price" value="'. $i .'"/>'. $i .'</span></td>';

	echo'</span></tr></table>';




}

// Save the comment meta data along with comment

add_action( 'comment_post', 'save_comment_meta_data' );
function save_comment_meta_data( $comment_id ) {

	if ( ( isset( $_POST['title'] ) ) && ( $_POST['title'] != '') )
	$title = wp_filter_nohtml_kses($_POST['title']);
	add_comment_meta( $comment_id, 'title', $title );

	if ( ( isset( $_POST['performance'] ) ) && ( $_POST['performance'] != '') ) 
	$performance = wp_filter_nohtml_kses($_POST['performance']);
	add_comment_meta( $comment_id, 'performance', $performance );

	if ( ( isset( $_POST['price'] ) ) && ( $_POST['price'] != '') ) 
	$price = wp_filter_nohtml_kses($_POST['price']);
	add_comment_meta( $comment_id, 'price', $price );
}

// Add the filter to check if the comment meta data has been filled or not

add_filter( 'preprocess_comment', 'verify_comment_meta_data' );
function verify_comment_meta_data( $commentdata ) {	
	if ( ! isset( $_POST['performance'] ) ) 
	wp_die( __( 'Error: You did not add your performance rating. Hit the BACK button of your Web browser and resubmit your review with a performance rating.') );
	return $commentdata;

	if ( ! isset( $_POST['price'] ) ) 
	wp_die( __( 'Error: You did not add your price rating. Hit the BACK button of your Web browser and resubmit your review with a price rating.') );
	return $commentdata;
}

//Add an edit option in comment edit screen  

add_action( 'add_meta_boxes_comment', 'extend_comment_add_meta_box' );
function extend_comment_add_meta_box() {
    add_meta_box( 'title', __( 'Comment Metadata - Extend Comment' ), 'extend_comment_meta_box', 'comment', 'normal', 'high' );
}
 
function extend_comment_meta_box ( $comment ) {
    $title = get_comment_meta( $comment->comment_ID, 'title', true );
	$performance = get_comment_meta( $comment->comment_ID, 'performance', true);
	$price = get_comment_meta( $comment->comment_ID, 'price', true);


    wp_nonce_field( 'extend_comment_update', 'extend_comment_update', false );
    ?>
    <p>
        <label for="title"><?php _e( 'Review Title' ); ?></label>
        <input type="text" name="title" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
    </p>
    <p>
        <label for="performance"><?php _e( 'Performance: ' ); ?></label>
			<span class="commentratingbox">
			<?php for( $i=1; $i <= 10; $i++ ) {
				echo '<span class="commentrating"><input type="radio" name="performance" id="performance" value="'. $i .'"';
				if ( $performance == $i ) echo ' checked="checked"';
				echo ' />'. $i .' </span>'; 
				}
			?>
			</span>
    </p>
    <p>
        <label for="price"><?php _e( 'Price: ' ); ?></label>
			<span class="commentratingbox">
			<?php for( $i=1; $i <= 10; $i++ ) {
				echo '<span class="commentrating"><input type="radio" name="price" id="price" value="'. $i .'"';
				if ( $price == $i ) echo ' checked="checked"';
				echo ' />'. $i .' </span>'; 
				}
			?>
			</span>
    </p>
    <?php
}

// Update comment meta data from comment edit screen 

add_action( 'edit_comment', 'extend_comment_edit_metafields' );
function extend_comment_edit_metafields( $comment_id ) {
    if( ! isset( $_POST['extend_comment_update'] ) || ! wp_verify_nonce( $_POST['extend_comment_update'], 'extend_comment_update' ) ) return;

	if ( ( isset( $_POST['title'] ) ) && ( $_POST['title'] != '') ):
	$title = wp_filter_nohtml_kses($_POST['title']);
	update_comment_meta( $comment_id, 'title', $title );
	else :
	delete_comment_meta( $comment_id, 'title');
	endif;

	if ( ( isset( $_POST['price'] ) ) && ( $_POST['price'] != '') ):
	$price = wp_filter_nohtml_kses($_POST['price']);
	update_comment_meta( $comment_id, 'price', $price );
	else :
	delete_comment_meta( $comment_id, 'price');
	endif;

	if ( ( isset( $_POST['performance'] ) ) && ( $_POST['performance'] != '') ):
	$performance = wp_filter_nohtml_kses($_POST['performance']);
	update_comment_meta( $comment_id, 'performance', $performance );
	else : 
	delete_comment_meta( $comment_id, 'performance');
	endif;

}

// Add the comment meta (saved earlier) to the comment text 
// You can also output the comment meta values directly in comments template  

add_filter( 'comment_text', 'modify_comment');
function modify_comment( $text ){

	$plugin_url_path = WP_PLUGIN_URL;
	$sportsmans = (get_comment_meta( get_comment_ID(), 'performance', true ) + get_comment_meta( get_comment_ID(), 'price', true ) )/2;
	if( $commenttitle = get_comment_meta( get_comment_ID(), 'title', true ) ) {
		$commenttitle = '<strong>' . esc_attr( $commenttitle ) . '</strong><br/>';
		$text = $commenttitle . $text;
	} 

	if ( $commentrating = get_comment_meta ( get_comment_ID(), 'performance', true ) ) {
		$commentrating = '<div class="ratingBox"><table class="ratingTable"><tr><td>Performance:</td><td> <strong>' . $commentrating . ' / 10</strong></td><td><img src="'. $plugin_url_path .
		'/comment-ratings/images/' . $commentrating . 'star.gif"/></td></tr>';
		$text = $text . $commentrating;
	}

	if ( $commentrating = get_comment_meta ( get_comment_ID(), 'price', true ) ) {
		$commentrating = '<tr><td>Price:</td><td><strong>' . $commentrating . ' / 10</strong></td><td><img src="'. $plugin_url_path .
		'/comment-ratings/images/' . $commentrating . 'star.gif"/></td></tr>';
		$text = $text . $commentrating;
	}

	if( $commentrating = get_comment_meta( get_comment_ID(), 'price', true ) ) {
		$commentrating = '<tr><td><strong>Sportsman\'s Rating:</strong></td><td>  <strong>' . $sportsmans . ' / 10</strong></td><td></td></tr></table></div>';
		$text = $text . $commentrating;
		return $text;		
	} else {
		return $text;		
	}
