<?php


namespace App\Controller\REST;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;


class ProfileController extends RESTController {
    private $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function patchAction(Request $request) {

        $indata = $request->request->all();

        if(!is_array($indata)) {
            $indata = [];
        }

        $user = $this->getUser();

        $this->userRepository->updateGuestData($user, $indata);
        return $this->Response(false, "", Response::HTTP_OK, []);
    }

    public function getAction() {
        //throw new HttpException(404, "Not implemented");
        $user = $this->getUser();
        $gdata = $this->userRepository->getGuestData($user);
        //var_dump($gdata);

        $data = [
            'first_name' => $gdata['name'],
            'last_name' => $gdata['lname'],
            'email' => $user->getUsername(),
            'rfid' => $gdata['rfid'],
            'title' => $gdata['job']

        ];

        return $this->Response(false, "", Response::HTTP_OK, $data);
    }
}