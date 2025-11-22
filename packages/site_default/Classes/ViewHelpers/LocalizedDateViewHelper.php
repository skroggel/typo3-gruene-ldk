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

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class LocalizedDateViewHelper
 *
 * Renders a date in a localized format using IntlDateFormatter. Supports classic date() formats
 * and falls back to PHP formatting if the intl extension is unavailable. Locale is detected from
 * the current frontend language unless explicitly set.
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Frischpack_SiteDefault
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class LocalizedDateViewHelper extends AbstractViewHelper
{
    /**
     * Whether to escape the output.
     *
     * @var bool
     */
    protected $escapeOutput = false;


    /**
     * Initializes arguments for the ViewHelper.
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('date', 'DateTime', 'Date object or string to be formatted', true);
        $this->registerArgument('format', 'string', 'PHP-style date format (e.g. d. F Y)', false, 'd. F Y');
        $this->registerArgument('locale', 'string', 'Locale (e.g. de_DE), falls back to FE context if not set', false, '');
        $this->registerArgument('convertToTimeZone', 'string', 'If set, converts UTC time to given time zone', false, '');

    }


    /**
     * Renders the formatted date string.
     *
     * @return string
     * @throws \DateMalformedStringException
     */
    public function render(): string
    {
        /** @var \DateTimeInterface|string $date */
        $date = $this->arguments['date'];

        /** @var string $format */
        $format = $this->arguments['format'];

        /** @var string $locale */
        $locale = $this->arguments['locale'];

        /** @var bool $convertUtc */
        $convertToTimeZone = $this->arguments['convertToTimeZone'];

        if (!$date instanceof \DateTimeInterface) {
            $date = new \DateTime((string)$date, new \DateTimeZone('UTC'));
        }

        if ($convertToTimeZone) {
            // get "raw" time and assume UTC
            $neutralString = $date->format('Y-m-d H:i:s');
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $neutralString, new \DateTimeZone('UTC'));
            $date = $date->setTimezone(new \DateTimeZone($convertToTimeZone));
        }

        if ($locale === '') {
            $locale = $this->detectLocaleFromFrontend();
        }

        if (class_exists(\IntlDateFormatter::class)) {
            $intlFormat = $this->convertDateFormatToIntlFormat($format);
            $formatter = new \IntlDateFormatter(
                $locale,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::NONE,
                null,
                null,
                $intlFormat
            );
            return $formatter->format($date);
        }

        return $date->format($format);
    }


    /**
     * Converts PHP-style date() format to IntlDateFormatter pattern.
     *
     * @param string $phpFormat
     * @return string
     */
    protected function convertDateFormatToIntlFormat(string $phpFormat): string
    {
        $map = [
            'd' => 'dd',
            'j' => 'd',
            'F' => 'MMMM',
            'M' => 'MMM',
            'm' => 'MM',
            'n' => 'M',
            'Y' => 'yyyy',
            'y' => 'yy',
            'H' => 'HH',
            'h' => 'hh',
            'i' => 'mm',
            's' => 'ss',
            'A' => 'a',
        ];

        $output = '';
        $len = strlen($phpFormat);
        $escape = false;

        for ($i = 0; $i < $len; $i++) {
            $char = $phpFormat[$i];

            if ($escape) {
                $output .= "'" . $char;
                while ($i + 1 < $len && $phpFormat[$i + 1] === '\\') {
                    $i += 2;
                    if ($i < $len) {
                        $output .= $phpFormat[$i];
                    }
                }
                $output .= "'";
                $escape = false;
            } elseif ($char === '\\') {
                $escape = true;
            } elseif (isset($map[$char])) {
                $output .= $map[$char];
            } else {
                $output .= $char;
            }
        }

        return $output;
    }



    /**
     * Detects the locale based on the current frontend language.
     *
     * @return string
     */
    protected function detectLocaleFromFrontend(): string
    {
        /** @var \TYPO3\CMS\Core\Site\Entity\SiteLanguage|null $siteLanguage */
        $siteLanguage = $GLOBALS['TYPO3_REQUEST']->getAttribute('language');

        return ($siteLanguage instanceof \TYPO3\CMS\Core\Site\Entity\SiteLanguage)
            ? (string)$siteLanguage->getLocale()
            : 'de_DE';
    }
}
