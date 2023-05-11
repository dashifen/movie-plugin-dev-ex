<?php

namespace Dashifen\Movies;

use Dashifen\Movies\Agents\MetaboxAgent;
use Dashifen\WPDebugging\WPDebuggingTrait;
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
  use WPDebuggingTrait;
  
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
      // Core.  but, it might not be loaded yet.  so, we check for that, and
      // once we guarantee that it exists, we call it and extract the text
      // domain from it.  since this is a non-trivial operation, we only want
      // to do it the first time.  for the second visit to this method and
      // beyond, we just return the property that we set here in this if-block.
      
      if (!function_exists('get_plugin_data')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
      }
      
      $pluginData = get_plugin_data(realpath('../index.php'));
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