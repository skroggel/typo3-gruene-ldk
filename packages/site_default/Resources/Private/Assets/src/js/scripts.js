window.addEventListener('DOMContentLoaded', () => {

  console.log('test');

  // ==========================================================================
  // Menu
  // ==========================================================================
  document.querySelectorAll('.js-flyout-toggle').forEach((el) => {
    new Madj2kFlyoutMenu(el, {
      animationDuration: 800,
      heightMode: 'full',
      scrollMode: 'inner'
    });
  });

  document.addEventListener('madj2k-flyoutmenu-opening', () => {
    document.querySelector('body').classList.add('block-scroll-classes');
  });
  document.addEventListener('madj2k-flyoutmenu-closed', () => {
    document.querySelector('body').classList.remove('block-scroll-classes');
  });

  // ==========================================================================

  document.querySelectorAll('.js-slide-nav-toggle').forEach((el) => {
    new Madj2kSlideMenu(el, { menuItemsJson: slideNavItems});
  });

  document.addEventListener('madj2k-slidemenu-opening', () => {
    document.querySelector('body').classList.add('block-scroll-classes');
  });
  document.addEventListener('madj2k-slidemenu-closed', () => {
    document.querySelector('body').classList.remove('block-scroll-classes');
  });

  // ==========================================================================
  // Slider
  // ==========================================================================
  $('.owl-carousel.js-stage-slider').each(function (index, element) {

    let owl = $(element);
    let config = {
      items: 1,
      margin: 0,
      dots: false,
      nav: true,
      autoWidth: false,
      center: false,
      autoplay: (!!owl.hasClass('js-autoplay')),
      loop: (!!owl.hasClass('js-loop')),
      autoHeight: true,
      onInitialized: function() {

        owl.find('.owl-item').attr('aria-selected', 'false');
        owl.find('.owl-item:not(.active)').find('a,button').attr('tabindex', '-1'); // deactivate links in inactive items
        owl.find('.owl-item.active, owl-dot.active').attr('aria-selected', 'true'); // let screen readers know an item is active

        // apply meta info to next and previous buttons and make them focusable
        // also stop autoslide on manual scrolling
        owl.find('.owl-prev').attr('role', 'button').attr('title', 'Previous');
        owl.find('.owl-next').attr('role', 'button').attr('title', 'Next');
        owl.find('.owl-prev, .owl-next, .owl-dot').attr('tabindex', '0')
          .on('click', function() {
            owl.trigger('stop.owl.autoplay');
          })

        // add instructions to keyboard users that are only visible when the carousel is focused
        // owl.find('.owl-wrapper-outer').append('');
      },
      onTranslated: function(event) {

        // deactivate links in inactive items and vice versa
        owl.find('.owl-item.active').find('a,button').attr('tabindex', '0');
        owl.find('.owl-item:not(.active)').find('a,button').attr('tabindex', '-1');

        // let screen readers know an item is active
        owl.find('.owl-item.active, .owl-dot.active').attr('aria-selected', 'true');
        owl.find('.owl-item:not(.active), .owl-dot:not(.active)').attr('aria-selected', 'false');
      },
    };

    owl.owlCarousel(config);

    // re-init on resize
    $(document).on('madj2k-resize-end', function (e) {
      owl.trigger('destroy.owl.carousel');
      owl.owlCarousel(config);
    });
  });






  // ==========================================================================
  // Menu
  // ==========================================================================
  const scrolling = new Madj2kScrolling({
    anchorScrolling: {
      selector: ['a[href^="#"]', 'a[href*="#"]'],
      offsetSelector: null,
      disableSelector: '.js-no-scroll',
      collapsibleSelector: ['.collapse'],
      behavior: 'smooth',
      scriptScrollTimeout: 800,
      timeout: 500,
      threshold: 40
    },
    appearOnScroll: {
      selector: ['.js-appear-on-scroll'],
      timeout: 500,
      threshold: 25
    },
    debug: false
  });

  // ==========================================================================
  // Responsive Tables
  // ==========================================================================
 // $('.table-responsive table').basictable({
 //   breakpoint: 768,
 // });


  // ==========================================================================
  // Select2
  // ==========================================================================
  const initSelect2 = () => {
    $('.js-select').each(function () {
      $(this).select2({});

      $(this).on('select2:open', function () {
        lenis.destroy();
      });

      $(this).on('select2:close', function () {
        lenis = new Lenis({
          autoRaf: true,
        });
      });
    });
  };

  initSelect2();

  document.addEventListener('madj2k-better-resize-event', () => {
    $('.js-select').select2('destroy');
    initSelect2();
  });




});
