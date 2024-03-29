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

namespace IesOretania\AticaCoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ElementEdgeRepository
 *
 * Aquí se implementarán los métodos para acceder al DAG de elementos
 *
 * Algoritmo derivado de http://www.codeproject.com/Articles/22824/A-Model-to-Represent-Directed-Acyclic-Graphs-DAG-o
 */
class ElementEdgeRepository extends EntityRepository
{
    /**
     * Añadir una flecha
     *
     * @return bool
     */
    public function addEdge(Element $startElement, Element $endElement)
    {
        $em = $this->getEntityManager();

        // Comprobar si la flecha ya existe

        $criteria = [
            'startElement' => $startElement,
            'endElement' => $endElement,
            'hops' => 0
        ];

        if ($this->findOneBy($criteria)) {
            // indicar que la operación se ha realizado con éxito
            return true;
        }

        // Comprobar si se crearía una relación circular

        $criteria = [
            'startElement' => $endElement,
            'endElement' => $startElement
        ];

        if (($startElement === $endElement) || ($this->findOneBy($criteria))) {
            // indicar que la operación no puede realizarse (pensar si sería mejor lanzar una excepción)
            return false;
        }

        $edge = new ElementEdge();
        $edge
            ->setEntryEdge($edge)
            ->setDirectEdge($edge)
            ->setExitEdge($edge)
            ->setStartElement($startElement)
            ->setEndElement($endElement)
            ->setHops(0);

        $em->persist($edge);

        /* Paso 1: Flechas entrantes de A hacia B */
        $incomingEdges = $this->findBy(
            [
                'endElement' => $startElement
            ]
        );

        /** @var ElementEdge $currentEdge */
        foreach ($incomingEdges as $currentEdge) {
            $newEdge = new ElementEdge();
            $newEdge
                ->setEntryEdge($currentEdge)
                ->setDirectEdge($edge)
                ->setExitEdge($edge)
                ->setStartElement($currentEdge->getStartElement())
                ->setEndElement($endElement)
                ->setHops($currentEdge->getHops() + 1);
            $em->persist($newEdge);
        }

        /* Paso 2: A hacia las flechas salientes de B */
        $outcomingEdges = $this->findBy(
            [
                'startElement' => $endElement
            ]
        );

        foreach ($outcomingEdges as $currentEdge) {
            $newEdge = new ElementEdge();
            $newEdge
                ->setEntryEdge($edge)
                ->setDirectEdge($edge)
                ->setExitEdge($currentEdge)
                ->setStartElement($startElement)
                ->setEndElement($currentEdge->getEndElement())
                ->setHops($currentEdge->getHops() + 1);

            $em->persist($newEdge);
        }

        /* Paso 3: flechas entrantes de A hacia flechas salientes de B */
        $crossEdges = $em->createQuery('SELECT A, B FROM AticaCoreBundle:ElementEdge A, AticaCoreBundle:ElementEdge B WHERE A.endElement = :startElement AND B.startElement = :endElement')
            ->setParameter('startElement', $startElement)
            ->setParameter('endElement', $endElement)
            ->getResult();

        $crossEdges = array_chunk($crossEdges, 2, true);

        /** @var ElementEdge[] $currentEdges */
        foreach ($crossEdges as $currentEdges) {
            $edgeA = $em->find('AticaCoreBundle:ElementEdge', $currentEdges[0]);
            $edgeB = $em->find('AticaCoreBundle:ElementEdge', $currentEdges[1]);

            $newEdge = new ElementEdge();
            $newEdge
                ->setEntryEdge($edgeA)
                ->setDirectEdge($edge)
                ->setExitEdge($edgeB)
                ->setStartElement($edgeA->getStartElement())
                ->setEndElement($edgeB->getEndElement())
                ->setHops($edgeA->getHops() + $edgeB->getHops() + 1);

            $em->persist($newEdge);
        }

        $em->flush();

        return true;
    }

    /**
     * Borrar flecha
     *
     * @return bool
     */
    public function deleteEdge(Element $startElement, Element $endElement)
    {
        $em = $this->getEntityManager();

        // Comprobar si la flecha ya existe
        $criteria = [
            'startElement' => $startElement,
            'endElement' => $endElement,
            'hops' => 0
        ];

        $item = $this->findOneBy($criteria);

        if (!$item) {
            // no existe: indicar que la operación ha fallado
            return false;
        }
        $em->remove($item);

        return true;
    }

    /**
     * Borrar todas las flechas de $startElement
     *
     * @return bool
     */
    public function deleteEdges(Element $startElement)
    {
        $em = $this->getEntityManager();

        $criteria = [
            'startElement' => $startElement,
        ];

        $items = $this->findBy($criteria);

        if (!$items) {
            // no hay: indicar que la operación ha fallado
            return false;
        }

        $em->createQuery('DELETE FROM AticaCoreBundle:ElementEdge e WHERE e IN (:items)')
            ->setParameter('items', $items)
            ->execute();

        return true;
    }

    /**
     * Devuelve los elementos que dependen directamente de $startElement y son del tipo $enumeration
     * @param Element $startElement
     * @param Enumeration $enumeration
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDirectEnumerationParentEdge(Element $startElement, Enumeration $enumeration)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.endElement', 'el')
            ->where('e.startElement = :start')
            ->andWhere('el.enumeration = :enum')
            ->setParameter('start', $startElement)
            ->setParameter('enum', $enumeration)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
