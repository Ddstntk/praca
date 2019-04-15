<?php
/**
 * PHP Version 5.6
 * Chat exists validator.
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
namespace Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ChatExistsValidator.
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
class ChatExistsValidator extends ConstraintValidator
{

    /**
     * Validate chat id existence
     *
     * @param mixed      $value      Value
     * @param Constraint $constraint Constraint
     *
     * @return null
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint->repository) {
            return;
        }

        $result = $constraint->repository->findForExistence(
            $value,
            $constraint->FKidConversations
        );

        if (!$result) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ FKidConversations }}', $value)
                ->addViolation();
        }
    }
}
