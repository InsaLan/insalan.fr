<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * GlobalVarsRepository
 *
 */
class InsaLanGlobalVarsRepository extends EntityRepository
{
    /**
    * Get GlobalVars matching with keys.
    *
    * @param Array $keys
    * @return Array
    */
    public function getGlobalVars(Array $keys)
    {
      $globalVars = array();

      foreach ($keys as $k) {
          $globVar = $this->findOneByGlobalKey($k);
          if ($globVar != null) { // Check to avoid exceptions if it doesn't exist.
            $globalVars[$k] = $globVar->getGlobalValue();
          } else {
              $globalVars[$k] = ""; // To avoid Twig exceptions.
          }
        }
        return $globalVars;
    }
}
