<?php

/*
 * Copyright (c) 2011-2015 Lp digital system
 *
 * This file is part of BackBee.
 *
 * BackBee5 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BackBee is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BackBee. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Charles Rouillon <charles.rouillon@lp-digital.fr>
 */

namespace BackBee\Security\Acl\Permission;

/**

 * @category    BackBee
 * @package     BackBee\Security
 * @subpackage  Acl\Permission
 * @copyright   Lp digital system
 * @author      k.golovin
 */
class InvalidPermissionException extends \InvalidArgumentException
{
    protected $permission;

    public function __construct($message, $permission, $previous = null)
    {
        $this->permission = $permission;

        parent::__construct($message, null, $previous);
    }

    /**
     *
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }
}