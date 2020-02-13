<?php


namespace App\Service;

use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class Event extends AbstractFOSRestController{
    //private $userRepository;
    //private $userAuthenticator;
    private $em;
    private $user;
    private $tokenStorage;
    private $security;

    public function __construct(Security $security, EntityManagerInterface $em) {
        $this->security = $security;
        $this->em = $em;
    }

    public function getParticipants() {
        $user = $this->security->getUser();

        if($user == null) {
            return [];
        }

        $eventId = $user->getEventId();

        if(!is_numeric($eventId)) {
            return [];
        }

        //get all memberlists
        $conn = $this->em->getConnection();
        $qb = $conn->createQueryBuilder();
        $qb->select("list_id")->from("events_lists")->where("event_id = ?")->setParameter(0,$eventId);
        $res = $qb->execute()->fetchAll();

        if(empty($res)) {
            return [];
        }


        $res = array_map(function($n) {
            return $n['list_id'];
        }, $res);
        //print_r($res);
        //die();

        //get all participants on all lists
        $qb = $conn->createQueryBuilder();
        $qb->select("name,lname,email,phone")->from("guests")->where("list IN (:ids)")->setParameter("ids", $res, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);
        echo $qb->getSQL();
        echo join(',',$res);
        $res = $qb->execute()->fetchAll();
        var_dump($res);

        return ["all" => $res];
    }

    public function getSpeakers() {

    }
}