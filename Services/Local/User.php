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

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * RPC services for User management
 *
 * @category    BackBee
 * @package     BackBee\Services
 * @subpackage  Local
 * @copyright   Lp digital system
 * @author      m.baptista <michel.baptista@lp-digital.fr>
 */
class User extends AbstractServiceLocal
{
    /**
     * @exposed(secured=true)
     */
    public function getConfigLayout()
    {
        $lessService = new \BackBee\Services\Local\Less($this->bbapp);

        $result = new \stdClass();
        $result->gridColumns = $lessService->getGridColumns();
        $result->gridConstants = $lessService->getGridConstant();

        return $result;
    }

    /**
     * @exposed(secured=true)
     */
    public function getUser()
    {
        $securityContext = $this->bbapp->getSecurityContext();
        if (null !== $token = $securityContext->getToken()) {
            return json_decode($token->getUser()->serialize());
        }

        return;
    }

    /**
     * @exposed(secured=false)
     */
    public function logoff()
    {
        $application = $this->getApplication();
        if (null !== $application->getEventDispatcher()) {
            $event = new GetResponseEvent($application->getController(), $application->getController()->getRequest(), 1);
            $application->getEventDispatcher()->dispatch('frontcontroller.request.logout', $event);
        }

        return;
    }

    /**
     * @exposed(secured=true)
     */
    public function getBBUserPreferences()
    {
        $securityContext = $this->bbapp->getSecurityContext();
        $userPreferencesRepository = $this->bbapp->getEntityManager()->getRepository('BackBee\Site\UserPreferences');
        if (null !== $token = $securityContext->getToken()) {
            $userPreferences = $userPreferencesRepository->loadPreferences($token);
            $values = array('identity' => $userPreferences->getUid(), 'preferences' => $userPreferences->getPreferences());

            return $values;
        }
    }

    /**
     * @exposed(secured=true)
     */
    public function setBBUserPreferences($identity, $preferences)
    {
        $securityContext = $this->bbapp->getSecurityContext();
        $userPreferencesRepository = $this->bbapp->getEntityManager()->getRepository('BackBee\Site\UserPreferences');
        $token = $securityContext->getToken();
        if (null !== $token && $userPreferencesRepository->retrieveUserPreferencesUid($token) == $identity) {
            $userPreferencesRepository->setPreferences($token, $preferences);
            $this->bbapp->getEntityManager()->flush();
        }
    }
}