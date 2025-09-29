<x-mail::message>
# Reset Password Notification

You are receiving this email because we received a password reset request for your account.

<x-mail::button :url="$url">
Reset Password
</x-mail::button>

This password reset link will expire in 60 minutes.

If you did not request a password reset, no further action is required.

Best regards,<br>
{{ config('app.name') }}

<x-slot:subcopy>
If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: <span class="break-all">[{{ $url }}]({{ $url }})</span>
</x-slot:subcopy>
</x-mail::message>