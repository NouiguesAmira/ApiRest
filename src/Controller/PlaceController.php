<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View; 
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\PlaceType;
use App\Entity\Place;

class PlaceController extends AbstractController
{
    /**
     * @Rest\View()
     * @Rest\Get("/places")
     */
    public function getPlacesAction(Request $request)
    {
        $places = $this->getDoctrine()->getManager()
        ->getRepository(Place::class)
        ->findAll();
        return $places;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/places/{id}")
     */
    public function getPlaceAction($id, Request $request)
    {
        $place = $this->getDoctrine()->getManager()
        ->getRepository(Place::class)
        ->find($id);

        if (empty($place)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        } 
        return $place;
    }

    

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/places")
     */
    public function postPlacesAction(Request $request)
    {
        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);

        $form->submit($request->request->all()); // Validation des données

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();
            return $place;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/places/{id}")
     */
    public function removePlaceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository(Place::class)
                    ->find($request->get('id'));
        /* @var $place Place */

        $em->remove($place);
        $em->flush();
    }



    /**
     * @Rest\View()
     * @Rest\Put("/places/{id}")
     */
    public function updatePlaceAction(Request $request)
    {
        return $this->updatePlace($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/places/{id}")
     */
    public function patchPlaceAction(Request $request)
    {
        return $this->updatePlace($request, false);
    }

    private function updatePlace(Request $request, $clearMissing)
    {
        $place = $this->getDoctrine()->getManager()
                ->getRepository(Place::class)
                ->find($request->get('id')); 

        if (empty($place)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PlaceType::class, $place);

        // Le paramètre false dit à Symfony de garder les valeurs dans notre
        // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();
            return $place;
        } else {
            return $form;
        }
    }
}