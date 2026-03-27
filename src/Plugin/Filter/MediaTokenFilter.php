<?php

declare(strict_types=1);

namespace Drupal\quick_media\Plugin\Filter;

use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\FilterProcessResult;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

/**
 * Replaces [media:ID] tokens with images.
 *
 * @Filter(
 *   id = "quick_media_token_filter",
 *   title = @Translation("Media token filter"),
 *   description = @Translation("Replaces [media:ID] with rendered images."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE
 * )
 */
final class MediaTokenFilter extends FilterBase {

  public function process($text, $langcode): FilterProcessResult {

    $cache_tags = [];

    $text = preg_replace_callback('/\[media:(\d+)\]/', function ($matches) use (&$cache_tags) {

      $mid = (int) $matches[1];

      $media = Media::load($mid);
      if (!$media) {
        return '';
      }

      // Track cache dependency
      $cache_tags[] = 'media:' . $mid;

      // Resolve source field properly
      $source = $media->getSource();
      $field_def = $source->getSourceFieldDefinition($media->bundle->entity);

      if (!$field_def) {
        return '';
      }

      $field_name = $field_def->getName();
      $item = $media->get($field_name)->first();

      if (!$item || empty($item->target_id)) {
        return '';
      }

      $file = File::load((int) $item->target_id);
      if (!$file) {
        return '';
      }

      $uri = $file->getFileUri();

      $url = \Drupal::service('file_url_generator')
                    ->generateAbsoluteString($uri);

      // Alt + title
      $alt = $item->alt ?? '';
      $title = $item->title ?? '';

      // Optional fallback (comment out if you prefer empty alt)
      if ($alt === '') {
        $alt = $file->getFilename();
      }

      return '<img src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"'
        . ' alt="' . htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') . '"'
        . ($title ? ' title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"' : '')
        . ' style="max-width:100%;">';

    }, $text);

    $result = new FilterProcessResult($text);

    // Attach cache tags so images update correctly
    if (!empty($cache_tags)) {
      $result->addCacheTags(array_unique($cache_tags));
    }

    return $result;
  }

}
