<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Transport extends Controller
{
    /**
     * Get and Display a list of STIB's ROUTEs.
     *
     * @return array $lignes in View lignes
     */
    public function lignes()
    {
        $lignes = $this->getStibsRoutes();
        return view('lignes')->with('lignes',$lignes);
    }

    /**
     * Get and Display Details of one STIB's ROUTE.
     * @param string $route_id the route to display
     * @return array routes
     */
    public function ligne($route_id,$route_direction)
    {
        $route = $this->getStibsRoutes($route_id);
        $route_stops = $this->getRouteStops($route,$route_direction);
        $positions = $this->getPositionsByRoute($route_id);

        $route_stops_with_live_position = $this->makeItHappen($route_stops,$positions);

        return view('ligne')->with(['stops'=>$route_stops_with_live_position,'route'=>$route]);
    }


    public function makeItHappen($routes_stops, $positions)
    {
        $last_stop_id = end($routes_stops)->stop_id;

        foreach ($routes_stops as $key => $route_stop) {
            foreach ($positions as $position){
                if ( $position->directionId != $last_stop_id ) {
                   continue;
                }
                if ($route_stop->stop_id == $position->pointId) {
                    $routes_stops[$key]->here = true;
                }
            }
        }
        return $routes_stops;
    }
    /**
     * Gets the Stibs Routes from JSON FILE.
     *
     * @return array routes
     */
    public function getStibsRoutes($route_id = null)
    {
        $json = file_get_contents( storage_path('stib/routes.json') );
        $obj = json_decode($json);
        $routes = $this->getRouteDirections($obj->routes);

        if ($route_id){
            $key = array_search($route_id, array_column($routes, 'route_short_name'));
            return $routes[$key];
        }
        return $routes;
    }


    public function getRouteStops($route,$direction){
        $path = storage_path("stib/routes_stops/$route->route_short_name.$route->route_long_name.json");
        $json = file_get_contents( $path );
        $obj = json_decode($json);

        $direction_name = $route->route_directions[$direction];
        $key = array_search($direction_name, $obj->directions);
        return $obj->stops[$key];
    }


    /**
     * Gets the Stibs Routes from JSON FILE.
     *
     * @return array routes
     */
    public function getRouteDirections($routes)
    {
        foreach ($routes as $key => $route) {
            $directions = explode(" - ",$route->route_long_name);
            $routes[$key]->route_directions = array(
                $this->slugify($directions[0]) => $directions[0],
                $this->slugify($directions[1]) => $directions[1]
            );
        }
        return $routes;
    }

    /**
     * Get the vehicles positions from STIB's API
     * @param string $route_id -> Stib Route
     * @return string Json
     */
    public function getPositionsByRoute($route_id)
    {
        $token = $this->getToken();
        $options = array(
            CURLOPT_URL             => "https://opendata-api.stib-mivb.be/OperationMonitoring/1.0/VehiclePositionByLine/$route_id",
            CURLOPT_HTTPHEADER      => array(
                                        "Authorization: Bearer $token"
                                    ),
            CURLOPT_RETURNTRANSFER  => true // Returns output instead of printing it
        );
        //INIT CURL
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        
        // EXCUTE COMMANDE CURL. Stocke Résultats
        $json = curl_exec($curl);

        //CLOSE CURL (c'est mieux!) 
        curl_close($curl);

        // Decode les résultats pour travailler en php.
        $data = json_decode($json);
        return $data->lines[0]->vehiclePositions;
    }


    /**
     * Get the Stib Open Data token.
     *
     * @return string token
     */
    public function getToken()
    {
        $options = array(
            CURLOPT_URL             => "https://opendata-api.stib-mivb.be/token",
            CURLOPT_HTTPHEADER      => array(
                                        'Authorization: Basic cVpTQnRCb0hOR2w2eEZZVzltS1VKZjNtY1h3YTpKSUdaTUl6Rm85ZlY4cTB3VTRIbEsyTmxmRGth'
                                    ),
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => "grant_type=client_credentials",
            CURLOPT_RETURNTRANSFER  => true // Returns output instead of printing it
        );
        //INIT CURL
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        
        // EXCUTE COMMANDE CURL. Stocke Résultats
        $json = curl_exec($curl);

        //CLOSE CURL (c'est mieux!) 
        curl_close($curl);

        // Decode les résultats pour travailler en php.
        $data = json_decode($json);
        return $data->access_token;
    }

    public function slugify($string, $replace = array(), $delimiter = '-') {
        // https://github.com/phalcon/incubator/blob/master/Library/Phalcon/Utils/Slug.php
        if (!extension_loaded('iconv')) {
          throw new Exception('iconv module not loaded');
        }
        // Save the old locale and set the new locale to UTF-8
        $oldLocale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, 'en_US.UTF-8');
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        if (!empty($replace)) {
          $clean = str_replace((array) $replace, ' ', $clean);
        }
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower($clean);
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        $clean = trim($clean, $delimiter);
        // Revert back to the old locale
        setlocale(LC_ALL, $oldLocale);
        return $clean;
    }
}
