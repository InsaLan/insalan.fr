<?php
namespace InsaLan\TournamentBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use InsaLan\InsaLanBundle\API\Lol;

/**
 * @Annotation
 */
class ExistingLolId extends Constraint
{

    public $message_not_found = 'L\'invocateur %string% n\'a pas été trouvé';
    public $message_api_error = 'Erreur de l\'API. Veuillez réessayer.';


    public function validatedBy()
    {
            return 'existing_lol_id';
    }
}

class ExistingLolIdValidator extends ConstraintValidator {
    protected $apiLol;
    
    public function validate($value, Constraint $constraint)
    {
        try {
            $apiSummoner = $this->apiLol->getApi()->summoner();
            $rSummoner = $apiSummoner->info($value);
        } catch(\Exception $e) {
            $className = get_class($e);
            if ('GuzzleHttp\\Exception\\ClientException' === $className
                && 404 == $e->getResponse()->getStatusCode()) {
                $this->context->addViolation($constraint->message_not_found, array('%string%' => $value));
            } else if (0 === strpos($className, 'GuzzleHttp')) {
                $this->context->addViolation($constraint->message_api_error, array('%string%' => $value));
            } else {
                throw $e;
            }
        }
    }
    
    public function setLolApi(Lol $apiLol) {
        $this->apiLol = $apiLol;
    }
}
