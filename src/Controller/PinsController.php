<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class PinsController extends AbstractController
{

    public function index(PinRepository $pinRepository): Response
    {
        // todo : pagination
        // ...->paginate(Pin::NUM_ITEMS_PER_PAGE);


        // // // ordonne par date de creation descendante
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }
    /**
     * @Security("is_granted('ROLE_USER') && user.isVerified()")
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin)
            // $form = $this->createFormBuilder($pin)
            // ->add('title', TextType::class)
            // ->add('description', TextareaType::class)
            // ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin ajouté avec succès !');

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
    /**
     * @IsGranted("PIN_MANAGE", subject= "pin")
     */

    public function edit(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);
            // $form = $this->createFormBuilder($pin)
            //     ->add('title', TextType::class)
            //     ->add('description', TextareaType::class)
            //     ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Pin modifié avec succès !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView()
        ]);
    }
    /**
     * @IsGranted("PIN_MANAGE", subject= "pin")
     */
    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        // $this->denyAccessUnlessGranted('PIN_MANAGE', $pin); cette méthode peut remplacer celle dans le commentaire @isgranted
        if ($this->isCsrfTokenValid('pin_deletion_' . $pin->getId(), $request->get('csrf_token'))) {
            $em->remove($pin);
            $em->flush();

            $this->addFlash('info', 'Pin supprimé avec succès !');
        }

        return $this->redirectToRoute('app_home');
    }
}
