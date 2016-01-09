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
use IesOretania\AticaCoreBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('reference', null, array(
                'property_path' => 'person.reference',
                'label' => 'reference',
                'disabled' => !$options['admin']
            ))
            ->add('firstName', null, array(
                'property_path' => 'person.firstName',
                'label' => 'firstName',
                'required' => true
            ))
            ->add('lastName', null, array(
                'property_path' => 'person.lastName',
                'label' => 'lastName',
                'required' => true
            ))
            ->add('displayName', null, array(
                'property_path' => 'person.displayName',
                'label' => 'displayName',
                'required' => true
            ))
            ->add('gender', ChoiceType::class, array(
                'property_path' => 'person.gender',
                'label' => 'gender',
                'choices_as_values' => true,
                'choices' => [
                    'gender.unknown' => Person::GENDER_UNKNOWN,
                    'gender.male' => Person::GENDER_MALE,
                    'gender.female' => Person::GENDER_FEMALE
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))
            ->add('initials', null, array(
                'property_path' => 'person.initials',
                'label' => 'initials',
                'required' => false
            ));

        if ($options['admin']) {
            $builder
                ->add('description', null, array(
                    'property_path' => 'person.description',
                    'label' => 'description',
                    'required' => false
                ));
        }

        $builder
            ->add('email', EmailType::class, [
                'label' => 'email',
                'required' => true
            ]);

        if ($options['admin']) {
            $builder
                ->add('enabled', null, [
                    'label' => 'enabled',
                    'required' => false
                ])
                ->add('globalAdministrator', null, [
                    'label' => 'globalAdministrator',
                    'required' => false,
                    'disabled' => $options['me']
                ]);
        }

        if (!$options['new']) {
            $builder
                ->add('submit', SubmitType::class, [
                    'label' => 'submit',
                    'attr' => ['class' => 'btn btn-success']
                ]);

            if (!$options['admin']) {
                $builder
                    ->add('oldPassword', PasswordType::class, [
                        'label' => 'Contraseña antigua',
                        'required' => false,
                        'mapped' => false,
                        'constraints' => new UserPassword([
                            'groups' => ['password']
                        ])
                    ]);
            }
        }

        $builder
            ->add('newPassword', RepeatedType::class, [
                'label' => 'newPassword',
                'required' => false,
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'password.no_match',
                'first_options' => [
                    'label' => 'newPassword',
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
                    'label' => 'newPassword.repeat'
                ]
            ])
            ->add('changePassword', SubmitType::class, array(
                'label' => 'changePassword',
                'attr' => array('class' => 'btn btn-success'),
                'validation_groups' => array('Default', 'password')
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'user',
            'admin' => false,
            'new' => false,
            'me' => false
        ]);
    }
}
