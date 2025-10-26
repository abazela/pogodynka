<?php

namespace App\Form;

use App\Entity\Weather;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;

class WeatherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
                'attr' => ['placeholder' => 'YYYY-MM-DD'],
            ])
            ->add('celcius', NumberType::class, [
                'label' => 'Temperature (°C)',
                'attr' => ['placeholder' => 'e.g. 16.0'],
                'scale' => 1,
            ])
            ->add('windSpeed', NumberType::class, [
                'label' => 'Wind Speed (m/s)',
                'attr' => ['placeholder' => 'e.g. 6.0'],
                'scale' => 1,
            ])
            ->add('precipitation', NumberType::class, [
                'label' => 'Precipitation (mm)',
                'attr' => ['placeholder' => 'e.g. 1.5'],
                'scale' => 1,
            ])
            ->add('humidity', NumberType::class, [
                'label' => 'Humidity (%)',
                'attr' => ['placeholder' => 'e.g. 65'],
                'scale' => 0,
            ])
            ->add('location', null, [
                'label' => 'Location',
                'choice_label' => fn($loc) => "{$loc->getCity()}, {$loc->getCountry()}",
                'placeholder' => 'Select location',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Weather::class,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                return $data && $data->getId() ? ['edit'] : ['create'];
            },
        ]);
    }
}