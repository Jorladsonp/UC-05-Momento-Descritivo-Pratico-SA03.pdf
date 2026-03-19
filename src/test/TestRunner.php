<?php

class TestRunner
{
    private int $total   = 0;
    private int $passou  = 0;
    private int $falhou  = 0;
    private array $falhas = [];

    public function executar(string $nome, callable $teste): void
    {
        $this->total++;
        try {
            $teste();
            $this->passou++;
            echo "  [PASS] {$nome}" . PHP_EOL;
        } catch (Throwable $e) {
            $this->falhou++;
            $this->falhas[] = "  [FAIL] {$nome}" . PHP_EOL . "         " . $e->getMessage();
            echo "  [FAIL] {$nome}" . PHP_EOL;
        }
    }

    public function assertIgual(mixed $esperado, mixed $obtido, string $msg = ''): void
    {
        if ($esperado !== $obtido) {
            $e = $msg ?: "Esperado [{$esperado}], obtido [{$obtido}]";
            throw new RuntimeException($e);
        }
    }

    public function assertVerdadeiro(bool $cond, string $msg = 'Condição falsa'): void
    {
        if (!$cond) throw new RuntimeException($msg);
    }

    public function assertLancaExcecao(callable $bloco, string $tipoEsperado, string $msgParcial = ''): void
    {
        try {
            $bloco();
            throw new RuntimeException("Exceção '{$tipoEsperado}' não foi lançada.");
        } catch (Throwable $e) {
            if ($e instanceof RuntimeException && str_contains($e->getMessage(), "não foi lançada")) {
                throw $e;
            }
            if (!($e instanceof $tipoEsperado)) {
                throw new RuntimeException("Esperava '{$tipoEsperado}', mas obteve '" . get_class($e) . "': " . $e->getMessage());
            }
            if ($msgParcial !== '' && !str_contains($e->getMessage(), $msgParcial)) {
                throw new RuntimeException("Mensagem esperada contendo '{$msgParcial}', obtida: '{$e->getMessage()}'");
            }
        }
    }

    public function resumo(): void
    {
        echo str_repeat('-', 65) . PHP_EOL;
        if (!empty($this->falhas)) {
            echo PHP_EOL . "  DETALHES DAS FALHAS:" . PHP_EOL;
            foreach ($this->falhas as $f) {
                echo $f . PHP_EOL;
            }
            echo PHP_EOL;
        }
        $status = $this->falhou === 0 ? 'TODOS OS TESTES PASSARAM' : "{$this->falhou} TESTE(S) FALHARAM";
        echo "  Resultado: {$status}" . PHP_EOL;
        echo "  Total: {$this->total} | Passou: {$this->passou} | Falhou: {$this->falhou}" . PHP_EOL;
        echo str_repeat('-', 65) . PHP_EOL;
    }

    public function getFalhou(): int { return $this->falhou; }
}
