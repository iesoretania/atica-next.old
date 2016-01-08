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

use Gedmo\Tree\Entity\MappedSuperclass\AbstractClosure;
use Gedmo\Tree\Entity\Repository\ClosureTreeRepository;

class ElementRepository extends ClosureTreeRepository
{
    /**
     * @see getParentsQueryBuilder
     */
    public function parentsQueryBuilder($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);

        $qb = $this->getQueryBuilder();
        if ($node !== null) {
            if ($node instanceof $meta->name) {
                if (!$this->_em->getUnitOfWork()->isInIdentityMap($node)) {
                    throw new InvalidArgumentException('Node is not managed by UnitOfWork');
                }

                $where = 'c.descendant = :node AND ';

                $qb->select('c, node')
                    ->from($config['closure'], 'c')
                    ->innerJoin('c.ancestor', 'node');

                if ($direct) {
                    $where .= 'c.depth = 1';
                } else {
                    $where .= 'c.ancestor <> :node';
                }

                $qb->where($where);

                if ($includeNode) {
                    $qb->orWhere('c.descendant = :node AND c.ascestor = :node');
                }
            } else {
                throw new \InvalidArgumentException('Node is not related to this repository');
            }
        } else {
            $qb->select('node')
                ->from($config['useObjectClass'], 'node');
            if ($direct) {
                $qb->where('node.'.$config['parent'].' IS NULL');
            }
        }

        if ($sortByField) {
            if ($meta->hasField($sortByField) && in_array(strtolower($direction), array('asc', 'desc'))) {
                $qb->orderBy('node.'.$sortByField, $direction);
            } else {
                throw new InvalidArgumentException("Invalid sort options specified: field - {$sortByField}, direction - {$direction}");
            }
        }

        if ($node) {
            $qb->setParameter('node', $node);
        }

        return $qb;
    }
    /**
     * @see getParentsQuery
     */
    public function parentsQuery($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        return $this->parentsQueryBuilder($node, $direct, $sortByField, $direction, $includeNode)->getQuery();
    }

    /**
     * @see getParents
     */
    public function parents($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        $result = $this->parentsQuery($node, $direct, $sortByField, $direction, $includeNode)->getResult();
        if ($node) {
            $result = array_map(function (AbstractClosure $closure) {
                return $closure->getAncestor();
            }, $result);
        }

        return $result;
    }

    /**
     * Get list of parents that ends on the given $node. This returns a QueryBuilder object
     *
     * @param object  $node        - if null, all tree nodes will be taken
     * @param boolean $direct      - true to take only direct children
     * @param string  $sortByField - field name to sort by
     * @param string  $direction   - sort direction : "ASC" or "DESC"
     * @param bool    $includeNode - Include the root node in results?
     *
     * @return \Doctrine\ORM\QueryBuilder - QueryBuilder object
     */
    public function getParentsQueryBuilder($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        return $this->parentsQueryBuilder($node, $direct, $sortByField, $direction, $includeNode);
    }

    /**
     * Get list of parents that ends on the given $node. This returns a Query object
     *
     * @param object  $node        - if null, all tree nodes will be taken
     * @param boolean $direct      - true to take only direct children
     * @param string  $sortByField - field name to sort by
     * @param string  $direction   - sort direction : "ASC" or "DESC"
     * @param bool    $includeNode - Include the root node in results?
     *
     * @return \Doctrine\ORM\Query - Query object
     */
    public function getParentsQuery($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        return $this->parentsQuery($node, $direct, $sortByField, $direction, $includeNode);
    }

    /**
     * Get list of parents that ends on the given $node
     *
     * @param object  $node        - if null, all tree nodes will be taken
     * @param boolean $direct      - true to take only direct children
     * @param string  $sortByField - field name to sort by
     * @param string  $direction   - sort direction : "ASC" or "DESC"
     * @param bool    $includeNode - Include the root node in results?
     *
     * @return array - list of given $node parents, null on failure
     */
    public function getParents($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        return $this->parents($node, $direct, $sortByField, $direction, $includeNode);
    }
}
