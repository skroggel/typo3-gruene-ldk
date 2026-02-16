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
  document.addEventListener('madj2k-flyoutmenu-opened', () => {
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
  const slider1 = document.querySelector('.swiper.js-stage-slider');
  if (slider1) {
    const swiper1 = new Swiper('.swiper.js-stage-slider', {
      // Optional parameters
      direction: 'horizontal',
      loop: slider1.classList.contains('js-loop'),
      autoplay: slider1.classList.contains('js-autoplay'),
      autoHeight: false,
      speed: 800,

      // Default parameters
      slidesPerView: 1,
      spaceBetween: 0,

      // load text via data attributes
      a11y: {
        prevSlideMessage: slider1.dataset.prevSlideMessage,
        nextSlideMessage: slider1.dataset.nextSlideMessage,
      },

      // Navigation arrows
      navigation: {
        nextEl: '.cta-next',
        prevEl: '.cta-prev',
        disabledClass: 'disabled'
      },

      on: {
        navigationNext: function () {
          swiper1.autoplay.stop();
        },
        navigationPrev: function () {
          swiper1.autoplay.stop();
        },
      },
    });
  }

  const slider2 = document.querySelector('.swiper.js-contact-slider');
  if (slider2) {
    const swiper2 = new Swiper('.swiper.js-contact-slider', {
      // Optional parameters
      direction: 'horizontal',
      loop: slider2.classList.contains('js-loop'),
      autoplay: slider2.classList.contains('js-autoplay')
        ? {
          delay: 3000,
          pauseOnMouseEnter: true
        }
        : false,
      speed: 800,

      // Default parameters
      slidesPerView: 1,
      spaceBetween: 0,

      // Responsive breakpoints
      breakpoints: {
        768: {
          slidesPerView: 2,
        },
        1200: {
          slidesPerView: 3,
        }
      },

      // load text via data attributes
      a11y: {
        prevSlideMessage: slider2.dataset.prevSlideMessage,
        nextSlideMessage: slider2.dataset.nextSlideMessage,
      },

      // Navigation arrows
      navigation: {
        nextEl: '.cta-next',
        prevEl: '.cta-prev',
        disabledClass: 'disabled'
      },

      on: {
        navigationNext: function () {
          swiper2.autoplay.stop();
        },
        navigationPrev: function () {
          swiper2.autoplay.stop();
        },
      },
    });
  }
  const slider3 = document.querySelector('.swiper.js-news-topic-slider');
  if (slider3) {

    const swiper3 = new Swiper('.swiper.js-news-topic-slider', {
      loop: slider3.classList.contains('js-loop'),
      autoplay: slider3.classList.contains('js-autoplay')
        ? {
          delay: 3000,
          pauseOnMouseEnter: true
        }
        : false,
      speed: 300,

      effect: 'coverflow',
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: 2,
      coverflowEffect: {
        rotate: 0,
        stretch: 80,
        depth: 300,
        modifier: 1,
        slideShadows: false,
      },

      // Responsive breakpoints
      breakpoints: {
        0: {
          slidesPerView: 1,
        },
        768: {
          slidesPerView: 2,
        },
        1200: {
          slidesPerView: 3,
        }
      },

      // load text via data attributes
      a11y: {
        prevSlideMessage: slider3.dataset.prevSlideMessage,
        nextSlideMessage: slider3.dataset.nextSlideMessage,
      },

      // Navigation arrows
      navigation: {
        nextEl: '.cta',
        disabledClass: 'disabled'
      },

      on: {
        navigationNext: function () {
          swiper2.autoplay.stop();
        },
        navigationPrev: function () {
          swiper2.autoplay.stop();
        },
      },

    });
  }

  // ==========================================================================
  // Scrolling
  // ==========================================================================
  const scrolling = new Madj2kScrolling({
    anchorScrolling: {
      enabled: true,
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
      enabled: true,
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
