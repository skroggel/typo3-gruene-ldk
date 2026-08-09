<?php
declare(strict_types=1);

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

namespace Madj2k\SiteDefault\ViewHelpers\Format;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class TimeRangeViewHelper
 *
 * Formats a start and optional stop timestamp as a readable time range.
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_SiteDefault
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TimeRangeViewHelper extends AbstractViewHelper
{
    /**
     * Initializes the ViewHelper arguments.
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'startDate',
            \DateTimeInterface::class,
            'The start date.',
            true
        );

        $this->registerArgument(
            'stopDate',
            \DateTimeInterface::class,
            'The optional stop date.',
            false,
            null
        );

        $this->registerArgument(
            'dateFormat',
            'string',
            'The date format.',
            false,
            'd.m.Y'
        );

        $this->registerArgument(
            'timeFormat',
            'string',
            'The time format.',
            false,
            'H:i \U\h\r'
        );

        $this->registerArgument(
            'separator',
            'string',
            'The separator between start and stop date.',
            false,
            ' - '
        );
    }


    /**
     * Renders the formatted time range.
     *
     * @return string
     */
    public function render(): string
    {
        /** @var \DateTimeInterface $startDate */
        $startDate = $this->arguments['startDate'];

        /** @var \DateTimeInterface|null $stopDate */
        $stopDate = $this->arguments['stopDate'] instanceof \DateTimeInterface
            ? $this->arguments['stopDate']
            : null;

        /** @var string $dateFormat */
        $dateFormat = (string)$this->arguments['dateFormat'];

        /** @var string $timeFormat */
        $timeFormat = (string)$this->arguments['timeFormat'];

        /** @var string $separator */
        $separator = (string)$this->arguments['separator'];

        /** @var string $startDateFormatted */
        $startDateFormatted = $startDate->format($dateFormat);

        /** @var string $startTimeFormatted */
        $startTimeFormatted = $startDate->format($timeFormat);

        if ($stopDate === null) {
            return sprintf(
                '%s %s',
                $startDateFormatted,
                $startTimeFormatted
            );
        }

        /** @var string $stopTimeFormatted */
        $stopTimeFormatted = $stopDate->format($timeFormat);

        if ($startDate->format('Y-m-d') === $stopDate->format('Y-m-d')) {
            return sprintf(
                '%s %s%s%s',
                $startDateFormatted,
                $startTimeFormatted,
                $separator,
                $stopTimeFormatted
            );
        }

        /** @var string $stopDateFormatted */
        $stopDateFormatted = $stopDate->format($dateFormat);

        return sprintf(
            '%s %s%s%s %s',
            $startDateFormatted,
            $startTimeFormatted,
            $separator,
            $stopDateFormatted,
            $stopTimeFormatted
        );
    }
}
