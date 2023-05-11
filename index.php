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
      
      // the catcher function simply dumps the exception to the screen.  this
      // would likely be a problem for production code, but it works in this
      // example.  for more info, see https://github.com/dashifen/debugging,
      // and find the method's definition in hte src/DebuggingTrait.php file.
      
      Movies::catcher($e);
    }
  })();
}
