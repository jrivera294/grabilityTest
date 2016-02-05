<?
/**
 * Por ejemplo: Confirma asignación de conductor y envía una notificación al usuario
 *
 * @return 
 */
public function post_confirm(){

	$id = Input::get('service_id');
	$servicio = Service::find($id);

	if($servicio != NULL){

		// Comentario correspondiente al status 2 
		if($servicio->status_id == '6'){
			return Response::json(Array(
				'error' => '2',
				'description' => 'Error correspondiente al nro 2'
			));
		}

		// Verificando que el servicio no tenga conductor asignado ni status diferente a 1
		if ($servicio->driver_id == NULL && $servicio->status_id == '1'){

			// Iniciar transacción para garantizar atomicidad y evitar inconsistencias
			DB::beginTransaction();

			try{

				$driver = Driver::find(Input::get('driver_id'));

				if( $driver->id != NULL && $driver->available != 0){

					$servicio = Service::update($id,array(
						'driver_id' => Input::get('driver_id'),
						'status_id' => '2',
						'card_id' => $driverTmp->car_id
					));

					$driver->available = 0;

				}

			} catch(\Exception $e){ // Esto se puede mejorar un poco, dependiendo de los posibles errores y estados
			    DB::rollback();
			    return Response::json(Array(
			    	'error' => '99',
			    	'description' => 'Conductor no disponible'
			    ));
			}

			DB::commit();
			// Fin de la transacción

			// Notificar al usuario
			$pushMessage = 'Tu servicio ha sido confirmado!';

			$servicio = Service::find($id);
			$push = Push::make();

			// Si el usuario no tiene dispositivo asignado, retornar.
			if($servicio->user->uuid == ''){
				return Response::json(Array(
					'error' => '0',
					'description' => 'Operación exitosa'
				));
			}

			// Type 1 = Iphone, Type 2 = Android
			if($servicio->user->type == '1'){
				$result = $push->ios($servicio->user->uuid, $pushMessage,1,'honk.wav','Open',array('serviceId' => $servicio->id));
			}else if($servicio->user->type == '2'){
				$result = $push->android2($servicio->user->uuid,$pushMessage,1,'default','Open',array('serviceId' => $servicio->id));
			}else{
				return Response::json(Array(
					'error' => '98',
					'description' => 'Dispositivo inválido'
					));
			}

			return Response::json(Array(
				'error' => '0',
				'description' => 'Operación exitosa'
			));

		}else{
			return Response::json(Array(
				'error' => '1',
				'description' => 'Este serivcio ya fue despachado'
			));
		}
	}else{
		return Response::json(Array(
			'error' => '3',
			'description' => 'Servicio inválido'
		));
	}
}