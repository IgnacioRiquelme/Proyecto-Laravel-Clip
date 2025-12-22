<table>
    <thead>
        <tr>
            <th colspan="11" style="font-weight: bold; font-size: 16px;">
                CLIP TECNOLOGÍA - Bitácora diaria de procesos y jobs - Fecha: {{ $fecha }}
            </th>
        </tr>
        <tr>
            <th>ID Proceso</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Total</th>
            <th>Inicio (Adm)</th>
            <th>Fin (Adm)</th>
            <th>Correo Inicio</th>
            <th>Correo Fin</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($procesosPorGrupo as $grupo => $procesos)
            <tr>
                <td colspan="11" style="background-color: #d1d5db; font-weight: bold;">
                    {{ strtoupper($grupo) }}
                </td>
            </tr>
            @foreach($procesos as $p)
                <tr>
                    <td>{{ $p->id_proceso }}</td>
                    <td>{{ $p->proceso }}</td>
                    <td>{{ $p->descripcion }}</td>
                    <td>{{ $p->inicio ? \Carbon\Carbon::parse($p->inicio)->format('Y-m-d H:i') : '' }}</td>
                    <td>{{ $p->fin ? \Carbon\Carbon::parse($p->fin)->format('Y-m-d H:i') : '' }}</td>
                    <td>{{ $p->total ?? '' }}</td>
                    <td>{{ $p->adm_inicio ?? '' }}</td>
                    <td>{{ $p->adm_fin ?? '' }}</td>
                    <td>{{ $p->correo_inicio ?? '' }}</td>
                    <td>{{ $p->correo_fin ?? '' }}</td>
                    <td>{{ $p->estado ?? 'Pendiente' }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
