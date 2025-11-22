/*!
 * jQuery Plugin – madj2kFlyoutMenu
 * Author: Steffen Kroggel <developer@steffenkroggel.de>
 *
 * Last updated: 08.11.2024
 * v2.0.6
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

(function ($, window, document, undefined) {
  // Create the defaults once
  // Additional options, extending the defaults, can be passed as an object from the initializing call
  let pluginName = "madj2kFlyoutMenu",
    defaults = {

      // status classes
      openStatusClass: 'open',
      animationOpenStatusClass: 'opening',
      animationCloseStatusClass: 'closing',
      animationBodyClassPrefix: 'flyout',
      openStatusBodyClass: 'flyout-open',
      openStatusBodyClassOverflow: 'flyout-open-overflow',

      // content-section
      contentSectionClass: 'js-main-content',

      // menu classes
      menuClass: 'js-flyout',
      menuToggleClass: "js-flyout-toggle", // toggle which opens / closes menu
      menuCloseClass: "js-flyout-close",  // class of close-button if a flyout-tab has it's own closing button
      menuContainerClass: "js-flyout-container",  // container around the flyout-tab
      menuInnerClass: "js-flyout-inner", // inner wrap around content. Needed in case of padding and in case of resizing content (e.g. accordions)

      // params
      fullHeight: true, // whether the flyout scales up to 100% of the inner height
      paddingBehavior: 0, // 0 = inactive, 1 = only on init, 2 = each time the menu opens
      paddingViewPortMinWidth: 0, // minimum viewPortWith to start with padding
      animationDuration: 500,
    };

  let plugin;

  // The plugin constructor
  function Plugin(element, options) {
    this.element = element;

    // Merge defaults with passed options
    this.settings = $.extend({}, defaults, options);

    this._defaults = defaults;
    this._name = pluginName;

    this.init();
  }

  $.extend(Plugin.prototype, {
    init: function () {
      // Use "plugin" to reference the current instance of the object
      // Can be used inside of functions to prevent conflict with "this"
      plugin = this;

      this.settings.$element = $(this.element);

      let controls = this.settings.$element.attr('aria-controls');
      this.settings.$menu = $('#' + controls);

      let positionReference = this.settings.$menu.attr('data-position-ref');
      this.settings.$positionReference = $('#' + positionReference);

      let paddingReference = this.settings.$menu.attr('data-padding-ref');
      this.settings.$paddingReference = $('#' + paddingReference);

      this.settings.$closeBtn = this.settings.$menu.find('.' + this.settings.menuCloseClass).first();
      this.settings.$menuContainer = this.settings.$menu.find('.' + this.settings.menuContainerClass).first();
      this.settings.$menuInner = this.settings.$menu.find('.' + this.settings.menuInnerClass).first();

      this.initNoScrollHelper();
      this.resizeAndPositionMenu();
      this.paddingMenu()
      this.bindEvents();
    },


    /**
     * Binds all relevant events
     *
     * @return void
     */
    bindEvents: function () {

      if (this.settings.$closeBtn.length) {
        this.settings.$closeBtn.on('click', this.closeEvent.bind(this))
          .on('keydown', this.keyboardEvent.bind(this));
      }

      this.settings.$element.on('click', this.toggleEvent.bind(this))
        .on('keydown', this.keyboardEvent.bind(this))
        .on('madj2k-flyoutmenu-close', this.closeEvent.bind(this));

      this.settings.$menu.find('a,button,input,textarea,select')
        .on('keydown', this.keyboardEvent.bind(this));

     // $(window).on('resize', this.closeEvent.bind(this));
      $(document).on('madj2k-flyoutmenu-close', this.closeEvent.bind(this));
      $(document).on('madj2k-flyoutmenu-resize', this.resizeAndPositionMenuEvent.bind(this));
    },


    /**
     * Keyboard interactions with link-elements and buttons
     *
     * @param e
     * @return void
     */
    keyboardEvent: function (e) {

      // is last link in current card?
      let element = $(e.target);

      switch (e.key) {
        case 'Tab':

          break;

        case 'ArrowUp':
          if (element.is(this.settings.$element)) {
            this.close();
          }
          break;

        case 'Enter':
          if (element.is(this.settings.$element)) {
            e.preventDefault();
            this.toggle();
          }
          break;

        case 'ArrowDown':
          if (element.is(this.settings.$element)) {
            e.preventDefault();
            this.open();
          }
          break;

        case 'Escape':
          e.preventDefault();
          this.close();
          this.focusToggle()
          break;
      }
    },


    /**
     * Toggle flyout
     *
     * @param e Event
     * @return void
     */
    toggleEvent: function (e) {
      e.preventDefault();
      this.toggle();
    },


    /**
     * Toggle flyout
     *
     * @return void
     */
    toggle: function () {

      if (this.settings.$element.hasClass(this.settings.openStatusClass)) {

        this.close();

      } else {

        // close all other flyouts via trigger
        let self = this;
        $('.' + this.settings.menuToggleClass).each(function () {
          if (!$(this).is(self.settings.$element)) {
            $(document).trigger('madj2k-flyoutmenu-close');
          }
        });

        this.open();
      }
    },


    /**
     * Opens flyout
     *
     * @return boolean
     */
    open: function () {
      if (
        (!this.settings.$menu.hasClass(this.settings.openStatusClass))
        && (!this.settings.$menu.hasClass(this.settings.animationOpenStatusClass))
      ){

        $(document).trigger('madj2k-slidemenu-close');
        $(document).trigger('madj2k-pulldownmenu-close');
        $(document).trigger('madj2k-flyoutmenu-opening');
        this.toggleNoScroll();
        this.resizeAndPositionMenu();
        this.paddingMenu();

        // add status classes early in for immediate visual feedback
        this.settings.$menu.addClass(this.settings.openStatusClass);
        this.settings.$menu.addClass(this.settings.animationOpenStatusClass);
        this.settings.$element.addClass(this.settings.openStatusClass);
        this.settings.$element.addClass(this.settings.animationOpenStatusClass);
        this.settings.$element.attr('aria-expanded', true);
        $('body').addClass(this.settings.animationBodyClassPrefix + '-' + this.settings.animationOpenStatusClass);

        let self = this;
        this.settings.$menuContainer.animate({'top': 0},
          this.settings.animationDuration,
          function() {

            // animation complete
            self.settings.$menu.removeClass(self.settings.animationOpenStatusClass);
            self.settings.$element.removeClass(self.settings.animationOpenStatusClass);
            $('body').removeClass(self.settings.animationBodyClassPrefix + '-' + self.settings.animationOpenStatusClass);

            $(document).trigger('madj2k-flyoutmenu-opened');
          });

        return true;
      }

      return false;
    },



    /**
     * Closes flyout
     *
     * @param e Event
     * @return void
     */
    closeEvent: function (e) {
      e.preventDefault();

      // android fires a resize-event if keyboard is activated
      if($(document.activeElement).is('input') === false) {
        this.close();

        // the close button jumps to the toggle
        if (this.settings.$closeBtn.length) {
          if ($(e.target).is(this.settings.$closeBtn)) {
            this.focusToggle();
          }
        }
      }
    },


    /**
     * Closes flyout
     *
     * @return boolean
     */
    close: function () {
      if (
        (this.settings.$menu.hasClass(this.settings.openStatusClass))
        && (! this.settings.$menu.hasClass(this.settings.animationCloseStatusClass))
      ){
        $(document).trigger('madj2k-flyoutmenu-closing');

        this.settings.$menu.addClass(this.settings.animationCloseStatusClass);
        this.settings.$element.addClass(this.settings.animationCloseStatusClass);
        $('body').addClass(this.settings.animationBodyClassPrefix + '-' + this.settings.animationCloseStatusClass);

        // removing classes early in order to have immediate visual feedback
        this.settings.$element.removeClass(this.settings.openStatusClass);
        this.settings.$element.attr('aria-expanded', false);

        this.toggleNoScroll();

        let self = this;
        this.settings.$menuContainer.animate({'top': '-100%'},
          this.settings.animationDuration,
          function (){
            // animation complete
            // remove status classes and set wai-aria for toggle
            self.settings.$menu.removeClass(self.settings.openStatusClass);
            self.settings.$menu.removeClass(self.settings.animationCloseStatusClass);
            self.settings.$element.removeClass(self.settings.animationCloseStatusClass);
            $('body').removeClass(self.settings.animationBodyClassPrefix+ '-' + self.settings.animationCloseStatusClass);

            $(document).trigger('madj2k-flyoutmenu-closed');
          });

        return true;
      }

      return false;
    },


    /**
     * Adds padding to the inner flyout
     *
     * @return void
     */
    paddingMenuEvent: function (e) {
      // android fires a resize-event if keyboard is activated
      if($(document.activeElement).is('input') === false) {
        this.paddingMenu();
      }
    },


    /**
     * Adds padding to the inner flyout
     *
     * @return boolean
     */
    paddingMenu: function () {

      if (this.settings.$paddingReference.length) {

        // is deactivated?
        if (this.settings.paddingBehavior === 0) {
          return false;
        }

        // on init only?
        if (
          (this.settings.paddingBehavior === 1)
          && (this.settings.$menuInner.attr('data-padding-set'))
        ) {
          return false;
        }

        // check for viewport-width
        let elementPosition = this.settings.$paddingReference.position();
        let elementPositionLeft = elementPosition.left;
        if (window.innerWidth < this.settings.paddingViewPortMinWidth) {
          elementPositionLeft = 0;
        }

        this.settings.$menuInner.css({'padding-left': elementPositionLeft});
        this.settings.$menuInner.attr('data-padding-set', true);

        return true;
      }
      return false;
    },


    /**
     * Position flyout
     *
     * @return void
     */
    resizeAndPositionMenuEvent: function (e) {
      // android fires a resize-event if keyboard is activated
      if($(document.activeElement).is('input') === false) {
        this.resizeAndPositionMenu();
      }
    },


    /**
     * Resizes and positions flyout
     *
     * @return void
     */
    resizeAndPositionMenu: function () {
      let referenceObject =  this.settings.$element;
      if (this.settings.$positionReference.length) {
        referenceObject = this.settings.$positionReference;
      }

      let referencePosition = referenceObject.position();
      let referenceHeight = referenceObject.outerHeight();
      let flyoutHeight = this.settings.$menuInner.outerHeight() || this.settings.$menu.outerHeight();  // in case the height of the opened menu is changed because of another effect (e.g. accordion)

      let flyoutTop = referencePosition.top + referenceHeight;
      let windowHeight = window.innerHeight;

      if (this.settings.fullHeight) {
        flyoutHeight = windowHeight - referenceHeight - referencePosition.top
      }

      this.settings.$menu.css({
        'top': flyoutTop,
        'height': flyoutHeight
      });
    },


    /**
     * Stop scrolling of body while flyout is open
     *
     * @return void
     * @see https://css-tricks.com/prevent-page-scrolling-when-a-modal-is-open/
     * @see https://kilianvalkhof.com/2022/css-html/preventing-smooth-scrolling-with-javascript/
     */
    toggleNoScroll: function () {

      // If there is a scrollbar, disable scrolling
      let noScrollClass = this.settings.openStatusBodyClass;
      if ($(document).height() > $(window).height()) {
        noScrollClass += ' ' + this.settings.openStatusBodyClassOverflow;
      }

      let body = $('body');
      let helper = body.find('.no-scroll-helper').first();
      let helperInner = body.find('.no-scroll-helper-inner').first();

      if (
        (!body.hasClass(this.settings.openStatusBodyClass))
        && (!body.hasClass(this.settings.openStatusBodyClassOverflow))
      ){

        let scrollTop = $(document).scrollTop() * -1;
        helper.attr('data-scroll-top', scrollTop);
        helper.css({'position': 'relative', 'overflow': 'hidden', 'height': '100vh', 'width': '100%'});
        helperInner.css({'position': 'absolute', 'top': scrollTop, 'height': '100%', 'width': '100%'});
        body.addClass(noScrollClass);
        window.scrollTo({top: 0, behavior: 'instant'})

      } else {

        let scrollTop = helper.attr('data-scroll-top');
        helper.removeAttr('style');
        helperInner.removeAttr('style');
        body.removeClass(this.settings.openStatusBodyClass)
          .removeClass(this.settings.openStatusBodyClassOverflow);
        window.scrollTo({top: parseInt(scrollTop || '0') * -1, behavior: 'instant'})
      }
    },


    /**
     * Init no-scroll helper
     *
     * @return void
     */
    initNoScrollHelper: function () {

      let body = $('body');
      let helper = body.find('.no-scroll-helper').first();
      let contentSection = undefined;

      // create helper div - this is needed for Firefox
      if (!helper.length) {
        if (
          (this.settings.contentSectionClass)
          && (contentSection = $('.' + this.settings.contentSectionClass))
          && (contentSection.length)
        ) {
          contentSection.wrapInner('<div class="no-scroll-helper"><div class="no-scroll-helper-inner"></div></div>');
        } else {
          body.wrapInner('<div class="no-scroll-helper"><div class="no-scroll-helper-inner"></div><</div>');
        }
      }
    },


    /**
     * Sets the focus to the toggleObject
     *
     * @param timeout timeout until focus is set
     * @return void
     */
    focusToggle: function (timeout = 0) {
      setTimeout(function (object) {
          object.focus();
        },
        timeout,
        this.settings.$element
      );

    },

  });

  // A really lightweight plugin wrapper around the constructor,
  // preventing against multiple instantiations
  $.fn[pluginName] = function (options) {
    return this.each(function () {
      if (!$.data(this, "plugin_" + pluginName)) {
        $.data(this, "plugin_" + pluginName, new Plugin(this, options));
      }
    });
  };
})(jQuery, window, document);
