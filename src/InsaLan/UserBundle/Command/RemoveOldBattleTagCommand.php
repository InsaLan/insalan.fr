<?php
namespace InsaLan\UserBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use InsaLan\UserBundle\Entity\User;

class RemoveOldBattleTagCommand extends ContainerAwareCommand
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:remove-old-battle-tag';
 
    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Remove old BattleTags.')
    
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Remove BattleTags older than 30 days.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $users = $em->getRepository('InsaLanUserBundle:User')->findAll();

        $ttl = (new \DateTime("now"))->modify("-30 day");
        foreach ($users as $user) {
            if ($user->getBattleTag() !== null && ($user->getBattleTagUpdatedAt() === null || $user->getBattleTagUpdatedAt() < $ttl) ) {
                $user->setBattleTag(null);
                $em->persist($user);
            }
        }
        $em->flush();
    }
}
