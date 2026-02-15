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

namespace Greens\Contact\Controller;

use Greens\Contact\Domain\Model\Person;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Greens\Contact\Domain\DTO\Search;
use Greens\Contact\Domain\Repository\PersonRepository;


/**
 * Class ContactController
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package greens_contact
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class ContactController extends  \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer|null $currentContentObject
     */
    protected ?ContentObjectRenderer $currentContentObject = null;


    /**
     * @var \TYPO3\CMS\Core\Site\Entity\SiteLanguage|null
     */
    protected ?SiteLanguage $siteLanguage = null;


    /**
     * @param \Greens\Contact\Domain\Repository\PersonRepository $personRepository
     * @param \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface $cache
     */
    public function __construct(
        private readonly PersonRepository $personRepository,
        private readonly FrontendInterface $cache,
    ) {
        // nothing to do here
    }


    /**
     * Set globally used objects
     */
    protected function initializeAction(): void
    {
        $this->currentContentObject = $this->request->getAttribute('currentContentObject');
        $this->siteLanguage = $this->request->getAttribute('language');

        if ($this->arguments->hasArgument('search')) {
            $propertyMappingConfiguration = $this->arguments->getArgument('search')->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->allowAllProperties();
        }
    }


    /**
     * Assign default variables to view
     */
    protected function initializeView(): void
    {
        $this->view->assign('data', $this->currentContentObject->data);
    }


    /**
     * action list
     *
     * @param \Greens\Contact\Domain\DTO\Search|null $search
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function listAction(?Search $search = null): ResponseInterface
    {

        // Check for id. This way multiple instances of the same plugin can be used on one page.
        if (
            (! $search)
            || ($search->getIdentifier() != $this->currentContentObject->data['uid'])
        ){
            $search = GeneralUtility::makeInstance(Search::class);
        }

        // check if pages are set
        $pages = [];
        if (!empty($this->currentContentObject->data['pages'])) {
            $pages = GeneralUtility::trimExplode(',', $this->currentContentObject->data['pages'], true);
        }

        if (
            ($search->getCategory() > 0)
            || (! empty($this->settings['category']))
        ){
            $persons = $this->personRepository->findByCategoryIdSorted(
                categoryId: (int) $this->settings['category'] ?? $search->getCategory()->getUid(),
                pages: $pages
            );

        } else {
            //$persons = $this->personRepository->findAllSortedByCategoryAndPerson(pages: $pages);
            $persons = $this->personRepository->findAllSortedByPerson(pages: $pages);
        }

        // items per page - since we only load more, we always start at the first page
        $currentPage = $search->getPage();
        $itemsPerPage = (int) ($this->settings['limit'] ?? 10) * $currentPage;
        $paginator = new ArrayPaginator($persons, 1, $itemsPerPage);
        $pagination = new SimplePagination($paginator);

        $this->assignFilterOptions();
        $this->view->assignMultiple([
            'search' => $search,
            'persons' => $persons,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'lastPaginatedItem' => $persons[$paginator->getKeyOfLastPaginatedItem()] ?? null
        ]);

        return $this->htmlResponse();
    }


    /**
     * action slider
     *
     * @param \Greens\Contact\Domain\DTO\Search|null $search
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function sliderAction(?Search $search = null): ResponseInterface
    {
        return $this->listAction($search);
    }


    /**
     * action detail
     *
     * @param \Greens\Contact\Domain\Model\Person $person
     * @return ResponseInterface
     */
    public function detailAction(Person $person): ResponseInterface
    {

        $this->view->assignMultiple([
            'person' => $person,
        ]);

        return $this->htmlResponse();
    }


    /**
     * Separate method for available filter options with cache.
     *
     * @return void
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    protected function assignFilterOptions (): void
    {
        $languageId = $this->siteLanguage->getLanguageId();
        $uid = (int) $this->currentContentObject->data['uid'];
        $cacheIdentifier = 'filteroptions_' . $uid . '_' . $languageId;

        if (!$filterOptions = $this->cache->get($cacheIdentifier)) {

            $filterOptions = [
                'categories' => $this->personRepository->findAssignedCategories()
            ];

            $this->cache->set(
                $cacheIdentifier,
                $filterOptions,
                [
                    'greenscontact_filteroptions', 'greenscontact_filteroptions_' . $uid . '_' . $languageId
                ]
            );
        }

        $this->view->assignMultiple($filterOptions);
    }


}
