<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PinsController extends AbstractController
{

    public function index(PinRepository $pinRepository): Response
    {
        // // // ordonne par date de creation descendante
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }

    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ])
            // $form = $this->createFormBuilder($pin)
            // ->add('title', TextType::class)
            // ->add('description', TextareaType::class)
            // ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pin = $form->getData();
            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function show(Pin $pin): Response
    {
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    public function edit(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PinType::class, $pin)
            // $form = $this->createFormBuilder($pin)
            //     ->add('title', TextType::class)
            //     ->add('description', TextareaType::class)
            //     ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView()
        ]);
    }

    public function delete(Pin $pin, EntityManagerInterface $em): Response
    {
        $em->remove($pin);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }
}
