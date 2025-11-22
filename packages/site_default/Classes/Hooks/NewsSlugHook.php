<?php
namespace Madj2k\SiteDefault\Hooks;

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


/**
 * Class NewsSlugHook
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_SiteDefault
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class NewsSlugHook
{


    /**
     * processDatamap_postProcessFieldArray
     * For deleting caches after change content element
     *
     * @param string $status
     * @param string $table
     * @param string $id
     * @param array $fieldArray
     * @param object $reference
     */
    public function processDatamap_postProcessFieldArray(
        string $status,
        string $table,
        string $id,
        array &$fieldArray,
        object &$reference
    ):void {

        try {

            if (
                ($table == 'tx_news_domain_model_news')
				&& (isset($fieldArray['title']))
            ){
				$fieldArray['tx_sitedefault_title_cleaned'] = strip_tags($fieldArray['title']);
            }

        } catch (\Exception $e) {
           // do nothing
        }

    }


}
