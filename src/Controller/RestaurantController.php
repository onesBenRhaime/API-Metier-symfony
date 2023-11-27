<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Form\RestaurantSearchType;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/restaurant')]
class RestaurantController extends AbstractController
{
    
#[Route('/search', name: 'ajax_search', methods: ['GET'])]
public function searchAction(Request $request, RestaurantRepository $charityDemandRepository): Response
{
    $em = $this->getDoctrine()->getManager();

    $requestString = $request->get('q');

    $charitydemands = $em->getRepository('App\Entity\Restaurant')->findEntitiesByString($requestString);

    if (!$charitydemands) {
        $result['charity_demands']['error'] = "NOT FOUND";
    } else {
        $result['charity_demands'] = $this->getRealEntities($charitydemands);
    }

    return new Response(json_encode($result));
}
public function getRealEntities($charitydemands)
{

    foreach ($charitydemands as $charitydemand) {
        $realEntities[$charitydemand->getIdRestau()] = $charitydemand->getNom();
    }
    return $realEntities;
}

  ////recherchee 
  #[Route('/view', name: 'app_charity_demand_View', methods: ['GET', 'POST'])]
  public function View(RestaurantRepository $charityDemandRepository,  Request $request): Response
  {
      $charitydemands = $charityDemandRepository->findAll();
      $form = $this->createForm(RestaurantSearchType::class);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $searchquery = $form->getData()['nom'];   
          $searchquery = $form->getData()['location'];         
          $charitydemands = $charityDemandRepository->search($searchquery);
      }
      return $this->render('restaurant/search.html.twig', [
          'charity_demands' => $charitydemands,
          'form' => $form->createView()
      ]);
  }

    #[Route('/', name: 'app_restaurant_index', methods: ['GET'])]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurantRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_restaurant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($restaurant);
            $entityManager->flush();

            return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route('/{id_restau}', name: 'app_restaurant_show', methods: ['GET'])]
    public function show(Restaurant $restaurant): Response { 
     
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }
   
    #[Route('/{id_restau}', name: 'app_restaurant_pdf', methods: ['GET'])]
    public function pdf(Restaurant $restaurant): Response { 
     
        return $this->render('restaurant/pdf.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/{id_restau}/edit', name: 'app_restaurant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route('/{idRestau}', name: 'app_restaurant_delete', methods: ['POST'])]
    public function delete(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getIdRestau(), $request->request->get('_token'))) {
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_restaurant_index', [], Response::HTTP_SEE_OTHER);
    }

  

}
