<?php

namespace App\Controllers;

use App\Models\ClientesModel;

class Registro extends BaseController
{
    public function index()
    {
        $json = array(
            "detalle" => "no encontrado"
        );

        return json_encode($json, true);
    }

    // CREAR UN REGISTRO
    public function create()
    {

        $request = \Config\Services::request();
        $validation = \Config\Services::validation();

        // echo '<pre>'; print_r($ciphertext); echo'</pre>';

        // Capturar registros
        $datos = array(
            "nombre" => $request->getVar("nombre"),
            "apellido" => $request->getVar("apellido"),
            "email" => $request->getVar("email")
        );

        // echo '<pre>'; print_r($datos); echo'</pre>';

        // Validar datos

        if (!empty($datos)) {

            //Validar datos

            $validation->setRules([
                'nombre' => 'required|string|max_length[255]',
                'apellido' => 'required|string|max_length[255]',
                'email' => 'required|valid_email|is_unique[clientes.email]'
            ]);

            $validation->withRequest($this->request)->run();

            if ($validation->getErrors()) {

                $errors = $validation->getErrors();

                $json = array(
                    "status" => 404,
                    "detalle" => $errors
                );

                return json_encode($json, true);

            } else {

                $id_cliente = crypt($datos["nombre"] . $datos["apellido"] . $datos["email"], '$2a$07$dfhdfrexfhgdfhdferttgsad$');
                $llave_secreta = crypt($datos["email"] . $datos["apellido"] . $datos["nombre"], '$2a$07$dfhdfrexfhgdfhdferttgsad$');

                $datos = array(
                    "nombre" => $datos["nombre"],
                    "apellido" => $datos["apellido"],
                    "email" => $datos["email"],
                    "id_cliente" => str_replace('$', 'a', $id_cliente),
                    "llave_secreta" => str_replace('$', 'o', $llave_secreta)
                );
                // print_r($datos); exit();

                $clientesModel = new ClientesModel();
                $clientesModel->save($datos);

                $json = array(
                    "status" => 200,
                    "detalle" => "Registro exitoso, tome sus credenciales y guárdelas",
                    "credenciales" => array("id_cliente" => str_replace('$', 'a', $id_cliente), "llave_secreta" => str_replace('$', 'o', $llave_secreta))

                );

                return json_encode($json, true);
            }
        } else {

            $json = array(

                "status" => 404,
                "detalle" => "Registro con errores"
            );

            return json_encode($json, true);
        }
    }
}
