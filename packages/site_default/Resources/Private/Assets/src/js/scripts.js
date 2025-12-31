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


  // ==========================================================================
  // Slider
  // ==========================================================================
  $('.owl-carousel.js-stage-slider').each(function (index, element) {

    let owl = $(element);
    let config = {
      items: 1,
      margin: 20,
      dots: true,
      nav: true,
      autoWidth: false,
      center: false,
      autoplay: (!!owl.hasClass('js-autoplay')),
      loop: (!!owl.hasClass('js-loop')),
      autoHeight: true,
    };

    owl.owlCarousel(config);

    // re-init on resize
    $(document).on('madj2k-resize-end', function (e) {
      owl.trigger('destroy.owl.carousel');
      owl.owlCarousel(config);
    });
  });

});
