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

namespace Greens\Contact\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Person
 *
 * Represents a contact person with relevant details.
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package greens_contact
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Person extends AbstractEntity
{

    /**
     * Salutation (Mr, Ms, Other)
     *
     * @var string
     */
    protected string $salutation = '';


    /**
     * Academic or professional title of the person
     *
     * @var string
     */
    protected string $title = '';


    /**
     * First name of the person
     *
     * @var string
     */
    protected string $firstName = '';


    /**
     * Last name of the person
     *
     * @var string
     */
    protected string $lastName = '';


    /**
     * Job position or function of the person
     *
     * @var string
     */
    protected string $position = '';


    /**
     * Categories the person belongs to
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
    protected ObjectStorage $category;


    /**
     * Email address of the person
     *
     * @var string
     */
    protected string $email = '';


    /**
     * Phone number of the person
     *
     * @var string
     */
    protected string $phone = '';


    /**
     * Vita or biography of the person
     *
     * @var string
     */
    protected string $vita = '';


    /**
     * Description
     *
     * @var string
     */
    protected string $description = '';


    /**
     * showDetail
     *
     * @var bool
     */
    protected bool $showDetail = false;


    /**
     * detailLink
     *
     * @var string
     */
    protected string $detailLink = '';


    /**
     * detailLinkLabel
     *
     * @var string
     */
    protected string $detailLinkLabel = '';


    /**
     * Small image of the person
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
     */
    protected ?FileReference $imageSmall = null;


    /**
     * Big image of the person
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
     */
    protected ?FileReference $imageBig = null;


    /**
     * Manual sorting value
     *
     * @var int
     */
    protected int $sorting = 0;



    /**
     * Slug
     *
     * @var string
     */
    protected string $slug = '';


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->category = new ObjectStorage();
    }


    /**
     * Get the salutation
     *
     * @return string
     */
    public function getSalutation(): string
    {
        return $this->salutation;
    }


    /**
     * Set the salutation
     *
     * @param string $salutation
     */
    public function setSalutation(string $salutation): void
    {
        $this->salutation = $salutation;
    }


    /**
     * Get the title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title
     *
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }


    /**
     * Get the first name
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }


    /**
     * Set the first name
     *
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }


    /**
     * Get the last name
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }


    /**
     * Set the last name
     *
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }


    /**
     * Get the combined name
     *
     * @return string
     */
    public function getCombinedName(): string
    {
        $combinedName = [];
        if ($this->getTitle()) {
            $combinedName[] = $this->getTitle();
        }
        if ($this->getFirstName()) {
            $combinedName[] = $this->getFirstName();
        }
        if ($this->getLastName()) {
            $combinedName[] = $this->getLastName();
        }
        return implode(' ', $combinedName);
    }


    /**
     * Get the job
     *
     * @return string
     */
    public function getJob(): string
    {
        return $this->job;
    }


    /**
     * Set the job
     *
     * @param string $job
     */
    public function setJob(string $job): void
    {
        $this->job = $job;
    }



    /**
     * Get the position
     *
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }


    /**
     * Set the position
     *
     * @param string $position
     */
    public function setPosition(string $position): void
    {
        $this->position = $position;
    }


    /**
     * Returns the categories
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
    public function getCategory(): ObjectStorage
    {
        return $this->category;
    }


    /**
     * Sets the categories
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category> $category
     */
    public function setCategory(ObjectStorage $category): void
    {
        $this->category = $category;
    }


    /**
     * Adds a category
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $category
     */
    public function addCategory(Category $category): void
    {
        $this->category->attach($category);
    }


    /**
     * Removes a category
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $category
     */
    public function removeCategory(Category $category): void
    {
        $this->category->detach($category);
    }


    /**
     * Get the email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }


    /**
     * Set the email
     *
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }


    /**
     * Get the phone number
     *
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }


    /**
     * Set the phone number
     *
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }


    /**
     * Get the vita
     *
     * @return string
     */
    public function getVita(): string
    {
        return $this->vita;
    }


    /**
     * Set the vita
     *
     * @param string $vita
     */
    public function setVita(string $vita): void
    {
        $this->vita = $vita;
    }


    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }


    /**
     * Set the description
     *
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    /**
     * Get the showDetail
     *
     * @return bool
     */
    public function getShowDetail(): bool
    {
        return $this->showDetail;
    }


    /**
     * Set the showDetail
     *
     * @param bool $showDetail
     */
    public function setShowDetail(bool $showDetail): void
    {
        $this->showDetail = $showDetail;
    }


    /**
     * Get the detailLink
     *
     * @return string
     */
    public function getDetailLink(): string
    {
        return $this->detailLink;
    }


    /**
     * Set the detailLink
     *
     * @param string $detailLink
     */
    public function setDetailLink(string $detailLink): void
    {
        $this->detailLink = $detailLink;
    }


    /**
     * Get the detailLinkLabel
     *
     * @return string
     */
    public function getDetailLinkLabel(): string
    {
        return $this->detailLinkLabel;
    }


    /**
     * Set the detailLinkLabel
     *
     * @param string $detailLinkLabel
     */
    public function setDetailLinkLabel(string $detailLinkLabel): void
    {
        $this->detailLinkLabel = $detailLinkLabel;
    }



    /**
     * Get the small image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
     */
    public function getImageSmall(): ?FileReference
    {
        return $this->imageSmall;
    }


    /**
     * Set the small image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference|null $imageSmall
     */
    public function setImageSmall(?FileReference $imageSmall): void
    {
        $this->imageSmall = $imageSmall;
    }


    /**
     * Get the big image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
     */
    public function getImageBig(): ?FileReference
    {
        return $this->imageBig;
    }


    /**
     * Set the big image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference|null $imageBig
     */
    public function setImageBig(?FileReference $imageBig): void
    {
        $this->imageBig = $imageBig;
    }


    /**
     * Get the sorting
     *
     * @return int
     */
    public function getSorting(): int
    {
        return $this->sorting;
    }


    /**
     * Set the sorting
     *
     * @param int $sorting
     */
    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }


    /**
     * Get the slug
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }


    /**
     * Set the slug
     *
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

}
