<?php

namespace App\Controller\Doctrine;

use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/categorie", name:"categorie_")]
class CategorieController extends AbstractController
{
    #[Route('', name: '')]
    public function index(ManagerRegistry $doctrine): Response
    {
        return $this->render('categorie/aperçu.html.twig', [
            "titre" => "Toutes les catégories",
            "listeCategories" => $doctrine->getRepository(Categorie::class)->findAll()
        ]);
    }

    #[Route("/creation", name:"creation")]
    public function createCategorie(Request $request, ManagerRegistry $doctrine): Response
    {
        $categorie = new Categorie();

        $form = $this->createFormBuilder($categorie)
            //Pour associer une URL au formulaire et la méthode d'envoi
            //->setAction($this->generateUrl("page_validation"))
            //->setMethod("GET")
            ->add('nomCategorie', TextType::class, ['label' => 'Titre de la catégorie:'])
            ->add('description', TextareaType::class, ['label' => 'Descritpion :'])
            ->add('save', SubmitType::class, ['label' => 'Créer la catégorie'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$categorie = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            $this->redirectToRoute("categorie_");
        }

        return $this->renderForm('categorie/creer.html.twig',[
            "formulaire" => $form,
        ]);
    }

    #[Route("/edition-{id}", name:"edition")]
    public function edition(Request $request, ManagerRegistry $doctrine, int $id): Response|RedirectResponse
    {
        $categorie = $doctrine->getRepository(Categorie::class)->find($id);

        $form = $this->createFormBuilder($categorie)
            //Pour associer une URL au formulaire et la méthode d'envoi
            //->setAction($this->generateUrl("page_validation"))
            //->setMethod("GET")
            ->add('nomCategorie', TextType::class, ['label' => 'Titre de la catégorie:'])
            ->add('description', TextareaType::class, ['label' => 'Description:'])
            ->add('save', SubmitType::class, ['label' => 'Modifier la catégorie'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            $this->redirectToRoute("categorie_");
        }

        return $this->renderForm('categorie/edition.html.twig',[
            "categorie" => $categorie,
            "formulaire" => $form,
        ]);
    }

    #[Route('/suppression-{id}', name: 'suppression')]
    public function suppression(ManagerRegistry $doctrine, int $id): RedirectResponse
    {
        $categorie = $doctrine->getRepository(Categorie::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException(
                'Aucune catégorie trouvé pour l\'id:'.$id
            );
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($categorie);
        $entityManager->flush();

        return $this->redirectToRoute('administration_');
    }

}
