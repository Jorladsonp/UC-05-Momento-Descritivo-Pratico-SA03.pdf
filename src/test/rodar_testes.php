<?php

echo str_repeat('=', 65) . PHP_EOL;
echo "  SA3 – SUITE DE TESTES UNITÁRIOS" . PHP_EOL;
echo str_repeat('=', 65) . PHP_EOL . PHP_EOL;

$falhasTotal = 0;

$falhasTotal += require __DIR__ . '/SolicitacaoTest.php';
echo PHP_EOL;
$falhasTotal += require __DIR__ . '/RepositorioSolicitacaoTest.php';

echo PHP_EOL;
echo str_repeat('=', 65) . PHP_EOL;
if ($falhasTotal === 0) {
    echo "  RESULTADO FINAL: TODOS OS TESTES PASSARAM" . PHP_EOL;
} else {
    echo "  RESULTADO FINAL: {$falhasTotal} TESTE(S) FALHARAM" . PHP_EOL;
}
echo str_repeat('=', 65) . PHP_EOL;

exit($falhasTotal > 0 ? 1 : 0);
