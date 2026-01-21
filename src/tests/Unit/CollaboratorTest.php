<?php

namespace Tests\Unit;

use App\DTO\CollaboratorDTO;
use App\UseCases\CollaboratorUseCase;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeCollaboratorRepository;

class CollaboratorTest extends TestCase
{
    public function test_that_true_is_true(): void
    {
        $collaboratorUseCase = new CollaboratorUseCase(new FakeCollaboratorRepository());
        $collaborators = $collaboratorUseCase->getAllCollaborators();
        $this->assertNotEmpty($collaborators);
        $this->assertCount(1, $collaborators);
        $this->assertEquals("Fulano", $collaborators[0]->name, "name don't match");
        $this->assertEquals("fulano@email.com", $collaborators[0]->email, "email don't match");
        $this->assertEquals("9912345678", $collaborators[0]->phone, "phone don't match");
    }

    public function test_that_true_is_true2(): void
    {
        $collaboratorUseCase = new CollaboratorUseCase(new FakeCollaboratorRepository());
        $collaboratorDTO = new CollaboratorDTO();
        $collaboratorDTO->name = 'Fulano';
        $collaboratorDTO->email = 'fulano@email.com';
        $collaboratorDTO->phone = '9912345678';
        $success = $collaboratorUseCase->saveCollaborator($collaboratorDTO);
        $this->assertTrue($success);
    }

    public function test_that_true_is_true3(): void
    {
        $collaboratorUseCase = new CollaboratorUseCase(new FakeCollaboratorRepository());
        $collaboratorDTO = new CollaboratorDTO();
        $collaboratorDTO->name = '';
        $collaboratorDTO->email = 'fulano@email.com';
        $collaboratorDTO->phone = '9912345678';
        $success = $collaboratorUseCase->saveCollaborator($collaboratorDTO);
        $this->assertFalse($success);
    }
}
