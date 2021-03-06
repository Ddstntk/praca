<?php
/**
 * PHP Version 5.6
 * Edit type.
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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Validator\Constraints as CustomAssert;

/**
 * Class EditType
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
class EditType extends AbstractType
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
            'name',
            TextType::class,
            [
                'label' => 'label.name',
                'required' => false,
                'attr' => [
                    'max_length' => 45,
                    'placeholder' => $options['placeholders']['name'],
                ],
                'constraints' => [
                    new Assert\Length(
                        [
                            'groups' => ['user-default'],
                            'min' => 1,
                            'max' => 45,
                        ]
                    ),
                ],
            ]
        )->add(
            'surname',
            TextType::class,
            [
                'label' => 'label.surname',
                'required' => false,
                'attr' => [
                    'max_length' => 45,
                    'placeholder' => $options['placeholders']['surname'],
                ],
                'constraints' => [
                    new Assert\Length(
                        [
                            'groups' => ['user-default'],
                            'min' => 1,
                            'max' => 45,
                        ]
                    ),
                ],
            ]
        )->add(
            'email',
            EmailType::class,
            [
                'label' => 'label.email',
                'required' => false,
                'attr' => [
                    'max_length' => 32,
                    'placeholder' => $options['placeholders']['email'],
                ],
                'constraints' => [
                    new Assert\Length(
                        [
                            'min' => 8,
                            'max' => 32,
                        ]
                    ),
                    new CustomAssert\UniqueEmail(
                        ['groups' => ['user-default'],
                            'repository' =>
                                isset($options['user_repository']) ?
                                    $options['user_repository'] :
                                    null,
                            'email' =>
                                isset($options['data']['email']) ?
                                    $options['data']['email'] :
                                    null, ]
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
                'placeholders' => null,
                'validation_groups' => 'user-default',
                'user_repository' => null,
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
        return 'user_type';
    }
}
