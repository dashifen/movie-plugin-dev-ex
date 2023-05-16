<?php

namespace Dashifen\Movies\Agents;

use Dashifen\Movies\Movies;

class ListingAgent
{
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
    add_filter('manage_' . Movies::POST_TYPE . '_posts_columns', [$this, 'addDirectorColumn']);
    add_action('manage_' . Movies::POST_TYPE . '_posts_custom_column', [$this, 'fillDirectorColumn'], 10, 2);
  }
  
  /**
   * addDirectorColumn
   *
   * Adds a custom column for directors to the Movie listing page.
   *
   * @param array $columns
   *
   * @return array
   */
  public function addDirectorColumn(array $columns): array
  {
    $newColumns = [];
    foreach ($columns as $id => $column) {
      $newColumns[$id] = $column;
      
      // above, we add each of the original, core columns to our array of new
      // columns.  we want to add our custom column after the one for the
      // movie's title.  so, if that one is this one, we do so.  this both
      // copies all of the original columns over to the new list and adds ours
      // where we want it.
      
      if ($id === 'title') {
        $newColumns[$this->handler->getPluginPrefix() . 'director'] = 'Director';
      }
    }
    
    return $newColumns;
  }
  
  /**
   * fillDirectorColumn
   *
   * @param string $column
   * @param int    $postId
   *
   * @return void
   */
  public function fillDirectorColumn(string $column, int $postId): void
  {
    if ($column === ($key = $this->handler->getPluginPrefix() . 'director')) {
      
      // if this is our custom column, then we get the value for our key out
      // of the database and print it to the screen.  this is one of the rare
      // cases where an action produces content for the screen.  this is
      // likely because WP Core doesn't provide any content for custom columns
      // by default, so there's technically nothing to filter.
      
      echo get_post_meta($postId, $key, true);
    }
  }
}