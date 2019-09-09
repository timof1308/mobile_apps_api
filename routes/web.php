<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * home route
 */
$router->get('/', function () use ($router) {
    return 'Mobile Apps RESTful API';
});

$router->group(['prefix' => 'v0'], function () use ($router) {
    /**
     * /users routes
     */
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('', ['as' => 'get_users', 'uses' => 'UserController@getUsers']);
        $router->post('', ['as' => 'create_user', 'uses' => 'UserController@createUser']);
        $router->get('{id}', ['as' => 'get_user', 'uses' => 'UserController@getUser']);
        $router->put('{id}', ['as' => 'update_user', 'uses' => 'UserController@updateUser']);
        $router->delete('{id}', ['as' => 'delete_user', 'uses' => 'UserController@deleteUser']);

        /**
         * /users/{userId}/meetings routes
         */
        $router->group(['prefix' => '{userId}/meetings'], function () use ($router) {
            $router->get('', ['as' => 'get_user_meetings', 'uses' => 'UserController@getMeetings']);
        });
    });

    /**
     * /meetings routes
     */
    $router->group(['prefix' => 'meetings'], function () use ($router) {
        $router->get('', ['as' => 'get_meetings', 'uses' => 'MeetingController@getMeetings']);
        $router->post('', ['as' => 'create_meeting', 'uses' => 'MeetingController@createMeeting']);
        $router->get('{id}', ['as' => 'get_meeting', 'uses' => 'MeetingController@getMeeting']);
        $router->put('{id}', ['as' => 'update_meeting', 'uses' => 'MeetingController@updateMeeting']);
        $router->delete('{id}', ['as' => 'delete_meeting', 'uses' => 'MeetingController@deleteMeeting']);

        /**
         * /meetings/{meetingId}/visitors routes
         */
        $router->group(['prefix' => '{meetingId}/visitors'], function () use ($router) {
            $router->get('', ['as' => 'get_meeting_visitors', 'uses' => 'MeetingController@getVisitors']);
        });
    });

    /**
     * /companies routes
     */
    $router->group(['prefix' => 'companies'], function () use ($router) {
        $router->get('', ['as' => 'get_companies', 'uses' => 'CompanyController@getCompanies']);
        $router->post('', ['as' => 'create_company', 'uses' => 'CompanyController@createCompany']);
        $router->get('{id}', ['as' => 'get_company', 'uses' => 'CompanyController@getCompany']);
        $router->put('{id}', ['as' => 'update_company', 'uses' => 'CompanyController@updateCompany']);
        $router->delete('{id}', ['as' => 'delete_company', 'uses' => 'CompanyController@deleteCompany']);

        /**
         * /companies/{companyId}/visitors routes
         */
        $router->group(['prefix' => '{companyId}/visitors'], function () use ($router) {
            $router->get('', ['as' => 'get_company_visitors', 'uses' => 'CompanyController@getVisitors']);
        });
    });

    /**
     * /visitors routes
     */
    $router->group(['prefix' => 'visitors'], function () use ($router) {
        $router->get('', ['as' => 'get_visitors', 'uses' => 'VisitorController@getVisitors']);
        $router->post('', ['as' => 'create_visitor', 'uses' => 'VisitorController@createVisitor']);
        $router->get('{id}', ['as' => 'get_visitor', 'uses' => 'VisitorController@getVisitor']);
        $router->put('{id}', ['as' => 'update_visitor', 'uses' => 'VisitorController@updateVisitor']);
        $router->delete('{id}', ['as' => 'delete_visitor', 'uses' => 'VisitorController@deleteVisitor']);

        $router->group(['prefix' => '{visitorId}'], function () use ($router) {
            $router->get('qr', ['as' => 'get_qr_code', 'uses' => 'VisitorController@createQr']);
        });
    });

    /**
     * /rooms routes
     */
    $router->group(['prefix' => 'rooms'], function () use ($router) {
        $router->get('', ['as' => 'get_rooms', 'uses' => 'RoomController@getRooms']);
        $router->post('', ['as' => 'create_room', 'uses' => 'RoomController@createRoom']);
        $router->get('{id}', ['as' => 'get_room', 'uses' => 'RoomController@getRoom']);
        $router->put('{id}', ['as' => 'update_room', 'uses' => 'RoomController@updateRoom']);
        $router->delete('{id}', ['as' => 'delete_room', 'uses' => 'RoomController@deleteRoom']);

        /**
         * /rooms/{roomId}/equipment routes
         */
        $router->group(['prefix' => '{roomId}/equipment'], function () use ($router) {
            $router->get('', ['as' => 'get_room_equipment', 'uses' => 'RoomEquipmentController@getRoomEquipment']);
            $router->post('', ['as' => 'create_room_equipment', 'uses' => 'RoomEquipmentController@createRoomEquipment']);
            $router->delete('{id}', ['as' => 'delete_room_equipment', 'uses' => 'RoomEquipmentController@deleteRoomEquipment']);
        });
    });

    /**
     * /equipment routes
     */
    $router->group(['prefix' => 'equipment'], function () use ($router) {
        $router->get('', ['as' => 'get_all_equipment', 'uses' => 'EquipmentController@getAllEquipment']);
        $router->post('', ['as' => 'create_equipment', 'uses' => 'EquipmentController@createEquipment']);
        $router->get('{id}', ['as' => 'get_equipment', 'uses' => 'EquipmentController@getEquipment']);
        $router->put('{id}', ['as' => 'update_equipment', 'uses' => 'EquipmentController@updateEquipment']);
        $router->delete('{id}', ['as' => 'delete_equipment', 'uses' => 'EquipmentController@deleteEquipment']);
    });
});

/**
 * all routes and methods
 * 404 Status Code
 */
$router->group(['prefix' => '{any:.*}'], function () use ($router) {
    $router->get( '/', function() use ($router) {
        return response("not found", 404);
    });
    $router->put( '/', function() use ($router) {
        return response("not found", 404);
    });
    $router->post( '/', function() use ($router) {
        return response("not found", 404);
    });
    $router->delete( '/', function() use ($router) {
        return response("not found", 404);
    });
    $router->patch( '/', function() use ($router) {
        return response("not found", 404);
    });
});
