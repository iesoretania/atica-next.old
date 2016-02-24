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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnumerationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['is_module']) {
            $builder
                ->add('name', null, [
                    'label' => 'form.name',
                    'required' => true,
                    'disabled' => true
                ])
                ->add('module', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                    'label' => 'form.module',
                    'disabled' => true,
                    'property_path' => 'module.description'
                ]);
        }

        $builder
            ->add('description', null, [
                'label' => 'form.description',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'IesOretania\AticaCoreBundle\Entity\Enumeration',
            'translation_domain' => 'enumeration',
            'is_module' => false
        ]);
    }
}
