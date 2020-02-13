<?php


namespace App\Controller\REST;

use App\Service\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ParticipantController extends RESTController {
    private $event;

    public function __construct(Event $event) {
        $this->event = $event;
    }

    public function cgetAction() {
        return $this->Response(false, "", Response::HTTP_OK, $this->event->getParticipants());
    }
}