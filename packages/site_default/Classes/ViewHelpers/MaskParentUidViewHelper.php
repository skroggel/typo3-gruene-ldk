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

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class MaskParentUidViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_SiteDefault
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MaskParentUidViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('data', 'array', 'The data array');
    }


    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     * @return  int
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): int {

        /** @var array $data */
        $data = $arguments['data'];

        if (
            ($data['l18n_diffsource'])
            && ($json = json_decode($data['l18n_diffsource'], true))
            && (! empty($json['tx_mask_content_parent_uid']))
        ) {
            return intval($json['tx_mask_content_parent_uid']);
        }

        return $data['tx_mask_content_parent_uid'] ? intval($data['tx_mask_content_parent_uid']) : 0;
    }

}
