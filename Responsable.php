<?php





/**CREATE TABLE responsable (
 * rnumeroempleado bigint AUTO_INCREMENT,
 * rnumerolicencia bigint,
 * rnombre varchar(150), 
 * rapellido  varchar(150), 
 * PRIMARY KEY (rnumeroempleado)
 * )ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;;
 */



class Responsable
{

    private $numeroEmpleado;
    private $numeroLicencia;
    private $nombre;
    private $apellido;
    private $viajes;
    private $mensajeOperacion;

    public function __construct()
    {
        $this->numeroEmpleado = "";
        $this->numeroLicencia = "";
        $this->nombre = "";
        $this->apellido = "";
        $this->viajes = [];
        $this->mensajeOperacion = "";
    }


    public function setNumeroEmpleado($num)
    {
        $this->numeroEmpleado = $num;
    }
    public function getNumeroEmpleado()
    {
        return $this->numeroEmpleado;
    }

    public function setNumeroLicencia($num)
    {
        $this->numeroLicencia = $num;
    }
    public function getNumeroLicencia()
    {
        return $this->numeroLicencia;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }
    public function getApellido()
    {
        return $this->apellido;
    }

    public function setViajes($col)
    {
        $this->viajes = $col;
    }
    public function getViajes()
    {
        return $this->viajes;
    }

    public function setMensajeOperacion($mensaje)
    {
        $this->mensajeOperacion = $mensaje;
    }
    public function getMensajeOperacion()
    {
        return $this->mensajeOperacion;
    }

    public function cargarDatosResponsable($numEmpleado, $numLicencia, $nombre, $apellido)
    {

        $this->setNumeroEmpleado($numEmpleado);
        $this->setNumeroLicencia($numLicencia);
        $this->setNombre($nombre);
        $this->setApellido($apellido);

    }


    public function buscar()
    {

        $bd = new BaseDatos();
        $respuesta = false;

        $consulta = "SELECT * FROM responsable WHERE rnumeroempleado =" . $this->getNumeroEmpleado();

        if ($this->getNumeroEmpleado() == null) {
            $this->setMensajeOperacion("No se encontro un valor cargado en la variable, Numero de empleado.");
        } else {

            if ($bd->Iniciar()) {
                if ($bd->Ejecutar($consulta)) {
                    $datos = $bd->Registro();

                    $this->setNumeroLicencia($datos["rnumerolicencia"]);
                    $this->setNombre($datos['rnombre']);
                    $this->setApellido($datos['rapellido']);
                    $respuesta = true;
                }
            } else {
                $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
            }
        }
        return $respuesta;
    }



    public function insertar()
    {

        $bd = new BaseDatos();

        $interaccion = " INSERT INTO responsable (rnumerolicencia, rnombre, rapellido)
        VALUES('".$this->getNumeroLicencia()."','".$this->getNombre()."','".$this->getApellido()."')";
        $respuesta = false;

        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($interaccion)) {
                $respuesta = true;
            } else {
                $this->setMensajeOperacion("Ocurrio un error al insertar al nuevo pasajero." . $bd->getError());
            }
        } else {
            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }

        return $respuesta;
    }

    public static function listar($condicion = "")
    {

        $bd = new BaseDatos();
        $interaccion = "SELECT * FROM responsable";
        if ($condicion != "") {
            $interaccion .= " WHERE " . $condicion;
        }
        $listadoResponsables = array();
        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($interaccion)) {
                while ($fila = $bd->Registro()) {


                    $resposable = new Responsable();
                    $resposable->cargarDatosResponsable($fila['rnumeroempleado'], $fila['rnumerolicencia'], $fila['rnombre'], $fila['rapellido']);
                    array_push($listadoResponsables, $resposable);
                }
            }
        } else {

            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }

        return $listadoResponsables;
    }


    public function actualizar()
    {
        $bd = new BaseDatos();

        //Cargamos los datos que se remplazaran en la ID seleccionada
        $interaccion = "UPDATE responsable SET rnumerolicencia='".$this->getNumeroLicencia()."',rnombre='".$this->getNombre()."',rapellido='".$this->getApellido()."'  WHERE rnumeroempleado=".$this->getNumeroEmpleado();
        $respuesta = false;

        //Se inicia la conexion con la base de datos y se verifica al mismo tiempo
        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($interaccion)) {
                //En caso de que la interaccion sea exitosa se retornara un true al finalizar la operacion
                $respuesta = true;
            } else {
                //Si la operacion falla o retorna un error se visualizara el siguente mensaje
                $this->setMensajeOperacion("No se pudo actualizar la informacion del responsable." . $bd->getError());
            }
        } else {
            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }
        return $respuesta;
    }


    public function eliminar()
    {
        $bd = new BaseDatos();
        $eliminar = "DELETE FROM responsable WHERE rnumeroempleado=" . $this->getNumeroEmpleado();
        $response = false;

        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($eliminar)) {
                $response = true;
            } else {
                $this->setMensajeOperacion("Ocurrio un error al momento de eliminar al responsable. " . $bd->getError());
            }
        } else {
            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }
        return $response;
    }


    public function mostrarListadoViajes()
    {

        $mensaje = "";
        $coleccionViajes = $this->getViajes();

        if (count($this->getViajes()) > 0) {
            $mensaje .= "------------ >> Listado de viajes anexados a este empleado <<------------- \n";
            for ($i = 0; $i < count($this->getViajes()); $i++) {
                
                $mensaje .= $coleccionViajes[$i] . "\n";
                $mensaje .= "--------------------------------------------------------- \n";
            }
        }

        return $mensaje;
    }


    public function __toString()
    {

        $mensaje = "-------------- >> Empleado/Responsable << -------------- \n";
        $mensaje .= "Numero de empleado: " . $this->getNumeroEmpleado() . "\n";
        $mensaje .= "Nombre de licencia: " . $this->getNumeroLicencia() . "\n";
        $mensaje .= "Nombre: " . $this->getNombre() . "\n";
        $mensaje .= "Apellido: " . $this->getApellido() . "\n";
        $mensaje .= $this->mostrarListadoViajes();
        $mensaje .= "-------------------------------------- \n";

        return $mensaje;
    }
}
