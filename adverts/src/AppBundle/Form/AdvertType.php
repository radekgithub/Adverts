<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class AdvertType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('photos', CollectionType::class, array(
                'entry_type' => PhotoType::class,
                'entry_options' => array(
                    'label' => false,
                ),
                'by_reference' => false,
                // this allows the creation of new forms and the prototype too
                'allow_add' => true,
                // this one allows the form to be removed
                'allow_delete' => true
            ))
            ->add('expiryDate', DateType::class, array(
                // renders it as a single text box
                'widget' => 'single_text'))
            ->add('category', null, ['choice_label' => 'name']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Advert'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_advert';
    }


}
