<?php
namespace App\Controller\Place;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\View\View; 
use FOS\RestBundle\View\ViewHandler;
use App\Form\PriceType;
use App\Entity\Price;
use App\Entity\Place;

class PriceController extends AbstractController
{
     

    /**
     * @Rest\View()
     * @Rest\Get("/places/{id}/prices")
     */
    public function getPricesAction(Request $request)
    {
        $place = $this->getDoctrine()->getManager()
                ->getRepository(Place::class)
                ->find($request->get('id')); 

        if (empty($place)) {
            return $this->placeNotFound();
        }
        //return $place;
        return $place->getPrices();
    }

     /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/places/{id}/prices")
     */
    public function postPricesAction(Request $request)
    {
        $place = $this->getDoctrine()->getManager()
                ->getRepository(Place::class)
                ->find($request->get('id'));
        /* @var $place Place */

        if (empty($place)) {
            return $this->placeNotFound();
        }

        $price = new Price();
        $price->setPlace($place); // Ici, le lieu est associé au prix
        $form = $this->createForm(PriceType::class, $price);

        // Le paramétre false dit à Symfony de garder les valeurs dans notre
        // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($price);
            $em->flush();
            return $price;
        } else {
            return $form;
        }
    }

    private function placeNotFound()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
    }
}
