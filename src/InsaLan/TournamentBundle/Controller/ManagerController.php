<?php

/**
 * ManagerController
 * All the stuff related to managers
 */

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use InsaLan\TournamentBundle\Form\SetManagerName;
use InsaLan\TournamentBundle\Form\TeamLoginType;
use InsaLan\TournamentBundle\Exception\ControllerException;

use InsaLan\TournamentBundle\Entity\Manager;
use InsaLan\TournamentBundle\Entity\Participant;
use InsaLan\TournamentBundle\Entity\Tournament;
use InsaLan\TournamentBundle\Entity\Team;
use InsaLan\TournamentBundle\Entity;

/**
* ManagerController
* All the stuff realted to managers management
* @Route("/manager")
*/
class ManagerController extends Controller
{

    /**
     * Create a new manager related to a tournament
     * @Route("/{tournament}/user/set")
     * @Template()
     */
    public function setNameAction(Request $request, Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();

        $manager = $em->getRepository('InsaLanTournamentBundle:Manager')->findOneByUserAndPendingTournament($usr, $tournament);

        if ($manager === null) {
            $manager = new Manager();
            $manager->setUser($usr);
            $manager->setTournament($tournament);
        }

        $form = $this->createForm(new SetManagerName(), $manager);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($manager);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('insalan_tournament_manager_jointeamwithpassword', array('tournament' => $tournament->getId()))
            );
        }

        return array('form' => $form->createView(), 'selectedGame' => $tournament->getType(), 'tournamentId' => $tournament->getId());
    }

    /**
     * Allow a new manager to join a team with name and password
     * @Route("/{tournament}/user/enroll")
     * @Template()
     */
    public function joinTeamWithPasswordAction(Request $request, Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();

        // handle only team tournaments
        if($tournament->getParticipantType() !== "team")
            throw new ControllerException("Équipes non acceptées dans ce tournois");

        // check if there is already a pending manager for this user and tournament
        $manager = $em->getRepository('InsaLanTournamentBundle:Manager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if($manager === null)
            return $this->redirect($this->generateUrl('insalan_tournament_manager_setname', array('tournament' => $tournament->getId())));

        $form_team = new Team();
        $form = $this->createForm(new TeamLoginType(), $form_team); // fill name and plainPassword
        $form->handleRequest($request);

        // inspired by UserController::joinExistingTeam
        // TODO rework this by putting the password hash into the request ?
        $error_details = null;
        if ($form->isValid()) {
            try {
                // hash password
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($usr);
                $form_team->setPassword($encoder->encodePassword($form_team->getPlainPassword(), sha1('pleaseHashPasswords'.$form_team->getName())));
                $team = $em
                    ->getRepository('InsaLanTournamentBundle:Manager')
                    ->findOneByNameAndTournament($form_team->getName(), $tournament);

                if ($team === null || $team->getTournament()->getId() !== $tournament->getId())
                    throw new ControllerException("Équipe invalide");

                if ($team->getPassword() === $form_team->getPassword()) {
                    // denied if there is already a manager in the team
                    if ($team->getManager() != null)
                        throw new ControllerException("L'équipe a déjà un manager");

                    $manager->setParticipant($team);
                    $team->setManager($manager);
                    $em->persist($manager);
                    $em->persist($team);
                    $em->flush();
                    return $this->redirect($this->generateUrl('insalan_tournament_manager_pay', array('tournament' => $tournament->getId())));

                } else
                    throw new ControllerException("Mot de passe invalide");
            } catch (ControllerException $e) {
                $error_details = $e->getMessage();
            }

        }
        return array('tournament' => $tournament, 'user' => $usr, 'manager' => $manager, 'error' => $error_details, 'form' => $form->createView());
    }

    /**
     * Allow a new manager to join a team with a specific token
     * @Route("/team/{team}/enroll/{authToken}")
     */
    public function joinTeamWithToken()
    {
        # code ...
    }

    /**
     * Payment for managers
     */
    public function payAction()
    {
        # code ...
    }

}

?>