<?php

namespace App\DataFixtures\Provider;


class NextProvider

{
    // tableau de 35 tags
    private $tags = [
        'RPG',
        'Shooter',
        'FPS',
        'Strategy',
        'Sport',
        'MOBA',
        'MMORPG',
        'Adventure',
        'Arcade',
        'Shooter',
        'Die and retry',
        'Fantasy',
        'Openworld',
        '2D',
        'Third person',
        'Hack and slash/Beatem up',
        'High Score',
        'Boss fight', 
        'Die and retry',
        'racing',
        'fighting',
        '2D',
        'Platform',
        'exploration',
        'Online',
        'RTS',
        'TBS',
        'Pixel art',
        'Non fiction',
        'Action',
        'Quiz/Trivia',
        'visual novel',
        'Indie',
        'Multi',
        'Music',
    ];

    // tableau avec 14 Thèmes
    private $types = [
        'Science Fiction',
        'Fantasy',
        'Horreur',
        'Survival',
        'Sandbox',
        'Drama',
        'Comedy',
        'Historical',
        'Gestion',
        'Warfare',
        'Thriller',
        'Mystery',
        'Romance',
        'Stealth',
    ];


    // tableau avec 17 questions
    private $questions = [
        'Aimez-vous l-aventure ?',
        'Avez-vous un esprit de compétition ?',
        'Diriez-vous que vous êtes un stratège ?',
        'Est-ce que vous aimez être immergé dans une histoire passionnante ?',
        'En général, avez-vous peu de temps à consacrer au jeu ?',
        'Préférez-vous jouer avec d-autres joueurs ?',
        'Apprécieriez-vous un jeu qui défoule ?',
        'Aimeriez-vous découvrir des jeux originaux et peu connu ?',
        'Jouer-vous en soirée avec vos amis à des jeux fun et rapides  ?',
        'Est-ce que vous aimeriez incarner un personnage créer par vos soins ?',
        'Jouer-vous en soirée avec vos amis à des jeux fun et rapides  ?',
        'Avez-vous le rythme dans la peau ?',
        'Aimez-vous le challenge et vous dépasser ?',
        'Diriez-vous que vous préférez les jeux en perspective latérale ?',
        'Préférez-vous voir ce que voit votre personnage ?',
        'Pour vous est il important de voir votre personnage ?',
        'Est-ce essentiel pour vous d-avoir un système de progression dans votre jeu ?',
    ];

/**
 * Fonction PHP (array_rand) pour récuperer une ou plusieurs clés aléatoirement dans un tableau, on ne veut en récuperer qu'une seule.
 * On met le tableau en paramètres pour qu'une seule clé soit retournée.
 * On récupère un tag : 
 */

public function getTagTitle()

{
    return $this->tags[array_rand($this->tags)];
}


/** On retourne à nouveau une valeur aléatoire dans notre tableau Type */

public function getTypeTitle()
{
    return $this->types[array_rand($this->types)];
}

/****On retourne à nouveau une valeur aléatoire dans notre tableau Question */

public function getQuestionTitle()
{
    return $this->questions[array_rand($this->questions)];
}

}

