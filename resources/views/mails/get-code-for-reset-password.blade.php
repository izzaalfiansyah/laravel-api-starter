<x-mail::message>

# Forgot Password?

You have requested to reset your password. You can use the following code to recover your account:

<x-mail::panel>
{{ $passwordResetToken->token }}
</x-mail::panel>

If you did not requested to reset your password, no further action is required.

</x-mail::message>
