<?php


/**
 * CREATE TABLE viaje (
 *  idviaje bigint AUTO_INCREMENT, codigo de viaje
 * vdestino varchar(150),
 *vcantmaxpasajeros int,
 *idempresa bigint,
 *rnumeroempleado bigint,
 *vimporte float,
 * PRIMARY KEY (idviaje),
 * FOREIGN KEY (idempresa) REFERENCES empresa (idempresa),
 * FOREIGN KEY (rnumeroempleado) REFERENCES responsable (rnumeroempleado)
 * ON UPDATE CASCADE
 * ON DELETE CASCADE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT = 1;
 */

require_once "Empresa.php";
require_once "Pasajero.php";
require_once "Responsable.php";


class Viaje
{

    private $id;
    private $destino;
    private $cantmaxPasajeros;
    private $empresa;
    private $responsable;
    private $importe;
    private $pasajeros;
    private $mensajeOperacion;




    public function __construct()
    {
        $this->id = null;
        $this->destino = "";
        $this->cantmaxPasajeros = "";
        $this->empresa = new Empresa();
        $this->responsable = new Responsable();
        $this->importe = 0;
        $this->pasajeros = [];
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

    public function setDestino($destino)
    {
        $this->destino = $destino;
    }
    public function getDestino()
    {
        return $this->destino;
    }

    public function setCantMaxPasajeros($cant)
    {
        $this->cantmaxPasajeros = $cant;
    }
    public function getCantMaxPasajeros()
    {
        return $this->cantmaxPasajeros;
    }

    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }
    public function getEmpresa()
    {
        return $this->empresa;
    }

    public function setResponsable($num)
    {
        $this->responsable = $num;
    }
    public function getResponsable()
    {
        return $this->responsable;
    }

    public function setImporte($imp)
    {
        $this->importe = $imp;
    }
    public function getImporte()
    {
        return $this->importe;
    }

    public function setPasajeros($colec)
    {
        $this->pasajeros = $colec;
    }
    public function getPasajeros()
    {
        return $this->pasajeros;
    }

    public function setMensajeOperacion($mensaje)
    {
        $this->mensajeOperacion = $mensaje;
    }
    public function getMensajeOperacion()
    {
        return $this->mensajeOperacion;
    }




    public function buscar()
    {

        $bd = new BaseDatos();
        $respuesta = false;

        $consulta = "SELECT * FROM viaje WHERE idviaje=" . $this->getID();

        if ($this->getID() == null) {
            $this->setMensajeOperacion("No se encontro un valor cargado en la variable ID viaje.");
        } else {

            if ($bd->Iniciar()) {
                if ($bd->Ejecutar($consulta)) {
                    $datos = $bd->Registro();

                    $responsable = new Responsable();
                    $responsable->setNumeroEmpleado($datos['rnumeroempleado']);
                    $responsable->buscar();

                    $empresa= new Empresa();
                    $empresa->setID($datos['idempresa']);
                    $empresa->buscar();

                    $pasajeros=new Pasajero();
                    $colPasajeros= $pasajeros->listar("idviaje = " . $datos["idviaje"]);

                    $this->setDestino($datos['vdestino']);
                    $this->setCantMaxPasajeros($datos['vcantmaxpasajeros']);
                    $this->setEmpresa($empresa);
                    $this->setResponsable($responsable);
                    $this->setPasajeros($colPasajeros);
                    $this->setImporte($datos['vimporte']);

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
        $interaccion= "SELECT * FROM viaje";
        if ($condicion != "") {
            $interaccion .= " WHERE " . $condicion;
        }
        $listadoViajes = array();
        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($interaccion)) {
                while ($fila = $bd->Registro()) {

                    $viaje=new Viaje;

                    $responsable = new Responsable();
                    $responsable->setNumeroEmpleado($fila['rnumeroempleado']);
                    $responsable->buscar();

                    $empresa= new Empresa();
                    $empresa->setID($fila['idempresa']);
                    $empresa->buscar();

                    $pasajeros=new Pasajero();
                    $colPasajeros= $pasajeros->listar("idviaje = " . $fila["idviaje"]);

                    $viaje->setID($fila['idviaje']);
                    $viaje->setDestino($fila['vdestino']);
                    $viaje->setCantMaxPasajeros($fila['vcantmaxpasajeros']);
                    $viaje->setEmpresa($empresa);
                    $viaje->setResponsable($responsable);
                    $viaje->setImporte($fila['vimporte']);
                    $viaje->setPasajeros($colPasajeros);


                    array_push($listadoViajes,$viaje);
                }
            }
        } else {

            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }

        return $listadoViajes;
    }


    public function insertar()
    {

        $bd = new BaseDatos();

        $interaccion = " INSERT INTO viaje (vdestino, vcantmaxpasajeros, idempresa, rnumeroempleado, vimporte) 
        VALUES ('" . $this->getDestino() ."','". $this->getCantMaxPasajeros() ."','". $this->getEmpresa()->getID() ."','". $this->getResponsable()->getNumeroEmpleado() ."','". $this->getImporte() ."')";
        $respuesta = false;

        if ($bd->Iniciar()) {
            if ($id = $bd->devuelveIDInsercion($interaccion)) {
                $this->setID($id);
                $respuesta = true;
            } else {
                $this->setMensajeOperacion("Ocurrio un error al insertar el viaje." . $bd->getError());
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
        $interaccion = "UPDATE viaje SET  vdestino = '" . $this->getDestino() . "', vcantmaxpasajeros = '" . $this->getCantMaxPasajeros() . "', idempresa = '" . $this->getEmpresa()->getID() . "', rnumeroempleado = '" . $this->getResponsable()->getNumeroEmpleado() . "', vimporte = '" . $this->getImporte() . "' WHERE idviaje = '" . $this->getID() . "'";
        $response = false;

        //Se inicia la conexion con la base de datos y se verifica al mismo tiempo
        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($interaccion)) {
                //En caso de que la interaccion sea exitosa se retornara un true al finalizar la operacion
                $response = true;
            } else {
                //Si la operacion falla o retorna un error se visualizara el siguente mensaje
                $this->setMensajeOperacion("No se pudo actualizar la informacion del viaje." . $bd->getError());
            }
        } else {
            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }
        return $response;
    }


    public function eliminar()
    {
        $bd = new BaseDatos();
        $eliminar = "DELETE FROM viaje WHERE idviaje=" . $this->getID();
        $response = false;

        if ($bd->Iniciar()) {
            if ($bd->Ejecutar($eliminar)) {
                $response = true;
            } else {
                $this->setMensajeOperacion("Ocurrio un error al momento de eliminar el viaje " . $bd->getError());
            }
        } else {
            $this->setMensajeOperacion("No se pudo conectar a la base de datos. BD error." . $bd->getError());
        }
        return $response;
    }



    public function mostrarListadoPasajeros()
    {

        $mensaje="";
        $coleccionPasajeros=$this->getPasajeros();

        if (count($this->getPasajeros())> 0){
        for($i=0;$i<count($this->getPasajeros());$i++){
          $mensaje.="------------ Pasajero: ". $i ."------------- \n";
          $mensaje.= $coleccionPasajeros[$i] ."\n";
        } 
        } else {
            $mensaje= "-- ** No hay pasajeros asignados a este viaje. ** -- \n";
        }

        return $mensaje;
    }

    public function __toString()
    {
        
        $mensaje="------------- ******** Viaje ID:". $this->getID(). " ******** --------------- \n";
        $mensaje.="Destino: ". $this->getDestino(). "\n";
        $mensaje.="Cantidad maxima de pasajeros:".$this->getCantMaxPasajeros(). "\n";
        $mensaje.="Importe: ". $this->getImporte(). "\n";
        $mensaje.="-------------- ***************************** ------------- \n";

        
        $mensaje.= "------------- ******** EMPRESA ******** --------------- \n";
        $mensaje.= $this->getEmpresa();
        

        $mensaje.="------------ ******** RESPONSABLE ******** ------------- \n";
        $mensaje.= $this->getResponsable();
    
        
        $mensaje.= "------------- ******** PASAJEROS ******** --------------- \n";
        $mensaje.= $this->mostrarListadoPasajeros();
        $mensaje.="-------------- ***************************** ------------- \n";

        return $mensaje;

    }

    


}
