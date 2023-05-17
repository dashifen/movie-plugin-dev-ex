<?php

namespace Dashifen\Movies\Agents;

use WP_Post;
use Dashifen\Movies\Movies;

/**
 * RegistrationAgent
 *
 * This agent handles the registration of our custom post type and its
 * taxonomy.
 */
class MetaboxAgent
{
  /**
   * MetaboxAgent constructor.
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
    add_action('add_meta_boxes', [$this, 'registerMetabox']);
    
    // this is an example of a "dynamic hook."  it fires after a post has been
    // saved in the database but only for posts of the specified post type.
    // this is somewhat easier than using the more generalized "save_post"
    // hook and then exiting when the post type isn't the one we care about.
    
    add_action('save_post_' . Movies::POST_TYPE, [$this, 'saveMetabox']);
  }
  
  /**
   * registerMetabox
   *
   * Adds the metabox for movie metadata to the editor.
   *
   * @return void
   */
  public function registerMetabox(): void
  {
    add_meta_box(
      $this->handler->getPluginPrefix() . 'metabox',  // HTML id of the DOM element
      'Movie Metadata',                               // on-screen title for that element
      [$this, 'showMetabox'],                         // callback function that fills it
      Movies::POST_TYPE,                              // the post type that needs it
      'normal',                                       // its context, i.e. where it goes on-screen
      'high'                                          // its priority, i.e. put it at the top
    );
  }
  
  /**
   * showMetabox
   *
   * Emits HTML that is placed within the metabox registered in the prior
   * method.
   *
   * @param WP_Post $post
   *
   * @return void
   */
  public function showMetabox(WP_Post $post): void
  {
    $prefix = $this->handler->getPluginPrefix();
    $metadata = self::getMetadata($post->ID, $prefix); ?>
    
    <div class="wrap">
      <table class="form-table">
        
        <?php wp_nonce_field($prefix . 'save-metadata', $prefix . 'nonce');
        foreach ($metadata as $key => $value) {
          $type = match ($key) {
            $prefix . 'director', $prefix . 'running-time' => 'text',
            $prefix . 'release-date'                       => 'date',
            $prefix . 'actors'                             => 'textarea',
          }; ?>
          
          <tr>
            <th>
              <label for="<?= $key ?>"><?= $this->unsanitizeKey($key) ?></label>
            </th>
            <td>
              <?php if ($type !== 'textarea') { ?>
                
                <input type="<?= $type ?>" id="<?= $key ?>" name="<?= $key ?>" value="<?= $value ?>"
                  class="regular-text">
                
                <?php if ($key === $prefix . 'running-time') { ?>
                  <p class="description">Enter running time in minutes.</p>
                <?php }
                
              } else { ?>
                
                <textarea rows="10" cols="50" id="<?= $key ?>" name="<?= $key ?>"><?= join("\n", $value) ?></textarea>
                <p class="description">Enter actors one per line.</p>
              
              <?php } ?>
            </td>
          </tr>
        
        <?php } ?>
      
      </table>
    </div>
  
  <?php }
  
  /**
   * getMetadata
   *
   * This static method returns an array of our movie metadata for easy use
   * elsewhere.  It's static because it's likely themes might need a good way
   * to grab all of the metadata for a movie post and making it static allows
   * that to happen most easily.
   *
   * @param int    $postId
   * @param string $prefix
   *
   * @return array
   */
  public static function getMetadata(int $postId, string $prefix): array
  {
    foreach (['director', 'release-date', 'running-time', 'actors'] as $key) {
      
      // for each of the keys in our loop, we prefix them in our as the indices
      // of our $metadata array.  this allows us to ensure that the IDs these
      // names become in the DOM are going to be unique.
      
      $metadata[$prefix . $key] = get_post_meta($postId, $prefix . $key, $key !== 'actors');
    }
    
    return $metadata;
  }
  
  /**
   * unsanitizeKey
   *
   * Takes a sanitized key, like movies-plugin-dev-ex-director, and returns
   * an unsanitized version, like Director, passing it through the i18n
   * function.
   *
   * @param string $key
   *
   * @return string
   */
  private function unsanitizeKey(string $key): string
  {
    // first, we remove our prefix.  that leaves us with just a slug-like
    // version of our metadata names, like director or release-date.  then, we
    // replace hyphens with spaces, run it all through the ucwords function to
    // make it pretty, and finally use the i18n function just in case.
    
    $key = str_replace($this->handler->getPluginPrefix(), '', $key);
    $key = str_replace('-', ' ', $key);
    $key = ucwords($key);
    
    return __($key, $this->handler->getTextDomain());
  }
  
  /**
   * saveMetabox
   *
   * Extracts the information we care about from the $_POST superglobal and
   * saves it in the database.
   *
   * @param int $postId
   *
   * @return void
   */
  public function saveMetabox(int $postId): void
  {
    $prefix = $this->handler->getPluginPrefix();
    
    if (isset($_POST[$prefix . 'nonce'])) {
      if (!wp_verify_nonce($_POST[$prefix . 'nonce'], $prefix . 'save-metadata')) {
        
        // if our nonce was found, but it was not valid, we can call the Core
        // "are you sure" (ays) function that will display an intentionally
        // ambiguous message to help mitigate someone who might have been
        // trying to attack the system.  this function calls wp_die internally
        // so if we end up in here, the response will end.
        
        wp_nonce_ays($prefix . 'save-metadata');
      }
      
      // if we're on this side of the inner if-block, then we have a nonce and
      // it was valid.  therefore, we want to extract and save our metadata.
      // the $_POST superglobal has a bunch of additional information within it
      // that we don't, at this time, need to be messing with.  to avoid doing
      // so, we can use array_filter to keep only the items in $_POST with a
      // key that starts with our plugin prefix as follows:
      
      $filter = fn($key) => str_starts_with($key, $prefix);
      $metadata = array_filter($_POST, $filter, ARRAY_FILTER_USE_KEY);
      foreach ($metadata as $key => $value) {
        if (!str_contains($key, 'actors')) {
          update_post_meta($postId, $key, $value);
        } else {
          
          // when saving our actors, we want to first break them up into
          // individual entries and save them all separately.  why?  who knows!
          // probably, this would help if we wanted to find all the movies in
          // our database with so-and-so in them.  but, this means that we
          // also need to remove all prior information about this movie's
          // actors in case we're updating things.
          
          delete_post_meta($postId, $key);
          $actors = explode("\n", $value);
          foreach ($actors as $actor) {
            
            // above we could call update post meta.  that's because there's
            // only one director, release data, or running time per movie.
            // but, there are multiple actors.  therefore, we just use the
            // add_post_meta which will happily link a single movie to multiple
            // values for this metadata key.
            
            add_post_meta($postId, $key, trim($actor));
          }
        }
      }
    }
  }
}