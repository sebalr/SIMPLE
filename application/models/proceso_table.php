<?php

class ProcesoTable extends Doctrine_Table {

    public function findProcesosDisponiblesParaIniciar($usuario_id,$cuenta=null){
        $usuario=Doctrine::getTable('Usuario')->find($usuario_id);
        
        $query=Doctrine_Query::create()
                ->from('Proceso p, p.Cuenta c, p.Tareas t, t.GruposUsuarios g, g.Usuarios u')
                ->where('t.inicial = 1')
                //Si el usuario tiene permisos de acceso
                ->andWhere('(t.acceso_modo="grupos_usuarios" AND u.id = ?) OR (t.acceso_modo = "registrados") OR (t.acceso_modo = "claveunica") OR (t.acceso_modo="publico")',$usuario->id)
                //Si la tarea se encuentra activa
                ->andWhere('1!=(t.activacion="no" OR ( t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin) )))')
                ->orderBy('p.id desc');
        
        if($cuenta)
            $query->andWhere('c.nombre = ?',$cuenta->nombre);
        
        return $query->execute();
    }
    
}
