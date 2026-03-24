<?php

namespace Tests\Details;

use Tests\TestCaseCrud;
use App\Models\User;

class UserTest extends TestCaseCrud
{
    public function index(): void
    {
        $this->index_serviceable('api.v1.users.index');
    }

    public function store(): void
    {
        $data = [
            'name' => self::NAME_TEST_STORE.uniqid(),
            'email' => self::NAME_TEST_STORE.uniqid().'@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $this->store_serviceable('api.v1.users.store', $data);
        $this->deleteTestRecords(User::class, 'name', self::NAME_TEST_STORE);

    }

    public function show(): void
    {
        $this->show_serviceable(
            User::class,
            'api.v1.users.show',
            'users',
        );
    }

    public function update(): void
    {
        $data = [
            'name' => self::NAME_TEST_STORE . uniqid(),
            'email' => self::NAME_TEST_STORE . uniqid() . '@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $newData = [
            'name' => self::NAME_TEST_UPDATE . uniqid(),
            'email' => self::NAME_TEST_UPDATE . uniqid() . '@test.com',
        ];

        $this->update_serviceable(
            'api.v1.users.update',
            'api.v1.users.store',
            $data,
            $newData,
            'id',
        );
        $this->deleteTestRecords(User::class, 'name', self::NAME_TEST_UPDATE);
    }

    public function destroy(): void
    {
        $data = [
            'name' => self::NAME_TEST_DELETE . uniqid(),
            'email' => self::NAME_TEST_DELETE . uniqid() . '@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->delete_serviceable(
            'api.v1.users.destroy',
            'api.v1.users.store',
            $data,
            'api.v1.users.show',
            'id',
        );
        $this->deleteTestRecords(User::class, 'name', self::NAME_TEST_DELETE);
    }
}
