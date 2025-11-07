<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Fluxo de Caixa</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background: #efefef; }
        .summary { margin-top: 10px; font-size: 13px; }
        .entrada { color: green; }
        .saida { color: red; }
    </style>
</head>
<body>
    <h2>Relatório de Fluxo de Caixa</h2>
    <p>Período: {{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th>Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($movimentos as $t)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($t->data)->format('d/m/Y') }}</td>
                    <td>{{ $t->descricao }}</td>
                    <td class="{{ strtolower($t->tipo) }}">{{ ucfirst($t->tipo) }}</td>
                    <td>{{ number_format($t->valor, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total de Entradas:</strong> R$ {{ number_format($entradas, 2, ',', '.') }}</p>
        <p><strong>Total de Saídas:</strong> R$ {{ number_format($saidas, 2, ',', '.') }}</p>
        <p><strong>Saldo do Período:</strong> 
            <span style="color: {{ $saldo >= 0 ? 'green' : 'red' }}">
                R$ {{ number_format($saldo, 2, ',', '.') }}
            </span>
        </p>
    </div>
</body>
</html>
