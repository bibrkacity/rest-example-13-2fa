<?php

namespace App\DTO;

use Illuminate\Http\Request;

/**
 * DTO for User's list
 */
readonly class UserDTO extends DTO
{
    public ?string $email;

    #[\Override]
    protected function fromRequest(Request $request): void
    {
        parent::fromRequest($request);

        $this->email = $request->input('email');

    }

    #[\Override]
    protected function getRules(Request $request): array
    {

        $rules = parent::getRules($request);
        $rules['email'] = 'email';

        return $rules;
    }

    #[\Override]
    protected function getSortNameDefault(): string
    {
        return 'name';
    }

    #[\Override]
    protected function getRouteName(): string
    {
        return 'api.v1.users.index';
    }

    #[\Override]
    protected function getDefaults(): array
    {
        return [
            ...parent::getDefaults(),
            'email' => null,
        ];
    }
}
