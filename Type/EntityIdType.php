<?php

namespace Gregwar\FormBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Kernel;

use Gregwar\FormBundle\DataTransformer\EntityToIdTransformer;

/**
 * Entity identitifer
 *
 * @author Gregwar <g.passault@gmail.com>
 */
class EntityIdType extends AbstractType
{
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ('2' == Kernel::MAJOR_VERSION && '2' < Kernel::MINOR_VERSION) {
            $em = $this->registry->getEntityManager($options['em']);
        } else {
            $em = $this->registry->getManager($options['em']);
        }

        $builder->addModelTransformer(new EntityToIdTransformer(
            $em,
            $options['class'],
            $options['property'],
            $options['query_builder'],
            $options['multiple']
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'class',
        ));

        $resolver->setDefaults(array(
            'em'            => null,
            'property'      => null,
            'query_builder' => null,
            'hidden'        => true,
            'multiple'      => false,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $entity = $form->getData();
        $title = null; // Entity title

        if ($entity && method_exists($entity, '__toString')) {
            $title = $entity->__toString();
        }

        $view->vars['title'] = $title;

        if (true === $options['hidden']) {
            $view->vars['type'] = 'hidden';
        }
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'entity_id';
    }
}
