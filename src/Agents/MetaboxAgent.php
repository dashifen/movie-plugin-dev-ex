<?php

namespace Dashifen\Movies\Agents;

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
  
  
}