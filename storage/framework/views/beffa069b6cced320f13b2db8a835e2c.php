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
    <p>Período: <?php echo e($inicioSemana->format('d/m/Y')); ?> a <?php echo e($fimSemana->format('d/m/Y')); ?></p>

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
            <?php $__currentLoopData = $transacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(\Carbon\Carbon::parse($t->data)->format('d/m/Y')); ?></td>
                    <td><?php echo e($t->descricao); ?></td>
                    <td class="<?php echo e($t->tipo); ?>"><?php echo e(ucfirst($t->tipo)); ?></td>
                    <td><?php echo e(number_format($t->valor, 2, ',', '.')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total de Entradas:</strong> R$ <?php echo e(number_format($entradas, 2, ',', '.')); ?></p>
        <p><strong>Total de Saídas:</strong> R$ <?php echo e(number_format($saidas, 2, ',', '.')); ?></p>
        <p><strong>Saldo da Semana:</strong> 
            <span style="color: <?php echo e($saldo >= 0 ? 'green' : 'red'); ?>">
                R$ <?php echo e(number_format($saldo, 2, ',', '.')); ?>

            </span>
        </p>
    </div>
</body>
</html>
<?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/fluxo-caixa/pdf.blade.php ENDPATH**/ ?>