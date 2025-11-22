/**
 * ProductTabs - A JavaScript class for handling tabbed product displays with load-more functionality
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright 2025 Steffen Kroggel
 * @version 1.0.0
 * @license GNU General Public License v3.0
 * @see https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * Features:
 * - Tab switching with buttons and dropdown
 * - Load more products functionality
 * - Configurable products per batch
 * - Responsive design support
 *
 * @example
 * // HTML structure:
 * <div class="product-tabs" data-products-per-batch="8">
 *    <!-- Desktop Tabs -->
 *    <ul class="nav nav-tabs d-none d-md-flex">
 *      <li class="nav-item">
 *        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1">Tab 1</a>
 *      </li>
 *      <li class="nav-item">
 *        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#tab2">Tab 2</a>
 *      </li>
 *    </ul>
 *
 *    <!-- Mobile Dropdown -->
 *    <select class="js-tab-dropdown form-select d-md-none">
 *      <option value="tab1">Tab 1</option>
 *      <option value="tab2">Tab 2</option>
 *    </select>
 *
 *    <!-- Tab Content -->
 *    <div class="tab-content">
 *      <div id="tab1" class="tab-pane active show">
 *        <div id="tab1-products">...</div>
 *      </div>
 *      <div id="tab2" class="tab-pane">
 *        <div id="tab2-products">...</div>
 *      </div>
 *    </div>
 *    <!-- Load More Buttons -->
 *    <button class="js-load-more-btn" data-tab="tab1">Load More (<span class="js-remaining-count">0</span>)</button>
 *    <button class="js-load-more-btn" data-tab="tab2">Load More (<span class="js-remaining-count">0</span>)</button>
 *  </div>
 *
 * // JavaScript initialization:
 * const tabsElement = document.querySelector('.product-tabs');
 * const productTabs = new ProductTabs(tabsElement, {
 *   productsPerBatch: 12,
 *   debug: true
 * });
 */
class ProductTabs {
  /**
   * Create a ProductTabs instance
   * @param {HTMLElement} wrapper - The containing element for the tabs component
   * @param {Object} config - Configuration options
   */
  constructor(wrapper, config = {}) {
    // Basic validation
    if (!wrapper) {
      console.error('[ProductTabs] No wrapper element found');
      return;
    }

    this.wrapper = wrapper;

    // First try to get value from config
    // Then from data attribute
    // Finally fall back to default value
    const dataProductsPerBatch = wrapper ? parseInt(wrapper.dataset.productsPerBatch, 10) : null;

    this.config = {
      productsPerBatch: 8, // default value
      selectors: {
        loadMoreBtn: '.js-load-more-btn',
        productItem: '.js-product-item',
        remainingCount: '.js-remaining-count',
        tabDropdown: '.js-tab-dropdown',
        tabPane: '.js-tab-pane',
        navLink: '.js-nav-tabs-link'
      },
      classes: {
        show: 'show',
        active: 'active'
      },
      debug: false,
      ...config // spread custom config
    };

    // Override productsPerBatch with data attribute if present
    if (dataProductsPerBatch) {
      this.config.productsPerBatch = dataProductsPerBatch;
    }

    // Debug logging
    this._log('Initializing ProductTabs with configuration:', this.config);

    this.init();
  }

  /**
   * Initialize the ProductTabs component
   * Sets up tab buttons, dropdown menu, and load more functionality
   * @private
   * @throws {Error} If initialization fails
   * @returns {void}
   */
  init() {
    if (!this.wrapper) return;

    try {
      this.initTabButtons();
      this.initDropdown();
      this.initLoadMoreButtons();
      this._log('Initialization completed');

    } catch (error) {
      console.error('[ProductTabs] Error during initialization:', error);
    }
  }

  /**
   * Initialize tab buttons and their event handlers
   */
  initTabButtons() {
    const tabButtons = this.wrapper.querySelectorAll('[data-bs-toggle="tab"]');
    this._log(`Found ${tabButtons.length} tab buttons`);

    tabButtons.forEach(button => {
      button.addEventListener('shown.bs.tab', (e) => {
        const targetId = e.target.getAttribute('data-bs-target')?.replace('#', '');
        if (targetId) {
          this.resetTabContent(targetId);
        }
      });
    });
  }

  /**
   * Initialize dropdown functionality
   */
  initDropdown() {
    try {
      const dropdown = this.wrapper.querySelector(this.config.selectors.tabDropdown);
      if (!dropdown) {
        this._log('No dropdown menu found');
        return;
      }

      dropdown.addEventListener('change', (e) => {
        const selected = e.target.value;
        if (!selected) return;

        this._log(`Tab switch to: ${selected}`);

        // Deactivate all tabs
        this.wrapper.querySelectorAll(this.config.selectors.tabPane).forEach(pane => {
          pane.classList.remove(this.config.classes.show, this.config.classes.active);
        });

        // Activate selected tab
        const targetPane = this.wrapper.querySelector(`#${selected}`);
        if (targetPane) {
          targetPane.classList.add(this.config.classes.show, this.config.classes.active);
          this.resetTabContent(selected);
        }

        // Sync navigation state
        this.wrapper.querySelectorAll(this.config.selectors.navLink).forEach(link => {
          link.classList.remove(this.config.classes.active);
          if (link.getAttribute('data-bs-target') === `#${selected}`) {
            link.classList.add(this.config.classes.active);
          }
        });
      });

    } catch (error) {
      console.error('[ProductTabs] Error initializing dropdown:', error);
    }
  }

  initLoadMoreButtons() {
    const loadMoreButtons = this.wrapper.querySelectorAll(this.config.selectors.loadMoreBtn);

    loadMoreButtons.forEach(button => {
        const tabId = button.dataset.tab;
        if (!tabId) {
            console.warn('[ProductTabs] Button found without data-tab attribute:', button);
            return;
        }

      // Note: The container already has the correct ID "content-{tabId}-products"
      const container = this.wrapper.querySelector(`#${tabId}-products`);
        if (!container) {
            console.warn(`[ProductTabs] Container not found for tab "${tabId}"`);
            return;
        }

        const items = Array.from(container.querySelectorAll(this.config.selectors.productItem));
        this._log(`Found ${items.length} products in tab "${tabId}"`);

        // Initially show first batch only
        this.resetTabContent(tabId);

        // Load More Button Handler
        button.addEventListener('click', (e) => {
            e.preventDefault();
            try {
                const shown = items.filter(el => el.style.display !== 'none').length;
                const nextBatch = items.slice(shown, shown + this.config.productsPerBatch);

                this._log(`Showing next ${nextBatch.length} products in tab "${tabId}"`);

                nextBatch.forEach(el => el.style.display = 'block');
                this.updateRemainingCount(tabId, items);
            } catch (error) {
                console.error('[ProductTabs] Error loading more products:', error);
            }
        });
    });
}

  /**
   * Reset tab content to initial state (show first batch, hide the rest)
   * @param {string} tabId - ID of the current tab
   */
  resetTabContent(tabId) {
    const container = this.wrapper.querySelector(`#${tabId}-products`);
    if (!container) {
      console.warn(`[ProductTabs] Container not found for tab "${tabId}"`);
      return;
    }

    const items = Array.from(container.querySelectorAll(this.config.selectors.productItem));

    items.forEach((el, i) => {
      el.style.display = i < this.config.productsPerBatch ? 'block' : 'none';
    });

    this.updateRemainingCount(tabId, items);
  }

  /**
   * Update remaining count and button visibility
   * @param {string} tabId - ID of the current tab
   * @param {Array} items - Array of product items
   */
  updateRemainingCount(tabId, items) {
    try {
      const shown = items.filter(el => el.style.display !== 'none').length;
      const remaining = items.length - shown;
      const nextBatchSize = Math.min(remaining, this.config.productsPerBatch);

      const countEl = this.wrapper.querySelector(
        `${this.config.selectors.loadMoreBtn}[data-tab="${tabId}"] ${this.config.selectors.remainingCount}`
      );
      const button = this.wrapper.querySelector(
        `${this.config.selectors.loadMoreBtn}[data-tab="${tabId}"]`
      );

      if (countEl) countEl.textContent = nextBatchSize;
      if (button) button.style.display = remaining > 0 ? '' : 'none';

      this._log(`Tab "${tabId}": ${nextBatchSize} products in next batch (${remaining} total remaining)`);
    } catch (error) {
      console.error('[ProductTabs] Error updating remaining count:', error);
    }
  }

  /**
   * Debug logging helper
   * @private
   */
  _log(...args) {
    if (this.config.debug) {
      console.log('[ProductTabs]', ...args);
    }
  }
}
