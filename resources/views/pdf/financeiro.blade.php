<h2>Histórico Financeiro</h2>
<table border="1" width="100%">
    <thead>
        <tr>
            <th>Data</th>
            <th>Descrição</th>
            <th>Valor</th>
            <th>Tipo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($histories as $history)
            <tr>
                <td>{{ $history->data_doacao }}</td>
                <td>{{ $history->descricao }}</td>
                <td>R$ {{ number_format($history->valor, 2, ',', '.') }}</td>
                <td>{{ ucfirst($history->meio) }}</td>
            </tr>

        @endforeach
    </tbody>
</table>
