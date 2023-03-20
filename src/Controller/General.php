<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class General extends AbstractController
{

    public function navbarCreate(): Response
    {
        $navbar = "";
        $navRoutes = [
            "Accueil" => "home",
            "Parametres url" => "commencement_urlparams",
            "Articles" => "article_aperçu",
            "Categories" => "categorie_",
            "Administration" => "administration_",
        ];
        foreach($navRoutes as $nomRoute => $route){
            $navbar .= "<a href=".$this->generateUrl($route).">$nomRoute</a>";
        }

        return new Response($navbar);
    }

    #[Route ("/", name:"home")]
    public function index(): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');

        return $this->render("/pages/home.html.twig",[
            "projectDir" => $projectDir,
            "Loto" => random_int(0, 100000)
        ]);
        /*
        $adminEmail = $this->getParameter("app.admin_email");
        return $this->render("/pages/home.html.twig",[
            "adminEmail" => $adminEmail
        ]);
        */
    }

}