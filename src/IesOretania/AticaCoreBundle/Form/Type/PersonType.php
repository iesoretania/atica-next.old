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

namespace IesOretania\AticaCoreBundle\Form\Type;

use IesOretania\AticaCoreBundle\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', null, array(
                'label' => 'Referencia',
                'disabled' => !$options['admin']
            ))
            ->add('firstName', null, array(
                'label' => 'Nombre*',
                'required' => true
            ))
            ->add('lastName', null, array(
                'label' => 'Apellidos*',
                'required' => true
            ))
            ->add('displayName', null, array(
                'label' => 'Nombre visualizado*',
                'required' => true
            ))
            ->add('gender', ChoiceType::class, array(
                'label' => 'Sexo*',
                'choices_as_values' => true,
                'choices' => [
                    'Desconocido' => Person::GENDER_UNKNOWN,
                    'Hombre' => Person::GENDER_MALE,
                    'Mujer' => Person::GENDER_FEMALE
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))
            ->add('initials', null, array(
                'label' => 'Iniciales',
                'required' => false
            ));

        if ($options['admin']) {
            $builder
                ->add('description', null, array(
                    'label' => 'Observaciones',
                    'required' => false
                ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Person::class,
            'admin' => false
        ));
    }
}
