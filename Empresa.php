<?php


/** 
 * CREATE TABLE empresa(
 * idempresa bigint AUTO_INCREMENT,
 * enombre varchar(150),
 * edireccion varchar(150),
 * PRIMARY KEY (idempresa)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
 */

class Empresa
{

    private $id;
    private $nombre;
    private $direccion;
    private $viajes;
    private $mensajeOperacion;




    public function __construct()
    {
        $this->id = null;
        $this->nombre = "";
        $this->direccion = "";
        $this->viajes = [];
        $this->mensajeOperacion = "";
    }

    public function setID($id)
    {
        $this->id = $id;
    }
    public function getID()
    {
        return $this->id;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }
    public function getDireccion()
    {
        return $this->direccion;
    }

    public function setViajes($viajes)
    {
        $this->viajes = $viajes;
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


    public function cargarDatosEmpresa($id, $nombre, $direccion)
    {
        $this->setID($id);

        $this->setNombre($nombre);

        $this->setDireccion($direccion);
    }

    public function buscar()
    {

        $bd = new BaseDatos();
        $respuesta = false;

        $consulta = "SELECT * FROM empresa WHERE idempresa=" . $this->getID();

        if ($this->getID() == null) {
            $this->setMensajeOperacion("No se encontro un valor cargado en la variable ID.");
        } else {

            if ($bd->Iniciar()) {
                if ($bd->Ejecutar($consulta)) {
                    $datos = $bd->Registro();
                    $this->setNombre($datos['enombre']);
                    $this->setDireccion($datos['edireccion']);
                    $respuesta = true;
                }
            } else {
                $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
            }
        }
        return $respuesta;
    }

    public static function listar($condicion = "")
    {

        $bd = new BaseDatos();
        $interaccion = "SELECT * FROM empresa";
        if ($condicion != "") {
            $interaccion .= " WHERE " . $condicion;
        }
        $registroEmpresas = array();
        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($interaccion)) {
                while ($fila = $bd->Registro()) {
                    $empresa = new Empresa();
                    $empresa->cargarDatosEmpresa($fila['idempresa'], $fila['enombre'], $fila['edireccion']);
                    array_push($registroEmpresas, $empresa);
                }
            }
        } else {

            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }

        return $registroEmpresas;
    }

    public function insertar()
    {

        $bd = new BaseDatos();

        $interaccion = " INSERT INTO empresa (enombre,edireccion) VALUES ('" . $this->getNombre() . "','" . $this->getDireccion() . "')";
        $respuesta = false;

        if ($bd->Iniciar()) {
            if ($id = $bd->devuelveIDInsercion($interaccion)) {
                $this->setID($id);
                $respuesta = true;
            } else {
                $this->setMensajeOperacion("Ocurrio un error al insertar la nueva empresa." . $bd->getError());
            }
        } else {
            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }

        return $respuesta;
    }

    public function actualizar()
    {
        $bd = new BaseDatos();

        //Cargamos los datos que se remplazaran en la ID seleccionada
        $interaccion = "UPDATE empresa SET enombre='" . $this->getNombre() . "', edireccion='" . $this->getDireccion() . "' WHERE idempresa=" . $this->getID();
        $response = false;

        //Se inicia la conexion con la base de datos y se verifica al mismo tiempo
        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($interaccion)) {
                //En caso de que la interaccion sea exitosa se retornara un true al finalizar la operacion
                $response = true;
            } else {
                //Si la operacion falla o retorna un error se visualizara el siguente mensaje
                $this->setMensajeOperacion("No se pudo actualizar la informacion de la empresa" . $bd->getError());
            }
        } else {
            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }
        return $response;
    }

    public function eliminar()
    {
        $bd = new BaseDatos();
        $eliminar = "DELETE FROM empresa WHERE idempresa=" . $this->getId();
        $response = false;

        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($eliminar)) {
                $response = true;
            } else {
                $this->setMensajeOperacion("Ocurrio un error al momento de eliminar la empresa. " . $bd->getError());
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

            $mensaje .= "---==================== ** Viajes anexados ** ====================--- \n";

            for ($i = 0; $i < count($this->getViajes()); $i++) {
                
                $mensaje .= $coleccionViajes[$i] . "\n";
            }
        } 
        return $mensaje;
    }

    public function __toString()
    {

        $mensaje = "==================== Empresa ID: ". $this->getID() ." ==================== \n";
        $mensaje .= "Nombre de la empresa: " . $this->getNombre() . "\n";
        $mensaje .= "Direccion de la empresa: " . $this->getDireccion() . "\n";
        $mensaje .= " \n";
        $mensaje .= $this->mostrarListadoViajes();
        $mensaje .= "====================================================================== \n";

        return $mensaje;
    }
}
