<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Repository\WeatherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/weather')]
final class WeatherController extends AbstractController
{
    // 1. Lista wszystkich miast + prognozy (główna strona)
    #[Route('/', name: 'app_weather_index')]
    public function index(WeatherRepository $weatherRepo): Response
    {
        // Pobieramy wszystkie rekordy pogodowe z relacją do lokalizacji
        $weatherRecords = $weatherRepo->createQueryBuilder('w')
            ->leftJoin('w.location', 'l')
            ->addSelect('l')
            ->orderBy('l.city', 'ASC')
            ->addOrderBy('w.date', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('weather/index.html.twig', [
            'weather_records' => $weatherRecords,
        ]);
    }

    // 2. Szczegóły prognozy dla konkretnego miasta (np. /weather/Szczecin/PL)
    #[Route('/{city}/{country?}', name: 'app_weather', requirements: ['city' => '[a-zA-Z]+', 'country' => '[A-Z]{2}'])]
    public function city(string $city, ?string $country, EntityManagerInterface $entityManager, WeatherRepository $repository): Response
    {
        $qb = $entityManager->createQueryBuilder();
        $qb->select('l')
            ->from(Location::class, 'l')
            ->where('LOWER(l.city) = LOWER(:city)')
            ->setParameter('city', $city);

        if ($country !== null) {
            $qb->andWhere('l.country = :country')
                ->setParameter('country', $country);
        }

        $location = $qb->getQuery()->getOneOrNullResult();

        if (!$location) {
            throw $this->createNotFoundException("Location not found: $city" . ($country ? ", $country" : ""));
        }

        $weather = $repository->findByLocation($location);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'weather'  => $weather,
        ]);
    }
}
