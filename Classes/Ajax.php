<?php
namespace Ks\CoreBundle\Classes;

use Symfony\Component\HttpFoundation\Response;

abstract class Ajax
{
	public static function responseResult($msg)
	{
		$response = array();
		$response['result'] = $msg;
		
		return new Response(
			json_encode($response),
			200,
			array('Content-Type' => 'application/json')
		);
	}
	
	public static function responseDenied()
	{
		$response = array();
		$response['result'] = 'Acceso denegado.';
		
		return new Response(
			json_encode($response),
			200,
			array('Content-Type' => 'application/json')
		);
	}
	
	public static function responseOk($data=false)
	{
		$response = array();
		$response['result'] = 'ok';
		
		if ($data) $response = array_merge($response, $data);
		
		return new Response(
			json_encode($response),
			200,
			array('Content-Type' => 'application/json')
		);
	}
}