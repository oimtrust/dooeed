<?php

use App\Models\User;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Mint a JWT for the user without resolving an auth guard, so later requests in
 * the test still have to prove themselves with the token.
 *
 * Pass a TTL in minutes to issue a token that expires that soon.
 */
function jwtFor(User $user, ?int $ttl = null): string
{
    $jwt = app('tymon.jwt');
    $factory = $jwt->factory();
    $originalTtl = $factory->getTTL();

    if ($ttl !== null) {
        $factory->setTTL($ttl);
    }

    $token = $jwt->fromUser($user);

    $factory->setTTL($originalTtl);

    return $token;
}

/**
 * Drop the authentication state an earlier request in the same test left behind.
 *
 * A real request gets a fresh container, but a test reuses one, so a guard that
 * authenticated the previous request would answer for the next one without ever
 * looking at its bearer token. Clear the guards and the parsed token to force
 * the next request to authenticate itself.
 */
function resetAuthState(): void
{
    app('auth')->forgetGuards();
    app('tymon.jwt')->unsetToken();
}
