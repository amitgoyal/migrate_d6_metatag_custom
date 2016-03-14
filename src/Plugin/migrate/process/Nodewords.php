<?php

/**
 * @file
 * Contains \Drupal\migrate_d6_metatag_custom\Plugin\migrate\process\Nodewords.
 */

namespace Drupal\migrate_d6_metatag_custom\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\Component\Utility\Unicode;


/**
 * This plugin converts D6 Nodewords to D8 Metatags.
 *
 * @MigrateProcessPlugin(
 *   id = "d6_nodewords"
 * )
 */
class Nodewords extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $metatags = unserialize($value);

    // Append 3 different fields in page_title to title field.
    $metatags['title'] = '';
    if (!empty($metatags['page_title'])) {
      $metatags['title'] = $metatags['page_title']['value'];
      // If append page title is enabled, add site name token.
      if ($metatags['page_title']['append']) {
        $metatags['title'] = $metatags['title'] . $metatags['page_title']['divider'] . '[site:name]';
      }
    }
    // We need to remove page_title, as this is now title.
    unset($metatags['page_title']);

    // Convert keywords to lowercase.
    $metatags['keywords'] = Unicode::strtolower($metatags['keywords']['value']);

    // Change key for Canonical URL.
    $metatags['canonical_url'] = $metatags['canonical']['value'];
    unset($metatags['canonical']);

    // Set robots settings.
    $robots = array();
    foreach ($metatags['robots']['value'] as $key => $value) {
      if ($value) {
        $robots[] = $key;
      }
    }
    $metatags['robots'] = implode(', ', $robots);

    // Convert the array to D8 format.
    foreach ($metatags as $key => $metatag) {
      if (isset($metatag['value'])) {
        $metatags[$key] = $metatag['value'];
      }
    }

    return serialize($metatags);
  }

}
