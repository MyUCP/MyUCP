<?php
/*
|--------------------------------------------------------------------------
| Пути к классам
|--------------------------------------------------------------------------
|
| Список классов в виде ключей массива и путей к их файлам в виде значений
|
*/

return [
	'Registry'	            =>	'engine/protected/Registry.php',
    'Application'	        =>	'engine/protected/Application.php',
	'Config'	            =>	'engine/protected/Config/Config.php',
	'Request'	            =>	'engine/protected/Request/Request.php',
    'Header'	            =>	'engine/protected/Request/Header.php',
	'File'		            =>	'engine/protected/Request/File/File.php',
    'UploadException'		=>	'engine/protected/Request/File/UploadException.php',
	'Cookie'	            =>	'engine/protected/Request/Cookie.php',
    'Session'	            =>	'engine/protected/Session/Session.php',
    'Response'	            =>	'engine/protected/Response/Response.php',
    'ResponseTrait'	        =>	'engine/protected/Response/ResponseTrait.php',
    'ResponseHeader'        =>	'engine/protected/Response/ResponseHeader.php',
    'HeaderBag'	            =>	'engine/protected/Response/HeaderBag.php',
    'HttpResponseException' =>	'engine/protected/Response/HttpResponseException.php',
    'Redirect'	            =>	'engine/protected/Response/Redirect.php',
	'Builder'	            =>	'engine/protected/Database/Builder.php',
	'DB'		            =>	'engine/protected/Database/DB.php',
    'Driver'		        =>	'engine/protected/Database/drivers/Driver.php',
	'MySQLDriver'           =>	'engine/protected/Database/drivers/MySQLDriver.php',
	'Load'		            =>	'engine/protected/Load.php',
	'Router'	            =>	'engine/protected/Routing/Router.php',
	'Route'		            =>	'engine/protected/Routing/Route.php',
    'RouteGroup'            =>	'engine/protected/Routing/RouteGroup.php',
    'RouteAction'           =>	'engine/protected/Routing/RouteAction.php',
    'RouteCollection'       =>	'engine/protected/Routing/RouteCollection.php',
    'RouteCompiler'         =>	'engine/protected/Routing/RouteCompiler.php',
    'CompiledRoute'         =>  'engine/protected/Routing/CompiledRoute.php',
    'CsrfToken'             =>  'engine/protected/Routing/CsrfToken.php',
    'RouteMatch'            =>	'engine/protected/Routing/RouteMatch.php',
    'RouteDependencyResolverTrait'=>'engine/protected/Routing/RouteDependencyResolverTrait.php',
    'UrlGenerator'          =>	'engine/protected/Routing/UrlGenerator.php',
	'HttpException'         =>  'engine/protected/Routing/HttpException.php',
	'Controller'            => 	'engine/protected/Controller/Controller.php',
	'Model'		            =>	'engine/protected/Model/Model.php',
	'View'		            =>	'engine/protected/Views/View.php',
	'Zara'		            =>	'engine/protected/Views/Zara/Zara.php',
	'ZaraCompiler'          =>  'engine/protected/Views/Zara/ZaraCompiler.php',
	'ZaraFactory'           =>	'engine/protected/Views/Zara/ZaraFactory.php',
    'ZaraService'           =>	'app/services/ZaraService.php',
    'HandleExceptions'		=>	'engine/protected/Debug/HandleExceptions.php',
	'Debug'		            =>	'engine/protected/Debug/Debug.php',
	'DebugException'        =>	'engine/protected/Debug/DebugException.php',
	'Dumper'	            =>	'engine/protected/Helpers/Dumper.php',
    'Lang'                  =>  'engine/protected/Localization/Lang.php',
    'Translator'            =>  'engine/protected/Localization/Translator.php',
    'LocalizationLoader'    =>  'engine/protected/Localization/LocalizationLoader.php',
    'Arr'                   =>  'engine/protected/Support/Arr.php',
    'Str'                   =>  'engine/protected/Support/Str.php',
    'Collection'            =>  'engine/protected/Collection/Collection.php',
    'Arrayable'             =>  'engine/protected/Collection/Arrayable.php',
    'Jsonable'              =>  'engine/protected/Collection/Jsonable.php',
];