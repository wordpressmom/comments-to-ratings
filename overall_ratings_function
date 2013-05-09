<?php /*
This function should be added to your functions.php file. 
To reference the overall rating (created from the meta_values of the meta_keys 'price' and 'performance') 
just call the function sportsmans_ratings() within your template.
This is NOT the cleanest or EASIEST method - this just worked for me!
*/

function overall_rating() {
$postid = get_the_ID(); 
$reviewCount = get_comments_number( $postid );
//setup to query price meta_values
$args = array(
  'post_id'      => $postid,
	'count'        => false,
	'meta_key'     => 'price',
	'meta_value'   => '',
	'meta_query'   => ''
);

// The Query
$comments_query = new WP_Comment_Query;
$comments = $comments_query->query( $args );

// Comment Loop
if ( $comments ) {
	$rating=0; 
	foreach ($comments as $comment) {
		$total += $comment->meta_value;} // add up all price rating values for all reviews
		$priceRtg = $total/$reviewCount; // take total vlaue and divide by total reviews
	} else {}
 
//setup to query performance meta_values 
$pargs = array(
	'post_id'      => $postid2,
	'count'        => false,
	'meta_key'     => 'performance',
	'meta_value'   => '',
	'meta_query'   => ''
);

// The Query
$comments_query2 = new WP_Comment_Query;
$comments2 = $comments_query2->query( $pargs );

// Comment Loop
if ( $comments2 ) {
	$prating=0;
	foreach ($comments2 as $comment2) {
		$total2 += $comment2->meta_value;} // add up all performance rarting values for all reviews
		$pRating = $total2/$reviewCount2; // take total value and divide by total reivews
	} else {}

$overall = ($pRating + $priceRtg) / 2; // add up total values for both price and performance rating then divide
$final = round($overall, 1); // round it up so its nice and pretty
if ($final == 0 ) { // if rating is 0 state that it hasn't been rating
	echo "Unrated";
} else {
echo $final; // echo the final rating
}
} ?>
