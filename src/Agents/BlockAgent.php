<?php

namespace Dashifen\Movies\Agents;

use WP_Post;
use Dashifen\Movies\Movies;

/**
 * BlockAgent
 *
 * Registers and renders a block that displays movie metadata.
 */
class BlockAgent
{
  /**
   * BlockAgent constructor.
   *
   * @param Movies $handler
   */
  public function __construct(
    private Movies $handler
  ) {
  
  }
  
  /**
   * initialize
   *
   * Attaches public methods of this object to the WordPress ecosystem of
   * action and filter hooks.
   *
   * @return void
   */
  public function initialize(): void
  {
    add_action('init', [$this, 'registerBlock']);
    add_action('enqueue_block_assets', [$this, 'addBlockAssets']);
  }
  
  /**
   * registerBlock
   *
   * Registers our block based on the block metadata within theme.json in the
   * build folder.
   *
   * @return void
   */
  public function registerBlock(): void
  {
    $assetsFolder = dirname(__FILE__, 3) . '/assets/src';
    register_block_type_from_metadata($assetsFolder, [
      'render_callback' => [$this, 'renderBlock'],
    ]);
  }
  
  /**
   * renderBlock
   *
   * Renders our block returning the information about a specific movie
   * which is added to the DOM.
   *
   * @param array $attributes
   *
   * @return string
   */
  public function renderBlock(array $attributes): string
  {
    $content = '';
    
    if (($movieId = $attributes['movieId'] ?? null) !== null) {
      $prefix = $this->handler->getPluginPrefix();
      $metadata = MetaboxAgent::getMetadata($movieId, $prefix);
      $poster = get_the_post_thumbnail_url($movieId, 'small');
      $movie = get_post($movieId);
      
      // now, we start an output buffer.  all the HTML we're about to build
      // will get sored in that buffer, and when we call ob_get_clean below, we
      // can extract all that content and return it to the calling scope.
      
      ob_start(); ?>

      <aside id="movie-<?= $movieId ?>" class="movie-plugin-dev-ex-container">
        <header><?= $movie->post_title ?></header>
        <figure><img src="<?= $poster ?>" alt="Movie poster for <?= $movie->post_title ?>"></figure>
        <p><?= apply_filters('the_content', $movie->post_content) ?></p>
        <dl>
          <dt>Director</dt>
          <dd><?= $metadata[$prefix . 'director'] ?></dd>
          <dt>Release Date</dt>
          <dd><?= $metadata[$prefix . 'release-date'] ?></dd>
          <dt>Running Time</dt>
          <dd><?= $metadata[$prefix . 'running-time'] ?></dd>
          <dt>Actors</dt>
          <dd><?= join('</dd><dd>', $metadata[$prefix . 'actors']) ?></dd>
        </dl>
      </aside>
      
      <?php $content = ob_get_clean();
    }
    
    return $content;
  }
  
  /**
   * addBlockAssets
   *
   * Enqueues the JavaScript for our block.
   *
   * @return void
   */
  public function addBlockAssets(): void
  {
    $block = require_once dirname(__FILE__, 3) . '/assets/build/movie-block.min.asset.php';
    $blockUrl = dirname(plugin_dir_url(__FILE__), 2) . '/assets/build/movie-block.min.js';
    wp_enqueue_script('movie-block', $blockUrl, $block['dependencies'], $block['version'], true);
  }
}