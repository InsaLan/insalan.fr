<?php

namespace InsaLan\ArchivesBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * StreamRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StreamRepository extends EntityRepository
{
		public function findStreamsAlbumByEdition(\InsaLan\ArchivesBundle\Entity\Edition $edition) {
			$query = $this->_em->createQuery('SELECT DISTINCT p.album FROM InsaLanArchivesBundle:Stream p WHERE p.edition = :e')
			->setParameter('e', $edition);
			return $query->getResult();
		}
}
