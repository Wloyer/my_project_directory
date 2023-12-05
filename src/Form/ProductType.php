<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Category;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('descriptions')
            ->add('prix')
            ->add('quantite')
            ->add('category', EntityType::class, [
                'class' => Category::class,
'choice_label' => 'id'])
            ->add('Envoyer', SubmitType ::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
