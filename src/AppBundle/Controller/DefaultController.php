<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="frontpage")
     */
    public function indexAction()
    {
        return $this->render('default/frontpage.html.twig');
    }

    /**
     * @Route("/admin", name="admin_menu")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminIndexAction()
    {
        return $this->render('default/admin.html.twig');
    }
}
