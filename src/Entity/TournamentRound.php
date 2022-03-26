<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\TournamentMatch;
use App\Entity\TournamentAbstractMatch;
use App\Entity\TournamentScore;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class TournamentRound
{
    const UPLOAD_PATH = 'uploads/tournament/replays/';
    const UPLOAD_EXT  = '.lrf';

    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="TournamentAbstractMatch", inversedBy="rounds")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $match;

    /**
     * @ORM\OneToMany(targetEntity="TournamentScore", mappedBy="round", cascade={"all"})
     */
    protected $scores;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $replay;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $data;

    
    public function __toString()
    {
        return "[" . implode(' - ', array_map(function($s) { return $s->__toString(); }, $this->scores->toArray())) . "]";
    }

    public function getTournament()
    {
        return $this->getMatch()->getTournament();
    }

    public function getGroupStage()
    {
        return $this->getMatch()->getGroupStage();
    }

    public function getGroup()
    {
        return $this->getMatch()->getGroup();
    }

    public function getExtraInfos()
    {   

        return $this->getMatch()->getExtraInfos();
    }

    // End Of Customs

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->scores = new \Doctrine\Common\Collections\ArrayCollection();
    }
    // Replay Upload management

    protected $replayFile;

    public function getReplayFile()
    {
        return $this->replayFile;
    }

    public function setReplayFile(UploadedFile $file = null)
    {
        $this->replayFile = $file;
        if($file === null)
            $this->setReplay(null);
        else
            $this->setReplay($this->getFileName());
    }

    /**
     * @ORM\PreRemove
     */
    public function onPreRemove()
    {
        $this->removeReplayFile($this->replay);
    }

     /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function uploadFile()
    {

        $this->removeReplayFile($this->oldReplay);

        if (null === $this->getReplayFile()) {
            return;
        }

        $this->getReplayFile()->move(
            self::UPLOAD_PATH,
            $this->getFileName()
        );

        $this->setReplayFile(null);
    }

    public function getFullReplay()
    {
        if(!$this->getReplay()) {
            return null;
        }

        return self::UPLOAD_PATH.$this->getReplay();
    }

    private function getFileName()
    {
        return sprintf("%s_M%d-R%d", date('Ymd-His'), $this->getMatch()->getId(), $this->getId()).self::UPLOAD_EXT;
    }

    private function removeReplayFile($name)
    {
        if(!$name) return;
        $name = self::UPLOAD_PATH.DIRECTORY_SEPARATOR.$name;
        if (file_exists($name))
            unlink($name);
    }
    // End of Replay Upload managemet

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
     * Set score
     *
     * @param \App\Entity\Participant $participant
     * @param integer $score
     * @return \App\Entity\TournamentRound
     */
    public function setScore(\App\Entity\Participant $participant, $score)
    {
        $scoreObj = $this->findScore($participant);

        if ($scoreObj !== null) {
            $scoreObj->setScore($scoreObj);
            return $this;
        }

        $scoreObj = new TournamentScore();
        $scoreObj->setRound($this);
        $scoreObj->setParticipant($participant);
        $scoreObj->setScore($score);

        $this->addScore($scoreObj);

        return $this;
    }

    /**
     * Add score
     *
     * @param \App\Entity\Participant $participant
     * @return integer
     */
    public function addScore(\App\Entity\TournamentScore $score)
    {
        $this->scores[] = $score;
        return $this;
    }

    /**
     * Get score
     *
     * @param \App\Entity\Participant $participant
     * @return integer
     */
    public function getScore(\App\Entity\Participant $participant)
    {
        $score = $this->findScore($participant);

        if ($score !== null) {
            return $score->getScore();
        }

        return 0;
    }

    /**
     * Has score
     *
     * @param \App\Entity\Participant $participant
     * @return bool
     */
    public function hasScore(\App\Entity\Participant $participant)
    {
        return $this->findScore($participant) !== null;
    }

    /**
     * Find score
     *
     * @param \App\Entity\Participant $participant
     * @return \App\Entity\TournamentScore $score
     */
    public function findScore(\App\Entity\Participant $participant)
    {
        foreach ($this->scores as $score) {
            if ($score->getParticipant()->getId() == $participant->getId()) {
                return $score;
            }
        }

        return null;
    }

    /**
     * Get scores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getScores()
    {
        return $this->scores;
    }

    /**
     * Set match
     *
     * @param TournamentAbstractMatch $match
     * @return \App\Entity\TournamentRound
     */
    public function setMatch(TournamentAbstractMatch $match = null)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match
     *
     * @return \App\Entity\TournamentAbstractMatch
     */
    public function getMatch()
    {
        return $this->match;
    }


    protected $oldReplay = ""; // for easy remove, not mapped
    /**
     * Set replay
     *
     * @param string $replay
     * @return \App\Entity\TournamentRound
     */
    public function setReplay($replay)
    {
        $this->oldReplay = $this->replay;
        $this->replay = $replay;

        return $this;
    }

    /**
     * Get replay
     *
     * @return string
     */
    public function getReplay()
    {
        return $this->replay;
    }


    /**
     * Set data
     *
     * @param string $data
     * @return \App\Entity\TournamentRound
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}
