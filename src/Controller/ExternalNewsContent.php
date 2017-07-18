<?php

namespace Drupal\external_news\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ExternalNewsContent.
 *
 * @package Drupal\external_news\Controller
 */
class ExternalNewsContent extends ControllerBase {

  /**
   * Content.
   *
   * @return string
   *   Return external_news rendered array.
   */
  public function content($news) {
    return [
      '#theme' => 'external_news',
      '#news' => $news,
      '#cache' => [
        'keys' => ['external_news_detail'],
        'contexts' => [],
        'tags' => [],
        'max-age' => 86400,
      ],
    ];
  }

}
