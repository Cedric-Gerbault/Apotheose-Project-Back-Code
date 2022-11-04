<?php

namespace App\Controller\Admin;

use App\Entity\Question;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class QuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Question::class;
    }

    public function __toString(): string
    {
        return $this->content.' '.$this->priority;
    }

    public function configureFields(string $pageName): iterable
    {
            yield Field::new('content');
            yield Field::new('priority');
            yield AssociationField::new('choiceId');
            yield AssociationField::new('tagId');
            yield AssociationField::new('quizId');
            yield DateTimeField::new('createdAt')->hideOnForm();
            yield DateTimeField::new('updatedAt')->hideOnForm();
    }
}
