<?php
/**
 * Plugin Name: Movies (Plugin Development Example)
 * Description: An example of WordPress plugin development focusing on a custom post type for movies.
 * Author URI: https://dashifen.com
 * Author: David Dashifen Kees
 * Text Domain: dashifen-movie-plugin
 * Version: 1.0.0
 *
 * For full Plugin Header options, see:
 * https://developer.wordpress.org/plugins/plugin-basics/header-requirements/
 */

namespace Dashifen;

use Dashifen\Movies\Movies;
use Dashifen\Movies\MoviesException;

if (version_compare(PHP_VERSION, '8.0', '<')) {
  $message = 'The Movies plugin requires PHP 8.0 or higher.  You\'re using %s.';
  $message = sprintf($message, PHP_VERSION);
  exit($message);
}

if (defined('ABSPATH')) {
  if (!class_exists(Movies::class)) {
    require_once 'vendor/autoload.php';
  }
  
  (function () {
    
    // by instantiating our class objects within the scope of this anonymous
    // function, they cannot be "seen" by the rest of WordPress Core.  primarily,
    // this is to avoid accidentally overwriting a global variable, but it also
    // helps make sure that our object's public methods won't accidentally be
    // triggered.
    
    try {
      $movies = new Movies();
      $movies->initialize();
    } catch (MoviesException $e) {
      if (defined(WP_DEBUG_DISPLAY) && WP_DEBUG_DISPLAY) {
        
        // if the WP debug display constant is defined and if it's true, then
        // we just print the exception to the screen.  this lets us fix
        // problems as they arrive in development, but in a production
        // environment the WP_DEBUG_DISPLAY flag should almost never be true.
        
        echo "<pre>" . print_r($e, true) . "</pre>";
      } else {
        if (!function_exists("write_log")) {
          
          // if a write_log function doesn't already exist, we'll make on.
          // this would let us define a more globally recognized log writer in
          // a larger project, but maybe just have this little one here during
          // development or something like that.
          
          function write_log($log): void
          {
            if (is_array($log) || is_object($log)) {
              error_log(print_r($log, true));
            } else {
              error_log($log);
            }
          }
        }
        
        write_log($e);
      }
    }
  })();
}
