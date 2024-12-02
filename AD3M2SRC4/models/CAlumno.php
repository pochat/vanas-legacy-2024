<?php

class CAlumno extends AppModel {

//    protected $table = 'c_alumno';
//    protected $primaryKey = 'fl_alumno';
//    protected $fillable = array();
//    public static $validateRules = array();

    public static function getTeachersByFlUsuario($flsUsuarios) {

        if (is_array($flsUsuarios)) {
            $flsUsuarios = implode(',', $flsUsuarios);
        } elseif ($flsUsuarios == '') {
            $flsUsuarios = 0;
        }
        if (!empty($flsUsuarios)) {
            $data = array();
            /*$rslts = parent::simpleQuery('
            SELECT kah.fl_alumno, CONCAT(us.ds_nombres, " ",us.ds_apaterno, " ",us.ds_amaterno) nb_maestro, ma.ds_ruta_avatar, 
            kah.no_grado, kah.fl_programa
            FROM k_alumno_historia kah
            LEFT JOIN c_maestro ma ON(ma.fl_maestro=kah.fl_maestro)
            LEFT JOIN c_usuario us ON(us.fl_usuario=kah.fl_maestro)
            LEFT JOIN c_periodo pe ON(pe.fl_periodo=kah.fl_periodo)
            LEFT JOIN c_programa pr ON(pr.fl_programa=kah.fl_programa)
            LEFT JOIN c_grupo gr ON(gr.fl_grupo=kah.fl_grupo)
            WHERE fl_alumno IN(' . $flsUsuarios . ') GROUP BY  kah.fl_grupo ORDER BY kah.fe_inicio;
            ');*/
            /*$rslts = parent::simpleQuery('
                SELECT AlumnoTerm.fl_alumno,
                       CONCAT(Usuario.ds_nombres, " ",
                       Usuario.ds_apaterno, " ",
                       Usuario.ds_amaterno) nb_maestro,
                       Maestro.ds_ruta_avatar ds_ruta_avatar,
                       Term.no_grado,
                       Term.fl_programa
                FROM c_usuario Usuario
                JOIN c_maestro Maestro ON (Usuario.fl_usuario = Maestro.fl_maestro)
                JOIN c_grupo Grupo ON (Grupo.fl_maestro = Maestro.fl_maestro)
                JOIN k_term Term ON (Grupo.fl_term = Term.fl_term)
                JOIN k_alumno_term AlumnoTerm ON (AlumnoTerm.fl_term = Grupo.fl_term)

                WHERE AlumnoTerm.fl_alumno IN(' . $flsUsuarios . ')

                GROUP BY  AlumnoTerm.fl_alumno, Term.fl_term ORDER BY  AlumnoTerm.fl_alumno, Term.no_grado;');*/
                $rslts = parent::simpleQuery('
                SELECT
                  a.fl_alumno,
                  CONCAT(c.ds_nombres, " ",c.ds_apaterno, " ",c.ds_amaterno) nb_maestro,
                  d.ds_ruta_avatar ds_ruta_avatar,
                  b.no_grado,
                  b.fl_programa
                FROM k_alumno_term a,
                  k_alumno_historia b,
                  c_usuario c,
                  c_maestro d
                WHERE a.fl_alumno=b.fl_alumno AND b.fl_maestro=c.fl_usuario AND b.fl_maestro=d.fl_maestro
                  AND a.fl_alumno IN('.$flsUsuarios.') 
                GROUP BY a.fl_alumno, b.fl_periodo
                ORDER BY a.fl_alumno, b.no_grado;');
            
           // Dbg::printQuerys();
            foreach ($rslts as $rslt) {
                $data[$rslt['fl_alumno']][] = $rslt;
            }
        }
        return $data;
    }
}
