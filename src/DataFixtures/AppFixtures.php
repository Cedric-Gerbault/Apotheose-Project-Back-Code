<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\nextProvider;
use App\Entity\Question;
use App\Entity\Tag;
use App\Entity\Type;
use App\Entity\Choice;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;





    class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void  
    {
    /** Les données n'étant pas en static, on instancie notre classe "Provider"
     * Creation d'une variable appelée "NextProvider"*/ 

    $nextProvider = new NextProvider();

    $choices = [
       
        1 => [
            'name'=> 'absolument', 
            'value'=> '+2'],

        2 => [
            'name'=> 'un peu', 
            'value'=> '+1'],

        3 => [
            'name'=> 'ne sais pas', 
            'value'=> '0'],

        4 => [
            'name'=> 'pas vraiment', 
            'value'=> '-1'],
            
        5 => [
            'name'=> 'Pas du tout', 
            'value'=> '-2'],

        ];

        foreach($choices as $key => $value){
            $choice = new Choice();

            $choice->setName($value['name']);
            $choice->setValue($value['value']);
            $date = new \DateTimeImmutable('@'.strtotime("now"));
            $choice->setCreatedAt($date);
            $manager->persist($choice);

        }

        $users = [
       
            1 => [
                'pseudo'=> 'admin', 
                'role'=> 'administrateur',
                'password'=> 'admin',
                'mail'=> 'admin@admin.com'],
    
            2 => [
                'pseudo'=> 'modo', 
                'role'=> 'modérateur',
                'password'=> 'modo',
                'mail'=> 'modo@modo.com'],

            3 => [
                'pseudo'=> 'utilisateur', 
                'role'=> 'user',
                'password'=> 'user',
                'mail'=> 'user@user.com'],

    
            ];
    
            foreach($users as $key => $value){
                $user = new User();
    
                $user->setPseudo($value['pseudo']);
                $user->setPassword($value['password']);
                $user->setRole($value['role']);
                $user->setMail($value['mail']);
                $date = new \DateTimeImmutable('@'.strtotime("now"));
                $user->setCreatedAt($date);
                $manager->persist($user);
    
            }
    
    /* creation de la table 20 tags!*/
    $tags = [];
    /* Tableau des tags deja utilisé (on stocke les titres dans un second tableau)*/
    //$tags_titles = [];
    
    for ($i = 1; $i <= 20; $i++) {
        $tag = new Tag();

        /* Si le tag a deja utilisé, on crée une variable
        -On récupere un titre de tag aléatoire */

        $tagTitle = $nextProvider->getTagTitle();

        /** On verifie avec une boucle (Si le titre du tag est deja présent dans mon tableau tag
         *alors on récupere un nouveau titre de tag ) */

       //     while(in_array($tagTitle, $tags_titles)) {
       //         $tagTitle = $nextProvider->getTagTitle(); }

        $tag->setName($tagTitle); 
        $tag->setApiId(mt_rand(1, 21));
        $manager->persist($tag);

        /* on stocke les tags dans des tableaux pour pouvoir les réutiliser plus tard*/
        $tags[] = $tag;
        //$tags_titles[] = $tag->getName();
    } 

    

    /* creation de la table 30 types!*/
    $types = [];
        /* Tableau des types deja utilisé (on stocke les titres dans un second tableau)*/
    //$types_titles = [];
    
        for ($i = 1; $i <= 30; $i++) {
        $type = new type();

            /* Si le type a deja utilisé, on crée une variable
            -On récupere un titre de type aléatoire*/

        $typeTitle = $nextProvider->getTypeTitle();

            /** On verifie avec une boucle (Si le titre du tag est deja présent dans mon tableau tag
             *alors on récupere un nouveau titre de tag ) */

        //while(in_array($typeTitle, $types_titles)) {
        //$typeTitle = $nextProvider->getTypeTitle(); }

        $type->setName($typeTitle); 
            $manager->persist($type);

            /* on stocke les types dans des tableaux pour pouvoir les réutiliser plus tard*/
        $types[] = $type;
        //$types_titles[] = $type->getName();
        } 


        /* create 15 questions! */
    
        $questions = [];
        /* Tableau des types deja utilisé (on stocke les titres dans un second tableau)*/
      //  $questions_titles = [];

    for ($i = 1; $i <= 15; $i++) {
        $question = new Question();

        /* Si le tag a deja utilisé, on crée une variable
        -On récupere un titre de tag aléatoire*/

        $questionTitle = $nextProvider->getquestionTitle();

        /** On évite une nouvelle fois le doublon avec un while  */

       // while(in_array($questionTitle, $questions_titles)) {
       //         $questionTitle = $nextProvider->getQuestionTitle(); }


        $date = new \DateTimeImmutable('@'.strtotime("now"));
        $question->setCreatedAt($date);
        $question->setContent($nextProvider->getQuestionTitle());

        $manager->persist($question);


    }   
        
        $manager->flush();

    }
}
