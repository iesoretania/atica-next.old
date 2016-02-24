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

use Doctrine\ORM\EntityRepository;
use IesOretania\AticaCoreBundle\Entity\Enumeration;
use IesOretania\AticaCoreBundle\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /**
             * @var Profile profile
             */
            $profile = $event->getData();
            $form = $event->getForm();

            $form
                ->add('enumeration', null, [
                    'label' => 'form.enumeration',
                    'translation_domain' => null,
                    'placeholder' => 'form.enumeration_placeholder',
                    'choice_label' => function(Enumeration $enum) {
                        return $enum->getDescription();
                    },
                    'query_builder' => function(EntityRepository $er) use ($profile) {
                        return $er->createQueryBuilder('q')
                            ->where('q.organization = :org')
                            ->setParameter('org', $profile->getOrganization())
                            ->orderBy('q.description');
                    },
                    'required' => false,
                    'disabled' => (null !== $profile->getModule())
                ]);

            if (null !== $profile->getModule()) {
                $form
                    ->add('module', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                        'label' => 'form.module',
                        'disabled' => true,
                        'property_path' => 'module.description'
                    ])
                    ->add('code', null, [
                        'label' => 'form.code',
                        'required' => false,
                        'disabled' => true
                    ]);
            }
        });

        $builder
            ->add('nameNeutral', null, [
                'label' => 'form.name_neutral'
            ])
            ->add('nameMale', null, [
                'label' => 'form.name_male'
            ])
            ->add('nameFemale', null, [
                'label' => 'form.name_female',
            ])
            ->add('initials', null, [
                'label' => 'form.initials',
            ])
            ->add('description', null, [
                'label' => 'form.description',
                'required' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'IesOretania\AticaCoreBundle\Entity\Profile',
            'translation_domain' => 'profile',
            'module' => null
        ]);
    }
}
