<?php

namespace Tests\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Tests\PlatformBundle\Repository\CategoryRepository;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $pattern = 'D%';

        $builder
            ->add('date', DateTimeType::class, [
                //'widget' => 'single_text',
                //'html5' => false,
                //'format' => 'dd-MM-yyyy',
            ])
            ->add('title', TextType::class)
            ->add('author', TextType::class)
            ->add('content', TextareaType::class)
            ->add('published', CheckboxType::class, ['required' => false,])
            ->add('image', ImageType::class, ['required' => false,])

            ->add('categories', EntityType::class, [
                    'class' => 'TestsPlatformBundle:Category',
                    'choice_label' => 'name',
                    'multiple' => true,
                    'query_builder' => function (CategoryRepository $repository) use ($pattern) {
                        return $repository->getLikeQueryBuilder($pattern);
                    }
            ])
            ->add('save', SubmitType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $advert = $event->getData();
            if ($advert === null) {
                return;
            }

            if (!$advert->getPublished() || $advert->getId() === null) {
                $event->getForm()->add('published', CheckboxType::class, ['required' => false]);
            } else {
                $event->getForm()->remove('published');
            }
        }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tests\PlatformBundle\Entity\Advert'
        ));
    }

    public function getBlockPrefix()
    {
        return 'tests_platformbundle_advert';
    }
}
