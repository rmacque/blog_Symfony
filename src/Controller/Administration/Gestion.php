<?php

namespace App\Controller\Administration;

use App\Entity\Article;
use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/administration", name  :"administration_")]
class Gestion extends AbstractController
{
    #[Route("", name : "")]
    public function index(ManagerRegistry $doctrine): Response
    {
        $listCategories = $doctrine->getRepository(Categorie::class)->findAll();
        $listArticles = $doctrine->getRepository(Article::class)->findAll();
        return $this->render("administration/index.html.twig",[
            "articles" => $listArticles,
            "categories" => $listCategories
        ]);
    }
}