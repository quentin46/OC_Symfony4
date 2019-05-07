<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Skill;

class LoadSkill extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Liste des noms de compétences à ajouter
        $names = array('PHP', 'Symfony', 'C++', 'Java', 'Photoshop', 'Blender', 'Bloc-Note');
        
        foreach ($names as $name) {
            $skill = new Skill();
            $skill->setName($name);
            
            $manager->persist($skill);
        }
        // $product = new Product();
        // $manager->persist($product);
        //On déclenche l'enregistrement
        $manager->flush();
    }
}
