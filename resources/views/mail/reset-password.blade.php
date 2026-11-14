<x-mail::message>
# Olá, {{ $name }}

Recebemos um pedido para redefinir a senha da sua conta no **Egg's Club**.

<x-mail::button :url="$url">
Redefinir minha senha
</x-mail::button>

O link vale por 60 minutos. Se você não pediu essa troca, ignore o e-mail — sua senha continua a mesma.

Obrigado,<br>
Egg's Club
</x-mail::message>
