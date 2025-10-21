<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\WeatherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class WeatherController extends AbstractController
{
    #[Route('/weather/{city}/{country?}', name: 'app_weather', requirements: ['city' => '[a-zA-Z]+', 'country' => '[A-Z]{2}'])]
    public function city(string $city, ?string $country, EntityManagerInterface $entityManager, WeatherRepository $repository): Response
    {
        $criteria = ['city' => $city];
        if ($country !== null) {
            $criteria['country'] = $country;
        }

        $location = $entityManager->getRepository(Location::class)->findOneBy($criteria);

        $weather = $repository->findByLocation($location);
        dump($location, $weather);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'weather' => $weather,
        ]);
    }
}
