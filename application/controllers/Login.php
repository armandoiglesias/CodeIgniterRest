<?php

require_once(APPPATH .'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Login extends REST_Controller{

public function __construct(){
        parent::__construct();
         $this->load->database();

        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");
    }

    public function index_post($pagina = 0){

        $data = $this->post();
        if(!isset($data['correo']) || !isset($data['contrasena']) ){
            $respuesta = array('error' => true
                            , 'mensaje' => 'Se Deben proporcionar las credenciales' );

        $this->response($respuesta, REST_Controller::HTTP_FORBIDDEN);
        return;

        }

        $condiciones = array('correo' => $data['correo'], 'contrasena' => $data['contrasena']);

        $query = $this->db->get_where('login', $condiciones);
        $usuarop = $query->row();

        if(!isset($usuarop)){
             $respuesta = array('error' => true
                            , 'mensaje' => 'Usuario/Contrasena Invalido' );

        $this->response($respuesta);
        return;
        }

        //Token
        //$token = bin2hex(openssl_random_pseudo_bytes());

        $token = hash('ripemd160', $data['correo']);

        // Guardo en BD
        $this->db->reset_query();

        $actualizar_token = array('token' => $token);
        $this->db->where( 'id' , $usuarop->id );
        $result =$this->db->update('login', $actualizar_token);

        $respuesta = array('error' => false, 'token' => $token
                            , 'id_usuario'=> $usuarop->id);

        $this->response($respuesta);

    }

//     public function porTipo_get($tipo= 0 , $pagina= 0){
//          $start = $pagina *10;

//          $sql = "SELECT codigo, producto, linea, linea_id, proveedor, descripcion, precio_compra 
// FROM productos ";

//         if($tipo == 0 ){
//             $respuesta = array('error' => true, 'mensaje' => 'missing arguments'       );

//         $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
//         return;
//         }
        
    
//         if($tipo != 0 ){
//             $sql = $sql ." where linea_id = $tipo";
//         }

//         $sql = $sql ." Limit $start, 10";

//         $query = $this->db->query($sql);

//         //die($query);
//          $respuesta = array('error' => false
//                             , 'productos' => $query->result_array());

//         $this->response($respuesta);
//     }

//     public function buscar_get($termino){

//         $query = $this->db->query("
// SELECT codigo, producto, linea, linea_id, proveedor, descripcion, precio_compra 
// FROM productos 
// where producto like '%$termino%' or descripcion like '%$termino'");

//         $respuesta = array('error' => false
//                             , 'productos' => $query->result_array());

//         $this->response($respuesta);
//     }


}