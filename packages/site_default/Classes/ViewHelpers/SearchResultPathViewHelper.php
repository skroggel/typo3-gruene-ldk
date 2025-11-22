<?php
declare(strict_types=1);
namespace Madj2k\SiteDefault\ViewHelpers;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;


/**
 * Class SearchWordViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_SiteDefault
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SearchResultPathViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;


    /**
     * Initialize arguments
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('pid', 'int', 'The pid from the search result',  true, '0');
        $this->registerArgument('removeRootPage', 'bool', 'If set the root page will not be part of the path',  false, true);
        $this->registerArgument('removeCurrentPage', 'bool', 'If set the current page will not be part of the path',  false, true);
        $this->registerArgument('dokTypes', 'string', 'Comma-separated list of dokTypes to include',  false, '1,4,7');

    }


    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {

        /** @var int $pid */
        if ($int = $arguments['pid']) {
            $rootlineTitles = [];
            $rootLine = BackendUtility::BEgetRootLine($int);

            // Remove last
            array_pop($rootLine);

            if (!empty($arguments['removeRootPage'])) {
                array_pop($rootLine);
            }

            if (!empty($arguments['removeCurrentPage'])) {
                array_shift($rootLine);
            }

            /** @var array $dokTypes */
            $dokTypes = GeneralUtility::trimExplode(',', $arguments['dokTypes']?: '', true);

            $rootLine = array_reverse($rootLine);
            foreach ($rootLine as $cnt => $rootLineItem) {
                if (in_array($rootLineItem['doktype'], $dokTypes)) {
                    $rootlineTitles[] = '<span class="path-element level-' . $cnt .  ($cnt == count($rootLine)-1 ? ' last' :'') . '">' . $rootLineItem['title'] . '</span>';
                }
            }

            return implode('', $rootlineTitles);
        }

        return '';
    }

}
