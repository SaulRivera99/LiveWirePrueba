<div>
    {{-- Mensajes de éxito y error --}}
    @if($successMessage)
        <div class="alert alert-success">
            {{ $successMessage }}
        </div>
    @endif

    @if($errorMessage)
        <div class="alert alert-danger">
            {{ $errorMessage }}
        </div>
    @endif

    {{-- Formulario para matricular --}}
    <form wire:submit.prevent="submit" class="mb-4">
        <div class="mb-3">
            <label for="carnet" class="form-label">Carnet Estudiante:</label>
            <select wire:model="carnet" id="carnet" class="form-select" required>
                <option value="">Seleccione un carnet</option>
                @foreach($estudiantes as $estudiante)
                    <option value="{{ $estudiante->carnet }}">{{ $estudiante->carnet }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="codigo" class="form-label">Código Materia:</label>
            <select wire:model="codigo" id="codigo" class="form-select" required>
                <option value="">Seleccione un código</option>
                @foreach($materias as $materia)
                    <option value="{{ $materia->codigo }}">{{ $materia->codigo }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Matricular</button>
    </form>

    {{-- Tabla de matrículas --}}
    <h2 class="mb-3">Matrículas Registradas</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Carnet Estudiante</th>
                    <th>Código Materia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matriculas as $matricula)
                    <tr>
                        <td>{{ $matricula->estudiante->carnet ?? 'N/A' }}</td>
                        <td>{{ $matricula->materia->codigo ?? 'N/A' }}</td>
                        <td>
                            <button 
                                wire:click="delete({{ $matricula->id }})" 
                                onclick="return confirm('¿Seguro que quieres eliminar esta matrícula?')" 
                                class="btn btn-danger btn-sm"
                            >
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No hay matrículas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
