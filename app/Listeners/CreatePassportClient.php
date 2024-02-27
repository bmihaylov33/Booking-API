<?php

namespace App\Listeners;

use Laravel\Passport\ClientRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\UserRegistered;

class CreatePassportClient implements ShouldQueue
{
    protected $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function handle(UserRegistered $event)
    {
        $user = $event->user;

        $client = $this->clientRepository->createPersonalAccessClient(
            null,
            $user->name.' Client',
            config('app.url')
        );
    }
}
