/*!
 * Scrolling-Events
 * Author: Steffen Kroggel <developer@steffenkroggel.de>
 * Last updated: 03.11.2024
 * v1.0.2
 */

/*
 * Example for initialization:
 *
 * $(() => {
 *  const images = new Images();
 * });
 */
class Images {

  config = {
    'animationDuration': 250,
    'swapTimeout': 500
  };

  /**
   * Constructor
   * @param config
   */
  constructor(config) {
    this.config = {...this.config, ...config }
    this.initImageSwap();
  }

  /**
   * ImageSwap
   */
  initImageSwap () {

    const self = this;
    let swapInTimeout;
    let swapOutTimeout;

    const hoverInEvent = function (element) {

      const $element = $(element);
      if ($element.data('image-id') && $element.data('image-src')) {

        const $image = $('#' + $element.data('image-id'));
        if ($image.length) {
          if ($image.attr('src') && !$image.data('src')) {
            $image.attr('data-src', $image.attr('src'))
          }
          if ($image.attr('alt') && !$image.data('alt')) {
            $image.attr('data-alt', $image.attr('alt'))
          }
          if ($image.attr('title') && !$image.data('title')) {
            $image.attr('data-title', $image.attr('title'))
          }
          $image.animate({opacity: 0}, self.config.animationDuration);
          setTimeout(function() {
            $image.attr('src', $element.data('image-src'));
            if ($element.data('image-alt')) {
              $image.attr('alt', $element.data('image-alt'));
            }
            if ($element.data('image-title')) {
              $image.attr('title', $element.data('image-title'));
            }
            $image.animate({opacity: 1}, self.config.animationDuration);
          }, self.config.animationDuration);
        }
      }
    }

    const hoverOutEvent = function (element) {
      const $element = $(element);
      if ($element.data('image-id') && $element.data('image-src')) {
        const $image = $('#' + $element.data('image-id'));
        if ($image.length) {
          if ($image.attr('src') !== $image.data('src') && $image.data('src')) {
            $image.animate({opacity: 0}, self.config.animationDuration);
            setTimeout(function() {
              $image.attr('src', $image.data('src'));
              if ($image.data('alt')) {
                $image.attr('alt', $image.data('alt'));
              }
              if ($image.data('title')) {
                $image.attr('title', $image.data('title'));
              }
              $image.animate({opacity: 1}, self.config.animationDuration);
            }, self.config.animationDuration);
          }
        }
      }
    }

    $('.js-image-swap').on('mouseenter', function(e){
      clearTimeout(swapOutTimeout);
      swapInTimeout = setTimeout(function() {
        hoverInEvent(e.target);
      }, self.config.swapTimeout);

    }).on('mouseleave', function(e){
      clearTimeout(swapInTimeout);
      swapOutTimeout = setTimeout(function() {
        hoverOutEvent(e.target)
      }, self.config.swapTimeout);
    });
  }
}
