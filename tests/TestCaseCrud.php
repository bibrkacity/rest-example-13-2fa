<?php

namespace Tests;

use App\Enums\VariableNames;
use Bibrkacity\SanctumSession\Services\SanctumSession;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

abstract class TestCaseCrud extends TestCase
{
    protected const string NAME_TEST_STORE = 'TestStore';
    protected const string NAME_TEST_UPDATE = 'TestUpdate';
    protected const string NAME_TEST_DELETE = 'TestDelete';

    protected static ?string $token = null;

    protected function getToken($createForce = false): string
    {

        try {

            if (TestCaseCrud::$token != null && ! $createForce) {
                return TestCaseCrud::$token;
            }

            $url = route('api.v1.login');
            $credentials = config('auth.test_admin_credentials');

            $response = Http::post($url, $credentials);

            $json = $response->body();

            $obj = json_decode($json);

            if ($response->status() != 200) {
                $token = 'No token: invalid credentials';
            } else {
                $token = $obj->token;

                if (! $createForce) {
                    TestCaseCrud::$token = $token;
                }

                SanctumSession::put($token, VariableNames::VERIFIED2FA->value, 'boolean', true);

            }

        } catch (\Exception $e) {
            $token = $e->getMessage();
        }

        return $token;
    }

    protected function index_serviceable(string $indexRoute, array|null|string $query = null): void
    {

        $token = $this->getToken();

        if (preg_match('/^\d+\|/', $token) !== 1) {
            self::fail($token);
        }

        $response = Http::withToken($token)->get(route($indexRoute), $query);

        $body = $response->body();
        self::assertEquals(ResponseAlias::HTTP_OK, $response->status());
        self::assertJson($body);
    }

    protected function store_serviceable(string $storeRoute, array $data): void
    {

        $token = $this->getToken();

        if (preg_match('/^\d+\|/', $token) !== 1) {
            self::fail($token);
        }

        $response = Http::withToken($token)
            ->post(route($storeRoute), $data);

        $body = $response->body();

        self::assertEquals(ResponseAlias::HTTP_CREATED, $response->status());
        self::assertJson($body);

    }

    /**
     * Test of method show() of CRUD controller
     * @param string $modelClass Class name of base model for CRUD
     * @param string $showRoute Route name for show()
     * @param string $objNamePlural Optional. Name of entity in plural. Default "objects"
     * @param string $idName Optional. Name of id parameter. Default "id"
     * @return void
     * @throws ConnectionException
     */
    protected function show_serviceable(
        string $modelClass,
        string $showRoute,
        string $objNamePlural = 'objects',
        string $idName = 'id'
    ): void {

        $token = $this->getToken();

        if (preg_match('/^\d+\|/', $token) !== 1) {
            self::fail($token);
        }

        $object = $modelClass::query()->first();

        if ($object === null) {
            self::assertTrue(true, 'No '.$objNamePlural.' in database');
        } else {
            $response = Http::withToken($token)->get(route($showRoute, ['id' => $object->$idName]));

            $body = $response->body();
            self::assertEquals(ResponseAlias::HTTP_OK, $response->status());
            self::assertJson($body);
        }

    }

    protected function update_serviceable(
        string $updateRoute,
        string $storeRoute,
        array $data,
        array $newData,
        string $idName = 'id',
    ): void {
        $token = $this->getToken();

        if (preg_match('/^\d+\|/', $token) !== 1) {
            self::fail($token);
        }

        $objectCreate = Http::withToken($token)->post(route($storeRoute), $data);

        if ($objectCreate->failed()) {
            self::fail($objectCreate->status());
        }

        $object = $objectCreate->json('data');

        $response = Http::withToken($token)->put(route($updateRoute, [$idName => $object[$idName]]), $newData);
        $body = $response->json('data');

        self::assertEquals(ResponseAlias::HTTP_OK, $response->status());
        self::assertEquals($body[$idName], $object[$idName], "Update error");
        $body = $response->body();
        self::assertJson($body);
    }

    protected function delete_serviceable(
        string $deleteRoute,
        string $storeRoute,
        array $data,
        string $showRoute,
        string $idName = 'id',
    ): void {
        $token = $this->getToken();

        if (preg_match('/^\d+\|/', $token) !== 1) {
            self::fail($token);
        }

        $objectCreate = Http::withToken($token)->post(route($storeRoute), $data);

        if ($objectCreate->failed()) {
            self::fail($objectCreate->status());
        }

        $object = $objectCreate->json('data');

        $response = Http::withToken($token)->delete(route($deleteRoute, [$idName => $object[$idName]]));

        self::assertEquals(ResponseAlias::HTTP_NO_CONTENT, $response->status());

        $search = Http::withToken($token)->get(route($showRoute, [$idName => $object[$idName]]));
        self::assertEquals(ResponseAlias::HTTP_BAD_REQUEST, $search->status());
    }
}
