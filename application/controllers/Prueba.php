<?php

require_once(APPPATH .'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Prueba extends REST_Controller{

    public function index(){
        echo ("Hola Mundo");
    }

    public function obtenerArreglo_get($index = null){

        

        $arreglo = array("manzana", "Pera", "pigna");

        $respuesta = array();

        if($index > count($arreglo) || $index < 0){
            $respuesta = array('Error' => true , 'mensaje' => 'Indice no existe' );
             $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            if($index != null ){
            //echo json_encode();    
            $respuesta = array('Error' => false, 'fruta' => $arreglo[$index-1]);
            //$this->response($arreglo[$index -1]);
            //return;
            $this->response($respuesta);
        }
            
        }

        
        //echo json_encode($arreglo);

        
    }

    public function ObtenerProducto_get($codigo){

       
        $query = $this->db->query("SELECT codigo, producto, linea, linea_id, proveedor, descripcion, precio_compra FROM productos where codigo = '" . $codigo ."'");

        echo json_encode($query->result());
        return;

// foreach ( as $row)
// {
//         echo $row->title;
//         echo $row->name;
//         echo $row->email;
// }

// echo 'Total Results: ' . $query->num_rows();
    }

    public function __construct(){
        parent::__construct();
         $this->load->database();

        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");
    }

}