<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Estudiante;
use App\Models\Materia;
use App\Models\MatriculaView;
use Illuminate\Support\Facades\DB;


class MatriculaComponent extends Component
{
    public $carnet;
    public $codigo;

    public $successMessage = '';
    public $errorMessage = '';

   

    public function submit()
    {
        $this->validate([
            'carnet' => 'required|exists:estudiantes,carnet',
            'codigo' => 'required|exists:materias,codigo',
        ]);

        try {
        DB::statement('EXEC sp_matricular_estudiante @carnet = ?, @codigo_materia = ?', [
            $this->carnet,
            $this->codigo,
        ]);

        $this->reset(['carnet', 'codigo']);
        $this->successMessage = 'Matrícula registrada correctamente.';
        $this->errorMessage = '';

        } catch (\Illuminate\Database\QueryException $e) {
            $this->errorMessage = 'Error en la base de datos: ' . $e->getMessage();
            $this->successMessage = '';

        } catch (\Exception $e) {
            $this->errorMessage = 'Error inesperado: ' . $e->getMessage();
            $this->successMessage = '';
        }

    }


    public function delete($id)
    {
        try {
            // Eliminar la matrícula por id
            DB::table('matriculas')->where('id', $id)->delete();

            $this->successMessage = 'Matrícula eliminada correctamente.';
            $this->errorMessage = '';
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al eliminar la matrícula: ' . $e->getMessage();
            $this->successMessage = '';
        }
    }



    public function render()
    {
        // Trae matrículas con datos relacionados para mostrar
        $matriculas = MatriculaView::with('estudiante', 'materia')->get();

        $estudiantes = Estudiante::all(['carnet']);
        $materias = Materia::all(['codigo']);

        return view('livewire.matricula-component', compact('matriculas', 'estudiantes', 'materias'));
    }
}
