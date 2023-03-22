<?php
/**
 * Plugin Name: WikiSearch
 */

// Register the shortcode
add_shortcode( 'wikipedia_search', 'wikipedia_search_shortcode' );

function wikipedia_search_shortcode() {
    // Check if the form was submitted
    if ( isset( $_POST['search_query'] ) ) {
        // Retrieve the search query
        $search_query = $_POST['search_query'];
        
        // Query Wikipedia
        $url = "https://en.wikipedia.org/w/api.php?action=query&list=search&format=json&srsearch=" . urlencode( $search_query );
        $response = file_get_contents( $url );
        $data = json_decode( $response );
        
        // Display the search results
        if (isset($data->query->search)) {
            $search_results = $data->query->search;
            $output = '<ul>';
            foreach ($search_results as $result) {
                $title_with_underscores = str_replace("+", "_", urlencode($result->title));
                $output .= '<li><a href="https://en.wikipedia.org/wiki/' . $title_with_underscores . '">' . $result->title . '</a></li>';
            }
            $output .= '</ul>';
            return $output;
        } else {
            return 'No results found.';
        }
    }

    // Display the search form
    $output = '<form method="post">';
    $output .= '<label for="search_query">Search Wikipedia: </label>';
    $output .= '<input type="text" id="search_query" name="search_query" />';
    $output .= '<input type="submit" value=" Search" />';
    $output .= '</form>';
    return $output;
}
?>