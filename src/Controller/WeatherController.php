<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Weather;
use App\Form\WeatherType;
use App\Repository\LocationRepository;
use App\Repository\WeatherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/weather')]
final class WeatherController extends AbstractController
{
    #[Route('/', name: 'app_weather_index', methods: ['GET'])]
    #[IsGranted('ROLE_WEATHER_INDEX')]
    public function index(WeatherRepository $weatherRepo): Response
    {
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


    #[Route('/{city}/{country?}', name: 'app_weather_city', requirements: ['city' => '[a-zA-Z]+', 'country' => '[A-Z]{2}'])]
    #[IsGranted('ROLE_WEATHER_INDEX')]
    public function show(string $city, ?string $country, LocationRepository $locationRepo, WeatherRepository $weatherRepo): Response
    {
        $location = $locationRepo->createQueryBuilder('l')
            ->where('LOWER(l.city) = LOWER(:city)')
            ->setParameter('city', $city)
            ->getQuery()
            ->getOneOrNullResult();

        if ($country !== null) {
            $location = $locationRepo->createQueryBuilder('l')
                ->where('LOWER(l.city) = LOWER(:city)')
                ->andWhere('l.country = :country')
                ->setParameter('city', $city)
                ->setParameter('country', $country)
                ->getQuery()
                ->getOneOrNullResult();
        }

        if (!$location) {
            throw $this->createNotFoundException("Location not found: $city" . ($country ? ", $country" : ""));
        }

        $weather = $weatherRepo->findBy(['location' => $location], ['date' => 'ASC']);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'weather'  => $weather,
        ]);
    }

    #[Route('/new', name: 'app_weather_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_WEATHER_NEW')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $weather = new Weather();
        $form = $this->createForm(WeatherType::class, $weather);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($weather);
            $entityManager->flush();
            return $this->redirectToRoute('app_weather_index');
        }

        return $this->render('weather/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_weather_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_WEATHER_EDIT')]
    public function edit(Request $request, Weather $weather, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WeatherType::class, $weather);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_weather_index');
        }

        return $this->render('weather/edit.html.twig', [
            'weather' => $weather,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_weather_delete', methods: ['POST'])]
    #[IsGranted('ROLE_WEATHER_DELETE')]
    public function delete(Request $request, Weather $weather, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $weather->getId(), $request->request->get('_token'))) {
            $entityManager->remove($weather);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_weather_index');
    }
}
