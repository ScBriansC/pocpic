<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;
$route['admin'] = 'admin/Home';
//Route Vehiculo
$route['admin/vehiculo']['GET'] = 'admin/Vehiculo';
$route['admin/vehiculo/list']['POST'] = 'admin/Vehiculo/Lister';
$route['admin/vehiculo/list2']['GET'] = 'admin/Vehiculo/Lister2';
$route['admin/vehiculo/create/modal']['GET']  = 'admin/Vehiculo/CreateModal';
$route['admin/vehiculo/create']['POST']  = 'admin/Vehiculo/Create';
$route['admin/vehiculo/update/(:num)']['GET']  = 'admin/Vehiculo/UpdateModal/$1';
$route['admin/vehiculo/update/(:num)']['POST']  = 'admin/Vehiculo/Update/$1';
//Route Policia
$route['admin/policia']['GET'] = 'admin/Policia';
$route['admin/policia/list']['POST'] = 'admin/Policia/Lister';
$route['admin/policia/create/modal']['GET']  = 'admin/Policia/CreateModal';
$route['admin/policia/create']['POST']  = 'admin/Policia/Create';
$route['admin/policia/update/(:num)']['GET']  = 'admin/Policia/UpdateModal/$1';
$route['admin/policia/update/(:num)']['POST']  = 'admin/Policia/Update/$1';
$route['admin/policia/delete/(:num)']['GET']  = 'admin/Policia/Delete/$1';
$route['admin/policia']['GET']  = 'admin/Policia';
//Route Comisaria
$route['admin/comisaria']['GET'] = 'admin/Comisaria';
$route['admin/comisaria/list']['POST'] = 'admin/Comisaria/Lister';
$route['admin/comisaria/create/modal']['GET']  = 'admin/Comisaria/CreateModal';
$route['admin/comisaria/create']['POST']  = 'admin/Comisaria/Create';
$route['admin/comisaria/update/(:num)']['GET']  = 'admin/Comisaria/UpdateModal/$1';
$route['admin/comisaria/update/(:num)']['POST']  = 'admin/Comisaria/Update/$1';
$route['admin/comisaria/delete/(:num)']['GET']  = 'admin/Comisaria/Delete/$1';
$route['admin/comisaria']['GET']  = 'admin/Comisaria';

/*
| -------------------------------------------------------------------------
| Sample REST API Routes
| -------------------------------------------------------------------------
*/
//Api policia
$route['api/policia/(:any)/(:any)']['GET'] = 'rest/Policia/policia/id/$1/format/$2';
$route['api/policia']['POST'] = 'rest/Policia/policia';
$route['api/policia/(:num)']['PUT'] = 'rest/Policia/policia/$1';
$route['api/policia/(:num)']['DELETE'] = 'rest/Policia/policia/$1';

//Api vehiculo
$route['api/vehiculo/(:any)/(:any)']['GET'] = 'rest/Vehiculo/vehiculo/id/$1/format/$2';
$route['api/vehiculo']['POST'] = 'rest/Vehiculo/vehiculo';
$route['api/vehiculo/(:num)']['PUT'] = 'rest/Vehiculo/vehiculo/$1';
$route['api/vehiculo/(:num)']['DELETE'] = 'rest/Vehiculo/vehiculo/$1';

//Api Tracker
$route['api/tracker/(:any)/(:any)']['GET'] = 'rest/Tracker/tracker/id/$1/format/$2';
$route['api/tracker']['POST'] = 'rest/Vehiculo/vehiculo';
$route['api/tracker/(:num)']['PUT'] = 'rest/Vehiculo/vehiculo/$1';
$route['api/tracker/(:num)']['DELETE'] = 'rest/Vehiculo/vehiculo/$1';