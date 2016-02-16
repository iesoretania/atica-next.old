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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                'label' => 'form.username',
                'required' => true
            ))
            ->add('reference', null, array(
                'label' => 'form.reference',
                'property_path' => 'person.reference',
                'disabled' => !$options['admin']
            ))
            ->add('firstName', null, array(
                'label' => 'form.first_name',
                'property_path' => 'person.firstName',
                'required' => true
            ))
            ->add('lastName', null, array(
                'label' => 'form.last_name',
                'property_path' => 'person.lastName',
                'required' => true
            ))
            ->add('displayName', null, array(
                'label' => 'form.display_name',
                'property_path' => 'person.displayName',
                'required' => true
            ))
            ->add('gender', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'label' => 'form.gender',
                'property_path' => 'person.gender',
                'choices_as_values' => true,
                'choices' => [
                    'form.gender_unknown' => Person::GENDER_UNKNOWN,
                    'form.gender_male' => Person::GENDER_MALE,
                    'form.gender_female' => Person::GENDER_FEMALE
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))
            ->add('initials', null, array(
                'label' => 'form.initials',
                'property_path' => 'person.initials',
                'required' => false
            ))
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', [
                'label' => 'form.email',
                'required' => true
            ]);

        if ($options['admin']) {
            $builder
                ->add('enabled', null, [
                    'label' => 'form.enabled',
                    'disabled' => $options['me'],
                    'required' => false
                ])
                ->add('globalAdministrator', null, [
                    'label' => 'form.global_administrator',
                    'required' => false,
                    'disabled' => $options['me']
                ]);
        }

        if (!$options['new']) {
            $builder
                ->add('submit', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
                    'label' => 'form.submit',
                    'attr' => ['class' => 'btn btn-success']
                ]);

            if (!$options['admin'] && $options['me']) {
                $builder
                    ->add('oldPassword', 'Symfony\Component\Form\Extension\Core\Type\PasswordType', [
                        'label' => 'form.old_password',
                        'required' => false,
                        'mapped' => false,
                        'constraints' => new UserPassword([
                            'groups' => ['password']
                        ])
                    ]);
            }
        }

        if ($options['admin'] || $options['me']) {
            $builder
                ->add('newPassword', 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', [
                    'label' => 'form.new_password',
                    'required' => false,
                    'type' => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
                    'mapped' => false,
                    'invalid_message' => 'password.no_match',
                    'first_options' => [
                        'label' => 'form.new_password',
                        'constraints' => [
                            new Length([
                                'min' => 7,
                                'minMessage' => 'password.min_length',
                                'groups' => ['password']
                            ]),
                            new NotNull([
                                'groups' => ['password']
                            ])
                        ]
                    ],
                    'second_options' => [
                        'label' => 'form.new_password_repeat'
                    ]
                ])
                ->add('changePassword', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                    'label' => 'form.change_password',
                    'attr' => array('class' => 'btn btn-success'),
                    'validation_groups' => array('Default', 'password')
                ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'IesOretania\AticaCoreBundle\Entity\User',
            'translation_domain' => 'user',
            'admin' => false,
            'new' => false,
            'me' => false
        ]);
    }
}
