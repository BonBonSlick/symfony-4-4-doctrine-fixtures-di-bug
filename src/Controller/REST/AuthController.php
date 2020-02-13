<?php

namespace App\Controller\REST;

use App\Repository\EventCodeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @RouteResource("Auth", pluralize=false)
 */

class AuthController extends RESTController {
    private $userRepository;
    private $passwordEncoder;
    private $eventCodeRepository;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, EventCodeRepository $eventCodeRepository) {
        //parent::__construct();

        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->eventCodeRepository = $eventCodeRepository;
    }

    public function postAction(Request $request) {
        if(!$request->request->has("ecode") || !$request->request->has("email") || !$request->request->has("password")) {
            return $this->Response(true, "invalid arguments", Response::HTTP_BAD_REQUEST);
        }

        $indata = $request->request->all();

        //find eventID from event code
        $eventCode = $this->eventCodeRepository->findOneBy(['name' => $indata['ecode']]);

        if($eventCode === null) {
            return $this->Response(true, "bad event code", Response::HTTP_BAD_REQUEST);
        }


        //check user exists and verify password
        $user = $this->userRepository->findOneBy(['email' => $indata['email'], 'event_id' => $eventCode->getEventId()]);

        if($user === null) {
            return $this->Response(true, "could not find user", Response::HTTP_BAD_REQUEST);
        }

        //$user->setPassword($this->passwordEncoder->encodePassword($user,"testing123"));
        //$this->userRepository->updateUser($user);

        $match = $this->passwordEncoder->isPasswordValid($user, $indata['password']);

        if(!$match) {
            return $this->Response(true, "invalid password", Response::HTTP_BAD_REQUEST);
        }

        $token = $this->userRepository->generateToken($user);

        if(empty($token)) {
            $this->Response(true, "internal error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user->setToken($token);
        $this->userRepository->updateUser($user);

        return $this->Response(false, "", Response::HTTP_CREATED, [
            'token' => $token
        ]);
    }

    public function getAction() {
        throw new HttpException(404, "Not implemented");
        //return $this->Response(false, "", Response::HTTP_OK);
    }
}