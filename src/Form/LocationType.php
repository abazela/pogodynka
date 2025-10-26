<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', null, [
                'attr' => ['placeholder' => 'Enter city name'],
                'label' => 'City',
            ])
            ->add('country', ChoiceType::class, [
                'choices' => [
                    'Poland' => 'PL',
                    'Germany' => 'DE',
                    'France' => 'FR',
                    'Spain' => 'ES',
                    'Italy' => 'IT',
                    'United Kingdom' => 'GB',
                    'United States' => 'US',
                ],
                'label' => 'Country',
                'placeholder' => 'Select country',
            ])
            ->add('latitude', NumberType::class, [
                'attr' => ['placeholder' => 'e.g. 52.229676'],
                'label' => 'Latitude',
                'scale' => 7,
            ])
            ->add('longitude', NumberType::class, [
                'attr' => ['placeholder' => 'e.g. 21.012229'],
                'label' => 'Longitude',
                'scale' => 7,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                return $data && $data->getId() ? ['edit'] : ['create'];
            },
        ]);
    }
}
