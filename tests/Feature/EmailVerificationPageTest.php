<?php

it('renders the email verification form', function (): void {
    $this->get(route('email.verify'))
        ->assertOk()
        ->assertSee('id="verify-email-form"', false)
        ->assertSee('Verifikasi dan masuk');
});
