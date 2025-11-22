window.addEventListener('DOMContentLoaded', () => {

  console.log('test');

  // ==========================================================================
  // Responsive Tables
  // ==========================================================================
  $('.table-responsive table').basictable({
    breakpoint: 768,
  });


  // ==========================================================================
  // Smooth Scrolling
  // ==========================================================================
  // Initialize Lenis
  let lenis = new Lenis({
    autoRaf: true,
  });


  // conflicts with smooth scrolling
  document.addEventListener('show.bs.modal', event => {
    lenis.destroy();
  })

  document.addEventListener('hide.bs.modal', event => {
    lenis = new Lenis({
      autoRaf: true,
    });
  })


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
  // Lotties
  // ==========================================================================
  document.querySelectorAll('.lottie').forEach(function (container) {
    const path = container.getAttribute('data-lottie-file');
    if (path) {
      lottie.loadAnimation({
        container: container,
        renderer: 'svg',
        loop: false,
        autoplay: false,
        path: path
      });

      const section = container.closest('.csp-section');
      if (section) {
        section.addEventListener('madj2k-element-in-viewport-active', () => {
          lottie.play();
        });
      }
    }
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

  //======================================================
  // globally used functions for sliders
  (function ($) {
    ($.videoUtilities = {
      replaceSplashWithVideo: function (splash) {
        let iframe = splash.closest(".video").find(".video-iframe").html();
        splash.replaceWith(iframe);
      },

      stopAllVideos: function (elementContainer) {
        let videos = elementContainer.find("iframe, video");
        videos.each(function () {
          if ($(this).prop("tagName") === "VIDEO") {
            $(this)[0].pause();
          } else {
            if ($(this).attr("src")) {
              let src = $(this).attr("src").replace("autoplay=1", "") + "&autoplay=0";
              $(this).attr("src", src);
            }
          }
        });
      },
    }), ($.sliderUtilities = {
      // because of bug: after touchend autostart was initialized
      fixTouchEventAutoScroll: function (event) {
        let owl = $(event.target);
        owl.on("dragged.owl.carousel", function (event) {
          let owl = $(event.target);
          owl.trigger("stop.owl.autoplay");
        });
      },

      getNumberOfItems: function (owl) {
        // since loop:true needs a defined item-property, we have to do some calculation here
        let owlItems = owl.find(".logo-list-item");
        let minWidth = $(window).width();

        // find smallest item
        owlItems.each(function () {
          let itemWidth = $(this).outerWidth();
          if (itemWidth < minWidth) {
            minWidth = itemWidth;
          }
        });
        return Math.ceil($(window).width() / minWidth);
      },

      getPageInfo: function (event) {
        if (event.item) {
          let index = event.item.index + 1;
          let count = event.item.count;
          return index + " / " + count;
        }
        return "";
      },

      setPaginationStatus: function (event) {
        let owl = $(event.target);
        owl
          .closest(".js-carousel-section")
          .find(".js-owl-nav-prev")
          .removeClass("disabled");

        owl
          .closest(".js-carousel-section")
          .find(".js-owl-nav-next")
          .removeClass("disabled");

        owl.closest(".js-carousel-section").find(".js-owl-nav").show();

        // copy state from original nav
        setInterval(function (owl) {
          let nav = owl.find(".owl-nav").first();
          let navPrev = nav.find(".owl-prev").first();
          let navNext = nav.find(".owl-next").first();

          if (navPrev.hasClass("disabled")) {
            owl
              .closest(".js-carousel-section")
              .find(".js-owl-nav-prev")
              .addClass("disabled");
          }

          if (navNext.hasClass("disabled")) {
            owl
              .closest(".js-carousel-section")
              .find(".js-owl-nav-next")
              .addClass("disabled");
          }

          if (navPrev.hasClass("disabled") && navNext.hasClass("disabled")) {
            owl.closest(".js-carousel-section").find(".js-owl-nav").hide();
          }
        }, 100, owl);
      },
    });
  })(jQuery);

  //======================================================
  // needed because if a mobile browser hides/shows the address-bar during scrolling this fires a resize event!
  // touchmove is fired on iPad when the scrolling starts, scroll is fired when scrolling ends
  let activeScrolling = false;
  $(window).on("scroll touchmove", function () {
    activeScrolling = true;
    waitForFinalEvent(function () {
      activeScrolling = false;
    }, 500, "scrolling");
  });

  let waitForFinalEvent = (function () {
    let timers = {};
    return function (callback, ms, uniqueId) {
      if (!uniqueId) {
        uniqueId = "Don't call this twice without a uniqueId";
      }
      if (timers[uniqueId]) {
        clearTimeout(timers[uniqueId]);
      }
      timers[uniqueId] = setTimeout(callback, ms);
    };
  })();

});
