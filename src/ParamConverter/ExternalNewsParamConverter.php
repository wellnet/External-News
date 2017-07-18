<?php

namespace Drupal\external_news\ParamConverter;

use Drupal\http_client_manager\HttpClient;
use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;

/**
 * Class ExternalNewsParamConverter.
 *
 * @package Drupal\external_news
 */
class ExternalNewsParamConverter implements ParamConverterInterface {

  /**
   * Drupal\http_client_manager\HttpClient definition.
   *
   * @var \Drupal\http_client_manager\HttpClient
   */
  protected $externalNewsHttpClientNews;

  /**
   * Constructs a new ExternalNewsParamConverter object.
   */
  public function __construct(HttpClient $external_news_http_client_news) {
    $this->externalNewsHttpClientNews = $external_news_http_client_news;
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    
    if (empty($value)) {
      return NULL;
    }

    $co = \Drupal::cache()->get('external_news_' . $value);
    if (!empty($co)) {
      return $co->data;
    }

    $news = [];
    try {
      $result = $this->externalNewsHttpClientNews->call('GetNewsDetail', [
        'idNotizia' => (int) $value,
      ]);

      if ($result['success']) {
        /* items array has:
         *
         * 'numero_righe' => 4
         * 'pagina_visualizzata' => 1
         * 'totale_pagine' => 5
         * 'totale_righe' => 19
         * 'news' => array(4)
         *
         */
        $items = $result['items'];
        $news = array_pop($items['news']);
        \Drupal::cache()->set('external_news_' . $value, $news, 86400);
      }
    }
    catch (CurlException $e) {
      // If the call to 'GetNewsDetail' for some reason timeouts
      // log it as error (the News Detail page does not shown no data)
      $this->logger->error('Caught exception on call to organigramma: %m',
        [
          '%m' => $e->getMessage(),
        ]
      );
    }

    return $news;
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    return (!empty($definition['type']) && $definition['type'] == 'external:news');
  }

}
