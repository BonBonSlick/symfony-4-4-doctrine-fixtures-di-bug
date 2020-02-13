<?php


namespace App\Controller\REST;


use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;

class RESTController extends AbstractFOSRestController implements ClassResourceInterface {
    public function Response($isError, $message, $code, $data = []) {
        if(empty($message)) {
            if ($isError) {
                $message = "error";
            } else {
                $message = "ok";
            }
        }

        return $this->handleView(
            $this->view(['message' => $message, 'error' => $isError, 'data' => $data], $code)
        );
    }
}