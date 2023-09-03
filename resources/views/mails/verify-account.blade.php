<x-mail::message>

# Verify Account

Please click the button below to verify your email address.

<x-mail::button :url="$verify_link">
Verify Email Address
</x-mail::button>

If you did not create an account, no further action is required.

<x-mail::subcopy>
If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser: [{{ $verify_link }}]({{ $verify_link }})
</x-mail::subcopy>
</x-mail::message>
