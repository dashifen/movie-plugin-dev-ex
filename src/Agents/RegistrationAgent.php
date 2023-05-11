<?php

namespace Dashifen\Movies\Agents;

use Dashifen\Movies\Movies;

/**
 * RegistrationAgent
 *
 * This agent handles the registration of our custom post type and its
 * taxonomy.
 */
class RegistrationAgent
{
  /**
   * RegistrationAgent constructor.
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
   * Attaches methods of this object to the WordPress Core ecosystem of action
   * and filter hooks.
   *
   * @return void
   */
  public function initialize(): void
  {
    add_action('init', [$this, 'registerPostType']);
    add_action('init', [$this, 'registerTaxonomy']);
  }
  
  /**
   * registerPostType
   *
   * @return void
   */
  public function registerPostType(): void
  {
    register_post_type(Movies::POST_TYPE, [
      'labels'          => $this->getLabels(),
      'menu_icon'       => 'data:image/svg+xml;base64,' . $this->getIcon(),
      'supports'        => ['title', 'editor', 'thumbnail', 'revisions'],
      'capability_type' => 'page',
      'public'          => true,
      'hierarchical'    => false,   // hierarchical is like pages; this is like posts
      'show_in_rest'    => true,    // required for the block editor; a good idea otherwise
      'has_archive'     => true,    // creates a /movies URL for an archive of our posts
      'menu_position'   => 20,      // will appear after Pages in the menu
    ]);
  }
  
  /**
   * getLabels
   *
   * Returns the labels for our custom post type.
   *
   * @return array
   */
  private function getLabels(): array
  {
    $plural = 'Movies';
    $singular = 'Movie';
    $textDomain = $this->handler->getTextDomain();
    
    return [
      'name'                     => _x($plural, $singular . ' General Name', $textDomain),
      'singular_name'            => _x($singular, $singular . ' Singular Name', $textDomain),
      'menu_name'                => __($plural, $textDomain),
      'name_admin_bar'           => __($singular, $textDomain),
      'archives'                 => __($singular . ' Archives', $textDomain),
      'attributes'               => __($singular . ' Attributes', $textDomain),
      'parent_item_colon'        => __('Parent ' . $singular . ':', $textDomain),
      'all_items'                => __('All ' . $plural, $textDomain),
      'add_new_item'             => __('Add New ' . $singular, $textDomain),
      'add_new'                  => __('Add New', $textDomain),
      'new_item'                 => __('New ' . $singular, $textDomain),
      'edit_item'                => __('Edit ' . $singular, $textDomain),
      'update_item'              => __('Update ' . $singular, $textDomain),
      'view_item'                => __('View ' . $singular, $textDomain),
      'view_items'               => __('View ' . $plural, $textDomain),
      'search_items'             => __('Search ' . $singular, $textDomain),
      'not_found'                => __('Not found', $textDomain),
      'not_found_in_trash'       => __('Not found in Trash', $textDomain),
      'featured_image'           => __('Movie Poster', $textDomain),
      'set_featured_image'       => __('Set movie poster', $textDomain),
      'remove_featured_image'    => __('Remove movie poster', $textDomain),
      'use_featured_image'       => __('Use as movie poster', $textDomain),
      'insert_into_item'         => __('Add to ' . $singular, $textDomain),
      'uploaded_to_this_item'    => __('Uploaded to this ' . $singular, $textDomain),
      'items_list'               => __($plural . ' list', $textDomain),
      'items_list_navigation'    => __($plural . ' list navigation', $textDomain),
      'filter_items_list'        => __('Filter ' . $plural . ' list', $textDomain),
      'item_published'           => __($singular . ' published.', $textDomain),
      'item_published_privately' => __($singular . ' published privately.', $textDomain),
      'item_reverted_to_draft'   => __($singular . ' reverted to draft.'),
      'item_scheduled'           => __($singular . ' scheduled.', $textDomain),
      'item_updated'             => __($singular . ' updated.', $textDomain),
      'item_link'                => __($singular . ' Link', $textDomain),
      'item_link_description'    => __('A link to a ' . $singular . '.', $textDomain),
    ];
  }
  
  /**
   * getIcon
   *
   * Returns a base64 encoded SVG icon for this post type to use in the
   * WordPress Dashboard menu.
   *
   * @return string
   */
  private function getIcon(): string
  {
    $icon = <<<ICON
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
        <!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
        <path fill="#a7aaad" d="M0 96C0 60.7 28.7 32 64 32H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM48 368v32c0 8.8 7.2 16 16 16H96c8.8 0 16-7.2 16-16V368c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V368c0-8.8-7.2-16-16-16H416zM48 240v32c0 8.8 7.2 16 16 16H96c8.8 0 16-7.2 16-16V240c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V240c0-8.8-7.2-16-16-16H416zM48 112v32c0 8.8 7.2 16 16 16H96c8.8 0 16-7.2 16-16V112c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16zM416 96c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V112c0-8.8-7.2-16-16-16H416zM160 128v64c0 17.7 14.3 32 32 32H320c17.7 0 32-14.3 32-32V128c0-17.7-14.3-32-32-32H192c-17.7 0-32 14.3-32 32zm32 160c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H320c17.7 0 32-14.3 32-32V320c0-17.7-14.3-32-32-32H192z"/>
      </svg>
ICON;
    
    return base64_encode($icon);
  }
  
  /**
   * registerTaxonomy
   *
   * Registers our custom genre taxonomy and "attaches" it to our movies.
   *
   * @return void
   */
  public function registerTaxonomy(): void
  {
    register_taxonomy(Movies::TAX_GENRE, Movies::POST_TYPE, [
      'labels'            => $this->getTaxonomyLabels(),
      'show_tagcloud'     => false,
      'public'            => true,
      'show_admin_column' => true,  // lists genre on the main admin page for movies
      'show_in_rest'      => true,  // required for block editor; otherwise a good idea
      'hierarchical'      => true,  // hierarchical like categories; not like tags
    ]);
  }
  
  /**
   * getTaxonomyLabels
   *
   * Returns an array of labels for our genre taxonomy.
   *
   * @return array
   */
  private function getTaxonomyLabels(): array
  {
    $plural = 'Genres';
    $singular = 'Genre';
    $textDomain = $this->handler->getTextDomain();
    
    return [
      'name'                       => _x($plural, $singular . ' General Name', $textDomain),
      'singular_name'              => _x($singular, $singular . ' Singular Name', $textDomain),
      'menu_name'                  => __($plural, $textDomain),
      'all_items'                  => __('All ' . $plural, $textDomain),
      'parent_item'                => __('Parent ' . $singular, $textDomain),
      'parent_item_colon'          => __('Parent ' . $singular . ':', $textDomain),
      'new_item_name'              => __('New ' . $singular . ' Name', $textDomain),
      'add_new_item'               => __('Add New ' . $singular, $textDomain),
      'edit_item'                  => __('Edit ' . $singular, $textDomain),
      'update_item'                => __('Update ' . $singular, $textDomain),
      'view_item'                  => __('View ' . $singular, $textDomain),
      'separate_items_with_commas' => __('Separate ' . $plural . ' with commas', $textDomain),
      'add_or_remove_items'        => __('Add or remove ' . $plural, $textDomain),
      'choose_from_most_used'      => __('Choose from the most used ' . $plural, $textDomain),
      'popular_items'              => __('Popular ' . $plural, $textDomain),
      'search_items'               => __('Search ' . $plural, $textDomain),
      'not_found'                  => __('Not Found', $textDomain),
      'no_terms'                   => __('No ' . $plural, $textDomain),
      'items_list'                 => __($plural . ' list', $textDomain),
      'items_list_navigation'      => __($plural . ' list navigation', $textDomain),
      'filter_by_item'             => __('Filter by ' . $singular, $textDomain),
      'back_to_items'              => __('Back to ' . $plural, $textDomain),
      'item_link'                  => __($singular . ' Link', $textDomain),
      'item_link_description'      => __('A link to a ' . $singular, $textDomain),
    ];
  }
}
