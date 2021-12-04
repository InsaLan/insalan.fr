<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * StreamRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArchivesStreamRepository extends EntityRepository
{
		public function findStreamsAlbumByEdition(\App\Entity\ArchivesEdition $edition) {
			$query = $this->_em->createQuery('SELECT DISTINCT p.album FROM \App\Entity\ArchivesStream p WHERE p.edition = :e')
			->setParameter('e', $edition);
			return $query->getResult();
		}
}