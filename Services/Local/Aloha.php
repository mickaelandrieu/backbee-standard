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

namespace BackBee\Services\Local;

/**
 * Description of Aloha
 *
 * @category    BackBee
 * @package     BackBee\Services
 * @subpackage  Local
 * @copyright   Lp digital system
 * @author      n.bremont <nicolas.bremont@lp-digital.fr>
 */
class Aloha extends AbstractServiceLocal
{
    /**
     * @codeCoverageIgnore
     * @param type $data
     */
    private function setStdObjectAloha($data)
    {
        $stdClass = new \stdClass();
    }

    /**
     * @exposed(secured=true)
     * @codeCoverageIgnore
     */
    public function getAlohaPluginsTbale()
    {
        $config = $this->bbapp->getConfig()->getAlohaConfig();

        return $config;
    }
}