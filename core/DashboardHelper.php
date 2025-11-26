<?php
// core/DashboardHelper.php

class DashboardHelper
{
    private static array $dashboardsPorPerfil = [
        'Administrador' => 'views/panel/partials/dashboard_administrador.php',
        'Empleado'      => 'views/panel/partials/dashboard_empleado.php',
        'Repartidor'    => 'views/panel/partials/dashboard_repartidor.php',
        'Cliente'       => 'views/panel/partials/dashboard_cliente.php',
    ];

    public static function obtenerDashboardPorPerfil(string $perfil): ?string
    {
        return self::$dashboardsPorPerfil[$perfil] ?? null;
    }
}
?>