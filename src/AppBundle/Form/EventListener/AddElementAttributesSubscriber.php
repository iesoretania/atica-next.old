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

namespace AppBundle\Form\EventListener;

use Doctrine\ORM\EntityRepository;
use IesOretania\AticaCoreBundle\Entity\Attribute;
use IesOretania\AticaCoreBundle\Entity\Element;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddElementAttributesSubscriber implements EventSubscriberInterface
{
    private $placeholder;

    public function __construct($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(FormEvent $event)
    {
        /** @var Element $element */
        $element = $event->getData();
        $form = $event->getForm();

        $attributes = $element->getEnumeration()->getAttributes();

        /**
         * @var Attribute $attribute
         */
        foreach($attributes as $attribute) {
            $form->add('element' . $attribute->getTarget()->getId(), 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
                'mapped' => false,
                'label' => $attribute->getTarget()->getDescription(),
                'class' => 'IesOretania\AticaCoreBundle\Entity\Element',
                'placeholder' => $this->placeholder,
                'translation_domain' => false,
                'choice_translation_domain' => false,
                'query_builder' => function(EntityRepository $er) use ($attribute) {
                    return $er->createQueryBuilder('e')
                        ->where('e.enumeration = :enum')
                        ->setParameter('enum', $attribute->getTarget())
                        ->orderBy('e.position');
                }
            ]);
        }
    }
}
