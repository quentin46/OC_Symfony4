<?php

namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Category;

class LoadCategory implements FixtureInterface
{
    //Dans l'argument de la méthode load, l'objet $managger est l'EntityManager
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