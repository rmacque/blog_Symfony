<?php

namespace App\Controller\Doctrine;

use App\Entity\Article;
use App\Entity\Categorie;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/article", name:"article_")]
class ArticleController extends AbstractController
{

    #[Route('', name: '')]
    public function index(ManagerRegistry $doctrine): Response
    {
        return $this->render('article/aperçu.html.twig', [
            "titre" => "Tous les articles",
            "listeArticles" => $doctrine->getRepository(Article::class)->findBy([], ["nomArticle" => "ASC"]),
            "page" => 0,
            "par_page" => 100,
            "total" => count($doctrine->getRepository(Article::class)->findAll())
        ]);
    }

    #[Route('/aperçu{page}', name: 'aperçu')]
    public function listeArticles(ManagerRegistry $doctrine, int $page = 0): Response
    {
        $par_page = 8;

        return $this->render('article/aperçu.html.twig', [
            "titre" => "Les derniers articles",
            "listeArticles" => $doctrine->getRepository(Article::class)->findBy([], ["date_creation" => "DESC"], $par_page, $page * $par_page),
            "page" => $page,
            "par_page" => $par_page,
            "total" => count($doctrine->getRepository(Article::class)->findAll())
        ]);
    }

    #[Route("/creation", name:"creation")]
    public function createArticle(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response|RedirectResponse
    {
        $listCategories = $doctrine->getRepository(Categorie::class)->findAll();
        $form = $this->createFormBuilder(null)
            //Pour associer une URL au formulaire et la méthode d'envoi
            //->setAction($this->generateUrl("page_validation"))
            //->setMethod("GET")
            ->add('nomArticle', TextType::class, ['label' => 'Titre de l\'article : '])
            ->add('contenu', TextareaType::class, ['label' => 'Contenu : '])
            ->add("categories", ChoiceType::class, [
                "choices" => $listCategories,
                "expanded" => true,
                "multiple" => true,
                "label" => "Catégories : ",
                "choice_label" => "nomCategorie",
                "choice_value" => "nomCategorie",
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer l\'article'])
            ->getForm();
        
        $form->handleRequest($request);

        $errorsString = ""; 
        if ($form->isSubmitted()) {
            // $form->getData() holds the submitted values
            $data = $form->getData();
            $article = new Article();
            $article->setNomArticle($data["nomArticle"])->setContenu($data["contenu"])->setDateCreation(new DateTime());

            foreach($data["categories"] as $categorie){
                $article->addCategory($categorie);
            }

            $errors = $validator->validate($article);

            if(count($errors) > 0){
                /*
                * Uses a __toString method on the $errors variable which is a
                * ConstraintViolationList object. This gives us a nice string
                * for debugging.
                */
                //$errorsString = (string) $errors;
                $errorsString = $errors;
            }else{
                // ... perform some action, such as saving the article to the database
                $entityManager = $doctrine->getManager();
                $entityManager->persist($article);// tell Doctrine you want to (eventually) save the article (no queries yet)
                $entityManager->flush();          // actually executes the queries (i.e. the INSERT query)

                return $this->redirectToRoute("article_aperçu");
            }
        }

        return $this->renderForm('article/creer.html.twig',[
            "formulaire" => $form,
            "erreurs" => $errorsString,
        ]);

    }

    #[Route("/edition-{id}", name:"edition")]
    public function edition(Request $request, ManagerRegistry $doctrine, int $id, ValidatorInterface $validator): Response|RedirectResponse
    {
        $entityManager = $doctrine->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);
        $select = [];
        foreach($article->getCategories() as $categorie){
            $select[] = $categorie;
            $article->removeCategory($categorie);
        }
        $listCategories = $doctrine->getRepository(Categorie::class)->findAll();

        $form = $this->createFormBuilder(null)
            //Pour associer une URL au formulaire et la méthode d'envoi
            //->setAction($this->generateUrl("page_validation"))
            //->setMethod("GET")
            ->add('nomArticle', TextType::class, ['label' => 'Titre de l\'article : ', "data" => $article->getNomArticle()])
            ->add('contenu', TextareaType::class, ['label' => 'Contenu : ', "data" => $article->getContenu()])
            ->add("categories", ChoiceType::class,[
                "label" => "Catégories : ",
                "choices" => $listCategories,
                "choice_value" => "nomCategorie",
                "choice_label" => "nomCategorie",
                "expanded" => true,
                "multiple" => true,
                "choice_attr" => 
                    function(?Categorie $category) use ($select) {
                        return in_array($category, $select) ? ["checked" => "checked"] : [];
                    }
            ])
            ->add('save', SubmitType::class, ['label' => 'Modifier l\'article'])
            ->getForm();

        $form->handleRequest($request);

        $errorsString = "";
        if ($form->isSubmitted()) {

            // $form->getData() holds the submitted values
            // but, the original `$article` variable has also been updated
            $data = $form->getData();
            $article->setNomArticle($data["nomArticle"])->setContenu($data["contenu"])->setDateCreation(new DateTime());
            foreach($data["categories"] as $categorie){
                $article->addCategory($categorie);
            }

            $errors = $validator->validate($article);

            if(count($errors) > 0){
                /*
                * Uses a __toString method on the $errors variable which is a
                * ConstraintViolationList object. This gives us a nice string
                * for debugging.
                */
                //$errorsString = (string) $errors;
                $errorsString = $errors;
            }else{
                //sauvegarde de l'article dans la BD
                $entityManager->flush();
                return $this->redirectToRoute('article_aperçu');
            }
        }

        return $this->renderForm('article/edition.html.twig',[
            "id" => $id,
            "formulaire" => $form,
            "erreurs" => $errorsString,
        ]);
    }

    #[Route("/details-{id}", name:"details")]
    public function details(ManagerRegistry $doctrine, int $id): Response
    {
        $article = $doctrine->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'Aucun article trouvé pour l\'id:'.$id
            );
        }

        return $this->render('article/details.html.twig', [
            'article' => $article
        ]);
    }

    #[Route("/suppression-{id}", name:"suppression")]
    public function suppression(ManagerRegistry $doctrine, int $id): RedirectResponse
    {
        $article = $doctrine->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'Aucun article trouvé pour l\'id:'.$id
            );
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('administration_');
    }

}
