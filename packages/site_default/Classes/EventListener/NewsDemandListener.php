<?php
namespace Madj2k\SiteDefault\EventListener;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use GeorgRinger\News\Event\CreateDemandObjectFromSettingsEvent;


/**
 * Class NewsDemandListener
 *
 * Filters news by type depending on the configured template layout.
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_SiteDefault
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class NewsDemandListener
{

    /**
     * __invoke
     *
     * @param \GeorgRinger\News\Event\CreateDemandObjectFromSettingsEvent $event
     * @return void
     */
    public function __invoke(
        CreateDemandObjectFromSettingsEvent $event
    ): void {

        try {

            $settings = $event->getSettings();
            $demand = $event->getDemand();

            $templateLayout = (int)($settings['templateLayout'] ?? 0);

            switch ($templateLayout) {
                case 2:
                    $demand->setTypes([0,1,2,3]);
                    break;
                default:
                    $demand->setTypes([0,1,2]);
                    break;
            }

        } catch (\Exception $e) {
            // do nothing
        }

    }


}
