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

use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ModifyFlexformEventListener
 *
 * Adds additional fields to the flexform of the news plugin.
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_SiteDefault
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ModifyFlexformEventListener
{

    /**
     * __invoke
     *
     * @param \TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent $event
     * @return void
     */
    public function __invoke(
        AfterFlexFormDataStructureParsedEvent $event
    ): void {

        try {

            $identifier = $event->getIdentifier();

            if (
                ($identifier['type'] ?? '') === 'tca'
                && ($identifier['tableName'] ?? '') === 'tt_content'
                && ($identifier['dataStructureKey'] ?? '') === '*,news_pi1'
            ) {

                $file = GeneralUtility::getFileAbsFileName('EXT:site_default/Configuration/FlexForms/News.xml');
                $content = file_get_contents($file);

                if ($content) {
                    $parsed = GeneralUtility::xml2array($content);
                    if (is_array($parsed)) {
                        $dataStructure = $event->getDataStructure();
                        ArrayUtility::mergeRecursiveWithOverrule(
                            $dataStructure['sheets'],
                            $parsed
                        );
                        $event->setDataStructure($dataStructure);
                    }
                }
            }

        } catch (\Exception $e) {
            // do nothing
        }

    }
}
