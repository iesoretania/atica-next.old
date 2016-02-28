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

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use IesOretania\AticaCoreBundle\Entity\Element;
use IesOretania\AticaCoreBundle\Entity\ElementEdge;
use IesOretania\AticaCoreBundle\Entity\Enumeration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/listas")
 */
class AdminEnumerationController extends Controller
{
    /**
     * @Route("/", name="admin_enumerations", methods={"GET"})
     */
    public function enumerationIndexAction(Request $request)
    {
        // permitir acceso si es administrador local o si es administrador global
        if (!$this->get('atica.core_bundle.user.extension')->isUserLocalAdministrator()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $enumQuery = $em->createQuery('SELECT e FROM AticaCoreBundle:Enumeration e LEFT JOIN AticaCoreBundle:Module m WITH e.module = m WHERE e.organization = :org')
            ->setParameter('org', $this->get('atica.core_bundle.user.extension')->getCurrentOrganization());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $enumQuery,
            $request->query->getInt('page', 1),
            $this->getParameter('page.size'),
            [
                'defaultSortFieldName' => 'e.description',
                'defaultSortDirection' => 'asc'
            ]
        );

        return $this->render(':admin:manage_enumerations.html.twig',
            [
                'breadcrumb' => [
                    ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                    ['caption' => 'menu.admin.manage.enumerations', 'icon' => 'list-ol']
                ],
                'title' => null,
                'pagination' => $pagination
            ]);
    }

    /**
     * @Route("/nueva", name="admin_enumeration_new", methods={"GET", "POST"}, requirements={"enumeration": "\d+"} )
     * @Route("/{enumeration}", name="admin_enumeration_form", methods={"GET", "POST"}, requirements={"enumeration": "\d+"} )
     */
    public function editEnumerationAction(Request $request, Enumeration $enumeration = null)
    {
        if (null === $enumeration) {
            $enumeration = $this->getDoctrine()->getManager()->getRepository('AticaCoreBundle:Enumeration')->createNewEnumeration($this->get('atica.core_bundle.user.extension')->getCurrentOrganization());
        }

        $form = $this->createForm('IesOretania\AticaCoreBundle\Form\Type\EnumerationType', $enumeration, [
            'is_module' => $enumeration->getModule() !== null
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($request->request->has('delete') && !($enumeration->getModule() !== null)) {
                // Eliminar la enumeración de la base de datos
                $this->getDoctrine()->getManager()->remove($enumeration);
                try {
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', $this->get('translator')->trans('alert.deleted', [], 'enumeration'));
                }
                catch(\Exception $e) {
                    $this->addFlash('error', $this->get('translator')->trans('alert.not_deleted', [], 'enumeration'));
                }
            } else {
                // Guardar la enumeracion en la base de datos
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', $this->get('translator')->trans('alert.saved', [], 'enumeration'));
            }
            return $this->redirectToRoute('admin_enumerations');
        }

        $title = (null === $enumeration->getId()) ? $this->get('translator')->trans('form.create', [], 'enumeration') : $enumeration->getDescription();

        return $this->render('admin/form_enumeration.html.twig', [
            'form' => $form->createView(),
            'breadcrumb' => [
                ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                ['caption' => 'menu.admin.manage.enumerations', 'icon' => 'list-ol', 'path' => 'admin_enumerations'],
                ['fixed' => $title]
            ],
            'title' => $title,
            'enumeration' => $enumeration
        ]);
    }

    /**
     * @Route("/eliminar/{enumeration}", name="admin_enumeration_delete", methods={"GET", "POST"}, requirements={"enumeration": "\d+"} )
     * @Security("is_granted('manage', enumeration) and (enumeration.getModule() == null)")
     */
    public function deleteEnumerationAction(Request $request, Enumeration $enumeration)
    {
        if ('POST' === $request->getMethod() && $request->request->has('delete')) {
            // Eliminar la organización de la base de datos
            $this->getDoctrine()->getManager()->remove($enumeration);
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', $this->get('translator')->trans('alert.deleted', [], 'enumeration'));
            }
            catch(\Exception $e) {
                $this->addFlash('error', $this->get('translator')->trans('alert.not_deleted', [], 'enumeration'));
            }
            return $this->redirectToRoute('admin_enumerations');
        }

        $title = $enumeration->getDescription();

        $breadcrumb = [
            ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
            ['caption' => 'menu.admin.manage.profiles', 'icon' => 'street-view', 'path' => 'admin_profiles'],
            ['fixed' => $title, 'path' => 'admin_enumeration_form', 'options' => ['enumeration' => $enumeration->getId()]],
            ['caption' => 'menu.delete']
        ];

        return $this->render(':admin:delete_enumeration.html.twig', [
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'enumeration' => $enumeration
        ]);
    }

    /**
     * @Route("/{enumeration}/elementos", name="admin_enumeration", methods={"GET"})
     * @Security("is_granted('manage', enumeration)")
     */
    public function enumerationDetailAction(Enumeration $enumeration, Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $enumQuery = $em->createQuery('SELECT e FROM AticaCoreBundle:Element e WHERE e.enumeration = :enum')
            ->setParameter('enum', $enumeration);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $enumQuery,
            $request->query->getInt('page', 1),
            $this->getParameter('page.size'),
            [
                'defaultSortFieldName' => 'e.position',
                'defaultSortDirection' => 'asc'
            ]
        );

        return $this->render(':admin:manage_elements.html.twig',
            [
                'breadcrumb' => [
                    ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                    ['caption' => 'menu.admin.manage.enumerations', 'icon' => 'list-ol', 'path' => 'admin_enumerations'],
                    ['fixed' => $enumeration->getDescription()]
                ],
                'title' => $enumeration->getDescription(),
                'enumeration' => $enumeration,
                'pagination' => $pagination
            ]);
    }

    /**
     * @Route("/{enumeration}/elemento/nuevo", name="admin_element_new", methods={"GET", "POST"})
     * @Route("/elemento/{element}", name="admin_element_form", methods={"GET", "POST"})
     */
    public function elementDetailAction(Enumeration $enumeration = null, Element $element = null, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ('admin_element_new' === $request->get('_route')) {
            $element = new Element();
            $element->setEnumeration($enumeration);
            $new = true;
        } else {
            $new = false;
        }

        $this->denyAccessUnlessGranted('manage', $element->getEnumeration());

        $form = $this->createForm('AppBundle\Form\Type\ExtendedElementType', $element);

        $attributes = $element->getEnumeration()->getAttributes();

        foreach($attributes as $attribute) {
            if (!$new) {
                /**
                 * @var ElementEdge $attributeEdge
                 */
                $attributeEdge = $em->getRepository('AticaCoreBundle:ElementEdge')->getDirectEnumerationParentEdge($element,
                    $attribute->getTarget());
                $attributeElement = $attributeEdge ? $attributeEdge->getEndElement() : null;
            } else {
                $attributeElement = null;
            }
            $form->get('element' . $attribute->getTarget()->getId())->setData($attributeElement);
        }

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {

            if (null === $element->getId()) {
                $em->persist($element);
            }
            // borrar relaciones del antiguo elemento
            $em->getRepository('AticaCoreBundle:ElementEdge')->deleteEdges($element);
            $em->flush();

            // añadir nuevas relaciones
            foreach($attributes as $attribute) {
                $attributeElement = $form->get('element' . $attribute->getTarget()->getId())->getData();
                if ($attributeElement) {
                    $em->getRepository('AticaCoreBundle:ElementEdge')->addEdge($element, $attributeElement);
                }
            }

            // guardar los cambios
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans('alert.saved', [], 'element'));
            return $this->redirectToRoute('admin_enumeration', ['enumeration' => $element->getEnumeration()->getId()]);
        }

        $title = $new ? 'Nuevo elemento' : $element->getName();
        return $this->render(':admin:form_element.html.twig',
            [
                'breadcrumb' => [
                    ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
                    ['caption' => 'menu.admin.manage.enumerations', 'icon' => 'list-ol', 'path' => 'admin_enumerations'],
                    ['fixed' => $element->getEnumeration()->getDescription(), 'path' => 'admin_enumeration', 'options' => ['enumeration' => $element->getEnumeration()->getId()]],
                    ['fixed' => $title]
                ],
                'title' => $title,
                'form' => $form->createView(),
                'element' => $element,
                'new' => $new
            ]);
    }

    /**
     * @Route("/elemento/eliminar/{element}", name="admin_element_delete", methods={"GET", "POST"}, requirements={"enumeration": "\d+"} )
     * @Security("is_granted('manage', element.getEnumeration())")
     */
    public function deleteElementAction(Request $request, Element $element)
    {
        if ('POST' === $request->getMethod() && $request->request->has('delete')) {

            // Eliminar la organización de la base de datos
            $this->getDoctrine()->getManager()->remove($element);
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', $this->get('translator')->trans('alert.deleted', [], 'element'));
            }
            catch(\Exception $e) {
                $this->addFlash('error', $this->get('translator')->trans('alert.not_deleted', [], 'element'));
            }
            return $this->redirectToRoute('admin_enumeration', ['enumeration' => $element->getEnumeration()->getId()]);
        }

        $title = $element->getName();

        $breadcrumb = [
            ['caption' => 'menu.manage', 'icon' => 'wrench', 'path' => 'admin_menu'],
            ['caption' => 'menu.admin.manage.enumerations', 'icon' => 'list-ol', 'path' => 'admin_enumerations'],
            ['fixed' => $element->getEnumeration()->getDescription(), 'path' => 'admin_enumeration', 'options' => ['enumeration' => $element->getEnumeration()->getId()]],
            ['fixed' => $title, 'path' => 'admin_element_form', 'options' => ['element' => $element->getId()]],
            ['caption' => 'menu.delete']
        ];

        return $this->render(':admin:delete_element.html.twig', [
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'element' => $element
        ]);
    }
}
