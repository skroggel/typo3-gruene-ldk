/**
 * OverlayToggle
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright 2025 Steffen Kroggel
 * @version 1.0.0
 * @license GNU General Public License v3.0
 * @see https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * This class toggles the visibility of a target element referenced by the `aria-controls`
 * attribute of a button or trigger element. It manages ARIA attributes to ensure accessibility
 * and allows overlays to be closed externally via a custom event.
 *
 * @example
 * Example HTML:
 * <button class="toggle js-searchbox-overlay-toggle"
 *         aria-label="Open search"
 *         aria-controls="searchbox-overlay"
 *         aria-expanded="false">
 *   <span class="icon-search icon toggle-icon"></span>
 * </button>
 *
 * <div id="searchbox-overlay" class="searchbox-overlay" aria-hidden="true">
 *   <!-- Searchbox content -->
 * </div>
 *
 * Example JavaScript:
 * const searchToggle = new OverlayToggle('.js-searchbox-overlay-toggle');
 *
 * // External close trigger
 * window.dispatchEvent(new CustomEvent('madj2k-overlaytoggle-close'));
 *
 * Example CSS:
 * .searchbox-overlay {
 *   opacity: 0;
 *   visibility: hidden;
 *   transition: opacity 0.5s ease-in-out, visibility 0s linear 0.5s;
 *
 *   &.is-visible {
 *     opacity: 1;
 *     visibility: visible;
 *     transition: opacity 0.5s ease-in-out, visibility 0s linear 0s;
 *   }
 * }
 *
 * .toggle[aria-expanded="true"] .icon-search::before {
 *   content: "\e8bb"; // Example: "close" icon (replace with actual icon code)
 * }
 */
class OverlayToggle {
  /**
   * Creates a new OverlayToggle instance.
   * @param {string} toggleSelector - CSS selector to identify all toggle buttons.
   */
  constructor(toggleSelector) {
    /** @type {NodeListOf<HTMLElement>} */
    this.toggles = document.querySelectorAll(toggleSelector);
    this.init();
    this.registerEvents();
  }

  /**
   * Initializes toggle buttons and sets up toggle behavior.
   */
  init() {
    this.toggles.forEach(toggle => {
      const targetId = toggle.getAttribute('aria-controls');
      const target = document.getElementById(targetId);
      if (!target) return;

      // Set initial ARIA state
      target.setAttribute('aria-hidden', 'true');
      toggle.setAttribute('aria-expanded', 'false');

      toggle.addEventListener('click', () => {
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!isExpanded));
        target.setAttribute('aria-hidden', String(isExpanded));
        target.classList.toggle('is-visible', !isExpanded);
      });
    });
  }

  /**
   * Listens for external 'madj2k-overlaytoggle-close' events
   * and closes all overlays when triggered.
   */
  registerEvents() {
    document.addEventListener('madj2k-overlaytoggle-close', () => {
      this.close();
    });
  }

  /**
   * Closes all overlays by removing the 'is-visible' class and updating ARIA attributes.
   */
  close() {
    this.toggles.forEach(toggle => {
      const targetId = toggle.getAttribute('aria-controls');
      const target = document.getElementById(targetId);
      if (!target) return;

      toggle.setAttribute('aria-expanded', 'false');
      target.setAttribute('aria-hidden', 'true');
      target.classList.remove('is-visible');
    });
  }
}
