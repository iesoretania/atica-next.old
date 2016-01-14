<?php
/*
  ÁTICA - Aplicación web para la gestión documental de centros educativos

  Copyright (C) 2015-2016: Luis Ramón López López

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see [http://www.gnu.org/licenses/].
*/

namespace AppBundle\Listener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class RequestListener
{
    public function __construct(Router $router) {
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $session = $event->getRequest()->getSession();

        if (($session->get('organization_id', '') === '') && $event->isMasterRequest() && $event->getRequest()->hasSession()) {
            $route = $event->getRequest()->get('_route');
            if ($route && substr($route, 0, 3) !== 'log' && substr($route, 0, 1) !== '_') {;
                $session->set('_security.organization.target_path', $event->getRequest()->getUri());
                $event->setResponse(
                    new RedirectResponse($this->router->generate('login_organization'))
                );
            }
        }
    }
}
