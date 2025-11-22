/*!
 * jQuery Plugin – madj2kPulldownMenu
 * Author: Steffen Kroggel <developer@steffenkroggel.de>
 *
 * Last updated: 05.04.2024
 * v2.0.0
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

(function ($, window, document, undefined) {
  // Create the defaults once
  // Additional options, extending the defaults, can be passed as an object from the initializing call
  let pluginName = "madj2kPulldownMenu",
    defaults = {

      // status classes
      openStatusClass: 'open',

      // menu classes
      menuClass: 'js-pulldown',
      menuToggleClass: "js-pulldown-toggle", // toggle which opens / closes menu
      menuWrapClass: "js-pulldown-wrap",  // optional wrap around whole menu including the toggle

      // params
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
      this.settings.$menuWrap = this.settings.$element.parents('.' + this.settings.menuWrapClass).first();

      this.bindEvents();
    },


    /**
     * Binds all relevant events
     *
     * @return void
     */
    bindEvents: function () {

      this.settings.$element.on('click', this.toggleEvent.bind(this))
        .on('keydown', this.keyboardEvent.bind(this))
        .on('madj2k-pulldownmenu-close', this.closeEvent.bind(this));

      this.settings.$menu.find('a,button,input,textarea,select')
        .on('keydown', this.keyboardEvent.bind(this));

      $(window).on('resize', this.closeEvent.bind(this));
      $(document).on('click', this.closeViaDocumentClickEvent.bind(this))
        .on('madj2k-pulldownmenu-close', this.closeEvent.bind(this));
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
     * Toggle pulldown
     *
     * @param e Event
     * @return void
     */
    toggleEvent: function (e) {
      e.preventDefault();
      e.stopPropagation();

      this.toggle();
    },


    /**
     * Toggle pulldown
     *
     * @return void
     */
    toggle: function () {

      if (this.settings.$element.hasClass(this.settings.openStatusClass)) {

        this.close();

      } else {

        // close all other pulldowns via trigger
        let self = this;
        $('.' + this.settings.menuToggleClass).each(function () {
          if (!$(this).is(self.settings.$element)) {
            $(document).trigger('madj2k-pulldownmenu-close');
          }
        });

        this.open();
      }
    },


    /**
     * Opens pulldown
     *
     * @return boolean
     */
    open: function () {
      if (!this.settings.$menu.hasClass(this.settings.openStatusClass)) {

        $(document).trigger('madj2k-slidemenu-close');
        $(document).trigger('madj2k-flyoutmenu-close');

        // add status classes
        this.settings.$menu.addClass(this.settings.openStatusClass);
        this.settings.$menuWrap.addClass(this.settings.openStatusClass);
        this.settings.$element.addClass(this.settings.openStatusClass);
        this.settings.$element.attr('aria-expanded', true);

        $(document).trigger('madj2k-pulldownmenu-opened');
        return true;
      }

      return false;
    },

    /**
     * Closes pulldown via document
     *
     * @param e Event
     * @return void
     */
    closeViaDocumentClickEvent: function (e) {
      e.stopPropagation();

      // do not close menu, if clicked in the opened pulldown
      if ($(e.target).closest(this.settings.$menu).length === 0) {

        // android fires a resize-event if keyboard is activated
        if($(document.activeElement).is('input') === false) {
          this.close();
        }
      }
    },


    /**
     * Closes pulldown
     *
     * @param e Event
     * @return void
     */
    closeEvent: function (e) {

      e.preventDefault();

      // android fires a resize-event if keyboard is activated
      if($(document.activeElement).is('input') === false) {
        this.close();
      }
    },


    /**
     * Closes pulldown
     *
     * @return boolean
     */
    close: function () {
      if (this.settings.$menu.hasClass(this.settings.openStatusClass)){

        // removing classes
        this.settings.$menu.removeClass(this.settings.openStatusClass);
        this.settings.$menuWrap.removeClass(this.settings.openStatusClass);
        this.settings.$element.removeClass(this.settings.openStatusClass);
        this.settings.$element.attr('aria-expanded', false);

        $(document).trigger('madj2k-pulldownmenu-closed');
        return true;
      }

      return false;
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
