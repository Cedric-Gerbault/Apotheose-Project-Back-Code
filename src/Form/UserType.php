<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\JsonType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            // ->add('roles') dans le UserController
            ->add('Birthdate', DateType::class,[
                    'widget'=>'single_text',
                    'input' =>'datetime'

            ])
            ->add('pseudo')
            ->add('platform', null, ['required' => false])
            ->add('avatar', null, ['required' => false])
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                // on récupère le user (les données du form)
                $user = $event->getData();
                // et le form, vu qu'on est dans une fonction anonyme
                $form = $event->getForm();

                // est-ce qu'on est en edit ou en ajout ?
                if(is_null($user->getId())) {
                    // ID null -> ajout d'un user

                    // on ajoute le champ password configuré pour l'ajout
                    $form->add('password', RepeatedType::class, [
                        'constraints' => [
                            new NotBlank(),
                            new Regex("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/", "Votre mot de passe ne respecte pas les règles de sécurité.")
                        ],
                        'type' => PasswordType::class,
                        'invalid_message' => 'Les deux mots de passe doivent être identiques !',
                        'first_options'  => [
                            'label' => 'Mot de passe',
                            'help' => 'Au moins 8 caractères, dont une lettre, un chiffre et un caractère spécial.'
                        ],
                        'second_options' => ['label' => 'Répétez votre mot de passe'],
                    ]);
                    

                } else {
                    // ID non null -> modification d'un user existant

                    // on ajoute le champ password configuré pour la modif
                    $form->add('password', RepeatedType::class, [
                        'constraints' => new Regex("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/", "Votre mot de passe ne respecte pas les règles de sécurité."),
                        'type' => PasswordType::class,
                        'invalid_message' => 'Les deux mots de passe doivent être identiques !',
                        'first_options'  => [
                            'label' => 'Mot de passe',
                            'help' => 'Laissez vide si inchangé.'
                        ],
                        'second_options' => ['label' => 'Répétez votre mot de passe'],
                    ]);
                }
            })
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
    $resolver->setDefaults([
        'data_class'      => User::class,
        // enable/disable CSRF protection for this form
        'csrf_protection' => false,
        // the name of the hidden HTML field that stores the token
        'csrf_field_name' => '_token',

    ]);
}

}