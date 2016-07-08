<?php

// Routes

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});


// users

// get all users

$app->get('/users', function($request, $response) use($db) {
    // Si necesitamos acceder a alguna variable global en el framework
    // Tenemos que pasarla con use() en la cabecera de la función. Ejemplo: use($db)
    // Va a devolver un objeto JSON con los datos de usuarios.
    // Preparamos la consulta a la tabla.
    $consulta = $db->prepare("select * from users");
    $consulta->execute();
    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    // Devolvemos ese array asociativo como un string JSON.

    $new_response = $response->withHeader('Content-type','application/json')
    ->withJson($resultados);

    return $new_response;
});

// get one user by id

$app->get('/users/{usersid}', function($request, $response) use($db) {
    // Va a devolver un objeto JSON con los datos de usuarios.
    // Preparamos la consulta a la tabla.
    // En PDO los parámetros para las consultas se pasan con :nameparametro (casualmente 
	// coincide con el método usado por Slim).
	// No confundir con el parámetro :usersid que si queremos usarlo tendríamos 
	// que hacerlo con la variable $users_id
    $consulta = $db->prepare("select * from users where users_id=:param1");

    // En el execute es dónde asociamos el :param1 con el valor que le toque.
    $consulta->execute(array(':param1' => $request->getAttribute('usersid')));

    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Devolvemos ese array asociativo como un string JSON.
    $new_response = $response->withHeader('Content-type','application/json')
    ->withJson($resultados);
    //echo json_encode($resultados);

    return $new_response;
});

// create a new user

$app->post('/users',function($request, $response) use($db,$app) {
    
    // Para acceder a los datos recibidos del formulario
    $datosform = $request->getParsedBody();  
    
    // Preparamos la consulta de insert.
    $consulta = $db->prepare("insert into users(name,lastname,email) 
					values (:name,:lastname,:email)");
 
    $estado = $consulta->execute(
    	array(
	        ':name'=> $datosform['name'],
	        ':lastname'=> $datosform['lastname'],
	        ':email'=> $datosform['email']
        )
    );
    
    if ($estado)
        $json_respuesta = array('estado'=>true,'mensaje'=>'Datos insertados correctamente.');
    else
        $json_respuesta = array('estado'=>false,'mensaje'=>'Error al insertar datos en la tabla.');

    $new_response = $response->withHeader('Content-type','application/json')
    ->withJson($json_respuesta);

    return $new_response;
});
 
// // Programamos la ruta de borrado en la API REST (DELETE)
// $app->delete('/users/:users_id',function($users_id) use($db)
// {
//    $consulta = $db->prepare("delete from users where users_id=:id");
 
//    $consulta->execute(array(':id'=>$users_id));
 
// if ($consulta->rowCount() == 1)
//    echo json_encode(array('estado'=>true,'mensaje'=>'El usuario '.$users_id.' ha sido borrado correctamente.'));
//  else
//    echo json_encode(array('estado'=>false,'mensaje'=>'ERROR: ese registro no se ha encontrado en la tabla.'));
 
// });
 
 
// // Actualización de datos de usuario (PUT)
// $app->put('/users/:users_id',function($users_id) use($db,$app) {
//     // Para acceder a los datos recibidos del formulario
//     $datosform = $app->request;
 
//     // Los datos serán accesibles de esta forma:
//     // $datosform->post('lastname')
 
//     // Preparamos la consulta de update.
//     $consulta = $db->prepare("update users set name=:name, lastname=:lastname, email=:email 
// 							where users_id=:users_id");
 
//     $estado = $consulta->execute(
//             array(
//                 ':users_id'=>$users_id,
//                 ':name'=> $datosform->post('name'),
//                 ':lastname'=> $datosform->post('lastname'),
//                 ':email'=> $datosform->post('email')
//                 )
//             );
 
//     // Si se han modificado datos...
//     if ($consulta->rowCount()==1)
//       echo json_encode(array('estado'=>true,'mensaje'=>'Datos actualizados correctamente.'));
//     else
//       echo json_encode(array('estado'=>false,'mensaje'=>'Error al actualizar datos, datos 
// 						no modificados o registro no encontrado.'));
// });