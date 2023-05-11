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
      if (!function_exists('get_plugin_data')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
      }
      
      $pluginData = get_plugin_data(realpath('../index.php'));
      $this->textDomain = $pluginData['TextDomain'];
    }
    
    return $this->textDomain;
  }
  
}