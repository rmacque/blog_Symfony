<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('nomArticle', null, [
            "label" => "Titre de l'article :",
        ]);

        $builder->add('contenu', TextareaType::class,[
            "label" => "Contenu :",
        ]);

        $builder->add('categories', CollectionType::class, [
            'entry_type' => ChoiceType::class,
            'entry_options' => [
                'label' => false,
                "choices" => [
                    "bla" => "bla" 
                ]
            ],
            "allow_add" => true,
            "allow_delete" => true,
            "by_reference" => false,
            "label" => "Catégories :",
        ]);

        $builder->add("save", SubmitType::class, ["label" => "Créer l'article"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class
        ]);
    }
}