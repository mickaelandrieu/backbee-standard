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

namespace BackBee\Profiler\DataCollector;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Routing data collector
 *
 * @category    BackBee
 * @package     BackBee\Profiler
 * @copyright   Lp digital system
 * @author      k.golovin
 */
class RoutingDataCollector extends DataCollector implements ContainerAwareInterface
{
    private $container;

    /**
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Collects the Information on the Route
     *
     * @param Request    $request   The Request Object
     * @param Response   $response  The Response Object
     * @param \Exception $exception The Exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $collection = $this->container->get('routing');

        $_routes = $collection->all();

        $routes = array();

        foreach ($_routes as $routeName => $route) {
            $defaults = $route->getDefaults();
            $requirements = $route->getRequirements();

            $controller = isset($defaults['_controller']) ? $defaults['_controller'] : 'unknown';

            if (is_object($controller)) {
                $controller = get_class($controller);
            } elseif ($this->container->hasDefinition($controller)) {
                $controllerDefinition = $this->container->findDefinition($controller);
                /*  @var $controllerDefinition \Symfony\Component\DependencyInjection\Definition */
                $controller = '@'.$controller.' - '.$controllerDefinition->getClass();
            }

            $routes[$routeName] = array(
                'name' => $routeName,
                'pattern' => $route->getPattern(),
                'controller' => $controller,
                'method' => isset($requirements['_method']) ? $requirements['_method'] : 'ANY',
                'action' => isset($defaults['_action']) ? $defaults['_action'] : 'n/a',
            );
        }
        ksort($routes);
        $this->data['matchRoute'] = $request->attributes->get('_route');
        $this->data['routes'] = $routes;

        $this->data['resources'] = array();

        // get BB route sources
        foreach ($this->container->get('bbapp')->getConfig()->getDebugData() as $configFile => $configData) {
            if ('route.yml' == basename($configFile)) {
                $this->data['resources'][$configFile] = $configData;
            } elseif (array_key_exists('route', $configData)) {
                $this->data['resources'][$configFile] = $configData['route'];
            }
        }

        // get bundle route sources
        foreach ($this->container->get('bbapp')->getBundles() as $bundle) {
            foreach ($bundle->getConfig()->getDebugData() as $configFile => $configData) {
                if ('route.yml' == basename($configFile)) {
                    $this->data['resources'][$configFile] = $configData;
                } elseif (array_key_exists('route', $configData)) {
                    $this->data['resources'][$configFile] = $configData['route'];
                }
            }
        }
    }

    /**
     * Returns the Amount of Routes
     *
     * @return integer Amount of Routes
     */
    public function getRouteCount()
    {
        return count($this->data['routes']);
    }

    /**
     * Returns the Matched Routes Information
     *
     * @return array Matched Routes Collection
     */
    public function getMatchRoute()
    {
        return $this->data['matchRoute'];
    }

    /**
     * Returns the Resources Information
     *
     * @return array Resources Information
     */
    public function getResources()
    {
        return $this->data['resources'];
    }

    /**
     * Returns the Amount of Ressources
     *
     * @return integer Amount of Ressources
     */
    public function getResourceCount()
    {
        return count($this->data['resources']);
    }

    /**
     * Returns all the Routes
     *
     * @return array Route Information
     */
    public function getRoutes()
    {
        return $this->data['routes'];
    }

    /**
     * Returns the Time
     *
     * @return int Time
     */
    public function getTime()
    {
        $time = 0;

        return $time;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'routing';
    }
}