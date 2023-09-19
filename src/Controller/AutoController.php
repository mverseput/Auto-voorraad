<?php

namespace App\Controller;

use App\Entity\Auto;
use App\Form\InsertType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AutoController extends AbstractController
{
    #[Route('/auto', name: 'app_auto')]
    public function showCars(ManagerRegistry $doctrine): Response
    {
        $auto = $doctrine->getRepository(Auto::class)->findAll();

        return $this->render('auto/auto.html.twig', ['auto' => $auto]);
    }

    #[Route('/details/{id}', name: 'app_detail')]
    public function showDetail(ManagerRegistry $doctrine, int $id): Response
    {
        $auto = $doctrine->getRepository(Auto::class)->findBy(['id' => $id]);

        return $this->render('auto/detail.html.twig', ['auto' => $auto, 'id' => $id]);
    }

    #[Route('/insert', name: 'app_insert')]
    public function insertAction(Request $request, EntityManagerInterface $entityManager)
    {

        $auto = new Auto();

        $form = $this->createForm(InsertType::class, $auto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $auto = $form->getData();

            $entityManager->persist($auto);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'The car has been added succesfully'
            );

            return $this->redirectToRoute('app_auto');
        }
        return $this->renderForm('auto/insert.html.twig', ['form' => $form]);
    }


    #[Route('/delete/{id}', name: 'app_delete')]
    public function deleteAction(Auto $auto, EntityManagerInterface $entityManager)
    {

        if (!$auto) {
            throw $this->createNotFoundException('Auto not found');
        }

        $entityManager->remove($auto);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'The car has been deleted successfully'
        );

        return $this->redirectToRoute('app_auto');
    }

    #[Route('/update/{id}', name: 'app_update')]
    public function updateAction(Auto $auto, Request $request, EntityManagerInterface $entityManager)
    {
        if (!$auto) {
            throw $this->createNotFoundException('Auto not found');
        }

        $form = $this->createForm(InsertType::class, $auto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'The car has been updated successfully'
            );

            return $this->redirectToRoute('app_auto');
        }

        return $this->renderForm('auto/insert.html.twig', ['form' => $form]);
    }
}