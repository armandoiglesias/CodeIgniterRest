<?php

require_once(APPPATH .'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Linea extends REST_Controller{

public function __construct(){
        parent::__construct();
         $this->load->database();

        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");
    }

    public function index_get(){
        $query = $this->db->query('select id, linea, icono from lineas');

        $respuesta = array('error' => false
                            , 'lineas' => $query->result_array());

        $this->response($respuesta);

// foreach ($query->result_array() as $row)
// {
//         echo $row['title'];
//         echo $row['name'];
//         echo $row['email'];
// }
    }


}