<?php


namespace MrkushalSharma\MediaManager\Form;


use MrkushalSharma\MediaManager\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',null, [
                'attr' =>[
                    'readonly'=>true
                ]
            ])
            ->add('url', TextType::class, [
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('caption')
            ->add('altName')
            ->add('description')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Media::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'media';
    }

}