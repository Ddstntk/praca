<?php
/**
 * PHP Version 5.6
 * Access type.
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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AccessType.
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
class AccessType extends AbstractType
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
            'role_id',
            ChoiceType::class,
            array(
                    'required' => false,
                    'label' => 'label.access',
                    'label_attr' => array('style' => 'white-space: nowrap;'),
                    'placeholder' => 'choose.label',
                    'choices'  => array(
                        'label.admin' => 1,
                        'label.user' => 2,
                    ),
                )
        )
            ->add(
                'name',
                TextType::class,
                [
                     'label' => 'label.name',
                     'required' => false,
                     'attr' => [
                         'max_length' => 45,
                     ],
                     'constraints' => [
                        new Assert\NotBlank(
                            ['groups' => ['user-default']]
                        ),
                        new Assert\Length(
                            [
                                 'groups' => ['user-default'],
                                 'min' => 1,
                                 'max' => 45,
                             ]
                        ),
                     ],
                 ]
            )
            ->add(
                'surname',
                TextType::class,
                [
                'label' => 'label.surname',
                'required' => false,
                'attr' => [
                    'max_length' => 45,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['user-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['user-default'],
                            'min' => 1,
                            'max' => 45,
                        ]
                    ),
                ],
                ]
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
        return 'access_type';
    }
}
