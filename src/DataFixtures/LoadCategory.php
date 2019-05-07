<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Category;

class LoadCategory extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Liste des noms et catégories à ajouters
        $names = array (
            'Developpement web',
            'Développement mobile',
            'Graphisme',
            'Intégration',
            'Réseau'        
        );
        
        foreach ($names as $name) {
            //On crée la catégorie
            $category = new Category();
            $category->setName($name);
            
            //On la persiste
            $manager->persist($category);
        }
        
        //On déclenche l'enregistrement
        $manager->flush();
    }
}
