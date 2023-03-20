<?php

namespace App\Controller\Administration;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('administration/connexion.html.twig', [
            "last_username" => $lastUsername,
            "error" => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route("/user/create", name:"user_create")]
    public function createUser(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
            //Pour associer une URL au formulaire et la méthode d'envoi
            //->setAction($this->generateUrl("page_validation"))
            //->setMethod("GET")
            ->add('pseudo', null, [
                'label' => 'Pseudo : '
            ])
            ->add("password", null, [
                "label" => "Mot de passe : ",
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer'])
            ->getForm();
        $form->handleRequest($request);

        $msg = ""; 
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setRoles(["ROLE_ADMIN"]);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            // ... perform some action, such as saving the article to the database
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);// tell Doctrine you want to (eventually) save the article (no queries yet)
            $entityManager->flush();          // actually executes the queries (i.e. the INSERT query)

            $msg = $user;
        }

        return $this->renderForm('administration/creer.html.twig',[
            "formulaire" => $form,
            "message" => $msg,
        ]);

    }

}
