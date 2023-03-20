<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route; //Pour un routage alternatif (C'est la manière conventionelle de créer des routes)
/*
Cette méthode requiert la dépendance : "composer require doctrine/annotations"
On peut aussi créer des routes dans le fichier /config/routes.yaml
Plus de détails :https://symfony.com/doc/current/routing.html
*/

/**
 * Pour definir des groupes de routes:
 * @Route("/routage_symfony", name="commencement_")
 * 
 * //Ainsi, toutes les urls des routes définies ici seront prefixés de "/routage_symfony" et les noms de route de "commencement_"
 */
class Routage extends AbstractController
{
    
    // Route avec paramètres:
    #[Route("/urlparams{param1}_{param2}", name:"urlparams")]
    // Chaque parametres doit avoir un nom unique
    public function urlParams(Request $request, string $param1 = "pas de parametres", string $param2 = "vraiment aucun"): Response //Le nom du parametre dans l'url doit correspondre avec celui de l'argument
    {
        $routeName = $request->attributes->get('_route');
        $routeParameters = $request->attributes->get('_route_params');

        $allAttributes = $request->attributes->all();

        return $this->render("pages/urlParams.html.twig",[
            "parametre1" => $param1,
            "parametre2" => $param2,
            "nomRoute" => $routeName,
            "paramsRoute" => implode(", ",$routeParameters),
            "all" => $allAttributes
        ]);
    }

}

/**
 * Il est meme possible de passer des objets en parametres de l'url, dependance: "composer require sensio/framework-extra-bundle"
 */