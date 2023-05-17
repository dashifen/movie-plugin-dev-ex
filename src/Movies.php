<?php

namespace Dashifen\Movies;

use Dashifen\Movies\Agents\BlockAgent;
use Dashifen\Movies\Agents\MetaboxAgent;
use Dashifen\Movies\Agents\ListingAgent;
use Dashifen\Movies\Agents\RegistrationAgent;

/**
 * Movies
 *
 * This object deals with plugin-wide initiation and configuration tasks.  In
 * Dash's parlance, this is the Handler which registers a series of Agents to
 * that perform specific tasks.
 */
class Movies
{
  public const SLUG = 'movies-plugin-dev-ex';
  public const POST_TYPE = 'movies';
  public const TAX_GENRE = 'genres';
  
  private ?string $textDomain = null;
  
  /**
   * initialize
   *
   * Instantiates this handler's Agents.
   *
   * @return void
   */
  public function initialize(): void
  {
    $agents = [
      RegistrationAgent::class => new RegistrationAgent($this),
      MetaboxAgent::class      => new MetaboxAgent($this),
      ListingAgent::class      => new ListingAgent($this),
      BlockAgent::class        => new BlockAgent($this),
    ];
    
    foreach ($agents as $agent) {
      $agent->initialize();
    }
  }
  
  /**
   * getTextDomain
   *
   * Returns this plugins text domain using the property that was defined
   * in the above constructor.
   *
   * @return string
   */
  public function getTextDomain(): string
  {
    if ($this->textDomain === null) {
      
      // if the textDomain property is currently null, then this is the first
      // time we've requested our text domain during this HTTP request.  to get
      // that information, we can use the get_plugin_data function within WP
      // Core.
      
      if (!function_exists('get_plugin_data')) {
        
        // if the get_plugin_data function is not yet loaded, we can do so
        // by requiring the following file within this scope.  because we're
        // within an object method, this won't impact WP Core when it loads
        // it later on.  ABSPATH is defined during the WordPress loading
        // process and points to the root of the site.
        
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
      }
      
      // once we know we have our function, we call it and send it the
      // absolute path to this plugin's index file.
      
      $pluginData = get_plugin_data(
        ABSPATH . 'wp-content/plugins/movie-plugin-dev-ex/index.php'
      );
      
      $this->textDomain = $pluginData['TextDomain'];
    }
    
    return $this->textDomain;
  }
  
  /**
   * getPluginPrefix
   *
   * Returns a prefix that our Agents can use to differentiate our information
   * from other information in the DOM or in the database.
   *
   * @return string
   */
  public function getPluginPrefix(): string
  {
    return self::SLUG . '-';
  }
}