<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Borrow;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BorrowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('borrowDate', null, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'), // Date minimale : aujourd'hui
                ],
                'label' => "Date d'emprunt",
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Disponible' => "Non disponible",
                ]
            ])
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'Title',
                'label' => 'Titre',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Utilisateur',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Borrow::class,
        ]);
    }
}
