<?php

namespace App\Actions\User;

use App\DTO\UserDTO;
use App\Http\Requests\User\IndexRequest;
use App\Http\Responses\IndexResponse;
use App\Repositories\UserRepository;

class IndexUser
{
    public function handle(IndexRequest $request, UserRepository $userRepository): IndexResponse
    {

        $dto = new UserDTO($request);
        $data = $userRepository->findByDTO($dto);
        $total = $userRepository->countByDTO($dto);

        return new IndexResponse($data, $dto, $total);
    }
}
