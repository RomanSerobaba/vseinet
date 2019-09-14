<?php

namespace AdminBundle\Bus\Product\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SetFirstImageCommandHandler extends MessageHandler
{
    public function handle(SetFirstImageCommand $command)
    {
        $api = $this->get('user.api.client');
        $params = [
            'underId' => 0,
        ];

        try {
            $api->put('/api/v1/baseProductImages/'.$command->id.'/sortOrder/', [], $params);
        } catch (BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
