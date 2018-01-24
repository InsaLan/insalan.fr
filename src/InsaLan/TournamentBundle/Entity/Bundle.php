<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Bundle extends Registrable
{
    const TYPE = 'bundle';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Tournament", inversedBy="bundles")
     */
    protected $tournaments;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add tournaments
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $tournaments
     * @return Group
     */
    public function addTournament(\InsaLan\TournamentBundle\Entity\Tournament $tournaments)
    {
        $this->tournaments[] = $tournaments;

        return $this;
    }

    /**
     * Remove tournaments
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $tournaments
     */
    public function removeTournament(\InsaLan\TournamentBundle\Entity\Tournament $tournaments)
    {
        return $this->tournaments->removeElement($tournaments);

    }

    /**
     * Get tournaments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTournaments()
    {
        return $this->tournaments;
    }

    /**
     * Check if has tournament
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $tournaments
     * @return bool
     */
    public function hasTournament(\InsaLan\TournamentBundle\Entity\Tournament $tournament)
    {
        return $this->tournaments->contains($tournament);
    }

    /**
     * Get list of tournaments as string
     *
     * @return string
     */
    public function getTournamentsString()
    {
        return implode(', ', array_map(function($t) { return $t->getName(); }, $this->tournaments->toArray()));
    }

    /**
     * Get registration_limit : min of limit for each tournaments
     *
     * @return \integer
     */
    public function getRegistrationLimit()
    {
        $min = PHP_INT_MAX;

        foreach ($this->tournaments as $t)
            $min = min($min, $t->getRegistrationLimit());

        if ($this->registrationLimit > 0)
            $min = min($min, $this->registrationLimit);

        return $min;
    }

    /**
     * Get free slots : min of remaining slots on each tournaments
     *
     * @return \integer
     */
    public function getFreeSlots() {
        $min = PHP_INT_MAX;

        foreach ($this->tournaments as $t)
            $min = min($min, $t->getFreeSlots());

        if ($this->registrationLimit > 0)
            $min = min($min, $this->registrationLimit);

        return $min;
    }

    /**
     * Get validated slots : sum of validated slots on each tournaments
     *
     * @return \integer
     */
    public function getValidatedSlots()
    {
        $slots = 0;

        foreach ($this->tournaments as $t)
            $slots += $t->getValidatedSlots();
    }

}
