<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

//$route['run/([0-9]+)T([0-9]+)/tcx.xml'] = "run/tcx/$1T$2";
$route['manage/instantiate'] = "manage/instantiate";
$route['manage/reset'] = "manage/reset";

$route['([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-]+)/retrieve'] = "$1/retrieve/$2";

$route['([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-]+)/process/([0-9]+)'] = "$1/process/$2/$3";
$route['([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-]+)/process'] = "$1/process/$2";

$route['([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-]+)/email/([0-9]+)'] = "$1/email/$2/$3";
$route['([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-]+)/email'] = "$1/email/$2";

$route['default_controller'] = "runs/redirect";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */
