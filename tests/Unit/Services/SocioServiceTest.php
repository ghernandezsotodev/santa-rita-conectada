<?php

namespace Tests\Unit\Services;

use Tests\TestCase; // Usamos el TestCase de Laravel para soportar el Facade DB::transaction
use App\Services\SocioService;
use App\Repositories\Contracts\SocioRepositoryInterface;
use App\Services\UserService;
use App\Models\Socio;
use App\Models\User;
use Mockery;

class SocioServiceTest extends TestCase
{
    protected $socioRepositoryMock;
    protected $userServiceMock;
    protected SocioService $socioService;

    protected function setUp(): void
    {
        parent::setUp();

        //Creamos versiones "falsas" de las dependencias
        $this->socioRepositoryMock = Mockery::mock(SocioRepositoryInterface::class);
        $this->userServiceMock = Mockery::mock(UserService::class);

        //Instanciamos el servicio inyectando los mocks en lugar de los reales
        $this->socioService = new SocioService(
            $this->socioRepositoryMock,
            $this->userServiceMock
        );
    }

    public function test_create_with_user_generates_temporary_password_if_email_provided()
    {
        $data = [
            'nombre' => 'Juan Pérez',
            'email' => 'juan@ejemplo.com'
        ];

        $socioSimulado = new Socio(['nombre' => 'Juan Pérez']);
        $socioSimulado->id = 1;

        $this->socioRepositoryMock
            ->shouldReceive('create')
            ->once() // Verificamos que se llame exactamente 1 vez
            ->with($data)
            ->andReturn($socioSimulado);

        $this->userServiceMock
            ->shouldReceive('createSocioUser')
            ->once()
            ->andReturn(new User());

        // Ejecutamos el método real de nuestro servicio
        $resultado = $this->socioService->createWithUser($data);

        // Comprobamos que el servicio hizo la lógica correcta (generar contraseña aleatoria de 10 caracteres)
        $this->assertNotNull($resultado['temporaryPassword']);
        $this->assertEquals(10, strlen($resultado['temporaryPassword']));
        $this->assertEquals($socioSimulado, $resultado['socio']);
    }

    public function test_create_with_user_does_not_generate_password_if_no_email_provided()
    {
        $data = [
            'nombre' => 'María Gómez',
            'email' => null // Simulamos que el socio no tiene email
        ];

        $socioSimulado = new Socio(['nombre' => 'María Gómez']);
        $socioSimulado->id = 2;

        $this->socioRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($socioSimulado);

        $this->userServiceMock
            ->shouldNotReceive('createSocioUser');

        $resultado = $this->socioService->createWithUser($data);

        // Comprobamos que no hay contraseña temporal y que devolvió el socio correctamente
        $this->assertNull($resultado['temporaryPassword']);
        $this->assertEquals($socioSimulado, $resultado['socio']);
    }

    protected function tearDown(): void
    {
        Mockery::close(); 
        parent::tearDown();
    }
}
