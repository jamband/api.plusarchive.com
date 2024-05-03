<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class LoginRulesTest extends TestCase
{
    /**
     * @param array<string, mixed> $data
     */
    protected function request(array $data): TestResponse
    {
        return $this->post('/auth/login', $data)
            ->assertUnprocessable();
    }

    public function testEmailRequiredRule(): void
    {
        $this->request(['email' => null])
            ->assertJsonPath('errors.email', __('validation.required', [
                'attribute' => 'email',
            ]));
    }

    public function testEmailStringRule(): void
    {
        $this->request(['email' => 1])
            ->assertJsonPath('errors.email', __('validation.string', [
                'attribute' => 'email',
            ]));
    }

    public function testEmailEmailRule(): void
    {
        $this->request(['email' => 'foo'])
            ->assertJsonPath('errors.email', __('validation.email', [
                'attribute' => 'email',
            ]));
    }

    public function testPasswordRequiredRule(): void
    {
        $this->request(['password' => null])
            ->assertJsonPath('errors.password', __('validation.required', [
                'attribute' => 'password',
            ]));
    }

    public function testPasswordStringRule(): void
    {
        $this->request(['password' => 1])
            ->assertJsonPath('errors.password', __('validation.string', [
                'attribute' => 'password',
            ]));
    }
}
