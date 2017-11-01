<?php

require_once(APPPATH .'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Pedido extends REST_Controller{

public function __construct(){
        parent::__construct();
         $this->load->database();

        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");
    }

    public function realizarPedido_post($token = 0, $usuario = "0"){
        $data = $this->post();

        if($token == 0 || $usuario == "0"){
            $respuesta = array(
                    'error' => true, 
                    'mensaje' => "USuario o Token invalido");
            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if( !isset($data["items"]) || strlen($data["items"]) == 0 ){
            $respuesta = array('error' => true, 
                    'mensaje' => "Se requieeren los Items");
                    $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);

                    return;
        }

        $condiciones = array('id' => $usuario, 'token' => $token );
        $this->db->where($condiciones);
        $query = $this->db->get('login');

        $existe = $query->row();
        if(!$existe){
            $respuesta = array(
                'error' => true, 
                'mensaje' => "Usuario/Token Invalidos");
            $this->response($respuesta,REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Usaurio y Token

        $this->db->reset_query();

        $insertar = array('usuario_id' => $usuario);

        $this->db->insert('ordenes', $insertar);

        $orden_id = $this->db->insert_id();

        // Crear Detalle

        $this->db->reset_query();
        $items = explode(',', $data['items']);

        foreach ($items as $key => $productoid){
            $data_insertar = array('orden_id' => $orden_id
                , 'producto_id' => $productoid);
            $this->db->insert('ordenes_detalle', $data_insertar) ;


        }

        $respuesta = array('error' => false
            , 'orden_id' => $orden_id );

        $this->response($respuesta);

    }

    public function obtenerPedido_get($token = 0, $usuario = 0){
         if($token == 0 || $usuario == 0){
            $respuesta = array(
                    'error' => true, 
                    'mensaje' => "USuario o Token invalido");
            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $condiciones = array('id' => $usuario, 'token' => $token . "" );
        $this->db->where($condiciones);
        $query = $this->db->get('login');

        $existe = $query->row();
        if(!$existe){
            $respuesta = array(
                'error' => true, 
                'mensaje' => "Usuario/Token Invalidos");
            $this->response($respuesta,REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Retornar ORdenes del Usuario 
        $query = $this->db->query("Select * from ordenes where usuario_id = $usuario");

        $ordenes = array();

        foreach ($query->result() as $row)
{
            $query_detalle = $this->db->query('SELECT a.orden_id, b.* from ordenes_detalle a inner join productos b on a.producto_id = b.codigo where a.orden_id=  ' . $row->id);
       $orden = array( 'id' => $row->id, 'creado' => $row->creado_en, 'detalle' => $query_detalle->result());

       array_push($ordenes, $orden);
}
$respuesta = array('error' => false,
        'ordenes' => $ordenes
    );

    $this->response($respuesta);

    }

    public function borrarPedido_delete($token= 0, $usuario= 0, $orden_id = 0){
        if($token == 0 || $usuario == 0 || $orden_id == 0){
            $respuesta = array(
                    'error' => true, 
                    'mensaje' => "USuario o Token invalido");
            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $condiciones = array('id' => $usuario, 'token' => $token . "" );
        $this->db->where($condiciones);
        $query = $this->db->get('login');

        $existe = $query->row();
        if(!$existe){
            $respuesta = array(
                'error' => true, 
                'mensaje' => "Usuario/Token Invalidos");
            $this->response($respuesta,REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // vErificar si orden es de usuario
        $this->db->reset_query();

        $condiciones = array('id' => $orden_id, "usuario_id" => $usuario);
        $this->db->where($condiciones);
        $query =$this->db->get('ordenes');

        $existe = $query->row();
        if(!$existe){
            $respuesta = array(
                'error' => true, 
                'mensaje' => "No se puede borrar orden");
            $this->response($respuesta,REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        $this->db->reset_query();
        $this->db->delete('ordenes', $condiciones);

        $condiciones = array('orden_id' => $orden_id);
        $this->db->delete('ordenes_detalle', $condiciones);

        $respuesta = array('error' => false,
        'mensaje' => 'Orden(es)? Borrada(s)?'
    );

    $this->response($respuesta);

    }

    // public function index_get($pagina = 0){

    //     $start = $pagina *10;
    //     $query = $this->db->query("select * from productos limit $start, 10 ");

    //     $respuesta = array('error' => false
    //                         , 'productos' => $query->result_array());

    //     $this->response($respuesta);


    // }


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