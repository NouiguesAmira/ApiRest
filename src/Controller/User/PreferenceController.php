<?php
# src/AppBundle/Controller/User/PreferenceController.php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use App\Form\PreferenceType;
use App\Entity\Preference;

class PreferenceController extends Controller
{

    /**
     * @Rest\View()
     * @Rest\Get("/users/{id}/preferences")
     */
    public function getPreferencesAction(Request $request)
    {
        $user = $this->getDoctrine()->getManager()
                ->getRepository('App:User')
                ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            return $this->userNotFound();
        }

        return $user->getPreferences();
    }


     /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"preference"})
     * @Rest\Post("/users/{id}/preferences")
     */
    public function postPreferencesAction(Request $request)
    {
        $user = $this->getDoctrine()->getManager()
                ->getRepository('App:User')
                ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            return $this->userNotFound();
        }

        $preference = new Preference();
        $preference->setUser($user);
        $form = $this->createForm(PreferenceType::class, $preference);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($preference);
            $em->flush();
            return $preference;
        } else {
            return $form;
        }
    }

    private function userNotFound()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }
}
?>