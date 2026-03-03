<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "site_default".
 *
 * (c) 2026 Steffen Kroggel <developer@steffenkroggel.de>
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Madj2k\SiteDefault\ViewHelpers\News;

use DateTimeInterface;
use GeorgRinger\News\Domain\Model\News;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GroupByMonthViewHelper
 *
 * Groups EXT:news items by month and year.
 * The group key is the UNIX timestamp (int) of the first day of the month (00:00:00).
 * Additionally, a flag is set for the first month of a new year within the sorted result.
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package SiteDefault
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GroupByMonthViewHelper extends AbstractViewHelper
{

    /**
     * Initializes the arguments.
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'news',
            QueryResult::class,
            'Iterable list of \\GeorgRinger\\News\\Domain\\Model\\News items.',
            true
        );

        $this->registerArgument(
            'sortDirection',
            'string',
            'Sort direction for groups: "asc" or "desc".',
            false,
            'asc'
        );
    }


    /**
     * Renders the ViewHelper.
     *
     * Return structure:
     * [
     *   1764543600 => [
     *     'date' => DateTime,
     *     'isFirstOfYear' => bool,
     *     'items' => [News, News, ...],
     *   ],
     * ]
     *
     * isFirstOfYear is TRUE if the year differs from the previous group in the sorted list.
     * For the very first group it is always FALSE.
     *
     * @return array<int, array{date:\DateTime,isFirstOfYear:bool,items:array<int,\GeorgRinger\News\Domain\Model\News>}>
     */
    public function render(): array
    {
        /** @var iterable<\GeorgRinger\News\Domain\Model\News> $news */
        $news = $this->arguments['news'];

        /** @var string $sortDirection */
        $sortDirection = strtolower((string)$this->arguments['sortDirection']);

        /** @var array<int, array{date:\DateTime,isFirstOfYear:bool,items:array<int,\GeorgRinger\News\Domain\Model\News>}> $groups */
        $groups = [];

        foreach ($news as $newsItem) {

            if (! $newsItem instanceof News) {
                continue;
            }

            /** @var \DateTimeInterface|null $date */
            $date = $newsItem->getDatetime();

            if (! $date instanceof DateTimeInterface) {
                continue;
            }

            /** @var \DateTime $monthStart */
            $monthStart = $this->getMonthStartDate($date);

            /** @var int $timestamp */
            $timestamp = $monthStart->getTimestamp();

            if (! isset($groups[$timestamp])) {
                $groups[$timestamp] = [
                    'date' => $monthStart,
                    'isFirstOfYear' => false,
                    'items' => [],
                ];
            }

            $groups[$timestamp]['items'][] = $newsItem;
        }

        if ($sortDirection === 'desc') {
            krsort($groups, SORT_NUMERIC);
        } else {
            ksort($groups, SORT_NUMERIC);
        }

        /** @var int|null $previousYear */
        $previousYear = null;

        /** @var bool $isFirst */
        $isFirst = true;

        foreach ($groups as $timestamp => $group) {

            /** @var int $currentYear */
            $currentYear = (int)$group['date']->format('Y');

            if ($isFirst) {
                $groups[$timestamp]['isFirstOfYear'] = true;
                $isFirst = false;
            } else {
                $groups[$timestamp]['isFirstOfYear'] = ($currentYear !== $previousYear);
            }

            $previousYear = $currentYear;
        }

        return $groups;
    }


    /**
     * Returns a DateTime representing the first day of the month (00:00:00).
     *
     * The timezone is derived from the given DateTimeInterface instance.
     *
     * @param \DateTimeInterface $date
     * @return \DateTime
     */
    protected function getMonthStartDate(DateTimeInterface $date): \DateTime
    {
        /** @var \DateTime $mutable */
        $mutable = \DateTime::createFromInterface($date);

        return $mutable
            ->setDate(
                (int)$mutable->format('Y'),
                (int)$mutable->format('m'),
                1
            )
            ->setTime(0, 0, 0);
    }
}
