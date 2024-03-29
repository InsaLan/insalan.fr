<?php
namespace App\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\TournamentRound;

class TournamentCRUD extends Controller
{
    public function createRoundAction(Request $request)
    {
        $id = $request->get($this->admin->getIdParameter());

        $match = $this->admin->getObject($id);

        if (!$match) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }


        $round = new TournamentRound();
        $round->setMatch($match);
        $match->addRound($round);

        $this->admin->create($round);

        $em = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $em->flush();

        foreach ($match->getParticipants() as $p) {
            $round->setScore($p, 0);
        }
        $em->persist($round);

        $this->admin->update($match);


        $this->addFlash('sonata_flash_success', 'Round ajouté');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}

?>
