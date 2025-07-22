@component('mail::message')
# Atualização sobre seu pedido de Batismo

Olá {{ $batismo->nome_batizando }},

O estado do seu pedido de batismo foi atualizado para:  
**{{ strtoupper($batismo->estado) }}**

@isset($batismo->data_batismo)
📅 **Data do Batismo:** {{ \Carbon\Carbon::parse($batismo->data_batismo)->format('d/m/Y') }}
@endisset

@isset($batismo->descricao_rejeicao)
📝 **Motivo da Rejeição:** {{ $batismo->descricao_rejeicao }}
@endisset

---

Se tiver dúvidas, entre em contato com a secretaria da paróquia.

🙏 Obrigado,  
{{ config('app.name') }}
@endcomponent
