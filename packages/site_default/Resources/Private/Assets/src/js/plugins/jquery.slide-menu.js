/*!
 * jQuery Plugin – madj2kSlideMenu
 * Author: Steffen Kroggel <developer@steffenkroggel.de>
 *
 * Last updated: 03.11.2024
 * v1.1.5
 */
(function ($, window, document, undefined) {
  // Create the defaults once
  // Additional options, extending the defaults, can be passed as an object from the initializing call
  let pluginName = "madj2kSlideMenu",
    defaults = {
      menuItemsJson: [],

      // status classes
      openStatusClass: 'open',
      openStatusBodyClass: 'slide-open',
      openStatusBodyClassOverflow: 'slide-open-overflow',

      openCardStatusClass: 'show',
      activeStatusClass: 'active',
      currentStatusClass: 'current',
      hasChildrenStatusClass: 'has-children',
      linkTypeClass: 'link-type',
      isLinkedClass: 'linked',

      animationOpenStatusClass: 'opening',
      animationCloseStatusClass: 'closing',

      // toggle classes
      menuToggleClass: 'js-slide-nav-toggle',
      lastCardToggleClass: 'js-slide-nav-back',
      nextCardToggleClass: 'js-slide-nav-next',

      // card class
      menuWrapClass: "js-slide-nav-container",
      menuCardClass: 'js-slide-nav-card',

      // content section
      contentSectionClass: 'js-main-content',

      // special classes
      templatePartsClass: 'js-slide-nav-tmpl',

      // params
      animationDuration: 500,
      loadOnOpen: true, // if set to true, the menu is generated the first time, when it is opened. Otherwise it will be generated as soon as the DOM is loaded.
      startOnHome: false, // if set to true, everytime the menu is opened, the first card is loaded (and not the current)
    };

  let plugin;

  // The plugin constructor
  function Plugin(element, options) {
    this.element = element;

    // Merge defaults with passed options
    this.settings = $.extend({}, defaults, options);
    this.settings.isLoaded = false;

    this._defaults = defaults;
    this._name = pluginName;

    this.init();
  }


  $.extend(Plugin.prototype, {

    /**
     * Initialize menu
     *
     * @return void
     */
    init: function () {
      // Use "plugin" to reference the current instance of the object
      // Can be used inside of functions to prevent conflict with "this"
      plugin = this;

      this.settings.keyMap = [];
      this.settings.$element = $(this.element);
      this.settings.$element.on('click', this.toggleEvent.bind(this))
        .on('madj2k-slidemenu-close', this.closeEvent.bind(this));

      let controls = this.settings.$element.attr('aria-controls');
      this.settings.$menu = $('#' + controls);

      let positionReference = this.settings.$menu.attr('data-position-ref');
      this.settings.$positionReference = $('#' + positionReference);

      this.settings.$cards = [];
      this.settings.$activeCards = [];
      this.settings.$openCard = [];

      this.initNoScrollHelper();
      if (!this.settings.loadOnOpen) {
        this.loadMenu();
      }
    },


    /**
     * Loads the menu completely
     *
     * @return bool
     */
    loadMenu: function () {
      if (this.settings.isLoaded === false) {
        if (this.settings.$menu.length) {
          if (this.settings.menuItemsJson.length) {

            // Get HTML-templates
            this.settings.menuWrapTemplate = $('.' + this.settings.templatePartsClass + '[data-type="menuWrap"]').first().html();
            this.settings.menuItemTemplate = $('.' + this.settings.templatePartsClass + '[data-type="menuItem"]').first().html();
            this.settings.subMenuWrapTemplate = $('.' + this.settings.templatePartsClass + '[data-type="subMenuWrap"]').first().html();

            if (
              (this.settings.menuWrapTemplate)
              && (this.settings.menuItemTemplate)
              && (this.settings.subMenuWrapTemplate)
            ) {
              // Output generated HTML
              this.settings.$menu.html(this.buildHtml(this.settings.menuItemsJson));
            }
          }

          // get wrap and cards and set at least the first card to active
          this.settings.$menuWrap = this.settings.$menu.find('.' + this.settings.menuWrapClass).first();
          this.settings.$cards = this.settings.$menu.find('.' + this.settings.menuCardClass);
          this.settings.$cards.first().addClass(this.settings.activeStatusClass);
          this.settings.$activeCards = this.settings.$menu.find('.' + this.settings.menuCardClass + '.' + this.settings.activeStatusClass);

          // set status of menu and bind events
          this.settings.isLoaded = true;
          this.bindEvents();

        } else {
          console.log('Menu container not found. Can not load menu.');
        }
      }

      return this.settings.isLoaded;
    },


    /**
     * Binds all relevant events
     *
     * @return boolean
     */
    bindEvents: function () {

      if (this.settings.isLoaded) {
        this.settings.$element.on("keydown", this.keyboardEvent.bind(this));

        this.settings.$menu.find('.' + this.settings.lastCardToggleClass)
          .on("click", this.previousCardEvent.bind(this));

        this.settings.$menu.find('.' + this.settings.nextCardToggleClass)
          .on("click", this.nextCardEvent.bind(this))
          .on("keydown", this.keyboardEvent.bind(this));

        this.settings.$menu.find('a,button,input,textarea,select')
          .on("keydown", this.keyboardEvent.bind(this));

        $(window).on('resize', this.resizeCardsEvent.bind(this))
          .on('resize', this.positionMenuEvent.bind(this));

        $(document).on('madj2k-slidemenu-close', this.closeEvent.bind(this));

        return true;
      }

      return false;
    },


    /**
     * Keyboard interactions with link-elements and buttons
     *
     * @param e
     * @return void
     */
    keyboardEvent: function (e) {

      // is last item in current card?
      let element = $(e.target);
      let isLastItem = element.is(this.settings.$openCard
        .find('a:not([tabindex]),button:not([tabindex]),input:not([tabindex]),textarea:not([tabindex]),select:not([tabindex])')
        .last()
      );
      let isFirstItem = element.is(this.settings.$openCard
        .find('a:not([tabindex]),button:not([tabindex]),input:not([tabindex]),textarea:not([tabindex]),select:not([tabindex])')
        .first()
      );

      switch (e.key) {
        case 'Tab':

          // first element with shift and last element without shift jump focus to toggle
          if (
            (isLastItem && !e.shiftKey)
            || (isFirstItem && e.shiftKey)
          ) {
            e.preventDefault();
            this.focusToggle()
          }

          // if menu is open, jump focus to first element
          if (
            (element.is(this.settings.$element))
            && (this.settings.$menu.hasClass(this.settings.openStatusClass))
          ) {
            e.preventDefault();
            this.focusFirstItemOfOpenCard();
          }

          break;

        case 'ArrowUp':
          if (element.is(this.settings.$element)) {
            this.close();
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
          this.focusToggle();
          break;
      }
    },


    /**
     * Hides current menu-level and shows the level below
     *
     * @param e Event
     * @return void
     */
    previousCardEvent: function (e) {
      e.preventDefault();

      let controlledCard = $(e.target).attr('aria-controls');
      let parentCard = $(e.target).data('parent-card');
      let controlledCardElement = $('#' + controlledCard);
      let parentCardElement = $('#' + parentCard);

      // set last card to active
      if (
        (controlledCardElement.length)
        && (parentCardElement.length)
      ) {

        // do this at first in order to prevent tab-jumps
        // if user presses tab during animation
        this.disableTabIndexOnAllCards();

        controlledCardElement.addClass(this.settings.animationCloseStatusClass);

        let self = this;
        controlledCardElement.animate({'left': '100%'},
          this.settings.animationDuration,
          function () {
            self.changeOpenCard(parentCardElement);
            controlledCardElement.removeClass(self.settings.animationCloseStatusClass);

            $(document).trigger('madj2k-slidemenu-previous-opened');
          });

      }
    },


    /**
     * Shows the next menu-level
     *
     * @param e Event
     * @return void
     */
    nextCardEvent: function (e) {
      e.preventDefault();

      let controlledCard = $(e.target).attr('aria-controls');
      let controlledCardElement = $('#' + controlledCard);

      // set next card to active and set wai-aria accordingly
      if (controlledCardElement.length) {

        // do this at first in order to prevent tab-jumps
        // if user presses tab during animation
        this.disableTabIndexOnAllCards();

        controlledCardElement.addClass(this.settings.animationOpenStatusClass);

        let self = this;
        controlledCardElement.animate({'left': '0'},
          this.settings.animationDuration,
          function () {
            self.changeOpenCard(controlledCardElement);
            controlledCardElement.removeClass(self.settings.animationOpenStatusClass);

            $(document).trigger('madj2k-slidemenu-next-opened');
          });
      }
    },


    /**
     * Sets the active card
     *
     * @param card
     * @return void
     */
    setOpenCard: function (card) {

      this.settings.$openCard = card;
      this.settings.$cards.removeClass(this.settings.openCardStatusClass);
      this.settings.$openCard.addClass(this.settings.openCardStatusClass);
    },


    /**
     * Changes the active card
     *
     * @param card
     * @return void
     */
    changeOpenCard: function (card) {

      this.setOpenCard(card);
      this.toggleTabIndexOnOpenCard();
      this.toggleWaiAriaForOpenCard();
      this.focusFirstItemOfOpenCard();
    },


    /**
     * Toggle menu event
     *
     * @param e Event
     * @return void
     */
    toggleEvent: function (e) {
      e.preventDefault();

      if (!this.open()) {
        this.close();
      }
    },


    /**
     * Open menu
     *
     * @return boolean
     */
    open: function () {

      if (
        (this.loadMenu())
        && (!this.settings.$menu.hasClass(this.settings.openStatusClass))
        && (!this.settings.$menu.hasClass(this.settings.animationOpenStatusClass))
      ) {
        $(document).trigger('madj2k-flyoutmenu-close');
        $(document).trigger('madj2k-slidemenu-opening');

        this.toggleNoScroll();
        this.disableTabIndexOnAllCards();
        this.positionMenu();

        // toggle this early for immediate visual feedback
        this.settings.$menu.addClass(this.settings.openStatusClass);
        this.settings.$menu.addClass(this.settings.animationOpenStatusClass);
        this.settings.$element.addClass(this.settings.openStatusClass);
        this.settings.$element.addClass(this.settings.animationOpenStatusClass);
        this.settings.$element.attr('aria-expanded', true);

        // set active card
        let openCard = this.settings.$openCard = this.settings.$activeCards.last();
        if (this.settings.startOnHome) {
          openCard = this.settings.$activeCards.first();
        }
        this.setOpenCard(openCard);

        // now resize and position card and toggle no-scroll
        this.resizeCards();
        this.repositionCards();

        // animate menu
        let self = this;
        this.settings.$menuWrap.animate({'top': '0'},
          this.settings.animationDuration,
          function () {

            self.settings.$menu.removeClass(self.settings.animationOpenStatusClass);
            self.settings.$element.removeClass(self.settings.animationOpenStatusClass);

            // now toggle statuses and focus after animation is complete
            self.toggleTabIndexOnOpenCard();
            self.toggleWaiAriaForOpenCard();
            self.focusFirstItemOfOpenCard();

            $(document).trigger('madj2k-slidemenu-opened');
          });

        return true;
      }

      return false;
    },


    /**
     * Close menu event
     *
     * @param e Event
     * @return void
     */
    closeEvent: function (e) {
      e.preventDefault();
      this.close();
    },


    /**
     * Close menu
     *
     * @return boolean
     */
    close: function () {

      if (
        (this.loadMenu())
        && (this.settings.$menu.hasClass(this.settings.openStatusClass))
        && (!this.settings.$menu.hasClass(this.settings.animationCloseStatusClass))
      ) {
        $(document).trigger('madj2k-slidemenu-closing');

        // toggle this early for immediate visual feedback
        this.settings.$menu.addClass(this.settings.animationCloseStatusClass);
        this.settings.$element.addClass(this.settings.animationCloseStatusClass);
        this.settings.$element.removeClass(this.settings.openStatusClass);
        this.settings.$element.attr('aria-expanded', false);

        this.toggleTabIndexOnOpenCard();
        this.toggleWaiAriaForOpenCard();

        let self = this;
        this.settings.$menuWrap.animate({'top': '-100%'},
          this.settings.animationDuration,
          function () {

            // animation complete
            self.settings.$menu.removeClass(self.settings.openStatusClass);
            self.settings.$menu.removeClass(self.settings.animationCloseStatusClass);
            self.settings.$element.removeClass(self.settings.animationCloseStatusClass);

            self.toggleNoScroll();

            $(document).trigger('madj2k-slidemenu-closed');

          });

        return true;
      }

      return false;
    },


    /**
     * Sets the focus to the first link in the list the given link
     *
     * @param timeout timeout until focus is set
     * @return boolean
     */
    focusFirstItemOfOpenCard: function (timeout = 0) {

      let firstItem = this.settings.$openCard
        .find('a:not([tabindex]),button:not([tabindex]),input:not([tabindex]),textarea:not([tabindex]),select:not([tabindex])')
        .first();

      if (firstItem.length) {
        setTimeout(function (firstItem) {
            firstItem.focus();
          },
          timeout,
          firstItem,
        );
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
      setTimeout(function (toggleObject) {
          toggleObject.focus();
        },
        timeout,
        this.settings.$element
      );
    },


    /**
     * Toggles the accessibility attributes for the active card
     *
     * @return void
     **/
    toggleWaiAriaForOpenCard: function () {

      // set all wai-aria to false
      this.settings.$menu.find('.' + this.settings.nextCardToggleClass).attr('aria-expanded', false);
      if (this.settings.$element.hasClass(this.settings.openStatusClass)) {

        // set the one nextCardToggle that controls the given card to true
        let cardId = this.settings.$openCard.attr('id');
        this.settings.$menu
          .find('.' + this.settings.nextCardToggleClass + '[aria-controls="' + cardId + '"]')
          .first()
          .attr('aria-expanded', true);
      }
    },


    /**
     * Toggle tab-index of open card
     *
     * @return void
     */
    toggleTabIndexOnOpenCard: function () {

      this.disableTabIndexOnAllCards();

      if (this.settings.$element.hasClass(this.settings.openStatusClass)) {

        let windowWidth = window.innerWidth;
        let interactionElementsOfCard = this.settings.$openCard.find('a,button,input,textarea,select');
        interactionElementsOfCard.each(function (index, element) {

          let elementPosition = $(element).offset();
          if (elementPosition.left > 0 && elementPosition.left <= windowWidth) {
            $(element).removeAttr('tabindex');
          }
        });
      }
    },


    /**
     * disable tab-index for all cards
     *
     * @return void
     */
    disableTabIndexOnAllCards: function () {

      // set all interactive element as non-focusable
      let interactionElements = this.settings.$menu.find('a,button,input,textarea,select');
      interactionElements.attr('tabindex', '-1');
    },


    /**
     * Resizes flyout
     *
     * @param e
     * @return void
     */
    resizeCardsEvent: function (e) {
      this.resizeCards();
    },


    /**
     * Positions menu
     *
     * @return void
     */
    repositionCards: function () {

      this.settings.$cards
        .css({
          'left': '100%',
        });

      // all cards from the open card and above are to be shown
      this.settings.$openCard
        .css({'left': 0});

      this.settings.$openCard
        .parents('.' + this.settings.menuCardClass + '.' + this.settings.activeStatusClass)
        .css({'left': 0});
    },


    /**
     * Resizes menu
     *
     * @return void
     */
    resizeCards: function () {

      let referenceObject = this.settings.$element;
      if (this.settings.$positionReference.length) {
        referenceObject = this.settings.$positionReference;
      }

      let elementPosition = referenceObject.position();
      let elementHeight = referenceObject.outerHeight();
      let windowHeight = window.innerHeight;

      this.settings.$cards
        .css({
          'height': windowHeight - elementHeight - elementPosition.top
        });
    },


    /**
     * Positions menu
     *
     * @param e
     * @return void
     */
    positionMenuEvent: function (e) {
      this.positionMenu();
    },


    /**
     * Positions menu
     *
     * @return void
     */
    positionMenu: function () {

      let referenceObject = this.settings.$element;
      if (this.settings.$positionReference.length) {
        referenceObject = this.settings.$positionReference;
      }

      let elementPosition = referenceObject.position();
      let elementHeight = referenceObject.outerHeight();
      let top = elementPosition.top + elementHeight;

      this.settings.$menu.css({
        'top': top,
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
     * Recursively constructs the html-code for the menu
     * @param items
     * @param parentItem
     * @param level
     * @return string
     */
    buildHtml: function (items, parentItem = undefined, level = 0) {

      let html = '';
      Object.entries(items).forEach(([key, item]) => {
        let marker = this.getItemMarker(item, parentItem, level);

        // hasSubpages may be true, but children empty because of menuLevel-settings via TYPO3
        if (item.hasSubpages && item.children.length) {
          marker['submenu'] = this.buildHtml(item.children, item, (level + 1));
        }
        html += this.replaceHtml(this.settings.menuItemTemplate, marker);
      });

      // add wrap for sub-items
      if (parentItem) {
        let marker = this.getItemMarker(parentItem, undefined, level);
        marker['menuItems'] = html;
        html = this.replaceHtml(this.settings.subMenuWrapTemplate, marker);

      // or for the whole menu
      } else {
        let marker = {
          'uid': items[0].data.pid,
          'menuItems': html,
          'levelClass': 'level-1',
        };
        html = this.replaceHtml(this.settings.menuWrapTemplate, marker);
      }

      // remove all comments
      return html.replace(/<!--[\s\S]*?(?:-->)/g, '');
    },


    /**
     * Returns the relevant markers for a menu-item
     *
     * @param item menu-item as JSON-object
     * @param parentItem the parent menu-item as JSON-object
     * @param level the current level
     * @returns JSON object
     */
    getItemMarker: function (item, parentItem = undefined, level = 0) {

      let marker = {
        'activeClass': (item.active ? this.settings.activeStatusClass : ''),
        'currentClass': (item.current ? this.settings.currentStatusClass : ''),
        'levelClass': 'level-' + (level+1),
        'ariaCurrent': (item.current ? 'page' : ''),
        'ariaExpanded': (item.current ? 'true' : 'false'),
        'hasChildrenClass': (item.hasSubpages ? this.settings.hasChildrenStatusClass : ''),
        'hasChildren': (!!item.hasSubpages),
        'linkTypeClass': (item.linkType ?  (this.settings.linkTypeClass + '-' + item.linkType) : ''),
        'isLinkedClass': (item.isLinked ? this.settings.isLinkedClass : ''),
        'uid': item.data.uid,
        'titleRaw': item.data.title,
        'title': item.title,
        'link': item.link,
        'target': (item.target ? item.target : '_self'),
        'parentUid': item.data.pid,
        'parentTitle': (parentItem ? parentItem.title : ''),
        'parentLink': (parentItem ? parentItem.link : ''),
        'parentTarget': (parentItem && parentItem.target ? parentItem.target : '_self'),
        'item': item,
        'parentItem': (parentItem ? parentItem : ''),

        // some usefull if-statements
        'ifIsLinkedStart':  (item.isLinked ? '' : '<!--'),
        'ifIsLinkedEnd':  (item.isLinked ? '' : '-->'),
        'ifIsNotLinkedStart':  (item.isLinked ? '<!--' : ''),
        'ifIsNotLinkedEnd':  (item.isLinked ? '-->' : ''),

        'ifHasChildrenStart':  (!!item.hasSubpages ? '' : '<!--'),
        'ifHasChildrenEnd':  (!!item.hasSubpages ? '' : '-->'),
        'ifHasNoChildrenStart':  (!!item.hasSubpages ? '<!--' : ''),
        'ifHasNoChildrenEnd':  (!!item.hasSubpages ? '-->' : ''),
      };

      return marker;
    },

    /**
     * Replaces the placeholders in the given html
     *
     * @param html Html-code to replace placeholders in
     * @param data Array with key-values-pairs where the key is the placeholder-key
     * @returns string
     */
    replaceHtml: function (html, data) {
      return html
        .replace(
          /%(\w*)%/g, // or /{(\w*)}/g for "{this} instead of %this%"
          function (m, key) {
            return data.hasOwnProperty(key) ? data[key] : '';
          }
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
