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

namespace AppBundle\Form\Type;

use AppBundle\Form\Model\ProfileElementModel;
use IesOretania\AticaCoreBundle\Entity\Person;
use IesOretania\AticaCoreBundle\Form\Type\UserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FullUserType extends UserType
{
    /**
     * {@inheritdoc}
     */
    public function additionalForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profiles', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => 'form.profiles',
                'mapped' => false,
                'expanded' => false,
                'multiple' => true,
                'choices_as_values' => true,
                'choices' => $options['profiles'],
                'choice_label' => function(ProfileElementModel $item) use ($options) {
                    return $item->toGenderString($options['user_gender']);
                },
                'choice_translation_domain' => false,
                'attr' => [
                    'class' => 'autocomplete'
                ]
            ]);
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'profiles' => [],
            'user_gender' => Person::GENDER_UNKNOWN
        ]);
    }
}
