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
 */

namespace Greens\Contact\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * A function held by a contact and assigned to a reusable function group.
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package greens_contact
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ContactFunction extends AbstractEntity
{
    /**
     * Contact owning this function.
     *
     * @var Person|null
     */
    protected ?Person $person = null;

    /**
     * Group in which this function is displayed.
     *
     * @var ContactFunctionGroup|null
     */
    protected ?ContactFunctionGroup $functionGroup = null;

    /**
     * Text of the function.
     *
     * @var string
     */
    protected string $position = '';


    /**
     * Returns the owning contact.
     *
     * @return Person|null
     */
    public function getPerson(): ?Person
    {
        return $this->person;
    }

    /**
     * Sets the owning contact.
     *
     * @param Person|null $person
     * @return void
     */
    public function setPerson(?Person $person): void
    {
        $this->person = $person;
    }

    /**
     * Returns the function group.
     *
     * @return ContactFunctionGroup|null
     */
    public function getFunctionGroup(): ?ContactFunctionGroup
    {
        return $this->functionGroup;
    }

    /**
     * Sets the function group.
     *
     * @param ContactFunctionGroup|null $functionGroup
     * @return void
     */
    public function setFunctionGroup(?ContactFunctionGroup $functionGroup): void
    {
        $this->functionGroup = $functionGroup;
    }

    /**
     * Returns the function text.
     *
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * Sets the function text.
     *
     * @param string $position
     * @return void
     */
    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

}
