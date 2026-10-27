<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TransaccionService;
use App\Repositories\Contracts\TransaccionRepositoryInterface;
use Mockery;

class TransaccionServiceTest extends TestCase
{
    protected $transaccionRepositoryMock;
    protected TransaccionService $transaccionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transaccionRepositoryMock = Mockery::mock(TransaccionRepositoryInterface::class);
        $this->transaccionService = new TransaccionService($this->transaccionRepositoryMock);
    }

    public function test_get_global_balance_calculates_correctly()
    {
        // Le decimos a Mockery exactamente qué método y con qué parámetro esperar
        $this->transaccionRepositoryMock
            ->shouldReceive('getSumByType')
            ->with('Ingreso') // Debe recibir este String
            ->once()          // Exactamente 1 vez
            ->andReturn(1500.00);

        $this->transaccionRepositoryMock
            ->shouldReceive('getSumByType')
            ->with('Egreso')  // Debe recibir este String
            ->once()          // Exactamente 1 vez
            ->andReturn(500.00);

        $resultado = $this->transaccionService->getGlobalBalance();

        $this->assertEquals(1500.00, $resultado['ingresos']);
        $this->assertEquals(500.00, $resultado['egresos']);
        $this->assertEquals(1000.00, $resultado['balance']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
