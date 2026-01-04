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

use TYPO3\CMS\Core\LinkHandling\TypolinkParameter;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\LinkHandling\TypoLinkCodecService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * Class PageTitleFromTypolinkViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_SiteDefault
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageTitleFromTypolinkViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('typolink', TypolinkParameter::class, 'Typolink to extract page title from', true);
    }


    /**
     * Processes the provided link, decodes it, and retrieves the title of an internal page
     * if the link corresponds to an internal page with a valid UID.
     *
     * @return string The title of the page if the link is valid and corresponds to an internal page
     * @throws \Doctrine\DBAL\Exception
     */
    public function render(): string
    {
        /** @var \TYPO3\CMS\Core\LinkHandling\TypolinkParameter $link */
        $link = $this->arguments['typolink'];
        if (! $link) {
            return '';
        }

        if (
            ($linkData = $link->toArray())
            && ($url = $linkData['url'])
        ) {

            if (str_starts_with($url, 't3://page?uid=')) {
                $queryString = parse_url($url, PHP_URL_QUERY);
                parse_str($queryString, $params);
                if ($uid = (int)($params['uid'] ?? 0)) {
                    return $this->fetchPageTitle($uid);
                }
            }
        }

        return '';
    }


    /**
     * Fetches the title of a given page from the database.
     *
     * @param int $pageUid The unique identifier of the page for which the title is to be retrieved.
     * @return string The title of the page, or null if no title is found.
     * @throws \Doctrine\DBAL\Exception
     */
    protected function fetchPageTitle(int $pageUid): string
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        /** @var QueryBuilder $qb */
        $qb = $connectionPool
            ->getQueryBuilderForTable('pages');

        $title = $qb
            ->select('title')
            ->from('pages')
            ->where(
                $qb->expr()->eq('uid', $qb->createNamedParameter($pageUid, Connection::PARAM_INT))
            )
                    ->executeQuery()
            ->fetchOne();

        return $title ?: '';
    }

}
