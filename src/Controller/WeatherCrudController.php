<?php

namespace App\Controller;

use App\Entity\Weather;
use App\Form\WeatherType;
use App\Repository\WeatherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/weather')]
final class WeatherCrudController extends AbstractController
{
    #[Route('/', name: 'app_weather_index', methods: ['GET'])]
    public function index(WeatherRepository $weatherRepository): Response
    {
        return $this->render('weather_crud/index.html.twig', [
            'weather_records' => $weatherRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_weather_new', methods: ['GET', 'POST'])]
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

        return $this->render('weather_crud/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_weather_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Weather $weather, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WeatherType::class, $weather);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_weather_index');
        }

        return $this->render('weather_crud/edit.html.twig', [
            'weather' => $weather,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_weather_delete', methods: ['POST'])]
    public function delete(Request $request, Weather $weather, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$weather->getId(), $request->request->get('_token'))) {
            $entityManager->remove($weather);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_weather_index');
    }
}
