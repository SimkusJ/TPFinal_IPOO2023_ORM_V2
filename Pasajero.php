<?php





/**
 * CREATE TABLE pasajero (
 * pdocumento varchar(15),
 * pnombre varchar(150), 
 * papellido varchar(150), 
 * ptelefono int, 
 * idviaje bigint,
 * PRIMARY KEY (pasajero),
 * FOREIGN KEY (idviaje) REFERENCES viaje (idviaje)	
 * )ENGINE=InnoDB DEFAULT CHARSET=utf8; 
 
 */


class Pasajero
{

    private $documento;
    private $nombre;
    private $apellido;
    private $telefono;
    private $viaje;
    private $mensajeOperacion;

    public function __construct()
    {
        $this->documento = "";
        $this->nombre = "";
        $this->apellido = "";
        $this->telefono = "";
        $this->viaje = new Viaje();
        $this->mensajeOperacion = "";
    }

    public function setDocumento($doc)
    {
        $this->documento = $doc;
    }
    public function getDocumento()
    {
        return $this->documento;
    }

    public function setNombre($nom)
    {
        $this->nombre = $nom;
    }
    public function getNombre()
    {
        return $this->nombre;
    }

    public function setApellido($ape)
    {
        $this->apellido = $ape;
    }
    public function getApellido()
    {
        return $this->apellido;
    }

    public function setTelefono($tel)
    {
        $this->telefono = $tel;
    }
    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setViaje($col)
    {
        $this->viaje = $col;
    }
    public function getViaje()
    {
        return $this->viaje;
    }


    public function setMensajeOperacion($mensaje)
    {
        $this->mensajeOperacion = $mensaje;
    }
    public function getMensajeOperacion()
    {
        return $this->mensajeOperacion;
    }

    public function cargarDatosPersona($documento, $nombre, $apellido, $telefono, $idviaje){

        $this->setDocumento($documento);
        $this->setNombre($nombre);
        $this->setApellido($apellido);
        $this->setTelefono($telefono);
        $this->setViaje($idviaje);
    }

    public function buscar()
    {

        $bd = new BaseDatos();
        $respuesta = false;

        $consulta = "SELECT * FROM pasajero WHERE pdocumento =" . $this->getDocumento();

        if ($this->getDocumento() == null) {
            $this->setMensajeOperacion("No se encontro un valor cargado en la variable Documento.");
        } else {

            if ($bd->Iniciar()) {
                if ($bd->Ejecutar($consulta)) {

                    $datos = $bd->Registro();

                    $viaje=new Viaje();
                    $viaje->setID($datos['idviaje']);

                    $this->setNombre($datos['pnombre']);
                    $this->setApellido($datos['papellido']);
                    $this->setTelefono($datos['ptelefono']);
                    $this->setViaje($viaje);
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

        $interaccion = " INSERT INTO pasajero (pdocumento, pnombre, papellido, ptelefono, idviaje)
        VALUES ('". $this->getDocumento() ."','" . $this->getNombre() ."','". $this->getApellido() ."','". $this->getTelefono() ."','". $this->getViaje()."')";
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
        $interaccion= "SELECT * FROM pasajero";
        if ($condicion != "") {
            $interaccion .= " WHERE " . $condicion;
        }
        $listadoPersonas = array();
        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($interaccion)) {
                while ($fila = $bd->Registro()) {
                    $pasajero = new Pasajero();
                    $viaje = new viaje();
                    $viaje->setID($fila['idviaje']);
                    $pasajero->cargarDatosPersona($fila['pdocumento'], $fila['pnombre'], $fila['papellido'], $fila['ptelefono'], $viaje);
                    array_push($listadoPersonas, $pasajero);
                }
            }
        } else {

            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }

        return $listadoPersonas;
    }

    public function actualizar()
    {
        $bd = new BaseDatos();

        //Cargamos los datos que se remplazaran en la ID seleccionada
        $interaccion = "UPDATE pasajero SET pnombre = '" . $this->getNombre() . "', papellido = '" . $this->getApellido() . "', ptelefono = '" . $this->getTelefono() ."', idviaje = '".$this->getViaje()->getID(). "' WHERE pdocumento = '" . $this->getDocumento() . "'";
        $response = false;

        //Se inicia la conexion con la base de datos y se verifica al mismo tiempo
        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($interaccion)) {
                //En caso de que la interaccion sea exitosa se retornara un true al finalizar la operacion
                $response = true;
            } else {
                //Si la operacion falla o retorna un error se visualizara el siguente mensaje
                $this->setMensajeOperacion("No se pudo actualizar la informacion de la persona." . $bd->getError());
            }
        } else {
            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }
        return $response;
    }




    public function eliminar()
    {
        $bd = new BaseDatos();
        $eliminar = "DELETE FROM pasajero WHERE pdocumento=" . $this->getDocumento();
        $response = false;

        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($eliminar)) {
                $response = true;
            } else {
                $this->setMensajeOperacion("Ocurrio un error al momento de eliminar al pasajero. " . $bd->getError());
            }
        } else {
            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }
        return $response;
    }



    public function __toString()
    {
        
        $mensaje="------------Informacion Pasajero ----------- \n";
        $mensaje.="Documento: ". $this->getDocumento(). "\n";
        $mensaje.="Nombre: ". $this->getNombre(). "\n";
        $mensaje.="Apellido: ".$this->getApellido(). "\n";
        $mensaje.="Telefono: ". $this->getTelefono(). "\n";
        $mensaje.="-------------------------------------- \n";
        return $mensaje;
    }

}
