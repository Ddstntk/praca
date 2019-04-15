<?php
/**
 * PHP Version 5.6
 * Post type.
 *
 * @category  Social_Network
 *
 * @author    Konrad Szewczuk <konrad3szewczuk@gmail.com>
 *
 * @copyright 2018 Konrad Szewczuk
 *
 * @license   https://opensource.org/licenses/MIT MIT license
 *
 * @link      cis.wzks.uj.edu.pl/~16_szewczuk
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PostType
 *
 * @category  Social_Network
 *
 * @author    Konrad Szewczuk <konrad3szewczuk@gmail.com>
 *
 * @copyright 2018 Konrad Szewczuk
 *
 * @license   https://opensource.org/licenses/MIT MIT license
 *
 * @link      cis.wzks.uj.edu.pl/~16_szewczuk
 */
class PostType extends AbstractType
{

    /**
     * Form builder
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array                $options Form options
     *
     * @return none
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'content',
            TextareaType::class,
            [
                'label' => 'label.content',
                'required' => true,
                'attr' => [
                    'rows' => 3,
                    'max_length' => 1000,
                    'style' => 'resize: none;',
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['post-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['post-default'],
                            'min' => 1,
                            'max' => 1000,
                        ]
                    ),
                ],
            ]
        )
            ->add(
                'visibility',
                ChoiceType::class,
                array(
                'label' => 'label.visibility',
                'choices'  => array(
                'label.friends' => 0,
                'label.everyone' => 1,
                ),
                )
            );
    }

    /**
     * Options configuration
     *
     * @param OptionsResolver $resolver Options Resolver
     *
     * @return none
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'post-default',
            ]
        );
    }

    /**
     * Get prefix
     *
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'post_type';
    }
}
