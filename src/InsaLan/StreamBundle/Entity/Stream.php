<?php

namespace InsaLan\StreamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use InsaLan\TournamentBundle\Entity\Tournament;

/**
 * Stream
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="InsaLan\StreamBundle\Entity\StreamRepository")
 */
class Stream
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="streamer", type="string", length=255)
     */
    private $streamer;

    /**
     * @var string
     *
     * @ORM\Column(name="streamLink", type="string", length=255)
     */
    private $streamLink;

    /**
     * @var boolean
     *
     * @ORM\Column(name="official", type="boolean")
     */
    private $official;

    /**
     * @var boolean
     *
     * @ORM\Column(name="display", type="boolean")
     */
    private $display;

    /**
     * @ORM\ManyToOne(targetEntity="InsaLan\TournamentBundle\Entity\Tournament")
     */
    private $tournament;


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
     * Set streamer
     *
     * @param string $streamer
     * @return Stream
     */
    public function setStreamer($streamer)
    {
        $this->streamer = $streamer;

        return $this;
    }

    /**
     * Get streamer
     *
     * @return string
     */
    public function getStreamer()
    {
        return $this->streamer;
    }

    /**
     * Set streamLink
     *
     * @param string $streamLink
     * @return Stream
     */
    public function setStreamLink($streamLink)
    {
        $this->streamLink = $streamLink;

        return $this;
    }

    /**
     * Get streamLink
     *
     * @return string
     */
    public function getStreamLink()
    {
        return $this->streamLink;
    }

      /**
       * Set official
       *
       * @param boolean $official
       * @return Stream
       */
      public function setOfficial($official)
      {
          $this->official = $official;

        return $this;
    }

    /**
     * Get official
     *
     * @return boolean
     */
    public function getOfficial()
    {
        return $this->official;
    }
    
    /**
     * Set display
     *
     * @param boolean $display
     * @return Stream
     */
    public function setDisplay($display)
    {
        $this->display = $display;

      return $this;
    }

    /**
    * Get display
    *
    * @return boolean
    */
    public function getDisplay()
    {
      return $this->display;
    }

        /**
         * Set tournament
         *
         * @param InsaLan\TournamentBundle\Entity\Tournament $tournament
         * @return Stream
         */
        public function setTournament(\InsaLan\TournamentBundle\Entity\Tournament $tournament = null)
        {
            $this->tournament = $tournament;

            return $this;
        }

        /**
         * Get tournament
         *
         * @return InsaLan\TournamentBundle\Entity\Tournament
         */
        public function getTournament()
        {
            return $this->tournament;
        }

}
