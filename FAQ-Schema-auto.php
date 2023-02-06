<?php
/*
 * Plugin Name: FAQ Schema auto
 * Description: Améliorez votre classement dans les moteurs de recherche avec notre plugin de schéma FAQ ! Créez facilement des rich snippets (données structurées) de questions fréquentes pour vos articles WordPress qui se chargeront rapidement et seront bien structurées pour le SEO. Augmentez la visibilité de votre site et aidez vos clients à trouver rapidement les informations qu'ils recherchent. Essayez notre plugin de schéma FAQ dès maintenant !
 * Version: 1.0
 * Author: Visual Web Click
 * Author URI: https://visualwebclick.com/
 */

add_action( 'wp_head', 'add_faq_schema' );

function add_faq_schema() {
  global $post;
	
  $content = $post->post_content;
  
  // Use regex to find h2 to h6 headings
  preg_match_all( '/<(h2|h3|h4|h5|h6).*?>(.*?)<\/(h2|h3|h4|h5|h6)>/', $content, $headings );
  $questions = $headings[2];
  $types = $headings[1];
	
  // Terminate if $questions is empty
  if (empty($questions)) {
    return;
  }
  // Use regex to find text between headings
  preg_match_all( '/(<(h2|h3|h4|h5|h6).*?>.*?<\/(h2|h3|h4|h5|h6)>)(.*?)(?=(<(h2|h3|h4|h5|h6).*?>|$))/s', $content, $matches );
  $answers = $matches[4];
  
  // Strip HTML from answers and questions
  $stripped_answers = array_map( function( $answer ) {
    return wp_strip_all_tags( $answer );
  }, $answers );
  $stripped_questions = array_map( function( $question ) {
	return wp_strip_all_tags( $question );
  }, $questions );
	
  // Create FAQ array
  $faq = array();
  for ( $i = 0; $i < count( $stripped_questions ); $i++ ) {
    $faq[] = array(
      "@type" => "Question",
      "name" => $stripped_questions[$i],
      "acceptedAnswer" => array(
        "@type" => "Answer",
        "text" => $stripped_answers[$i]
      )
    );
  }
  
  // Output JSON-LD
  $output = '<script type="application/ld+json">{"@context":"https://schema.org","@type":"FAQPage","mainEntity":' . json_encode( $faq ) . '}</script>';
  echo $output;
}
?>