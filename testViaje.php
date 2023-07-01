<?php




include_once "BaseDatos.php";
include_once "Empresa.php";
include_once "Responsable.php";
include_once "Viaje.php";
include_once "Pasajero.php";




//----------------------------------------Seccion de Empresa ---------------------------------------
function menuEmpresa($empresa)
{

    echo " \n Bienvenido al menu de Empresa ¿que desea hacer? \n";





    do {

        echo " 1) Listado de empresas \n 
        2) Agregar nueva empresa \n
        3) Modificar una empresa existente\n 
        4) Borrar una empresa \n 
        5) Volver \n";

        $opcion = trim(fgets(STDIN));
        switch ($opcion) {
            case 1:
                echo "cargando listado de empresas... \n";
                listadoEmpresas();
                break;

            case 2:
                echo "Ingrese los siguentes datos \n ";
                agregarEmpresa();

                break;

            case 3:
                echo "Ingrese los datos señalados. \n";
                modificarEmpresa();
                break;

            case 4:
                echo "Menu de eliminacion de empresa \n";
                eliminarEmpresa();
                break;

            case 5:
                echo "Volviendo al menu principal... \n";
                break;

            default:
                echo "La opcion ingresada es incorrecta, vuelta a intentarlo. \n";
                break;
        }
    } while ($opcion != 5);
}

function listadoEmpresas()
{
    $empresas = new Empresa();
    $colEmpresas = $empresas->listar();
    
    if (count($colEmpresas)!==null){
    for ($i = 0; $i < count($colEmpresas); $i++) {

        $empresa = $colEmpresas[$i];
        $viajes = new Viaje();
        $arregloViajes = $viajes->listar("idempresa = " . $empresa->getID());

        $empresa->setViajes($arregloViajes);

        echo $empresa;
        sleep(2);
    }
    }else {
        echo "-----*** No hay empresas cargadas ***----- \n";
    }
}

//---------------------------------------

function agregarEmpresa()
{
    $empresa = new Empresa();

    echo "Ingrese el nombre de la empresa \n";
    $nombre = trim(fgets(STDIN));
    echo "Ingrese la direccion de la empresa \n";
    $direccion = trim(fgets(STDIN));

    $empresa->setNombre($nombre);
    $empresa->setDireccion($direccion);

    if ($empresa->insertar()) {
        echo "Los datos se cargaron exitosamente. Estos son los datos: \n ";
        echo $empresa;
    }
}

//---------------------------------------

function eliminarEmpresa()
{

    echo "El listado de empresas es el siguente:\n";
    sleep(2);
    listadoEmpresas();

    echo "Desea eliminar alguna de ellas? \n";
    $respuesta = trim(fgets(STDIN));
    $respuesta = strtoupper($respuesta);

    if ($respuesta == "SI") {

        $empresa = new Empresa();
        echo "Ingrese la ID de la empresa que desea eliminar. \n";
        $idEmpresa = trim(fgets(STDIN));
        $empresa->setID($idEmpresa);
        $empresa->buscar();
        $empresa->setViajes(Viaje::listar("idempresa = " . $empresa->getID()));
        $viajesEmpresa = $empresa->getViajes();


        // if (count($viajesEmpresa) !== null) {

        // echo "La Empresa seleccionada cuenta con viajes anexados a ella, no puede ser eliminada, primero elimine los viajes.";


        /**
         * Voy a dejar esto comentado porque es mi intento de hacer la eliminacion en cascada, lo intente de varias maneras pero no 
         * consegui hacer que funcionara y tampoco pude contactar via discord con las profesoras para solucionarlo, si esto aun esta comentado
         * cuando lo suba al github es porque no consegui solucionarlo. 
         */


        if (count($viajesEmpresa) !== null) {
            echo "La empresa seleccionada tiene viajes asociados, se eliminaran con ella ¿seguro que desea eliminarlos? \n";
            $respuesta = trim(fgets(STDIN));
            $respuesta = strtoupper($respuesta);
            if ($respuesta == "SI") {



                for ($i = 0; $i < count($viajesEmpresa); $i++) {
                    $viaje = $viajesEmpresa[$i];



                    $colPasajeros = Pasajero::listar("idviaje = " . $viaje->getID());
                    if (count($colPasajeros) !== null) {
                        foreach ($colPasajeros as $p) {
                          $p->eliminar();
                        }
                    }

                    $responsable = $viaje->getResponsable();
                    $responsable->eliminar();

                    $viaje->eliminar();
                }

                if ($empresa->eliminar()) {
                    echo "Se elimino la informacion con exito \n";
                } else {
                    echo "Ocurrio un error durante la eliminacion \n";
                    echo $empresa->getMensajeOperacion();
                }
            } else {
                echo "Volviendo al menu de empresas.";
            }
        } else {

            if ($empresa->eliminar()) {
                echo "Se elimino la informacion con exito \n";
            } else {
                echo "Ocurrio un error durante la eliminacion \n";
                echo $empresa->getMensajeOperacion();
            }
        }
    } else {
        echo "Volviendo al menu de la seccion..";
    }
}

//---------------------------------------

function modificarEmpresa()
{

    $empresa = new Empresa();

    echo "Inserta la id de la empresa a modificar \n";
    $idempresa = trim(fgets(STDIN));
    $empresa->setID($idempresa);
    $empresa->buscar();

    echo "La empresa a modificar sera la siguente: \n";
    echo $empresa . "\n";

    echo "¿Es la empresa correcta? \n";
    $respuesta = trim(fgets(STDIN));
    $respuesta = strtoupper($respuesta);


    if ($respuesta == "SI") {
        echo "Ingrese el nuevo nombre para la empresa. \n";
        $nombre = trim(fgets(STDIN));
        echo "Ingrese la nueva direccion de la empresa si es que la hay. \n";
        $direccion = trim(fgets(STDIN));
        $empresa->setNombre($nombre);
        $empresa->setDireccion($direccion);

        if ($empresa->actualizar()) {
            echo "La actualizacion fue exitosa! \n";
            echo "la informacion nueva es: \n";
            echo $empresa;
        } else {
            echo "Hubo un error al actualizar";
            echo $empresa->getMensajeOperacion();
        }
    } else {
        echo "Volviendo al menu de empresas.. \n";
    }
}


//----------------------------------------Seccion de pasajeros ---------------------------------------
function menuPasajeros($empresa)
{

    echo "\n Bienvenido al menu de pasajero ¿que desea hacer? \n";





    do {

        echo " 1) Listado de pasajeros \n 
        2) Agregar nuevo pasajero a un viaje \n
        3) Modificación de un pasajero existente\n 
        4) Borrar un pasajero \n 
        5) Volver \n";


        $opcion = trim(fgets(STDIN));
        switch ($opcion) {
            case 1:
                echo "Cargando listado pasajeros... \n";
                listadoPasajeros();
                break;

            case 2:
                echo "Ingrese los siguentes valores... \n";
                agregarPasajero();
                break;

            case 3:
                echo "Ingresando al modo edicion de pasajero.. \n";
                modificarPasajero();
                break;

            case 4:
                echo "Menu de eliminacion de pasajeros.";
                eliminarPasajero();
                break;

            case 5:
                echo "Volviendo al menu principal... \n";
                break;
            default:
                echo "La opcion ingresada es incorrecta, vuelta a intentarlo. \n";
                break;
        }
    } while ($opcion != 5);
}

//---------------------------------------

function agregarPasajero()
{
    $pasajero = new Pasajero();

    echo "Ingrese el DNI: \n";
    $dni = trim(fgets(STDIN));
    echo "Nombre: \n";
    $nombre = trim(fgets(STDIN));
    echo "Apellido: \n";
    $apellido = trim(fgets(STDIN));
    echo "Numero de telefono: \n";
    $telefono = trim(fgets(STDIN));
    echo "Ingrese la ID del viaje \n";
    $viajeid = trim(fgets(STDIN));

    $pasajero->setDocumento($dni);
    $pasajero->setNombre($nombre);
    $pasajero->setApellido($apellido);
    $pasajero->setTelefono($telefono);
    $pasajero->setViaje($viajeid);

    if ($pasajero->insertar()) {
        echo "Los datos se cargaron exitosamente. Estos son los datos: \n ";
        echo $pasajero;
    }
}

//---------------------------------------

function listadoPasajeros()
{

    $pasajeros = new Pasajero();
    $colpasajeros = $pasajeros->listar();

    for ($i = 0; $i < count($colpasajeros); $i++) {
        echo  $colpasajeros[$i];
        sleep(2);
    }
}

//---------------------------------------

function modificarPasajero()
{

    $pasajero = new Pasajero();

    echo "Escriba el numero de documento del pasajero  \n";
    $documento = trim(fgets(STDIN));
    $pasajero->setDocumento($documento);
    $pasajero->buscar();

    echo "La persona modificada sera la siguente: \n";
    echo $pasajero . "\n";

    echo "¿Es la pasajero que desea modificar? \n";
    $respuesta = trim(fgets(STDIN));
    $respuesta = strtoupper($respuesta);


    if ($respuesta == "SI") {
        echo "Ingrese el nombre del pasajero \n";
        $nombre = trim(fgets(STDIN));
        echo "Ingrese el apellido del pasajero \n";
        $apellido = trim(fgets(STDIN));
        echo "Ingrese el numero de telefono \n";
        $telefono = trim(fgets(STDIN));

        $pasajero->setNombre($nombre);
        $pasajero->setApellido($apellido);
        $pasajero->setTelefono($telefono);

        echo "¿desea cambiar el viaje del pasajero? \n";
        $respuesta = trim(fgets(STDIN));
        $respuesta = strtoupper($respuesta);

        if ($respuesta == "SI") {
            echo "Los viajes son los siguentes.. \n";
            listadoViajes();
            echo "Ingrese el ID del viaje por el que desea remplazarlo, o el mismo en caso de error \n";
            $idViaje = trim(fgets(STDIN));
            $viajes = new Viaje();
            $viajes->setID($idViaje);
            $viajes->buscar();
            $pasajero->setViaje($viajes);
        } else {
            echo "Se mantendra la informacion actual del viaje. \n";
        }



        if ($pasajero->actualizar()) {
            echo "La actualizacion fue exitosa! \n";
            echo "la informacion nueva es: \n";
            echo $pasajero;
        } else {
            echo "Hubo un error al actualizar";
            echo $pasajero->getMensajeOperacion();
        }
    } else {
        echo "No se desea modificar a este pasajero... Volviendo al menu de empresas.. \n";
    }
}

//---------------------------------------

function eliminarPasajero()
{

    echo "Los pasajeros registrados son los siguentes:\n";
    listadoPasajeros();

    echo "¿Desea eliminar alguno de ellos? \n";
    $respuesta = trim(fgets(STDIN));
    $respuesta = strtoupper($respuesta);

    if ($respuesta == "SI") {

        $pasajero = new Pasajero();
        echo "Ingrese el documento del pasajero que desea eliminar. \n";
        $documento = trim(fgets(STDIN));
        $pasajero->setDocumento($documento);
        if ($pasajero->eliminar()) {
            echo "Se elimino la informacion con exito \n";
        } else {
            echo "Ocurrio un error durante la eliminacion \n";
            echo $pasajero->getMensajeOperacion();
        }
    } else {
        echo "Volviendo al menu de la seccion..";
    }
}


//----------------------------------------Seccion del Empleado/Responsable ---------------------------------------
function menuResponsable($empresa)
{

    echo " \nBienvenido al menu del Responsable ¿que desea hacer? \n";





    do {

        echo " 1) Listado de responsables  \n 
        2) Agregar nuevo Responsable al viaje \n
        3) Modificacar un responsable existente\n 
        4) Borrar un responsable \n 
        5) Volver \n";

        $opcion = trim(fgets(STDIN));
        switch ($opcion) {
            case 1:
                echo "Cargando lista de empleados... \n \n";
                listadoResponsables();
                break;

            case 2:
                echo "Ingrese los datos de un nuevo responsable para los viajes \n";
                agregarResponsable();
                break;

            case 3:
                echo "Iniciando menu de edicion. \n";
                modificarResponsable();
                break;

            case 4:
                echo "Menu eliminacion de empleados \n";
                eliminarResponsable();
                break;

            case 5:
                echo "Volviendo al menu principal... \n";
                break;

            default:
                echo "La opcion ingresada es incorrecta, vuelta a intentarlo. \n";
                break;
        }
    } while ($opcion != 5);
}

//---------------------------------------

function listadoResponsables()
{

    $responsables = new Responsable();
    $colResponsables = $responsables->listar();

    for ($i = 0; $i < count($colResponsables); $i++) {

        $responsable = $colResponsables[$i];
        $viajes = new Viaje();
        $arregloViajes = $viajes->listar("rnumeroempleado=" . $responsable->getNumeroEmpleado());


        $responsable->setViajes($arregloViajes);

        echo $responsable;

        sleep(2);
    }
}

//---------------------------------------

function agregarResponsable()
{
    $responsable = new Responsable();

    echo "Ingrese el numero de licencia: \n";
    $licencia = trim(fgets(STDIN));
    echo "Nombre: \n";
    $nombre = trim(fgets(STDIN));
    echo "Apellido: \n";
    $apellido = trim(fgets(STDIN));

    $responsable->setNumeroLicencia($licencia);
    $responsable->setNombre($nombre);
    $responsable->setApellido($apellido);

    if ($responsable->insertar()) {
        echo "Los datos se cargaron exitosamente. Estos son los datos: \n ";
        echo $responsable;
    }
}

//---------------------------------------

function modificarResponsable()
{

    $responsable = new Responsable();

    echo "Inserte el numero del empleado que desea modificar \n";
    $numeroEmpleado = trim(fgets(STDIN));
    $responsable->setNumeroEmpleado($numeroEmpleado);
    $responsable->buscar();

    echo "El empleado seleccionado es el siguente: \n";
    echo $responsable . "\n";

    echo "¿Desea modificar su informacion? \n";
    $respuesta = trim(fgets(STDIN));
    $respuesta = strtoupper($respuesta);


    if ($respuesta == "SI") {
        echo "Ingrese el nombre del empleado. \n";
        $nombre = trim(fgets(STDIN));
        echo "Ingrese el apellido del empleado. \n";
        $apellido = trim(fgets(STDIN));
        echo "Ingrese el numero de licencia nuevo. \n ";
        $numLicencia = trim(fgets(STDIN));

        $responsable->setNombre($nombre);
        $responsable->setApellido($apellido);
        $responsable->setNumeroLicencia($numLicencia);

        if ($responsable->actualizar()) {
            echo "La actualizacion fue exitosa! \n";
            echo "la informacion nueva es: \n";
            echo $responsable;
        } else {
            echo "Hubo un error al actualizar";
            echo $responsable->getMensajeOperacion();
        }
    } else {
        echo "Volviendo al menu de empleados.. \n";
    }
}

function eliminarResponsable()
{

    echo "Los empleados registrados son los siguentes:\n";
    listadoResponsables();

    echo "¿Desea eliminar alguno de ellos? \n";
    $respuesta = trim(fgets(STDIN));
    $respuesta = strtoupper($respuesta);

    if ($respuesta == "SI") {

        $responsable = new Responsable();
        echo "Ingrese el numero del empleado que desea eliminar. \n";
        $numEmpleado = trim(fgets(STDIN));
        $responsable->setNumeroEmpleado($numEmpleado);
        if ($responsable->eliminar()) {
            echo "Se elimino la informacion con exito \n";
        } else {
            echo "Ocurrio un error durante la eliminacion \n";
            echo $responsable->getMensajeOperacion();
        }
    } else {
        echo "Volviendo al menu de la seccion..";
    }
}


//----------------------------------------Seccion de Viaje---------------------------------------
function menuViaje($empresa)
{

    echo " \n Bienvenido al menu del Viaje ¿que desea hacer? \n";





    do {
        echo " 1) Listado de Viajes \n 
        2) Crear un nuevo viaje  \n
        3) Modificar un viaje existente\n 
        4) Borrar un viaje \n 
        5) Volver \n";

        $opcion = trim(fgets(STDIN));
        switch ($opcion) {
            case 1:
                echo "Cargando listado de viajes \n";
                listadoViajes();
                break;

            case 2:
                echo "Ingrese los datos del nuevo viaje \n";
                agregarViaje();
                break;

            case 3:
                echo "Edicion de viaje: \n";
                modificarViaje();
                break;

            case 4:
                echo "Menu de eliminacion de viajes.";
                eliminarViaje();
                break;

            case 5:
                echo "Volviendo al menu principal... \n";
                break;

            default:
                echo "La opcion ingresada es incorrecta, vuelta a intentarlo. \n";
                break;
        }
    } while ($opcion != 5);
}

//---------------------------------------

function listadoViajes()
{

    $Viaje = new Viaje();
    $colViajes = $Viaje->listar();

    for ($i = 0; $i < count($colViajes); $i++) {
        echo $colViajes[$i];
        sleep(2);
    }
}

//---------------------------------------

function agregarViaje()
{
    $viaje = new Viaje();

    echo "Ingrese el numero el destino: \n";
    $destino = trim(fgets(STDIN));
    echo "Ingrese el limite de pasajeros: \n";
    $cantMaxima = trim(fgets(STDIN));

    echo "Ingrese la ID de la empresa: \n";
    $idempresa = trim(fgets(STDIN));
    $empresa = new Empresa();
    $empresa->setID($idempresa);
    $empresa->buscar();

    echo "Ingrese el numero del empleado: \n";
    $numEmpleado = trim(fgets(STDIN));
    $responsable = new Responsable();
    $responsable->setNumeroEmpleado($numEmpleado);
    $responsable->buscar();

    echo "Ingrese el importe del viaje: \n";
    $importe = trim(fgets(STDIN));



    $viaje->setDestino($destino);
    $viaje->setCantMaxPasajeros($cantMaxima);
    $viaje->setEmpresa($empresa);
    $viaje->setResponsable($responsable);
    $viaje->setImporte($importe);

    if ($viaje->insertar()) {
        echo "Los datos se cargaron exitosamente. Estos son los datos: \n ";
        echo $viaje;
    }
}

function modificarViaje()
{

    echo "Los viajes actualmente disponibles para modificar son los siguentes: \n";
    listadoViajes();

    $viaje = new Viaje();

    echo "Inserte el ID del viaje que desea modificar. \n";
    $numID = trim(fgets(STDIN));
    $viaje->setID($numID);
    $viaje->buscar();

    echo "El viaje seleccionado es el siguente: \n";
    echo $viaje . "\n";

    echo "¿Desea modificar su informacion? \n";
    $respuesta = trim(fgets(STDIN));
    $respuesta = strtoupper($respuesta);


    if ($respuesta == "SI") {
        echo "Ingrese el nuevo destino. \n";
        $destino = trim(fgets(STDIN));
        echo "Ingrese la cantidad maxima de pasajeros. \n";
        $maxPasajeros = trim(fgets(STDIN));
        echo "Ingrese el nuevo importe \n ";
        $importe = trim(fgets(STDIN));

        echo "¿Desea cambiar al responsable del viaje?  (si/no) \n";
        $cambiarResponsable = trim(fgets(STDIN));
        $cambiarResponsable = strtoupper($cambiarResponsable);

        if ($cambiarResponsable == "SI") {

            $responsable = new Responsable;
            echo "Listado de empleados: \n";
            listadoResponsables();

            echo "Ingrese la ID del empleado que remplazara al actual responsable. \n";
            $idEmpleado = trim(fgets(STDIN));
            $responsable->setNumeroEmpleado($idEmpleado);
            $responsable->buscar();
            $viaje->setResponsable($responsable);
        } else {
            echo "Se conservara la informacion actual referente al Responsable del viaje. \n";
        }


        echo "¿Desea cambiar la empresa de este viaje?  (si/no) \n";
        $cambiarEmpresa = trim(fgets(STDIN));
        $cambiarEmpresa = strtoupper($cambiarEmpresa);

        if ($cambiarEmpresa == "SI") {
            $empresa = new Empresa();
            echo "Ingrese el id de la nueva empresa para este viaje. \n";
            $idEmpresa = trim(fgets(STDIN));
            $empresa->setID($idEmpresa);
            $empresa->buscar();
            $viaje->setEmpresa($empresa);
        } else {
            echo "El viaje conservara la empresa actual del viaje. \n";
        }

        $viaje->setDestino($destino);
        $viaje->setCantMaxPasajeros($maxPasajeros);
        $viaje->setImporte($importe);

        if ($viaje->actualizar()) {
            echo "La actualizacion fue exitosa! \n";
            echo "la informacion nueva es: \n";
            echo $viaje;
        } else {
            echo "Hubo un error al actualizar";
            echo $viaje->getMensajeOperacion();
        }
    } else {
        echo "Volviendo al menu de empleados.. \n";
    }
}

//---------------------------------------

function eliminarViaje()
{

    echo "El listado de viajes es el siguente:\n";
    listadoViajes();

    echo "Desea eliminar alguno de ellos? \n";
    $respuesta = trim(fgets(STDIN));
    $respuesta = strtoupper($respuesta);

    if ($respuesta == "SI") {

        $viaje = new Viaje();
        echo "Ingrese la ID del viaje que desea eliminar. \n";
        $idViaje = trim(fgets(STDIN));
        $viaje->setID($idViaje);
        if ($viaje->eliminar()) {
            echo "Se elimino la informacion con exito \n";
        } else {
            echo "Ocurrio un error durante la eliminacion \n";
            echo $viaje->getMensajeOperacion();
        }
    } else {
        echo "Volviendo al menu de la seccion..";
    }
}


//----------------------------------------Seccion de testViaje---------------------------------------

function menuTestViaje()
{

    echo " \n Bienvenido al menu del TestViaje ¿que desea hacer? \n";

    $empresa = new Empresa();

    do {

        echo " 1) Menu Empresas. \n 
        2) Menu Empleados/Responsable. \n
        3) Menu Personas/Pasajeros. \n 
        4) Menu Viaje. \n 
        5) Salir \n";

        $opcion = trim(fgets(STDIN));
        switch ($opcion) {
            case 1:
                echo "Cargando menu empresas...";
                menuEmpresa($empresa);
                break;

            case 2:
                echo "Cargando menu empleados...";
                menuResponsable($empresa);
                break;

            case 3:
                echo "Cargando menu pasajeros...";
                menuPasajeros($empresa);
                break;

            case 4:
                echo "Cargando menu del viaje...";
                menuViaje($empresa);
                break;

            case 5:
                echo "Cerrando programa... \n";
                break;

            default:
                echo "La opcion ingresada es incorrecta, vuelta a intentarlo. \n";
                break;
        }
    } while ($opcion != 5);
}

menuTestViaje();
