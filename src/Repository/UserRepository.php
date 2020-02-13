<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface {
    const TOKEN_SALT1 = "lknlkn3242pkada21231";
    const TOKEN_SALT2 = "xcbvopjkq2342g[pasda";

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function updateUser(UserInterface $user) {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $this->_em->persist($user);
        $this->_em->flush();
    }

    //TODO: move guestdata to proper repository and entities later
    public function getGuestData(UserInterface $user) {
        $conn = $this->_em->getConnection();
        $s = $conn->prepare("select * from guests where id = :id");
        $s->bindValue('id', $user->getGuestId());
        $s->execute();
        $result = $s->fetchAll();

        if($s->rowCount() == 0) {
            return null;
        }

        return $result[0];
    }

    public function updateGuestData(UserInterface $user, array $data) {
        $guestID = $user->getGuestId();

        if(!is_numeric($guestID)) {
            return false;
        }

        if(empty($data)) {
            return false;
        }

        $conn = $this->_em->getConnection();
        $qb = $conn->createQueryBuilder();
        $qb->update("guests");

        $params = ["guestID" => $guestID];

        foreach($data as $k => $v) {
            $paramNum = count($params)+1;
            switch($k) {
                case 'first_name':
                    $qb->set("name", ":firstName");
                    $params["firstName"] = $v;
                    break;
                case 'last_name':
                    $qb->set("lname", ":lastName");
                    $params["lastName"] = $v;
                    break;
                case 'email':
                    $qb->set("email", ":email");
                    $params["email"] = $v;
                    break;
                case 'title':
                    $qb->set("job", ":title");
                    $params["title"] = $v;
                    break;
                default:
                    continue;
            }
        }

        //return if no params
        if(count($params) == 1) {
            return false;
        }

        $qb->where("id = :guestID");
        $qb->setParameters($params);

        $qb->execute();
        return true;
    }

    public function generateToken(UserInterface $user) {
        return strtoupper(md5(md5($user->getId() . time() . self::TOKEN_SALT1) . uniqid() . self::TOKEN_SALT2));
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
