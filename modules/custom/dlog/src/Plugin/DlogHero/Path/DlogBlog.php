<?php

namespace Drupal\dlog\Plugin\DlogHero\Path;

use Drupal\dlog_hero\Annotation\DlogHeroPath;
use Drupal\dlog_hero\Plugin\DlogHero\Path\DlogHeroPathPluginBase;
use Drupal\media\MediaInterface;

/**
 * Hero blog for path
 *
 * @DlogHeroPath(
 *   id = "dlog_blog",
 *   match_type="listed",
 *   match_path={"/blog"} *
 * )
 */

class DlogBlog extends DlogHeroPathPluginBase {

  /**
   * @inerhitDoc
   */
  public function getHeroSubtitle() {
    return 'dlog hero video';
  }

  /**
   * @inerhitDoc
   */
  public function getHeroImage() {
    /** @var \Drupal\media\MediaStorage $media_storage */
    $media_storage = $this->getEntityTypeManager()->getStorage('media');
    //$media_storage->load(id'21') - 21 is ID image in Media what will use on Hero
    $media_image = $media_storage->load('21');
    if ($media_image instanceof MediaInterface) {
      return $media_image->get('field_media_image')->entity->get('uri')->value;
    }
  }

  /**
   * @inerhitDoc
   */
  public function getHeroVideo() {
    /** @var \Drupal\media\MediaStorage $media_storage */
    $media_storage = $this->getEntityTypeManager()->getStorage('media');
    //$media_storage->load(id'20') - 20 is ID video in Media
    $media_video = $media_storage->load('20');
    if ($media_video instanceof MediaInterface) {
      return [
        'video/mp4' => $media_video->get('field_media_video_file')->entity->get('uri')->value,
      ];
    }
  }

}
