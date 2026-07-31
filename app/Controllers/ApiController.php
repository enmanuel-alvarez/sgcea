<?php

declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\Controller;
use Src\Models\Repositories\EstudianteRepository;

class ApiController extends Controller
{
    public function obtenerEstudiante(int $id)
    {
        if (!isset($_SESSION['usuario_id'])) {
            return $this->json(['error' => 'No autorizado'], 401);
        }

        $estudianteRepo = new EstudianteRepository();
        $estudiante = $estudianteRepo->obtenerPorId($id);

        if ($estudiante) {
            return $this->json($estudiante);
        }

        return $this->json(['error' => 'Estudiante no encontrado'], 404);
    }

    public function obtenerEstudiantesPorSeccion(int $id)
    {
        if (!isset($_SESSION['usuario_id'])) {
            return $this->json(['error' => 'No autorizado'], 401);
        }

        $estudianteRepo = new EstudianteRepository();
        $estudiantes = $estudianteRepo->obtenerEstudiantesPorSeccion($id);

        return $this->json($estudiantes);
    }
}
