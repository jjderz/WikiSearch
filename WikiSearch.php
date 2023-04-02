<?php
/**
 * Plugin Name: WikiSearch
 */

// Register the shortcode
add_shortcode( 'wikipedia_search', 'wikipedia_search_shortcode' );

function wikipedia_search_shortcode() {
    // Start a session to persist the search flag
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if the form was submitted
    if ( isset( $_POST['search_query'] ) ) {
        // Set the search flag
        $_SESSION['search_flag'] = true;
        
        // Retrieve the search query
        $search_query = $_POST['search_query'];
        
        // Query Wikipedia
        $url = "https://en.wikipedia.org/w/api.php?action=query&list=search&format=json&srsearch=" . urlencode( $search_query );
        $response = file_get_contents( $url );
        $data = json_decode( $response );
        
        // Display the search results
        if (isset($data->query->search)) {
            $search_results = $data->query->search;
            $output = '';
            $output .= '<form method="post">';
            $output .= '<label for="search_query">Search Wikipedia: </label>';
            $output .= '<input type="text" id="search_query" name="search_query" value="' . htmlentities($search_query) . '" />';
            $output .= '<input type="submit" value=" Search" />';
            $output .= '</form>';
            $output .= '<br />'; // add a line break between the search form and the search results
            $output .= '<h3>Search Results:</h3>';
            $output .= '<ul>';
            foreach ($search_results as $result) {
                $title_with_underscores = str_replace("+", "_", urlencode($result->title));
                $output .= '<li><a href="https://en.wikipedia.org/wiki/' . $title_with_underscores . '" target="_blank">' . $result->title . '</a></li>';
            }
            $output .= '</ul>';
        } else {
            $output = 'No results found.<br />'; // add a line break after the message
        }
    } else {
        // Unset the search flag
        unset($_SESSION['search_flag']);
        $output = ''; // initialize the output variable
        
        // Display the search form
        $output .= '<form method="post">';
        $output .= '<label for="search_query">Search Wikipedia: </label>';
        $output .= '<input type="text" id="search_query" name="search_query" />';
        $output .= '<input type="submit" value=" Search" />';
        $output .= '</form>';
    }
    
    return $output;
}
?>
